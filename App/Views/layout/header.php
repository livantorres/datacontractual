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
            z-index: 1040;
        }
        .sidebar-header {
            padding: 20px;
            text-align: center;
            font-size: 1.5rem;
            font-weight: 700;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            white-space: nowrap;
            overflow: hidden;
        }
        .sidebar-header span { color: #5bc0be; }
        
        .sidebar-menu {
            padding: 20px 0;
            list-style: none;
            margin: 0;
        }
        .sidebar-menu li {
            padding: 5px 15px;
            position: relative;
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
        
        /* Sidebar Collapsed (Desktop Only) */
        @media (min-width: 769px) {
            .sidebar.collapsed {
                width: 70px;
            }
            .sidebar.collapsed .sidebar-header {
                font-size: 0;
                padding: 20px 0;
            }
            .sidebar.collapsed .sidebar-header::before {
                content: 'DC';
                font-size: 1.5rem;
                color: #5bc0be;
            }
            .sidebar.collapsed .sidebar-menu li a {
                justify-content: center;
                padding: 12px 0;
            }
            .sidebar.collapsed .sidebar-menu li a i {
                margin-right: 0;
            }
            .sidebar.collapsed .sidebar-menu li a .menu-text {
                position: absolute;
                left: 75px;
                background-color: #0f2027;
                padding: 8px 15px;
                border-radius: 5px;
                font-size: 0.85rem;
                opacity: 0;
                visibility: hidden;
                box-shadow: 0 5px 15px rgba(0,0,0,0.2);
                transition: opacity 0.2s;
                z-index: 1050;
                pointer-events: none;
            }
            .sidebar.collapsed .sidebar-menu li a:hover .menu-text {
                opacity: 1;
                visibility: visible;
            }
            
            .main-content {
                margin-left: 250px;
            }
            .main-content.collapsed {
                margin-left: 70px;
            }
        }
        
        /* Main Content */
        .main-content {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            width: 100%;
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
            position: sticky;
            top: 0;
            z-index: 1030;
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
        
        /* Mobile Overlay */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1035;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar { 
                transform: translateX(-100%); 
            }
            .sidebar.mobile-open { 
                transform: translateX(0); 
            }
            .sidebar-overlay.active {
                display: block;
            }
            
            /* Ajustes del topbar en móvil para evitar superposición */
            .topbar {
                padding: 0 15px;
            }
            .topbar-right-content {
                display: flex;
                flex-direction: column;
                align-items: flex-end;
            }
            #selector_vigencia {
                width: 80px !important;
                font-size: 0.8rem;
                padding: 0.25rem;
            }
            .user-profile span {
                font-size: 0.85rem;
            }
            .user-profile .btn {
                padding: 0.35rem 0.6rem !important;
            }
        }
    </style>
</head>
<body>

<div class="sidebar-overlay" id="sidebar-overlay"></div>

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
            <button class="btn btn-light me-2 me-md-3" id="toggle-sidebar" style="position: relative; z-index: 1050;">
                <i class="fas fa-bars"></i>
            </button>
            <span class="d-md-none fw-bold text-dark me-2" style="font-size: 1.1rem;">Data<span style="color: #5bc0be;">Contractual</span></span>
            <div class="d-flex align-items-center d-none d-sm-flex">
                <span class="fw-bold text-muted me-2">Vigencia:</span>
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
        <div class="user-profile topbar-right-content d-flex align-items-center">
            <div class="d-md-none d-sm-flex me-2">
                <select class="form-select form-select-sm" id="selector_vigencia_movil" style="width: 80px;">
                    <?php 
                    foreach ($vigencias as $v):
                        $selected = ($_SESSION['vigencia_id'] ?? '') == $v['id'] ? 'selected' : '';
                    ?>
                        <option value="<?php echo $v['id']; ?>" <?php echo $selected; ?>><?php echo htmlspecialchars($v['anio']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <span class="me-2 text-dark fw-semibold d-none d-sm-inline"><?php echo $_SESSION['usuario_nombre']; ?></span>
            <div class="btn btn-secondary rounded-circle px-3 py-2"><i class="fas fa-user"></i></div>
        </div>
    </div>
    
    <div class="page-content">
