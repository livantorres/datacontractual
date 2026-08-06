<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="row mb-4">
    <div class="col-12">
        <h2 class="fw-bold">Resumen General</h2>
        <p class="text-muted">Bienvenido de nuevo, <?php echo htmlspecialchars($usuario_nombre); ?>. Aquí tienes el estado de los contratos.</p>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Tarjetas de Estadísticas -->
    <div class="col-md-3">
        <div class="card card-premium bg-primary text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50">Contratos Activos</h6>
                        <h2 class="mb-0 fw-bold">45</h2>
                    </div>
                    <div class="fs-1 text-white-50"><i class="fas fa-file-signature"></i></div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card card-premium bg-warning text-dark h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-black-50">Por Vencer (30 días)</h6>
                        <h2 class="mb-0 fw-bold">8</h2>
                    </div>
                    <div class="fs-1 text-black-50"><i class="fas fa-exclamation-triangle"></i></div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card card-premium bg-success text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50">Monto Total</h6>
                        <h2 class="mb-0 fw-bold">$1.2M</h2>
                    </div>
                    <div class="fs-1 text-white-50"><i class="fas fa-dollar-sign"></i></div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card card-premium bg-info text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50">Clientes Activos</h6>
                        <h2 class="mb-0 fw-bold">12</h2>
                    </div>
                    <div class="fs-1 text-white-50"><i class="fas fa-building"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card card-premium">
            <div class="card-header bg-transparent border-bottom-0 pt-4 pb-0">
                <h5 class="fw-bold">Contratos Recientes</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Cliente</th>
                                <th>Tipo</th>
                                <th>Vencimiento</th>
                                <th>Estado</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="fw-semibold">Colegio San José</span></td>
                                <td>Prestación de Servicios</td>
                                <td>15/10/2026</td>
                                <td><span class="badge bg-success rounded-pill px-3">Firmado</span></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td><span class="fw-semibold">Librería Nacional</span></td>
                                <td>Suministros</td>
                                <td>30/08/2026</td>
                                <td><span class="badge bg-warning text-dark rounded-pill px-3">Borrador</span></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card card-premium">
            <div class="card-header bg-transparent border-bottom-0 pt-4 pb-0">
                <h5 class="fw-bold">Accesos Rápidos</h5>
            </div>
            <div class="card-body">
                <button class="btn btn-primary w-100 mb-3 py-2 text-start"><i class="fas fa-plus me-2"></i> Nuevo Contrato</button>
                <button class="btn btn-outline-secondary w-100 mb-3 py-2 text-start"><i class="fas fa-file-excel me-2"></i> Exportar Reporte</button>
                <button class="btn btn-outline-secondary w-100 py-2 text-start"><i class="fas fa-user-plus me-2"></i> Registrar Cliente</button>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
