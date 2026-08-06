<?php
namespace App\Controllers;

use App\Models\Cliente;
use Exception;

class ClienteController {
    private $clienteModel;
    private $uploadDir = __DIR__ . '/../../public/uploads/clientes/';

    public function __construct() {
        // Verificar sesión
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: /login');
            exit;
        }

        $this->clienteModel = new Cliente();
        
        // Crear directorio de uploads si no existe
        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }
    }

    /**
     * Carga la vista principal del listado de clientes
     */
    public function index() {
        $usuario_nombre = $_SESSION['usuario_nombre'] ?? 'Usuario';
        require_once __DIR__ . '/../Views/clientes/index.php';
    }

    /**
     * Retorna los datos en formato JSON para DataTables
     */
    public function getData() {
        header('Content-Type: application/json');
        try {
            $clientes = $this->clienteModel->getAll();
            // DataTables espera el array de datos bajo la clave "data"
            echo json_encode(['data' => $clientes]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit;
    }

    /**
     * Guarda (crea o actualiza) un cliente vía AJAX
     */
    public function save() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit;
        }

        try {
            $id = $_POST['cliente_id'] ?? '';
            $data = [
                'tipo_cliente'        => $_POST['tipo_cliente'] ?? '',
                'nombre_razon_social' => $_POST['nombre_razon_social'] ?? '',
                'nit_rfc'             => $_POST['nit_rfc'] ?? '',
                'direccion'           => $_POST['direccion'] ?? '',
                'email'               => $_POST['email'] ?? '',
                'telefono'            => $_POST['telefono'] ?? '',
                'estado'              => $_POST['estado'] ?? 'Activo'
            ];

            // Validaciones básicas
            if (empty($data['tipo_cliente']) || empty($data['nombre_razon_social']) || empty($data['nit_rfc'])) {
                echo json_encode(['success' => false, 'message' => 'Faltan campos obligatorios.']);
                exit;
            }

            // Validar NIT único
            if ($this->clienteModel->nitExists($data['nit_rfc'], $id ? $id : null)) {
                echo json_encode(['success' => false, 'message' => 'El Documento/NIT ya se encuentra registrado.']);
                exit;
            }

            // Procesar Foto
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
                $fotoName = 'foto_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
                if (move_uploaded_file($_FILES['foto']['tmp_name'], $this->uploadDir . $fotoName)) {
                    $data['foto'] = $fotoName;
                }
            }

            // Procesar PDF
            if (isset($_FILES['documento_pdf']) && $_FILES['documento_pdf']['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES['documento_pdf']['name'], PATHINFO_EXTENSION);
                if (strtolower($ext) === 'pdf') {
                    $pdfName = 'doc_' . time() . '_' . rand(1000, 9999) . '.pdf';
                    if (move_uploaded_file($_FILES['documento_pdf']['tmp_name'], $this->uploadDir . $pdfName)) {
                        $data['documento_pdf'] = $pdfName;
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'El archivo adjunto debe ser PDF.']);
                    exit;
                }
            }

            if ($id) {
                // Actualizar
                $result = $this->clienteModel->update($id, $data);
                $msg = "Cliente actualizado correctamente.";
            } else {
                // Crear
                $result = $this->clienteModel->create($data);
                $msg = "Cliente registrado correctamente.";
            }

            if ($result) {
                echo json_encode(['success' => true, 'message' => $msg]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al guardar en la base de datos.']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Excepción: ' . $e->getMessage()]);
        }
        exit;
    }

    /**
     * Alterna el estado Activo/Inactivo (Eliminación lógica)
     */
    public function toggleEstado() {
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);
        
        $id = $input['id'] ?? null;
        $estado = $input['estado'] ?? null;

        if ($id && $estado) {
            $nuevoEstado = ($estado === 'Activo') ? 'Inactivo' : 'Activo';
            if ($this->clienteModel->toggleEstado($id, $nuevoEstado)) {
                echo json_encode(['success' => true, 'message' => 'Estado actualizado a ' . $nuevoEstado]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al cambiar estado.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Datos inválidos.']);
        }
        exit;
    }

    /**
     * Eliminación Física (Requerimiento: Implementado pero no usado por defecto en UI)
     */
    public function deleteFisico() {
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);
        
        $id = $input['id'] ?? null;
        if ($id) {
            // Eliminar archivos adjuntos si existen (opcional)
            $cliente = $this->clienteModel->getById($id);
            if ($cliente) {
                if ($cliente['foto'] && file_exists($this->uploadDir . $cliente['foto'])) {
                    unlink($this->uploadDir . $cliente['foto']);
                }
                if ($cliente['documento_pdf'] && file_exists($this->uploadDir . $cliente['documento_pdf'])) {
                    unlink($this->uploadDir . $cliente['documento_pdf']);
                }
            }
            
            if ($this->clienteModel->deleteFisico($id)) {
                echo json_encode(['success' => true, 'message' => 'Cliente eliminado físicamente de la base de datos.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al eliminar el cliente. Puede tener contratos asociados.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'ID no proporcionado.']);
        }
        exit;
    }
}
