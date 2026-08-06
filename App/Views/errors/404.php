<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Página No Encontrada</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(180deg, #0f2027 0%, #203a43 50%, #2c5364 100%);
            background-attachment: fixed;
            color: #fff;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }
        .error-container {
            text-align: center;
            background: rgba(33, 37, 41, 0.6);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 50px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            max-width: 500px;
            width: 90%;
        }
        .error-code {
            font-size: 6rem;
            font-weight: 700;
            color: #5bc0be;
            line-height: 1;
            margin-bottom: 20px;
            text-shadow: 0 5px 15px rgba(91, 192, 190, 0.4);
        }
        .error-message {
            font-size: 1.5rem;
            font-weight: 300;
            margin-bottom: 30px;
        }
        .btn-home {
            background-color: #5bc0be;
            color: #0f2027;
            font-weight: 600;
            padding: 10px 30px;
            border-radius: 50px;
            transition: all 0.3s;
            border: none;
        }
        .btn-home:hover {
            background-color: #4eb0ae;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(91, 192, 190, 0.4);
        }
    </style>
</head>
<body>
    <div class="error-container">
        <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
        <div class="error-code">404</div>
        <div class="error-message">Oops! La página que buscas no existe o fue movida.</div>
        <p class="text-white-50 mb-4">Verifica que la URL sea correcta o regresa al inicio.</p>
        <a href="/dashboard" class="btn btn-home"><i class="fas fa-arrow-left me-2"></i> Volver al Dashboard</a>
    </div>
</body>
</html>
