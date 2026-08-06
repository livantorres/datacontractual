<?php
namespace App\Controllers;

class BaseController {
    /**
     * Cargar y renderizar una vista
     *
     * @param string $viewPath Ej: 'login' o 'dashboard/index'
     * @param array $data Variables que se pasarán a la vista
     */
    protected function render($viewPath, $data = []) {
        // Extraer el array asociativo en variables
        extract($data);
        
        $file = __DIR__ . '/../Views/' . $viewPath . '.php';
        
        if (file_exists($file)) {
            require $file;
        } else {
            die("Vista no encontrada: " . $viewPath);
        }
    }
}
