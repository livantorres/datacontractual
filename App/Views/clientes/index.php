<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">Gestión de Clientes</h2>
    <button class="btn btn-primary" id="btnNuevoCliente">
        <i class="fas fa-plus me-2"></i> Nuevo Cliente
    </button>
</div>

<div class="card card-premium">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle w-100" id="clientesTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Foto</th>
                        <th>Tipo</th>
                        <th>Nombre / Razón Social</th>
                        <th>NIT / RFC</th>
                        <th>Email</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Llenado por DataTables -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Cliente -->
<div class="modal fade" id="modalCliente" tabindex="-1" aria-labelledby="modalClienteLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formCliente" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalClienteLabel">Registrar Cliente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="cliente_id" name="cliente_id">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Tipo de Cliente *</label>
                            <select class="form-select select2-modal" id="tipo_cliente" name="tipo_cliente" required>
                                <option value="Institucion">Institución</option>
                                <option value="Independiente">Independiente</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Documento (NIT/RFC/Cédula) *</label>
                            <input type="text" class="form-control" id="nit_rfc" name="nit_rfc" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Nombre o Razón Social *</label>
                            <input type="text" class="form-control" id="nombre_razon_social" name="nombre_razon_social" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="telefono" name="telefono">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Dirección</label>
                            <input type="text" class="form-control" id="direccion" name="direccion">
                        </div>
                        <hr>
                        <div class="col-md-6">
                            <label class="form-label">Foto / Logo (Opcional)</label>
                            <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Documento PDF (Opcional)</label>
                            <input type="file" class="form-control" id="documento_pdf" name="documento_pdf" accept=".pdf">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnGuardar">Guardar Cliente</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>

<!-- DataTables & Script Específico -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Inicializar Select2 en Modal (requiere dropdownParent para Z-Index en modals de Bootstrap)
    $('.select2-modal').select2({
        theme: 'bootstrap-5',
        dropdownParent: $('#modalCliente'),
        width: '100%'
    });

    // Inicializar DataTables
    const tabla = $('#clientesTable').DataTable({
        ajax: '/clientes/getData',
        columns: [
            { data: 'id' },
            { 
                data: 'foto',
                render: function(data, type, row) {
                    if (data) {
                        return `<img src="/uploads/clientes/${data}" alt="Foto" class="rounded-circle" width="40" height="40" style="object-fit: cover;">`;
                    }
                    return `<div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" style="width:40px; height:40px;"><i class="fas fa-building"></i></div>`;
                }
            },
            { data: 'tipo_cliente' },
            { data: 'nombre_razon_social' },
            { data: 'nit_rfc' },
            { data: 'email' },
            { 
                data: 'estado',
                render: function(data, type, row) {
                    let badge = data === 'Activo' ? 'bg-success' : 'bg-danger';
                    return `<span class="badge ${badge} rounded-pill px-3">${data}</span>`;
                }
            },
            { 
                data: null,
                render: function(data, type, row) {
                    let btnEstado = data.estado === 'Activo' 
                        ? `<button class="btn btn-sm btn-outline-warning btn-estado" title="Inactivar"><i class="fas fa-ban"></i></button>`
                        : `<button class="btn btn-sm btn-outline-success btn-estado" title="Activar"><i class="fas fa-check"></i></button>`;
                    
                    return `
                        <div class="d-flex gap-1">
                            <button class="btn btn-sm btn-outline-primary btn-editar" title="Editar"><i class="fas fa-edit"></i></button>
                            ${btnEstado}
                            <button class="btn btn-sm btn-outline-danger btn-eliminar-fisico" title="Eliminar Físicamente"><i class="fas fa-trash"></i></button>
                        </div>
                    `;
                }
            }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
        responsive: true,
        order: [[0, 'desc']]
    });

    const modalCliente = new bootstrap.Modal(document.getElementById('modalCliente'));
    const formCliente = document.getElementById('formCliente');

    // Botón Nuevo Cliente
    document.getElementById('btnNuevoCliente').addEventListener('click', function() {
        formCliente.reset();
        document.getElementById('cliente_id').value = '';
        $('#tipo_cliente').val('Institucion').trigger('change');
        document.getElementById('modalClienteLabel').innerText = 'Registrar Cliente';
        modalCliente.show();
    });

    // Guardar Cliente (Fetch AJAX)
    formCliente.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const btnGuardar = document.getElementById('btnGuardar');
        btnGuardar.disabled = true;
        btnGuardar.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Guardando...';

        const formData = new FormData(this);

        fetch('/clientes/save', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            btnGuardar.disabled = false;
            btnGuardar.innerText = 'Guardar Cliente';

            if (data.success) {
                modalCliente.hide();
                tabla.ajax.reload(null, false); // Recargar tabla sin perder la paginación
                Swal.fire('¡Éxito!', data.message, 'success');
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        })
        .catch(error => {
            btnGuardar.disabled = false;
            btnGuardar.innerText = 'Guardar Cliente';
            Swal.fire('Error', 'Ocurrió un error inesperado al guardar.', 'error');
        });
    });

    // Delegación de eventos para botones de la tabla
    $('#clientesTable tbody').on('click', '.btn-editar', function() {
        const data = tabla.row($(this).parents('tr')).data();
        
        document.getElementById('cliente_id').value = data.id;
        document.getElementById('nombre_razon_social').value = data.nombre_razon_social;
        document.getElementById('nit_rfc').value = data.nit_rfc;
        document.getElementById('email').value = data.email;
        document.getElementById('telefono').value = data.telefono;
        document.getElementById('direccion').value = data.direccion;
        
        $('#tipo_cliente').val(data.tipo_cliente).trigger('change');
        
        document.getElementById('modalClienteLabel').innerText = 'Editar Cliente';
        modalCliente.show();
    });

    // Alternar Estado (Eliminación Lógica)
    $('#clientesTable tbody').on('click', '.btn-estado', function() {
        const data = tabla.row($(this).parents('tr')).data();
        const accion = data.estado === 'Activo' ? 'Inactivar' : 'Activar';
        
        Swal.fire({
            title: `¿${accion} cliente?`,
            text: `El cliente pasará a estado ${data.estado === 'Activo' ? 'Inactivo' : 'Activo'}.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, continuar'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('/clientes/toggleEstado', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: data.id, estado: data.estado })
                })
                .then(res => res.json())
                .then(resData => {
                    if (resData.success) {
                        tabla.ajax.reload(null, false);
                        Swal.fire('¡Actualizado!', resData.message, 'success');
                    } else {
                        Swal.fire('Error', resData.message, 'error');
                    }
                });
            }
        });
    });

    // Eliminar Físicamente
    $('#clientesTable tbody').on('click', '.btn-eliminar-fisico', function() {
        const data = tabla.row($(this).parents('tr')).data();
        
        Swal.fire({
            title: `¿Eliminar físicamente a ${data.nombre_razon_social}?`,
            text: "¡Esta acción NO se puede deshacer y borrará sus archivos adjuntos!",
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, borrar definitivamente'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('/clientes/deleteFisico', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: data.id })
                })
                .then(res => res.json())
                .then(resData => {
                    if (resData.success) {
                        tabla.ajax.reload(null, false);
                        Swal.fire('¡Eliminado!', resData.message, 'success');
                    } else {
                        Swal.fire('Error', resData.message, 'error');
                    }
                });
            }
        });
    });

});
</script>
