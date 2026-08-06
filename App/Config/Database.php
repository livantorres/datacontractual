<?php
namespace App\Config;

use PDO;
use PDOException;

class Database {
    private static $host = '127.0.0.1';
    private static $db_name = 'datacontractual_db';
    private static $username = 'root';
    private static $password = ''; // Ajustar si laragon/mysql tiene contraseña
    private static $connection = null;

    /**
     * Retorna la instancia de conexión PDO
     * @return PDO
     */
    public static function getConnection() {
        if (self::$connection === null) {
            try {
                $dsn = "mysql:host=" . self::$host . ";dbname=" . self::$db_name . ";charset=utf8mb4";
                $options = [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Lanza excepciones en errores
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Devuelve arrays asociativos por defecto
                    PDO::ATTR_EMULATE_PREPARES   => false,                  // Usa consultas preparadas nativas (más seguro)
                ];
                
                self::$connection = new PDO($dsn, self::$username, self::$password, $options);
            } catch (PDOException $e) {
                // En producción no se debe mostrar el mensaje de error directamente
                die("Error de conexión a la base de datos: " . $e->getMessage());
            }
        }
        return self::$connection;
    }
}
