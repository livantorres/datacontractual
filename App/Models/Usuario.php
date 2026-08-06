<?php
namespace App\Models;

use App\Config\Database;
use PDO;

class Usuario {
    
    /**
     * Autenticar a un usuario por email y contraseña
     */
    public static function authenticate($email, $password) {
        $db = Database::getConnection();
        
        $sql = "SELECT id, nombre, email, password, rol_id, estado FROM usuarios WHERE email = :email LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        
        $user = $stmt->fetch();
        
        if ($user) {
            if ($user['estado'] === 'Inactivo') {
                return ['success' => false, 'message' => 'El usuario se encuentra inactivo.'];
            }
            
            if (password_verify($password, $user['password'])) {
                // Eliminar la contraseña del array de retorno
                unset($user['password']);
                return ['success' => true, 'user' => $user];
            } else {
                return ['success' => false, 'message' => 'Credenciales incorrectas.'];
            }
        }
        
        return ['success' => false, 'message' => 'Credenciales incorrectas.'];
    }
    
    /**
     * Obtener las vigencias disponibles (años fiscales)
     */
    public static function getVigencias() {
        $db = Database::getConnection();
        $sql = "SELECT id, anio FROM vigencias ORDER BY anio DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener la vigencia activa por defecto
     */
    public static function getVigenciaActiva() {
        $db = Database::getConnection();
        $sql = "SELECT id, anio FROM vigencias WHERE estado = 'Activa' LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    /**
     * Registra el inicio de sesión en la bitácora
     */
    public static function registrarAcceso($usuario_id, $ip) {
        $db = Database::getConnection();
        $sql = "INSERT INTO bitacora_auditoria (usuario_id, accion, modulo, detalles, ip) VALUES (:user_id, 'Inicio de Sesión', 'Autenticación', 'El usuario ingresó al sistema', :ip)";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':user_id', $usuario_id, PDO::PARAM_INT);
        $stmt->bindParam(':ip', $ip, PDO::PARAM_STR);
        $stmt->execute();
    }
}
