<?php
namespace App\Controllers;

class DashboardController extends BaseController {
    
    public function __construct() {
        // Proteger el acceso, solo usuarios logueados
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: /login');
            exit;
        }
    }
    
    public function index() {
        $data = [
            'titulo' => 'Dashboard',
            'usuario_nombre' => $_SESSION['usuario_nombre'],
            'vigencia_actual' => $_SESSION['vigencia_actual']
        ];
        
        $this->render('dashboard/index', $data);
    }
}
