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
        $sql = "SELECT id, anio FROM vigencias WHERE estado = 'Activa' ORDER BY anio DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
