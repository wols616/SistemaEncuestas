<?php
require_once 'config.php';
inicializarSesion();

// Obtener información antes de cerrar la sesión
$totalEncuestas = isset($_SESSION['encuestas']) ? count($_SESSION['encuestas']) : 0;

// Destruir la sesión
session_destroy();

// Configurar título de la página
$pageTitle = 'Sesión Cerrada - Sistema de Encuestas Cinematográficas';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome para iconos -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Estilos personalizados -->
    <link href="assets/style.css" rel="stylesheet">
</head>

<body>
    <!-- Navegación simplificada -->
    <nav class="navbar navbar-expand-lg navbar-dark header-custom">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-film me-2"></i>
                Sistema de Encuestas Cinematográficas
            </a>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card-custom text-center">
                    <div class="card-body p-5">
                        <div class="mb-4">
                            <i class="fas fa-sign-out-alt icon-purple mb-3" style="font-size: 4rem;"></i>
                            <h2 class="title-gradient mb-3">Sesión Cerrada</h2>
                        </div>

                        <div class="alert alert-custom-success mb-4">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Tu sesión ha sido cerrada exitosamente.</strong>
                            <br>
                            <small>Todas las encuestas almacenadas han sido eliminadas (<?php echo $totalEncuestas; ?>
                                encuesta<?php echo $totalEncuestas != 1 ? 's' : ''; ?>).</small>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-4 mb-3">
                                <div class="d-flex align-items-center justify-content-center">
                                    <i class="fas fa-shield-check icon-purple me-3"></i>
                                    <div class="text-start">
                                        <h6 class="mb-1">Datos Seguros</h6>
                                        <small class="text-muted">Información protegida</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="d-flex align-items-center justify-content-center">
                                    <i class="fas fa-trash-alt icon-purple me-3"></i>
                                    <div class="text-start">
                                        <h6 class="mb-1">Datos Eliminados</h6>
                                        <small class="text-muted">Sesión limpia</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="d-flex align-items-center justify-content-center">
                                    <i class="fas fa-redo icon-purple me-3"></i>
                                    <div class="text-start">
                                        <h6 class="mb-1">Listo para Comenzar</h6>
                                        <small class="text-muted">Nueva sesión</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="divider-purple"></div>

                        <h5 class="mb-3">¿Qué deseas hacer ahora?</h5>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <a href="index.php" class="btn btn-purple w-100">
                                    <i class="fas fa-home me-2"></i>
                                    Volver al Inicio
                                </a>
                            </div>
                            <div class="col-md-4 mb-3">
                                <a href="formulario.php" class="btn btn-purple-outline w-100">
                                    <i class="fas fa-plus-circle me-2"></i>
                                    Nueva Encuesta
                                </a>
                            </div>
                            <div class="col-md-4 mb-3">
                                <a href="resultados.php" class="btn btn-purple-outline w-100">
                                    <i class="fas fa-chart-bar me-2"></i>
                                    Ver Resultados
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Información adicional -->
                <div class="card-custom mt-4">
                    <div class="card-header-custom">
                        <i class="fas fa-info-circle me-2"></i>
                        Información sobre el Sistema
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-3 mb-3">
                                <i class="fas fa-database icon-purple mb-2" style="font-size: 2rem;"></i>
                                <h6>Sin Base de Datos</h6>
                                <small class="text-muted">Almacenamiento en sesiones PHP</small>
                            </div>
                            <div class="col-md-3 mb-3">
                                <i class="fas fa-code icon-purple mb-2" style="font-size: 2rem;"></i>
                                <h6>Programación POO</h6>
                                <small class="text-muted">Clases especializadas y modulares</small>
                            </div>
                            <div class="col-md-3 mb-3">
                                <i class="fas fa-chart-line icon-purple mb-2" style="font-size: 2rem;"></i>
                                <h6>Estadísticas Dinámicas</h6>
                                <small class="text-muted">Gráficos interactivos con Chart.js</small>
                            </div>
                            <div class="col-md-3 mb-3">
                                <i class="fas fa-mobile-alt icon-purple mb-2" style="font-size: 2rem;"></i>
                                <h6>Diseño Responsivo</h6>
                                <small class="text-muted">Bootstrap con paleta violeta</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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

    <script>
        // Redirigir automáticamente después de 10 segundos
        setTimeout(function () {
            if (confirm('¿Desea ser redirigido al inicio automáticamente?')) {
                window.location.href = 'index.php';
            }
        }, 10000);

        // Animación de entrada
        document.addEventListener('DOMContentLoaded', function () {
            const card = document.querySelector('.card-custom');
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';

            setTimeout(() => {
                card.style.transition = 'all 0.6s ease';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 100);
        });
    </script>
</body>

</html>