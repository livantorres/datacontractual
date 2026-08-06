<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($titulo) ? $titulo . ' - DataContractual' : 'DataContractual'; ?></title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- DataTables -->
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f7f6;
            color: #333;
            overflow-x: hidden;
        }
        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 250px;
            background: linear-gradient(180deg, #0f2027 0%, #203a43 50%, #2c5364 100%);
            color: #fff;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            z-index: 1000;
            overflow: hidden;
        }
        .sidebar.collapsed {
            width: 70px;
        }
        .sidebar-header {
            padding: 20px;
            text-align: center;
            font-size: 1.5rem;
            font-weight: 700;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            white-space: nowrap;
        }
        .sidebar-header span { color: #5bc0be; }
        .sidebar.collapsed .sidebar-header {
            font-size: 0;
            padding: 20px 0;
        }
        .sidebar.collapsed .sidebar-header::before {
            content: 'DC';
            font-size: 1.5rem;
            color: #5bc0be;
        }
        
        .sidebar-menu {
            padding: 20px 0;
            list-style: none;
            margin: 0;
        }
        .sidebar-menu li {
            padding: 5px 15px;
        }
        .sidebar-menu li a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            display: flex;
            align-items: center;
            border-radius: 8px;
            padding: 12px 15px;
            transition: all 0.3s;
            white-space: nowrap;
        }
        .sidebar-menu li a:hover, .sidebar-menu li.active a {
            background-color: rgba(255,255,255,0.1);
            color: #fff;
        }
        .sidebar-menu li a i {
            margin-right: 15px;
            width: 20px;
            text-align: center;
            font-size: 1.1rem;
        }
        .sidebar.collapsed .sidebar-menu li a {
            justify-content: center;
            padding: 12px 0;
        }
        .sidebar.collapsed .sidebar-menu li a i {
            margin-right: 0;
        }
        .sidebar.collapsed .sidebar-menu li a .menu-text {
            display: none;
        }
        
        /* Main Content */
        .main-content {
            margin-left: 250px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }
        .main-content.collapsed {
            margin-left: 70px;
        }
        
        /* Topbar */
        .topbar {
            background-color: #fff;
            height: 70px;
            padding: 0 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .page-content {
            padding: 25px;
            flex-grow: 1;
        }
        
        .card-premium {
            border: none;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.03);
            transition: transform 0.3s;
        }
        .card-premium:hover {
            transform: translateY(-5px);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-250px); width: 250px; }
            .sidebar.collapsed { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .main-content.collapsed { margin-left: 0; }
        }
    </style>
</head>
<body>

<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        Data<span>Contractual</span>
    </div>
    <ul class="sidebar-menu">
        <li class="active">
            <a href="/dashboard"><i class="fas fa-home"></i> <span class="menu-text">Dashboard</span></a>
        </li>
        <li>
            <a href="/clientes"><i class="fas fa-users"></i> <span class="menu-text">Clientes</span></a>
        </li>
        <li>
            <a href="/plantillas"><i class="fas fa-file-code"></i> <span class="menu-text">Plantillas</span></a>
        </li>
        <li>
            <a href="/contratos"><i class="fas fa-file-signature"></i> <span class="menu-text">Contratos</span></a>
        </li>
        <li>
            <a href="/reportes"><i class="fas fa-chart-line"></i> <span class="menu-text">Reportes</span></a>
        </li>
        <li>
            <a href="/login/logout"><i class="fas fa-sign-out-alt text-danger"></i> <span class="menu-text">Salir</span></a>
        </li>
    </ul>
</div>

<div class="main-content" id="main-content">
    <div class="topbar">
        <div class="d-flex align-items-center">
            <button class="btn btn-light me-3" id="toggle-sidebar">
                <i class="fas fa-bars"></i>
            </button>
            <div class="d-flex align-items-center">
                <span class="fw-bold text-muted me-2 d-none d-sm-inline">Vigencia:</span>
                <select class="form-select form-select-sm" id="selector_vigencia" style="width: 100px;">
                    <?php 
                    require_once __DIR__ . '/../../Models/Usuario.php';
                    $vigencias = \App\Models\Usuario::getVigencias();
                    foreach ($vigencias as $v):
                        $selected = ($_SESSION['vigencia_id'] ?? '') == $v['id'] ? 'selected' : '';
                    ?>
                        <option value="<?php echo $v['id']; ?>" <?php echo $selected; ?>><?php echo htmlspecialchars($v['anio']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="user-profile">
            <span class="me-2 text-dark fw-semibold"><?php echo $_SESSION['usuario_nombre']; ?></span>
            <div class="btn btn-secondary rounded-circle px-3 py-2"><i class="fas fa-user"></i></div>
        </div>
    </div>
    
    <div class="page-content">
