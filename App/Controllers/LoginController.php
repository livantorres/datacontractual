<?php
namespace App\Controllers;

use App\Models\Usuario;

class LoginController extends BaseController {
    
    /**
     * Muestra la vista de login
     */
    public function index() {
        // Si ya está logueado, redirigir al dashboard
        if (isset($_SESSION['usuario_id'])) {
            header('Location: /dashboard');
            exit;
        }
        
        $this->render('login');
    }
    
    /**
     * Procesa el login vía AJAX
     */
    public function authenticate() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Leer el body JSON (Fetch API)
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);
            
            $email = trim($data['email'] ?? '');
            $password = $data['password'] ?? '';
            
            if (empty($email) || empty($password)) {
                echo json_encode(['success' => false, 'message' => 'Por favor, complete todos los campos.']);
                return;
            }
            
            $result = Usuario::authenticate($email, $password);
            
            if ($result['success']) {
                // Obtener vigencia activa por defecto
                $vigencia = Usuario::getVigenciaActiva();
                
                // Configurar sesión
                $_SESSION['usuario_id'] = $result['user']['id'];
                $_SESSION['usuario_nombre'] = $result['user']['nombre'];
                $_SESSION['rol_id'] = $result['user']['rol_id'];
                $_SESSION['vigencia_actual'] = $vigencia ? $vigencia['anio'] : date('Y');
                $_SESSION['vigencia_id'] = $vigencia ? $vigencia['id'] : null;
                
                // Registrar IP en bitácora
                $ip = $_SERVER['REMOTE_ADDR'];
                Usuario::registrarAcceso($result['user']['id'], $ip);
                
                echo json_encode(['success' => true, 'redirect' => '/dashboard']);
            } else {
                echo json_encode(['success' => false, 'message' => $result['message']]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
        }
    }
    
    /**
     * Cierra la sesión
     */
    public function logout() {
        session_destroy();
        header('Location: /login');
        exit;
    }
}
