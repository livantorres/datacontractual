<?php
session_start();

// Habilitar reporte de errores para desarrollo (desactivar en producción)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Cargar Autoloader de Composer (Para librerías y nuestras propias clases si lo configuramos)
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

// Autoloader básico para nuestro namespace App
spl_autoload_register(function ($class) {
    // Ejemplo: App\Controllers\LoginController -> App/Controllers/LoginController.php
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/App/';
    $len = strlen($prefix);
    
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

// Enrutador muy básico
$url = isset($_GET['url']) ? $_GET['url'] : 'home';
$url = rtrim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);
$urlParts = explode('/', $url);

$controllerName = 'Home';
$methodName = 'index';

if (!empty($urlParts[0])) {
    $controllerName = ucfirst($urlParts[0]);
}
if (isset($urlParts[1])) {
    $methodName = $urlParts[1];
}

$controllerClass = "App\\Controllers\\" . $controllerName . "Controller";

// Verificamos si el usuario no ha iniciado sesión y no está intentando acceder a Login
if (!isset($_SESSION['usuario_id']) && $controllerName !== 'Login') {
    // Forzar ir a login
    $controllerClass = "App\\Controllers\\LoginController";
    $methodName = 'index';
}

if (class_exists($controllerClass)) {
    $controller = new $controllerClass();
    if (method_exists($controller, $methodName)) {
        // Ejecutar método
        $controller->{$methodName}();
    } else {
        // Método no encontrado (404)
        http_response_code(404);
        echo "404 - Método no encontrado";
    }
} else {
    // Controlador no encontrado (404)
    http_response_code(404);
    echo "404 - Página no encontrada";
}
