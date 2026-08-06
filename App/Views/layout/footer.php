    </div> <!-- Fin page-content -->
</div> <!-- Fin main-content -->

<!-- jQuery (Requerido por Select2 y DataTables) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Inicializar Select2 en todos los select que tengan la clase form-select (excepto los que no queramos)
    $(document).ready(function() {
        // Para Selects Generales
        $('select.form-select:not(.vigencia-select)').select2({
            theme: 'bootstrap-5',
            width: 'resolve'
        });
        
        // Para los selectores de vigencia
        $('.vigencia-select').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
    });

    // Toggle Sidebar para móvil y escritorio
    document.getElementById('toggle-sidebar')?.addEventListener('click', function() {
        if (window.innerWidth <= 768) {
            document.getElementById('sidebar').classList.toggle('mobile-open');
            document.getElementById('sidebar-overlay').classList.toggle('active');
        } else {
            document.getElementById('sidebar').classList.toggle('collapsed');
            document.getElementById('main-content').classList.toggle('collapsed');
        }
    });
    
    // Cerrar sidebar al hacer clic en el overlay (Móvil)
    document.getElementById('sidebar-overlay')?.addEventListener('click', function() {
        document.getElementById('sidebar').classList.remove('mobile-open');
        this.classList.remove('active');
    });

    // Dark Mode Toggle
    const themeToggleBtn = document.getElementById('theme-toggle');
    const themeToggleIcon = themeToggleBtn?.querySelector('i');
    
    function setDarkMode(isDark) {
        if (isDark) {
            document.documentElement.setAttribute('data-bs-theme', 'dark');
            themeToggleIcon?.classList.replace('fa-moon', 'fa-sun');
            localStorage.setItem('theme', 'dark');
        } else {
            document.documentElement.setAttribute('data-bs-theme', 'light');
            themeToggleIcon?.classList.replace('fa-sun', 'fa-moon');
            localStorage.setItem('theme', 'light');
        }
    }

    // Inicializar Dark Mode basado en localStorage
    if (localStorage.getItem('theme') === 'dark') {
        setDarkMode(true);
    }

    themeToggleBtn?.addEventListener('click', function() {
        const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
        setDarkMode(!isDark);
    });

    // Función para Actualizar Vigencia
    function actualizarVigencia(vigencia_id, anio) {
        fetch('/dashboard/setVigencia', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ vigencia_id: vigencia_id, anio: anio })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Vigencia Actualizada',
                    text: 'Cambiando al periodo ' + anio,
                    showConfirmButton: false,
                    timer: 1000
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire('Error', 'No se pudo cambiar la vigencia', 'error');
            }
        });
    }

    // Eventos para selectores de vigencia (usando jQuery por Select2)
    $('.vigencia-select').on('change', function() {
        const vigencia_id = $(this).val();
        const anio = $(this).find('option:selected').text();
        actualizarVigencia(vigencia_id, anio);
    });
</script>

</body>
</html>
