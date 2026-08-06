<?php
namespace App\Models;

use PDO;
use PDOException;
use App\Config\Database;

class Cliente {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Obtiene todos los clientes ordenados por el más reciente
     */
    public function getAll() {
        try {
            $stmt = $this->db->query("SELECT * FROM clientes ORDER BY creado_en DESC");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error en Cliente::getAll - " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene un cliente por su ID
     */
    public function getById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM clientes WHERE id = :id");
            $stmt->execute([':id' => $id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error en Cliente::getById - " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verifica si un NIT o RFC ya existe (para validaciones)
     */
    public function nitExists($nit, $exclude_id = null) {
        try {
            if ($exclude_id) {
                $stmt = $this->db->prepare("SELECT id FROM clientes WHERE nit_rfc = :nit AND id != :id");
                $stmt->execute([':nit' => $nit, ':id' => $exclude_id]);
            } else {
                $stmt = $this->db->prepare("SELECT id FROM clientes WHERE nit_rfc = :nit");
                $stmt->execute([':nit' => $nit]);
            }
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error en Cliente::nitExists - " . $e->getMessage());
            return true; // Asumir true en caso de error para evitar duplicados accidentales
        }
    }

    /**
     * Crea un nuevo cliente
     */
    public function create($data) {
        try {
            $sql = "INSERT INTO clientes (tipo_cliente, nombre_razon_social, nit_rfc, direccion, email, telefono, estado, foto, documento_pdf, rut_pdf, actualizado_por, creado_en) 
                    VALUES (:tipo_cliente, :nombre, :nit, :direccion, :email, :telefono, :estado, :foto, :documento_pdf, :rut_pdf, :actualizado_por, NOW())";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':tipo_cliente' => $data['tipo_cliente'],
                ':nombre'       => $data['nombre_razon_social'],
                ':nit'          => $data['nit_rfc'],
                ':direccion'    => $data['direccion'] ?? null,
                ':email'        => $data['email'] ?? null,
                ':telefono'     => $data['telefono'] ?? null,
                ':estado'       => $data['estado'] ?? 'Activo',
                ':foto'         => $data['foto'] ?? null,
                ':documento_pdf'=> $data['documento_pdf'] ?? null,
                ':rut_pdf'      => $data['rut_pdf'] ?? null,
                ':actualizado_por' => $_SESSION['usuario_id'] ?? null
            ]);
            
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error en Cliente::create - " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualiza un cliente existente
     */
    public function update($id, $data) {
        try {
            // Construimos la consulta dinámica para no sobreescribir archivos si no se envían
            $campos = [
                "tipo_cliente = :tipo_cliente",
                "nombre_razon_social = :nombre",
                "nit_rfc = :nit",
                "direccion = :direccion",
                "email = :email",
                "telefono = :telefono",
                "estado = :estado",
                "actualizado_por = :actualizado_por"
            ];
            
            $params = [
                ':id'           => $id,
                ':tipo_cliente' => $data['tipo_cliente'],
                ':nombre'       => $data['nombre_razon_social'],
                ':nit'          => $data['nit_rfc'],
                ':direccion'    => $data['direccion'] ?? null,
                ':email'        => $data['email'] ?? null,
                ':telefono'     => $data['telefono'] ?? null,
                ':estado'       => $data['estado'] ?? 'Activo',
                ':actualizado_por' => $_SESSION['usuario_id'] ?? null
            ];

            if (isset($data['foto'])) {
                $campos[] = "foto = :foto";
                $params[':foto'] = $data['foto'];
            }
            if (isset($data['documento_pdf'])) {
                $campos[] = "documento_pdf = :documento_pdf";
                $params[':documento_pdf'] = $data['documento_pdf'];
            }
            if (isset($data['rut_pdf'])) {
                $campos[] = "rut_pdf = :rut_pdf";
                $params[':rut_pdf'] = $data['rut_pdf'];
            }

            $sql = "UPDATE clientes SET " . implode(", ", $campos) . " WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Error en Cliente::update - " . $e->getMessage());
            return false;
        }
    }

    /**
     * Cambia el estado de un cliente (Eliminación lógica)
     */
    public function toggleEstado($id, $nuevoEstado) {
        try {
            $stmt = $this->db->prepare("UPDATE clientes SET estado = :estado, actualizado_por = :usuario WHERE id = :id");
            return $stmt->execute([
                ':estado' => $nuevoEstado,
                ':usuario' => $_SESSION['usuario_id'] ?? null,
                ':id' => $id
            ]);
        } catch (PDOException $e) {
            error_log("Error en Cliente::toggleEstado - " . $e->getMessage());
            return false;
        }
    }

    /**
     * Eliminación física de un cliente
     * (Actualmente el botón de eliminar estará comentado según requerimiento, pero el método existe)
     */
    public function deleteFisico($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM clientes WHERE id = :id");
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log("Error en Cliente::deleteFisico - " . $e->getMessage());
            return false;
        }
    }
}
