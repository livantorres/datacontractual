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
    
    /**
     * Endpoint para actualizar la vigencia actual vía AJAX
     */
    public function setVigencia() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);
            
            if (!empty($data['vigencia_id']) && !empty($data['anio'])) {
                $_SESSION['vigencia_id'] = $data['vigencia_id'];
                $_SESSION['vigencia_actual'] = $data['anio'];
                echo json_encode(['success' => true]);
                return;
            }
        }
        echo json_encode(['success' => false]);
    }
}
