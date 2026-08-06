<?php
namespace App\Controllers;

use App\Models\Cliente;
use Exception;
use setasign\Fpdi\Tcpdf\Fpdi;
use Smalot\PdfParser\Parser;

class ClientesController {
    private $clienteModel;
    private $uploadBaseDir = __DIR__ . '/../../public/uploads/clientes/';
    private $dirs = [
        'fotos' => 'fotos/',
        'documentos_nit' => 'documentos_nit/',
        'rut' => 'rut/'
    ];

    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: /login');
            exit;
        }

        $this->clienteModel = new Cliente();
        
        // Crear directorios si no existen
        foreach ($this->dirs as $dir) {
            $path = $this->uploadBaseDir . $dir;
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }
        }
    }

    public function index() {
        $usuario_nombre = $_SESSION['usuario_nombre'] ?? 'Usuario';
        require_once __DIR__ . '/../Views/clientes/index.php';
    }

    public function getData() {
        header('Content-Type: application/json');
        try {
            $clientes = $this->clienteModel->getAll();
            echo json_encode(['data' => $clientes]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit;
    }

    public function save() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit;
        }

        try {
            $id = $_POST['cliente_id'] ?? '';
            $nit = $_POST['nit_rfc'] ?? '';
            $data = [
                'tipo_cliente'        => $_POST['tipo_cliente'] ?? '',
                'nombre_razon_social' => $_POST['nombre_razon_social'] ?? '',
                'nit_rfc'             => $nit,
                'direccion'           => $_POST['direccion'] ?? '',
                'email'               => $_POST['email'] ?? '',
                'telefono'            => $_POST['telefono'] ?? '',
                'estado'              => $_POST['estado'] ?? 'Activo'
            ];

            if (empty($data['tipo_cliente']) || empty($data['nombre_razon_social']) || empty($nit)) {
                echo json_encode(['success' => false, 'message' => 'Faltan campos obligatorios.']);
                exit;
            }

            if ($this->clienteModel->nitExists($nit, $id ? $id : null)) {
                echo json_encode(['success' => false, 'message' => 'El Documento/NIT ya se encuentra registrado.']);
                exit;
            }

            // Validación Inteligente del RUT (si se subió archivo PDF)
            if (isset($_FILES['rut_pdf']) && $_FILES['rut_pdf']['error'] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($_FILES['rut_pdf']['name'], PATHINFO_EXTENSION));
                if ($ext === 'pdf') {
                    try {
                        $parser = new Parser();
                        $pdfParsed = $parser->parseFile($_FILES['rut_pdf']['tmp_name']);
                        $text = $pdfParsed->getText();
                        
                        // Si el texto está vacío, probablemente sea un escaneo (imagen) sin capa de texto.
                        // Si tiene texto, validamos la coincidencia del NIT.
                        if (trim($text) !== '') {
                            // Limpiar guiones y espacios para hacer coincidencia exacta
                            $nitBusqueda = preg_replace('/[^a-zA-Z0-9]/', '', $nit);
                            $textoLimpio = preg_replace('/[^a-zA-Z0-9]/', '', $text);
                            
                            if (strpos($textoLimpio, $nitBusqueda) === false) {
                                echo json_encode([
                                    'success' => false, 
                                    'message' => 'Validación de IA fallida: El número de identificación (NIT/RFC) no coincide con el texto detectado dentro del archivo RUT adjunto.'
                                ]);
                                exit;
                            }
                        }
                    } catch (Exception $e) {
                        // Ignorar errores de parseo si el PDF está corrupto o cifrado de antemano
                    }
                }
            }

            // Si es edición, obtenemos el cliente actual para poder eliminar archivos viejos
            $clienteActual = $id ? $this->clienteModel->getById($id) : null;
            $archivosAActualizar = [];

            // 1. Inserción o Actualización Inicial
            if (!$id) {
                $id = $this->clienteModel->create($data);
                if (!$id) {
                    echo json_encode(['success' => false, 'message' => 'Error al crear el registro inicial.']);
                    exit;
                }
                $msg = "Cliente registrado correctamente.";
            } else {
                $this->clienteModel->update($id, $data);
                $msg = "Cliente actualizado correctamente.";
            }

            // 2. Procesamiento de Archivos con nombre $nit_$id
            $nitLimpio = preg_replace('/[^A-Za-z0-9\-]/', '', $nit); // Limpiar NIT para nombre de archivo

            // -- FOTO --
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
                $fotoName = 'foto_' . $nitLimpio . '_' . $id . '.' . $ext;
                
                // Borrar foto anterior si existe (nueva ruta o legado)
                if ($clienteActual && $clienteActual['foto']) {
                    if (file_exists($this->uploadBaseDir . $this->dirs['fotos'] . $clienteActual['foto'])) {
                        unlink($this->uploadBaseDir . $this->dirs['fotos'] . $clienteActual['foto']);
                    } elseif (file_exists($this->uploadBaseDir . $clienteActual['foto'])) {
                        unlink($this->uploadBaseDir . $clienteActual['foto']);
                    }
                }

                if (move_uploaded_file($_FILES['foto']['tmp_name'], $this->uploadBaseDir . $this->dirs['fotos'] . $fotoName)) {
                    $archivosAActualizar['foto'] = $fotoName;
                }
            }

            // -- DOCUMENTO NIT PDF --
            if (isset($_FILES['documento_pdf']) && $_FILES['documento_pdf']['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES['documento_pdf']['name'], PATHINFO_EXTENSION);
                if (strtolower($ext) === 'pdf') {
                    $pdfName = 'doc_nit_' . $nitLimpio . '_' . $id . '.pdf';
                    
                    if ($clienteActual && $clienteActual['documento_pdf']) {
                        if (file_exists($this->uploadBaseDir . $this->dirs['documentos_nit'] . $clienteActual['documento_pdf'])) {
                            unlink($this->uploadBaseDir . $this->dirs['documentos_nit'] . $clienteActual['documento_pdf']);
                        } elseif (file_exists($this->uploadBaseDir . $clienteActual['documento_pdf'])) {
                            unlink($this->uploadBaseDir . $clienteActual['documento_pdf']);
                        }
                    }

                    if (move_uploaded_file($_FILES['documento_pdf']['tmp_name'], $this->uploadBaseDir . $this->dirs['documentos_nit'] . $pdfName)) {
                        $archivosAActualizar['documento_pdf'] = $pdfName;
                    }
                }
            }

            // -- RUT PDF --
            if (isset($_FILES['rut_pdf']) && $_FILES['rut_pdf']['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES['rut_pdf']['name'], PATHINFO_EXTENSION);
                if (strtolower($ext) === 'pdf') {
                    $rutName = 'doc_rut_' . $nitLimpio . '_' . $id . '.pdf';
                    
                    if ($clienteActual && $clienteActual['rut_pdf']) {
                        if (file_exists($this->uploadBaseDir . $this->dirs['rut'] . $clienteActual['rut_pdf'])) {
                            unlink($this->uploadBaseDir . $this->dirs['rut'] . $clienteActual['rut_pdf']);
                        } elseif (file_exists($this->uploadBaseDir . $clienteActual['rut_pdf'])) {
                            unlink($this->uploadBaseDir . $clienteActual['rut_pdf']);
                        }
                    }

                    if (move_uploaded_file($_FILES['rut_pdf']['tmp_name'], $this->uploadBaseDir . $this->dirs['rut'] . $rutName)) {
                        $archivosAActualizar['rut_pdf'] = $rutName;
                    }
                }
            }

            // 3. Actualizar la base de datos si se subieron nuevos archivos
            if (!empty($archivosAActualizar)) {
                $datosFinales = array_merge($data, $archivosAActualizar);
                $this->clienteModel->update($id, $datosFinales);
            }

            echo json_encode(['success' => true, 'message' => $msg]);

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Excepción: ' . $e->getMessage()]);
        }
        exit;
    }

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

    public function deleteFisico() {
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);
        
        $id = $input['id'] ?? null;
        if ($id) {
            $cliente = $this->clienteModel->getById($id);
            if ($cliente) {
                // Eliminar archivos adjuntos si existen en sus carpetas o en la carpeta raíz (legado)
                if ($cliente['foto']) {
                    if (file_exists($this->uploadBaseDir . $this->dirs['fotos'] . $cliente['foto'])) {
                        unlink($this->uploadBaseDir . $this->dirs['fotos'] . $cliente['foto']);
                    } elseif (file_exists($this->uploadBaseDir . $cliente['foto'])) {
                        unlink($this->uploadBaseDir . $cliente['foto']);
                    }
                }
                
                if ($cliente['documento_pdf']) {
                    if (file_exists($this->uploadBaseDir . $this->dirs['documentos_nit'] . $cliente['documento_pdf'])) {
                        unlink($this->uploadBaseDir . $this->dirs['documentos_nit'] . $cliente['documento_pdf']);
                    } elseif (file_exists($this->uploadBaseDir . $cliente['documento_pdf'])) {
                        unlink($this->uploadBaseDir . $cliente['documento_pdf']);
                    }
                }
                
                if ($cliente['rut_pdf']) {
                    if (file_exists($this->uploadBaseDir . $this->dirs['rut'] . $cliente['rut_pdf'])) {
                        unlink($this->uploadBaseDir . $this->dirs['rut'] . $cliente['rut_pdf']);
                    } elseif (file_exists($this->uploadBaseDir . $cliente['rut_pdf'])) {
                        unlink($this->uploadBaseDir . $cliente['rut_pdf']);
                    }
                }
            }
            
            if ($this->clienteModel->deleteFisico($id)) {
                echo json_encode(['success' => true, 'message' => 'Cliente y archivos eliminados físicamente.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al eliminar el cliente.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'ID no proporcionado.']);
        }
        exit;
    }

    /**
     * Descarga de PDF original o Encriptado usando FPDI/TCPDF al vuelo
     */
    public function descargarPdf() {
        $id = $_GET['id'] ?? null;
        $tipo = $_GET['tipo'] ?? 'documento_pdf'; // 'documento_pdf' o 'rut_pdf'
        $protegido = isset($_GET['protegido']) && $_GET['protegido'] === '1';

        if (!$id) {
            die("ID inválido.");
        }

        $cliente = $this->clienteModel->getById($id);
        if (!$cliente) {
            die("Cliente no encontrado.");
        }

        $archivo = $cliente[$tipo];
        $subcarpeta = $tipo === 'rut_pdf' ? 'rut/' : 'documentos_nit/';
        
        if (!$archivo) {
            die("No hay archivo adjunto de este tipo.");
        }

        $rutaArchivo = $this->uploadBaseDir . $subcarpeta . $archivo;

        if (!file_exists($rutaArchivo)) {
            die("El archivo físico no existe en el servidor.");
        }

        if ($protegido) {
            // Generar PDF protegido con FPDI y TCPDF
            $pdf = new Fpdi();
            
            // Configurar protección: la contraseña es el NIT
            $nit = $cliente['nit_rfc'];
            // TCPDF protection: setProtection($permissions, $user_pass, $owner_pass, $mode, $pubkeys)
            $pdf->SetProtection(['print', 'copy'], $nit, null, 3);
            
            $pageCount = $pdf->setSourceFile($rutaArchivo);
            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $templateId = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($templateId);
                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($templateId);
            }
            
            $nombreDescarga = 'Protegido_' . $archivo;
            $pdf->Output($nombreDescarga, 'D'); // 'D' fuerza la descarga
        } else {
            // Descarga normal sin procesar
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . basename($archivo) . '"');
            header('Content-Length: ' . filesize($rutaArchivo));
            readfile($rutaArchivo);
        }
        exit;
    }
}
