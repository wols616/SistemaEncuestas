<?php
// Incluir configuración y clases necesarias
require_once 'config.php';
require_once 'clases/EncuestaManager.php';
require_once 'clases/EncuestaEstadisticas.php';

// Configurar título de la página
$pageTitle = 'Inicio - Sistema de Encuestas Cinematográficas';

// Crear instancias de las clases
$manager = new EncuestaManager();
$estadisticas = new EncuestaEstadisticas($manager->obtenerTodas());

// Obtener datos para mostrar
$totalEncuestas = $estadisticas->totalEncuestas();
$generoPopular = $estadisticas->generoMasPopular();
$plataformaPopular = $estadisticas->plataformaMasUsada();
$paisPopular = $estadisticas->paisConMasParticipantes();

// Incluir header
include 'views/header.php';
?>

<!-- Bienvenida -->
<div class="row mb-5">
    <div class="col-12">
        <div class="card-custom fade-in-up">
            <div class="card-header-custom text-center">
                <h1 class="mb-2">
                    <i class="fas fa-film me-3"></i>
                    Sistema de Encuestas Cinematográficas Wilber Rivas y Eyleen Salinas
                </h1>
                <p class="mb-0 fs-5">Plataforma integral para el análisis de gustos cinematográficos</p>
            </div>
            <div class="card-body text-center p-5">
                <p class="lead mb-4">
                    Desarrollado con <strong>PHP POO</strong>, <strong>Bootstrap</strong> y almacenamiento en
                    <strong>sesiones</strong>.
                    Gestiona encuestas, visualiza estadísticas y genera reportes interactivos.
                </p>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <a href="formulario.php" class="btn btn-purple btn-lg w-100">
                            <i class="fas fa-plus-circle me-2"></i>
                            Nueva Encuesta
                        </a>
                    </div>
                    <div class="col-md-4 mb-3">
                        <a href="resultados.php" class="btn btn-purple-outline btn-lg w-100">
                            <i class="fas fa-chart-bar me-2"></i>
                            Ver Resultados
                        </a>
                    </div>
                    <div class="col-md-4 mb-3">
                        <a href="buscar.php" class="btn btn-purple-outline btn-lg w-100">
                            <i class="fas fa-search me-2"></i>
                            Buscar Encuestas
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Estadísticas rápidas -->
<div class="row mb-5">
    <div class="col-md-3 mb-4">
        <div class="stat-card hover-effect">
            <div class="stat-number"><?php echo $totalEncuestas; ?></div>
            <div class="stat-label">
                <i class="fas fa-poll me-1"></i>
                Total Encuestas
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="stat-card hover-effect">
            <div class="stat-number">
                <?php echo !empty($generoPopular['generos']) ? count($generoPopular['todos_conteos']) : 0; ?>
            </div>
            <div class="stat-label">
                <i class="fas fa-theater-masks me-1"></i>
                Géneros Registrados
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="stat-card hover-effect">
            <div class="stat-number">
                <?php echo !empty($plataformaPopular['plataformas']) ? count($plataformaPopular['todos_conteos']) : 0; ?>
            </div>
            <div class="stat-label">
                <i class="fas fa-tv me-1"></i>
                Plataformas Usadas
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="stat-card hover-effect">
            <div class="stat-number">
                <?php echo !empty($paisPopular['paises']) ? count($paisPopular['todos_conteos']) : 0; ?>
            </div>
            <div class="stat-label">
                <i class="fas fa-globe me-1"></i>
                Países Participantes
            </div>
        </div>
    </div>
</div>

<!-- Resumen destacado -->
<?php if ($totalEncuestas > 0): ?>
    <div class="row mb-5">
        <div class="col-md-6 mb-4">
            <div class="card-custom hover-effect">
                <div class="card-header-custom">
                    <i class="fas fa-trophy me-2"></i>
                    Género Más Popular
                </div>
                <div class="card-body text-center p-4">
                    <?php if (!empty($generoPopular['generos'])): ?>
                        <h3 class="title-gradient mb-3">
                            <?php echo implode(', ', $generoPopular['generos']); ?>
                        </h3>
                        <p class="fs-5 mb-0">
                            <span class="badge badge-purple fs-6">
                                <?php echo $generoPopular['cantidad']; ?>
                                voto<?php echo $generoPopular['cantidad'] > 1 ? 's' : ''; ?>
                            </span>
                        </p>
                        <?php if (count($generoPopular['generos']) > 1): ?>
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Empate entre géneros
                            </small>
                        <?php endif; ?>
                    <?php else: ?>
                        <p class="text-muted">No hay datos disponibles</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card-custom hover-effect">
                <div class="card-header-custom">
                    <i class="fas fa-play-circle me-2"></i>
                    Plataforma Más Utilizada
                </div>
                <div class="card-body text-center p-4">
                    <?php if (!empty($plataformaPopular['plataformas'])): ?>
                        <h3 class="title-gradient mb-3">
                            <?php echo implode(', ', $plataformaPopular['plataformas']); ?>
                        </h3>
                        <p class="fs-5 mb-0">
                            <span class="badge badge-purple fs-6">
                                <?php echo $plataformaPopular['cantidad']; ?>
                                selección<?php echo $plataformaPopular['cantidad'] > 1 ? 'es' : ''; ?>
                            </span>
                        </p>
                        <?php if (count($plataformaPopular['plataformas']) > 1): ?>
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Empate entre plataformas
                            </small>
                        <?php endif; ?>
                    <?php else: ?>
                        <p class="text-muted">No hay datos disponibles</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-12">
            <div class="card-custom hover-effect">
                <div class="card-header-custom">
                    <i class="fas fa-flag me-2"></i>
                    País con Mayor Participación
                </div>
                <div class="card-body text-center p-4">
                    <?php if (!empty($paisPopular['paises'])): ?>
                        <h3 class="title-gradient mb-3">
                            <?php echo implode(', ', $paisPopular['paises']); ?>
                        </h3>
                        <p class="fs-5 mb-3">
                            <span class="badge badge-purple fs-6">
                                <?php echo $paisPopular['cantidad']; ?>
                                participante<?php echo $paisPopular['cantidad'] > 1 ? 's' : ''; ?>
                            </span>
                        </p>

                        <!-- Ranking de países -->
                        <?php if (count($paisPopular['ranking']) > 1): ?>
                            <div class="mt-3">
                                <h5 class="mb-3">Ranking de Participación:</h5>
                                <div class="row">
                                    <?php
                                    $posicion = 1;
                                    foreach (array_slice($paisPopular['ranking'], 0, 5, true) as $pais => $cantidad):
                                        ?>
                                        <div class="col-md-4 col-sm-6 mb-2">
                                            <div class="d-flex align-items-center">
                                                <span class="badge badge-purple me-2"><?php echo $posicion; ?>°</span>
                                                <span class="flex-grow-1"><?php echo $pais; ?></span>
                                                <span class="text-muted"><?php echo $cantidad; ?></span>
                                            </div>
                                        </div>
                                        <?php
                                        $posicion++;
                                    endforeach;
                                    ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <p class="text-muted">No hay datos disponibles</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="row mb-5">
        <div class="col-12">
            <div class="card-custom text-center">
                <div class="card-body p-5">
                    <i class="fas fa-info-circle icon-purple mb-3" style="font-size: 4rem;"></i>
                    <h3 class="title-gradient mb-3">¡Comienza ahora!</h3>
                    <p class="lead mb-4">
                        No hay encuestas registradas aún. Crea tu primera encuesta para comenzar a recopilar datos sobre
                        gustos cinematográficos.
                    </p>
                    <a href="formulario.php" class="btn btn-purple btn-lg">
                        <i class="fas fa-plus-circle me-2"></i>
                        Crear Primera Encuesta
                    </a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Información del sistema -->
<div class="row">
    <div class="col-12">
        <div class="card-custom">
            <div class="card-header-custom">
                <i class="fas fa-info-circle me-2"></i>
                Características del Sistema
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-code icon-purple me-3"></i>
                            <div>
                                <h6 class="mb-1">Programación Orientada a Objetos</h6>
                                <small class="text-muted">Clases especializadas para cada función</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-database icon-purple me-3"></i>
                            <div>
                                <h6 class="mb-1">Almacenamiento en Sesiones</h6>
                                <small class="text-muted">Sin necesidad de base de datos</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-chart-line icon-purple me-3"></i>
                            <div>
                                <h6 class="mb-1">Estadísticas Dinámicas</h6>
                                <small class="text-muted">Gráficos interactivos con Chart.js</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-shield-alt icon-purple me-3"></i>
                            <div>
                                <h6 class="mb-1">Validación Robusta</h6>
                                <small class="text-muted">Verificación de datos en tiempo real</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-search icon-purple me-3"></i>
                            <div>
                                <h6 class="mb-1">Búsqueda Avanzada</h6>
                                <small class="text-muted">Por ID o nombre de participante</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-mobile-alt icon-purple me-3"></i>
                            <div>
                                <h6 class="mb-1">Diseño Responsivo</h6>
                                <small class="text-muted">Bootstrap con paleta violeta</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'views/footer.php'; ?>