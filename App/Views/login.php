<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - DataContractual</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f2027 0%, #203a43 50%, #2c5364 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            overflow: hidden;
            width: 100%;
            max-width: 450px;
            padding: 2.5rem;
            animation: fadeIn 0.8s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .logo-text {
            font-weight: 700;
            font-size: 1.8rem;
            color: #0f2027;
            text-align: center;
            margin-bottom: 0.5rem;
        }
        .logo-text span {
            color: #2c5364;
        }
        .subtitle {
            text-align: center;
            color: #6c757d;
            margin-bottom: 2rem;
            font-size: 0.9rem;
        }
        .form-control, .form-select {
            border-radius: 10px;
            padding: 0.75rem 1rem;
            border: 1px solid #dee2e6;
            margin-bottom: 1.5rem;
            transition: all 0.3s;
        }
        .form-control:focus, .form-select:focus {
            box-shadow: 0 0 0 0.25rem rgba(44, 83, 100, 0.25);
            border-color: #2c5364;
        }
        .btn-primary {
            background-color: #0f2027;
            border-color: #0f2027;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s;
        }
        .btn-primary:hover {
            background-color: #2c5364;
            border-color: #2c5364;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(44, 83, 100, 0.3);
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="logo-text">Data<span>Contractual</span></div>
        <div class="subtitle">Gestión inteligente de contratos</div>
        
        <form id="loginForm">
            <div class="mb-3">
                <label for="email" class="form-label text-muted small">Correo Electrónico</label>
                <input type="email" class="form-control" id="email" name="email" required placeholder="admin@datacontractual.com">
            </div>
            
            <div class="mb-3">
                <label for="password" class="form-label text-muted small">Contraseña</label>
                <input type="password" class="form-control" id="password" name="password" required placeholder="••••••••">
            </div>
            
            <div class="mb-4">
                <label for="vigencia_id" class="form-label text-muted small">Vigencia (Año Fiscal)</label>
                <select class="form-select" id="vigencia_id" name="vigencia_id" required>
                    <?php if (!empty($vigencias)): ?>
                        <?php foreach($vigencias as $v): ?>
                            <option value="<?php echo htmlspecialchars($v['id']); ?>"><?php echo htmlspecialchars($v['anio']); ?></option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="">No hay vigencias activas</option>
                    <?php endif; ?>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary" id="btnSubmit">
                Ingresar al Sistema
            </button>
        </form>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const btn = document.getElementById('btnSubmit');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Verificando...';
            btn.disabled = true;
            
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const vigencia_id = document.getElementById('vigencia_id').value;
            
            fetch('/login/authenticate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    email: email,
                    password: password,
                    vigencia_id: vigencia_id
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Bienvenido!',
                        text: 'Ingresando al sistema...',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = data.redirect;
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de Autenticación',
                        text: data.message,
                        confirmButtonColor: '#0f2027'
                    });
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error del servidor',
                    text: 'Ocurrió un problema de comunicación.',
                    confirmButtonColor: '#0f2027'
                });
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        });
    </script>
</body>
</html>
