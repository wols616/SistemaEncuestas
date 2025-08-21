<?php
// Incluir configuración y clases necesarias
require_once 'config.php';
require_once 'clases/EncuestaManager.php';
require_once 'clases/EncuestaEstadisticas.php';

// Configurar título de la página
$pageTitle = 'Resultados y Estadísticas - Sistema de Encuestas Cinematográficas';

// Crear instancias
$manager = new EncuestaManager();
$encuestas = $manager->obtenerTodas();
$estadisticas = new EncuestaEstadisticas($encuestas);

// Obtener estadísticas
$resumen = $estadisticas->resumenCompleto();
$datosGraficos = $estadisticas->datosParaGraficos();
$analisisRelaciones = $estadisticas->analisisRelaciones();
$promedioSemanal = $estadisticas->promedioSemanal();
$estadisticasFallos = $manager->obtenerEstadisticasFallos();

// Procesar acciones
if (isset($_POST['action'])) {
    if ($_POST['action'] === 'eliminar' && isset($_POST['id'])) {
        $resultado = $manager->eliminarEncuesta($_POST['id']);

        // Recargar datos después de eliminar
        $encuestas = $manager->obtenerTodas();
        $estadisticas->setEncuestas($encuestas);
        $resumen = $estadisticas->resumenCompleto();
        $datosGraficos = $estadisticas->datosParaGraficos();
        $promedioSemanal = $estadisticas->promedioSemanal();
        $estadisticasFallos = $manager->obtenerEstadisticasFallos();

        $mensaje = $resultado['message'];
        $tipoMensaje = $resultado['success'] ? 'success' : 'danger';
    } elseif ($_POST['action'] === 'reiniciar_contador_fallos') {
        $manager->reiniciarContadorFallidas();

        // Recargar estadísticas de fallos
        $estadisticasFallos = $manager->obtenerEstadisticasFallos();

        $mensaje = 'Contador de encuestas fallidas reiniciado exitosamente';
        $tipoMensaje = 'success';
    }
}

// Incluir header
include 'views/header.php';
?>

<!-- Título y acciones -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h2 class="title-gradient mb-0">
                    <i class="fas fa-chart-bar me-2"></i>
                    Resultados y Estadísticas
                </h2>
                <p class="text-muted mt-2">Análisis completo de las encuestas cinematográficas</p>
            </div>
            <div>
                <a href="formulario.php" class="btn btn-purple me-2">
                    <i class="fas fa-plus-circle me-1"></i>
                    Nueva Encuesta
                </a>
                <a href="index.php" class="btn btn-purple-outline">
                    <i class="fas fa-home me-1"></i>
                    Inicio
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Mostrar mensajes -->
<?php if (isset($mensaje)): ?>
    <div class="alert alert-custom-<?php echo $tipoMensaje; ?>">
        <i class="fas fa-<?php echo $tipoMensaje === 'success' ? 'check-circle' : 'exclamation-triangle'; ?> me-2"></i>
        <?php echo htmlspecialchars($mensaje); ?>
    </div>
<?php endif; ?>

<?php if (empty($encuestas)): ?>
    <!-- Sin datos -->
    <div class="row">
        <div class="col-12">
            <div class="card-custom text-center">
                <div class="card-body p-5">
                    <i class="fas fa-chart-bar icon-purple mb-3" style="font-size: 4rem;"></i>
                    <h3 class="title-gradient mb-3">No hay datos para mostrar</h3>
                    <p class="lead mb-4">
                        No se han registrado encuestas aún. Crea tu primera encuesta para ver estadísticas y gráficos.
                    </p>
                    <a href="formulario.php" class="btn btn-purple btn-lg">
                        <i class="fas fa-plus-circle me-2"></i>
                        Crear Primera Encuesta
                    </a>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>

    <!-- Estadísticas generales -->
    <div class="row mb-5">
        <div class="col-md-3 mb-4">
            <div class="stat-card hover-effect">
                <div class="stat-number"><?php echo $resumen['total_encuestas']; ?></div>
                <div class="stat-label">
                    <i class="fas fa-poll me-1"></i>
                    Total Encuestas
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="stat-card hover-effect">
                <div class="stat-number">
                    <?php echo count($resumen['genero_popular']['todos_conteos'] ?? []); ?>
                </div>
                <div class="stat-label">
                    <i class="fas fa-theater-masks me-1"></i>
                    Géneros Diferentes
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="stat-card hover-effect">
                <div class="stat-number">
                    <?php echo count($resumen['plataforma_popular']['todos_conteos'] ?? []); ?>
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
                    <?php echo count($resumen['pais_popular']['todos_conteos'] ?? []); ?>
                </div>
                <div class="stat-label">
                    <i class="fas fa-globe me-1"></i>
                    Países Participantes
                </div>
            </div>
        </div>
    </div>

    <!-- Botón para mostrar estadísticas de fallos -->
    <div class="row mb-3">
        <div class="col-12 text-center">
            <button type="button" class="btn btn-outline-purple" id="toggleEstadisticasFallos"
                onclick="toggleEstadisticasFallos()">
                <i class="fas fa-chart-line me-2"></i>
                Ver Estadísticas de Intentos
                <span class="badge bg-secondary ms-2"><?php echo $estadisticasFallos['total_intentos']; ?></span>
                <i class="fas fa-chevron-down ms-2" id="iconoToggleFallos"></i>
            </button>
            <div class="mt-2">
                <small class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    Muestra detalles sobre intentos exitosos y fallidos de encuestas
                </small>
            </div>
        </div>
    </div>

    <!-- Estadísticas de intentos y fallos (ocultas por defecto) -->
    <div class="row mb-5 d-none" id="estadisticasFallos">
        <div class="col-md-3 mb-4">
            <div class="stat-card hover-effect">
                <div class="stat-number text-success"><?php echo $estadisticasFallos['total_exitosas']; ?></div>
                <div class="stat-label">
                    <i class="fas fa-check-circle me-1"></i>
                    Encuestas Exitosas
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="stat-card hover-effect">
                <div class="stat-number text-danger"><?php echo $estadisticasFallos['total_fallidas']; ?></div>
                <div class="stat-label">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    Intentos Fallidos
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="stat-card hover-effect">
                <div class="stat-number"><?php echo $estadisticasFallos['total_intentos']; ?></div>
                <div class="stat-label">
                    <i class="fas fa-calculator me-1"></i>
                    Total de Intentos
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="stat-card hover-effect">
                <div class="stat-number text-success"><?php echo $estadisticasFallos['tasa_exito']; ?>%</div>
                <div class="stat-label">
                    <i class="fas fa-percentage me-1"></i>
                    Tasa de Éxito
                </div>
            </div>
        </div>
    </div>

    <!-- Navegación de pestañas -->
    <ul class="nav nav-tabs nav-tabs-custom mb-4" id="resultadosTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="graficos-tab" data-bs-toggle="tab" data-bs-target="#graficos" type="button"
                role="tab">
                <i class="fas fa-chart-pie me-1"></i>Gráficos
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="listado-tab" data-bs-toggle="tab" data-bs-target="#listado" type="button"
                role="tab">
                <i class="fas fa-list me-1"></i>Listado Completo
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="estadisticas-tab" data-bs-toggle="tab" data-bs-target="#estadisticas" type="button"
                role="tab">
                <i class="fas fa-calculator me-1"></i>Estadísticas
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="analisis-tab" data-bs-toggle="tab" data-bs-target="#analisis" type="button"
                role="tab">
                <i class="fas fa-brain me-1"></i>Análisis
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="fallos-tab" data-bs-toggle="tab" data-bs-target="#fallos" type="button" role="tab">
                <i class="fas fa-exclamation-circle me-1"></i>Historial de Fallos
                <?php if ($estadisticasFallos['total_fallidas'] > 0): ?>
                    <span class="badge bg-danger ms-1"><?php echo $estadisticasFallos['total_fallidas']; ?></span>
                <?php endif; ?>
            </button>
        </li>
    </ul>

    <!-- Contenido de las pestañas -->
    <div class="tab-content" id="resultadosTabContent">
        <!-- Pestaña de Gráficos -->
        <div class="tab-pane fade show active" id="graficos" role="tabpanel">
            <div class="row">
                <!-- Gráfico de Géneros -->
                <div class="col-lg-6 mb-4">
                    <div class="chart-container">
                        <h4 class="chart-title"><?php echo $datosGraficos['generos']['titulo']; ?></h4>
                        <div class="chart-wrapper">
                            <canvas id="graficoGeneros"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Gráfico de Plataformas -->
                <div class="col-lg-6 mb-4">
                    <div class="chart-container">
                        <h4 class="chart-title"><?php echo $datosGraficos['plataformas']['titulo']; ?></h4>
                        <div class="chart-wrapper">
                            <canvas id="graficoPlataformas"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Gráfico de Países -->
                <div class="col-lg-6 mb-4">
                    <div class="chart-container">
                        <h4 class="chart-title"><?php echo $datosGraficos['paises']['titulo']; ?></h4>
                        <div class="chart-wrapper">
                            <canvas id="graficoPaises"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Gráfico de Frecuencias -->
                <div class="col-lg-6 mb-4">
                    <div class="chart-container">
                        <h4 class="chart-title"><?php echo $datosGraficos['frecuencias']['titulo']; ?></h4>
                        <div class="chart-wrapper">
                            <canvas id="graficoFrecuencias"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pestaña de Listado -->
        <div class="tab-pane fade" id="listado" role="tabpanel">
            <div class="card-custom">
                <div class="card-header-custom">
                    <i class="fas fa-table me-2"></i>
                    Listado Completo de Encuestas
                    <span class="badge badge-purple ms-2"><?php echo count($encuestas); ?>
                        encuesta<?php echo count($encuestas) != 1 ? 's' : ''; ?></span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-custom mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>País</th>
                                    <th>Género Favorito</th>
                                    <th>Frecuencia</th>
                                    <th>Plataformas</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($encuestas as $encuesta): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($encuesta['id']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($encuesta['nombre']); ?></td>
                                        <td>
                                            <i class="fas fa-flag me-1"></i>
                                            <?php echo htmlspecialchars($encuesta['pais']); ?>
                                        </td>
                                        <td>
                                            <span class="badge badge-purple">
                                                <?php echo htmlspecialchars($encuesta['genero']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars($encuesta['frecuencia']); ?></td>
                                        <td>
                                            <?php if (!empty($encuesta['plataformas'])): ?>
                                                <?php foreach ($encuesta['plataformas'] as $plataforma): ?>
                                                    <small class="badge bg-light text-dark me-1">
                                                        <?php echo htmlspecialchars($plataforma); ?>
                                                    </small>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <small class="text-muted">Ninguna</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <form method="POST" style="display: inline;"
                                                onsubmit="return confirmarEliminacion(<?php echo $encuesta['id']; ?>, '<?php echo htmlspecialchars($encuesta['nombre']); ?>')">
                                                <input type="hidden" name="action" value="eliminar">
                                                <input type="hidden" name="id" value="<?php echo $encuesta['id']; ?>">
                                                <button type="submit" class="btn btn-danger-custom btn-sm">
                                                    <i class="fas fa-trash me-1"></i>
                                                    Eliminar
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pestaña de Estadísticas -->
        <div class="tab-pane fade" id="estadisticas" role="tabpanel">
            <div class="row">
                <!-- Género más popular -->
                <div class="col-lg-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom">
                            <i class="fas fa-trophy me-2"></i>
                            Género Más Popular
                        </div>
                        <div class="card-body text-center p-5">
                            <?php $generoData = $resumen['genero_popular']; ?>
                            <h3 class="title-gradient">👑 <?php echo implode(', ', $generoData['generos']); ?></h3>
                            <p class="fs-5">
                                <span class="badge badge-purple"><?php echo $generoData['cantidad']; ?>
                                    voto<?php echo $generoData['cantidad'] > 1 ? 's' : ''; ?></span>
                            </p>
                            <?php if (!empty($generoData['todos_conteos'])): ?>
                                <hr>
                                <h6>Distribución completa:</h6>
                                <?php foreach ($generoData['todos_conteos'] as $genero => $cantidad): ?>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span><?php echo htmlspecialchars($genero); ?></span>
                                        <span><strong><?php echo $cantidad; ?></strong></span>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Plataforma más usada -->
                <div class="col-lg-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom">
                            <i class="fas fa-play-circle me-2"></i>
                            Plataforma Más Utilizada
                        </div>
                        <div class="card-body text-center p-5">
                            <?php $plataformaData = $resumen['plataforma_popular']; ?>
                            <?php if (!empty($plataformaData['plataformas'])): ?>
                                <h3 class="title-gradient">👑 <?php echo implode(', ', $plataformaData['plataformas']); ?></h3>
                                <p class="fs-5">
                                    <span class="badge badge-purple"><?php echo $plataformaData['cantidad']; ?>
                                        selección<?php echo $plataformaData['cantidad'] > 1 ? 'es' : ''; ?></span>
                                </p>
                                <?php if (!empty($plataformaData['todos_conteos'])): ?>
                                    <hr>
                                    <h6>Distribución completa:</h6>
                                    <?php foreach ($plataformaData['todos_conteos'] as $plataforma => $cantidad): ?>
                                        <div class="d-flex justify-content-between mb-1">
                                            <span><?php echo htmlspecialchars($plataforma); ?></span>
                                            <span><strong><?php echo $cantidad; ?></strong></span>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            <?php else: ?>
                                <p class="text-muted">No hay plataformas seleccionadas</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- País con más participantes -->
                <div class="col-lg-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom">
                            <i class="fas fa-flag me-2"></i>
                            País con Más Participantes
                        </div>
                        <div class="card-body text-center p-5">
                            <?php $paisData = $resumen['pais_popular']; ?>
                            <h3 class="title-gradient">👑 <?php echo implode(', ', $paisData['paises']); ?></h3>
                            <p class="fs-5">
                                <span class="badge badge-purple"><?php echo $paisData['cantidad']; ?>
                                    participante<?php echo $paisData['cantidad'] > 1 ? 's' : ''; ?></span>
                            </p>
                            <?php if (!empty($paisData['ranking']) && count($paisData['ranking']) > 1): ?>
                                <hr>
                                <h6>Ranking de países:</h6>
                                <?php foreach (array_slice($paisData['ranking'], 0, 5, true) as $pais => $cantidad): ?>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span><?php echo htmlspecialchars($pais); ?></span>
                                        <span><strong><?php echo $cantidad; ?></strong></span>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Distribución de frecuencias -->
                <div class="col-lg-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom">
                            <i class="fas fa-calendar-alt me-2"></i>
                            Frecuencia de Consumo
                        </div>
                        <div class="card-body p-5">
                            <?php $frecuenciaData = $resumen['distribuciones_frecuencia']; ?>
                            <?php if (!empty($frecuenciaData['distribuciones'])): ?>
                                <?php foreach ($frecuenciaData['distribuciones'] as $dist): ?>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span><?php echo htmlspecialchars($dist['frecuencia']); ?></span>
                                        <div class="text-end">
                                            <span class="badge badge-purple"><?php echo $dist['cantidad']; ?></span>
                                            <small class="text-muted d-block"><?php echo $dist['porcentaje']; ?>%</small>
                                        </div>
                                    </div>
                                    <div class="progress mb-3" style="height: 8px;">
                                        <div class="progress-bar"
                                            style="width: <?php echo $dist['porcentaje']; ?>%; background: var(--purple-gradient);">
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-muted">No hay datos de frecuencia</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pestaña de Análisis -->
        <div class="tab-pane fade" id="analisis" role="tabpanel">
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom">
                            <i class="fas fa-link me-2"></i>
                            Relación Género - Plataforma
                        </div>
                        <div class="card-body p-5">
                            <?php if (!empty($analisisRelaciones['genero_plataforma'])): ?>
                                <p class="text-muted mb-3">Combinaciones más frecuentes:</p>
                                <?php foreach (array_slice($analisisRelaciones['genero_plataforma'], 0, 10, true) as $combinacion => $cantidad): ?>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-sm"><?php echo htmlspecialchars($combinacion); ?></span>
                                        <span class="badge badge-purple"><?php echo $cantidad; ?></span>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-muted">No hay datos suficientes para este análisis</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom">
                            <i class="fas fa-globe me-2"></i>
                            Relación País - Género
                        </div>
                        <div class="card-body p-5">
                            <?php if (!empty($analisisRelaciones['pais_genero'])): ?>
                                <p class="text-muted mb-3">Preferencias por país:</p>
                                <?php foreach (array_slice($analisisRelaciones['pais_genero'], 0, 10, true) as $combinacion => $cantidad): ?>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-sm"><?php echo htmlspecialchars($combinacion); ?></span>
                                        <span class="badge badge-purple"><?php echo $cantidad; ?></span>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-muted">No hay datos suficientes para este análisis</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Promedio de películas por semana -->
                <div class="col-lg-12 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom">
                            <i class="fas fa-film me-2"></i>
                            Promedio de Películas Vistas por Semana
                        </div>
                        <div class="card-body p-5">
                            <?php if ($promedioSemanal['total_participantes'] > 0): ?>
                                <div class="row">
                                    <div class="col-md-4 text-center">
                                        <div class="mb-4">
                                            <h2 class="title-gradient mb-2">
                                                📽️ <?php echo $promedioSemanal['promedio']; ?>
                                            </h2>
                                            <p class="fs-5 mb-1">
                                                <span class="badge badge-purple">películas/semana</span>
                                            </p>
                                            <small class="text-muted">
                                                <?php echo $promedioSemanal['interpretacion']; ?>
                                            </small>
                                        </div>

                                        <div class="card bg-light border-0 p-3 mb-3">
                                            <h6 class="mb-2"><i class="fas fa-calculator me-1"></i>Cálculos</h6>
                                            <div class="text-start">
                                                <small class="d-block mb-1">
                                                    <strong>Total estimado:</strong><br>
                                                    <?php echo $promedioSemanal['total_peliculas_semana']; ?> películas/semana
                                                </small>
                                                <small class="d-block mb-1">
                                                    <strong>Participantes:</strong><br>
                                                    <?php echo $promedioSemanal['total_participantes']; ?> personas
                                                </small>
                                                <small class="d-block">
                                                    <strong>Promedio:</strong><br>
                                                    <?php echo $promedioSemanal['total_peliculas_semana']; ?> ÷
                                                    <?php echo $promedioSemanal['total_participantes']; ?> =
                                                    <?php echo $promedioSemanal['promedio']; ?>
                                                </small>
                                            </div>
                                        </div>

                                        <?php if (!empty($promedioSemanal['frecuencia_mas_comun'])): ?>
                                            <div class="alert alert-info py-2">
                                                <small>
                                                    <i class="fas fa-crown me-1"></i>
                                                    <strong>Más común:</strong>
                                                    <?php echo $promedioSemanal['frecuencia_mas_comun']; ?>
                                                </small>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-8">
                                        <h6 class="mb-3">Distribución por frecuencia de consumo:</h6>
                                        <?php foreach ($promedioSemanal['distribuciones'] as $frecuencia => $data): ?>
                                            <div class="mb-3">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <span class="text-sm">
                                                        <?php echo htmlspecialchars($frecuencia); ?>
                                                        <?php if ($frecuencia === $promedioSemanal['frecuencia_mas_comun']): ?>
                                                            👑
                                                        <?php endif; ?>
                                                    </span>
                                                    <div class="text-end">
                                                        <span class="badge badge-purple me-2">
                                                            <?php echo $data['cantidad']; ?> personas
                                                        </span>
                                                        <small class="text-muted">
                                                            (~<?php echo $data['peliculas_semana']; ?> pel/sem)
                                                        </small>
                                                    </div>
                                                </div>
                                                <div class="progress mb-1" style="height: 6px;">
                                                    <div class="progress-bar"
                                                        style="width: <?php echo $data['porcentaje']; ?>%; background: var(--purple-gradient);">
                                                    </div>
                                                </div>
                                                <small class="text-muted"><?php echo $data['porcentaje']; ?>%</small>
                                            </div>
                                        <?php endforeach; ?>

                                        <div class="mt-4 pt-3 border-top">
                                            <h6 class="mb-2">
                                                <i class="fas fa-info-circle me-1"></i>
                                                Metodología de Cálculo
                                            </h6>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <small class="text-muted">
                                                        <strong>Estimaciones por frecuencia:</strong><br>
                                                        • <strong>Diario:</strong> 7 películas/semana<br>
                                                        • <strong>Semanal:</strong> 1 película/semana<br>
                                                        • <strong>Mensual:</strong> 0.25 películas/semana<br>
                                                        • <strong>Rara vez:</strong> 0.1 películas/semana
                                                    </small>
                                                </div>
                                                <div class="col-md-6">
                                                    <small class="text-muted">
                                                        <strong>Cálculo del promedio:</strong><br>
                                                        Se suma el consumo estimado de todos los participantes y se divide entre
                                                        el total de participantes para obtener un promedio ponderado del consumo
                                                        cinematográfico semanal.
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="text-center">
                                    <i class="fas fa-film icon-purple mb-3" style="font-size: 3rem; opacity: 0.5;"></i>
                                    <p class="text-muted">No hay datos suficientes para calcular el promedio</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pestaña de Historial de Fallos -->
        <div class="tab-pane fade" id="fallos" role="tabpanel">
            <div class="row">
                <div class="col-12">
                    <div class="card-custom">
                        <div class="card-header-custom">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Historial de Intentos Fallidos
                            <span class="badge bg-danger ms-2"><?php echo $estadisticasFallos['total_fallidas']; ?></span>
                        </div>
                        <div class="card-body">
                            <?php if ($estadisticasFallos['total_fallidas'] > 0): ?>
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="alert alert-info">
                                            <h6><i class="fas fa-info-circle me-2"></i>Resumen de Fallos</h6>
                                            <ul class="mb-0">
                                                <li><strong>Total de intentos:</strong>
                                                    <?php echo $estadisticasFallos['total_intentos']; ?></li>
                                                <li><strong>Encuestas exitosas:</strong> <span
                                                        class="text-success"><?php echo $estadisticasFallos['total_exitosas']; ?></span>
                                                </li>
                                                <li><strong>Intentos fallidos:</strong> <span
                                                        class="text-danger"><?php echo $estadisticasFallos['total_fallidas']; ?></span>
                                                </li>
                                                <li><strong>Tasa de éxito:</strong> <span
                                                        class="text-success"><?php echo $estadisticasFallos['tasa_exito']; ?>%</span>
                                                </li>
                                                <li><strong>Tasa de fallo:</strong> <span
                                                        class="text-danger"><?php echo $estadisticasFallos['tasa_fallo']; ?>%</span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="text-center">
                                            <h6>Distribución de Resultados</h6>
                                            <div class="d-flex justify-content-center align-items-center"
                                                style="height: 120px;">
                                                <canvas id="graficoFallos" style="max-height: 120px;"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <?php if (!empty($estadisticasFallos['historial_fallos'])): ?>
                                    <h6><i class="fas fa-history me-2"></i>Historial Detallado</h6>
                                    <div class="table-responsive">
                                        <table class="table table-custom table-sm">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Fecha y Hora</th>
                                                    <th>Motivo del Fallo</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $historialOrdenado = array_reverse($estadisticasFallos['historial_fallos']);
                                                foreach ($historialOrdenado as $index => $fallo):
                                                    ?>
                                                    <tr>
                                                        <td><span class="badge bg-danger"><?php echo $index + 1; ?></span></td>
                                                        <td>
                                                            <small>
                                                                <i class="fas fa-calendar me-1"></i>
                                                                <?php echo date('d/m/Y H:i:s', strtotime($fallo['timestamp'])); ?>
                                                            </small>
                                                        </td>
                                                        <td>
                                                            <span class="text-muted">
                                                                <?php echo htmlspecialchars($fallo['motivo']); ?>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="mt-3">
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="action" value="reiniciar_contador_fallos">
                                            <button type="submit" class="btn btn-warning btn-sm"
                                                onclick="return confirm('¿Estás seguro de que quieres reiniciar el contador de fallos?')">
                                                <i class="fas fa-redo me-1"></i>
                                                Reiniciar Contador
                                            </button>
                                        </form>
                                        <small class="text-muted ms-2">
                                            Esto eliminará todo el historial de fallos registrado
                                        </small>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                                    <h4 class="text-success mt-3">¡Perfecto!</h4>
                                    <p class="text-muted">No se han registrado intentos fallidos hasta el momento.</p>
                                    <p class="text-muted">Todas las encuestas se han procesado exitosamente.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php endif; ?>

<!-- Scripts para gráficos -->
<script>
    // Configuración de colores púrpura para los gráficos
    const purpleColors = [
        '#6f42c1', '#8e44ad', '#9b59b6', '#bb8fce', '#d2b4de',
        '#e8daef', '#5a359e', '#7d3c98', '#a569bd', '#c39bd3'
    ];

    // Configuración común para gráficos
    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    usePointStyle: true,
                    padding: 15,
                    boxWidth: 12
                }
            }
        },
        layout: {
            padding: {
                top: 10,
                bottom: 10
            }
        }
    };

    <?php if (!empty($encuestas)): ?>
        // Datos para los gráficos
        const datosGraficos = <?php echo json_encode($datosGraficos); ?>;

        // Función para agregar coronita al elemento más popular
        function agregarCoronita(labels, data) {
            const maxValue = Math.max(...data);
            const maxIndex = data.indexOf(maxValue);
            if (maxIndex !== -1) {
                labels[maxIndex] = '👑 ' + labels[maxIndex];
            }
            return labels;
        }

        // Función para crear gráficos después de que el DOM esté listo
        document.addEventListener('DOMContentLoaded', function () {
            // Gráfico de géneros
            if (datosGraficos.generos.labels.length > 0) {
                const labelsGeneros = [...datosGraficos.generos.labels];
                const dataGeneros = [...datosGraficos.generos.data];

                const ctxGeneros = document.getElementById('graficoGeneros').getContext('2d');
                new Chart(ctxGeneros, {
                    type: 'doughnut',
                    data: {
                        labels: agregarCoronita(labelsGeneros, dataGeneros),
                        datasets: [{
                            data: dataGeneros,
                            backgroundColor: purpleColors.slice(0, datosGraficos.generos.labels.length),
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: commonOptions
                });
            }

            // Gráfico de plataformas
            if (datosGraficos.plataformas.labels.length > 0) {
                const labelsPlataformas = [...datosGraficos.plataformas.labels];
                const dataPlataformas = [...datosGraficos.plataformas.data];

                const ctxPlataformas = document.getElementById('graficoPlataformas').getContext('2d');
                new Chart(ctxPlataformas, {
                    type: 'bar',
                    data: {
                        labels: agregarCoronita(labelsPlataformas, dataPlataformas),
                        datasets: [{
                            label: 'Selecciones',
                            data: dataPlataformas,
                            backgroundColor: purpleColors[0],
                            borderColor: purpleColors[1],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        ...commonOptions,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            },
                            x: {
                                ticks: {
                                    maxRotation: 45,
                                    minRotation: 0
                                }
                            }
                        }
                    }
                });
            }

            // Gráfico de países
            if (datosGraficos.paises.labels.length > 0) {
                const labelsPaises = [...datosGraficos.paises.labels];
                const dataPaises = [...datosGraficos.paises.data];

                const ctxPaises = document.getElementById('graficoPaises').getContext('2d');
                new Chart(ctxPaises, {
                    type: 'pie',
                    data: {
                        labels: agregarCoronita(labelsPaises, dataPaises),
                        datasets: [{
                            data: dataPaises,
                            backgroundColor: purpleColors.slice(0, datosGraficos.paises.labels.length),
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: commonOptions
                });
            }

            // Gráfico de frecuencias
            if (datosGraficos.frecuencias.labels.length > 0) {
                const labelsFrecuencias = [...datosGraficos.frecuencias.labels];
                const dataFrecuencias = [...datosGraficos.frecuencias.data];

                const ctxFrecuencias = document.getElementById('graficoFrecuencias').getContext('2d');
                new Chart(ctxFrecuencias, {
                    type: 'polarArea',
                    data: {
                        labels: agregarCoronita(labelsFrecuencias, dataFrecuencias),
                        datasets: [{
                            data: dataFrecuencias,
                            backgroundColor: purpleColors.slice(0, datosGraficos.frecuencias.labels.length),
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: commonOptions
                });
            }

            // Gráfico de fallos (siempre disponible)
            const ctxFallos = document.getElementById('graficoFallos');
            if (ctxFallos) {
                const estadisticasFallos = <?php echo json_encode($estadisticasFallos); ?>;

                new Chart(ctxFallos, {
                    type: 'doughnut',
                    data: {
                        labels: ['Exitosas', 'Fallidas'],
                        datasets: [{
                            data: [estadisticasFallos.total_exitosas, estadisticasFallos.total_fallidas],
                            backgroundColor: ['#28a745', '#dc3545'],
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    padding: 10,
                                    boxWidth: 10
                                }
                            }
                        }
                    }
                });
            }
        });
    <?php endif; ?>

    // Función para mostrar/ocultar estadísticas de fallos
    function toggleEstadisticasFallos() {
        const estadisticasFallos = document.getElementById('estadisticasFallos');
        const boton = document.getElementById('toggleEstadisticasFallos');
        const totalIntentos = <?php echo $estadisticasFallos['total_intentos']; ?>;

        if (estadisticasFallos.classList.contains('d-none')) {
            // Mostrar con animación
            estadisticasFallos.classList.remove('d-none');
            estadisticasFallos.style.opacity = '0';
            estadisticasFallos.style.transform = 'translateY(-20px)';

            // Animación de entrada
            setTimeout(() => {
                estadisticasFallos.style.transition = 'all 0.3s ease-in-out';
                estadisticasFallos.style.opacity = '1';
                estadisticasFallos.style.transform = 'translateY(0)';
            }, 10);

            // Cambiar texto y icono del botón
            boton.innerHTML = '<i class="fas fa-chart-line me-2"></i>Ocultar Estadísticas de Intentos<span class="badge bg-light text-dark ms-2">' + totalIntentos + '</span><i class="fas fa-chevron-up ms-2"></i>';
            boton.classList.remove('btn-outline-purple');
            boton.classList.add('btn-purple');
        } else {
            // Ocultar con animación
            estadisticasFallos.style.transition = 'all 0.3s ease-in-out';
            estadisticasFallos.style.opacity = '0';
            estadisticasFallos.style.transform = 'translateY(-20px)';

            setTimeout(() => {
                estadisticasFallos.classList.add('d-none');
                estadisticasFallos.style.opacity = '';
                estadisticasFallos.style.transform = '';
                estadisticasFallos.style.transition = '';
            }, 300);

            // Cambiar texto y icono del botón
            boton.innerHTML = '<i class="fas fa-chart-line me-2"></i>Ver Estadísticas de Intentos<span class="badge bg-secondary ms-2">' + totalIntentos + '</span><i class="fas fa-chevron-down ms-2"></i>';
            boton.classList.remove('btn-purple');
            boton.classList.add('btn-outline-purple');
        }
    }
</script>

<?php include 'views/footer.php'; ?>