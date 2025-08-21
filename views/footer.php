</div> <!-- Cierre del container -->

<!-- Footer -->
<footer class="footer-custom mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h5><i class="fas fa-film me-2"></i>Sistema de Encuestas Cinematográficas</h5>
                <p class="mb-0">Desarrollado con PHP POO y Bootstrap</p>
            </div>
            <div class="col-md-6 text-md-end">
                <p class="mb-0">
                    <i class="fas fa-calendar-alt me-1"></i>
                    © <?php echo date('Y'); ?> - Todos los derechos reservados
                </p>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Scripts personalizados -->
<script>
    // Función para confirmar eliminación
    function confirmarEliminacion(id, nombre) {
        return confirm(`¿Está seguro de que desea eliminar la encuesta de ${nombre} (ID: ${id})?`);
    }

    // Función para mostrar alertas temporales
    function mostrarAlerta(mensaje, tipo = 'info') {
        const alertaHtml = `
                <div class="alert alert-custom-${tipo} alert-dismissible fade show" role="alert">
                    <i class="fas fa-info-circle me-2"></i>
                    ${mensaje}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;

        const container = document.querySelector('.container');
        container.insertAdjacentHTML('afterbegin', alertaHtml);

        // Auto-ocultar después de 5 segundos
        setTimeout(() => {
            const alerta = document.querySelector('.alert');
            if (alerta) {
                alerta.remove();
            }
        }, 5000);
    }

    // Función para validar formularios en tiempo real
    function validarCampo(campo, patron, mensaje) {
        campo.addEventListener('input', function () {
            if (patron.test(this.value)) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            } else {
                this.classList.remove('is-valid');
                this.classList.add('is-invalid');
            }
        });
    }

    // Animaciones de entrada
    document.addEventListener('DOMContentLoaded', function () {
        const elementos = document.querySelectorAll('.card-custom, .stat-card');
        elementos.forEach((elemento, index) => {
            elemento.style.opacity = '0';
            elemento.style.transform = 'translateY(30px)';

            setTimeout(() => {
                elemento.style.transition = 'all 0.6s ease';
                elemento.style.opacity = '1';
                elemento.style.transform = 'translateY(0)';
            }, index * 100);
        });
    });
</script>
</body>

</html>