<?php
// Incluir configuración y clases necesarias
require_once 'config.php';
require_once 'clases/EncuestaManager.php';

// Configurar título de la página
$pageTitle = 'Buscar Encuestas - Sistema de Encuestas Cinematográficas';

// Crear instancia del manager
$manager = new EncuestaManager();

// Variables para la búsqueda
$criterio = '';
$resultados = [];
$busquedaRealizada = false;
$mensaje = '';

// Procesar búsqueda
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'buscar' && isset($_POST['criterio'])) {
            $criterio = trim($_POST['criterio']);
            $busquedaRealizada = true;

            if (!empty($criterio)) {
                $resultados = $manager->buscarEncuestas($criterio);

                if (empty($resultados)) {
                    $mensaje = "No se encontraron encuestas que coincidan con: \"$criterio\"";
                } else {
                    $mensaje = "Se encontraron " . count($resultados) . " encuesta(s) para: \"$criterio\"";
                }
            } else {
                $resultados = $manager->obtenerTodas();
                $mensaje = "Mostrando todas las encuestas (" . count($resultados) . " total)";
            }
        } elseif ($_POST['action'] === 'eliminar' && isset($_POST['id'])) {
            $resultado = $manager->eliminarEncuesta($_POST['id']);
            $mensaje = $resultado['message'];
            $tipoMensaje = $resultado['success'] ? 'success' : 'danger';

            // Rehacer la búsqueda si había una activa
            if (isset($_POST['criterio_anterior'])) {
                $criterio = trim($_POST['criterio_anterior']);
                $busquedaRealizada = true;
                if (!empty($criterio)) {
                    $resultados = $manager->buscarEncuestas($criterio);
                } else {
                    $resultados = $manager->obtenerTodas();
                }
            }
        }
    }
}

// Obtener total de encuestas para mostrar
$totalEncuestas = $manager->contarEncuestas();

// Incluir header
include 'views/header.php';
?>

<!-- Título de la página -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h2 class="title-gradient mb-0">
                    <i class="fas fa-search me-2"></i>
                    Buscar Encuestas
                </h2>
                <p class="text-muted mt-2">Busque encuestas por ID de participante o nombre</p>
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

<!-- Formulario de búsqueda -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card-custom">
            <div class="card-header-custom">
                <i class="fas fa-filter me-2"></i>
                Filtros de Búsqueda
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <input type="hidden" name="action" value="buscar">
                    <div class="row align-items-end">
                        <div class="col-md-8 mb-3">
                            <label for="criterio" class="form-label fw-bold">
                                <i class="fas fa-search me-1"></i>
                                Criterio de Búsqueda
                            </label>
                            <input type="text" class="form-control form-control-custom" id="criterio" name="criterio"
                                value="<?php echo htmlspecialchars($criterio); ?>"
                                placeholder="Ingrese ID (ej: 123) o nombre (ej: Juan)">
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Búsqueda por ID exacto o coincidencia parcial en el nombre
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-purple">
                                    <i class="fas fa-search me-1"></i>
                                    Buscar
                                </button>
                                <button type="button" class="btn btn-purple-outline" onclick="limpiarBusqueda()">
                                    <i class="fas fa-broom me-1"></i>
                                    Limpiar
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Información general -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="stat-card">
            <div class="stat-number"><?php echo $totalEncuestas; ?></div>
            <div class="stat-label">
                <i class="fas fa-database me-1"></i>
                Total de Encuestas
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="stat-card">
            <div class="stat-number"><?php echo $busquedaRealizada ? count($resultados) : '-'; ?></div>
            <div class="stat-label">
                <i class="fas fa-filter me-1"></i>
                Resultados Filtrados
            </div>
        </div>
    </div>
</div>

<!-- Mostrar mensajes -->
<?php if (isset($tipoMensaje)): ?>
    <div class="alert alert-custom-<?php echo $tipoMensaje; ?>">
        <i class="fas fa-<?php echo $tipoMensaje === 'success' ? 'check-circle' : 'exclamation-triangle'; ?> me-2"></i>
        <?php echo htmlspecialchars($mensaje); ?>
    </div>
<?php elseif ($busquedaRealizada && !empty($mensaje)): ?>
    <div class="alert alert-custom-info">
        <i class="fas fa-info-circle me-2"></i>
        <?php echo htmlspecialchars($mensaje); ?>
    </div>
<?php endif; ?>

<!-- Resultados -->
<?php if ($totalEncuestas === 0): ?>
    <!-- Sin encuestas -->
    <div class="row">
        <div class="col-12">
            <div class="card-custom text-center">
                <div class="card-body p-5">
                    <i class="fas fa-search icon-purple mb-3" style="font-size: 4rem;"></i>
                    <h3 class="title-gradient mb-3">No hay encuestas registradas</h3>
                    <p class="lead mb-4">
                        No se han registrado encuestas aún. Crea tu primera encuesta para poder buscar.
                    </p>
                    <a href="formulario.php" class="btn btn-purple btn-lg">
                        <i class="fas fa-plus-circle me-2"></i>
                        Crear Primera Encuesta
                    </a>
                </div>
            </div>
        </div>
    </div>

<?php elseif (!$busquedaRealizada): ?>
    <!-- Estado inicial -->
    <div class="row">
        <div class="col-12">
            <div class="card-custom text-center">
                <div class="card-body p-5">
                    <i class="fas fa-search icon-purple mb-3" style="font-size: 3rem;"></i>
                    <h4 class="title-gradient mb-3">Busque encuestas</h4>
                    <p class="lead mb-4">
                        Ingrese un criterio de búsqueda en el campo superior para filtrar las encuestas.
                    </p>
                    <div class="row text-start">
                        <div class="col-md-6">
                            <h6><i class="fas fa-hashtag me-2"></i>Búsqueda por ID:</h6>
                            <p class="text-muted">Ingrese el número exacto del ID del participante.</p>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-user me-2"></i>Búsqueda por Nombre:</h6>
                            <p class="text-muted">Ingrese parte del nombre del participante.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php elseif (empty($resultados)): ?>
    <!-- Sin resultados -->
    <div class="row">
        <div class="col-12">
            <div class="card-custom text-center">
                <div class="card-body p-5">
                    <i class="fas fa-search-minus icon-purple mb-3" style="font-size: 3rem;"></i>
                    <h4 class="title-gradient mb-3">Sin resultados</h4>
                    <p class="lead mb-4">
                        No se encontraron encuestas que coincidan con
                        "<strong><?php echo htmlspecialchars($criterio); ?></strong>".
                    </p>
                    <div class="mb-4">
                        <h6>Sugerencias:</h6>
                        <ul class="list-unstyled text-muted">
                            <li><i class="fas fa-check me-2"></i>Verifique que el ID sea correcto</li>
                            <li><i class="fas fa-check me-2"></i>Intente con parte del nombre</li>
                            <li><i class="fas fa-check me-2"></i>Verifique las mayúsculas y minúsculas</li>
                        </ul>
                    </div>
                    <button onclick="limpiarBusqueda()" class="btn btn-purple">
                        <i class="fas fa-broom me-2"></i>
                        Nueva Búsqueda
                    </button>
                </div>
            </div>
        </div>
    </div>

<?php else: ?>
    <!-- Mostrar resultados -->
    <div class="row">
        <div class="col-12">
            <div class="card-custom">
                <div class="card-header-custom">
                    <i class="fas fa-list me-2"></i>
                    Resultados de la Búsqueda
                    <span class="badge badge-purple ms-2"><?php echo count($resultados); ?>
                        resultado<?php echo count($resultados) != 1 ? 's' : ''; ?></span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-custom mb-0">
                            <thead>
                                <tr>
                                    <th width="80">ID</th>
                                    <th>Nombre</th>
                                    <th>País</th>
                                    <th>Género Favorito</th>
                                    <th>Frecuencia</th>
                                    <th>Plataformas</th>
                                    <th width="120">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($resultados as $encuesta): ?>
                                    <tr>
                                        <td>
                                            <strong class="badge bg-light text-dark">
                                                <?php echo htmlspecialchars($encuesta['id']); ?>
                                            </strong>
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($encuesta['nombre']); ?></strong>
                                        </td>
                                        <td>
                                            <i class="fas fa-flag me-1 text-muted"></i>
                                            <?php echo htmlspecialchars($encuesta['pais']); ?>
                                        </td>
                                        <td>
                                            <span class="badge badge-purple">
                                                <?php echo htmlspecialchars($encuesta['genero']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <small><?php echo htmlspecialchars($encuesta['frecuencia']); ?></small>
                                        </td>
                                        <td>
                                            <?php if (!empty($encuesta['plataformas'])): ?>
                                                <div class="d-flex flex-wrap gap-1">
                                                    <?php foreach (array_slice($encuesta['plataformas'], 0, 3) as $plataforma): ?>
                                                        <small class="badge bg-light text-dark">
                                                            <?php echo htmlspecialchars($plataforma); ?>
                                                        </small>
                                                    <?php endforeach; ?>
                                                    <?php if (count($encuesta['plataformas']) > 3): ?>
                                                        <small
                                                            class="text-muted">+<?php echo count($encuesta['plataformas']) - 3; ?></small>
                                                    <?php endif; ?>
                                                </div>
                                            <?php else: ?>
                                                <small class="text-muted">Ninguna</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <form method="POST" style="display: inline;"
                                                onsubmit="return confirmarEliminacion(<?php echo $encuesta['id']; ?>, '<?php echo htmlspecialchars($encuesta['nombre']); ?>')">
                                                <input type="hidden" name="action" value="eliminar">
                                                <input type="hidden" name="id" value="<?php echo $encuesta['id']; ?>">
                                                <input type="hidden" name="criterio_anterior"
                                                    value="<?php echo htmlspecialchars($criterio); ?>">
                                                <button type="submit" class="btn btn-danger-custom btn-sm">
                                                    <i class="fas fa-trash"></i>
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
    </div>

    <!-- Estadísticas de la búsqueda -->
    <?php if (count($resultados) > 1): ?>
        <div class="row mt-4">
            <div class="col-12">
                <div class="card-custom">
                    <div class="card-header-custom">
                        <i class="fas fa-chart-pie me-2"></i>
                        Resumen de Resultados
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php
                            // Análisis rápido de los resultados
                            $generos = array_count_values(array_column($resultados, 'genero'));
                            $paises = array_count_values(array_column($resultados, 'pais'));
                            $frecuencias = array_count_values(array_column($resultados, 'frecuencia'));

                            arsort($generos);
                            arsort($paises);
                            arsort($frecuencias);
                            ?>

                            <div class="col-md-4 mb-3">
                                <h6><i class="fas fa-theater-masks me-2"></i>Géneros más comunes:</h6>
                                <?php foreach (array_slice($generos, 0, 3, true) as $genero => $cantidad): ?>
                                    <div class="d-flex justify-content-between">
                                        <span><?php echo htmlspecialchars($genero); ?></span>
                                        <span class="badge badge-purple"><?php echo $cantidad; ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="col-md-4 mb-3">
                                <h6><i class="fas fa-flag me-2"></i>Países más frecuentes:</h6>
                                <?php foreach (array_slice($paises, 0, 3, true) as $pais => $cantidad): ?>
                                    <div class="d-flex justify-content-between">
                                        <span><?php echo htmlspecialchars($pais); ?></span>
                                        <span class="badge badge-purple"><?php echo $cantidad; ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="col-md-4 mb-3">
                                <h6><i class="fas fa-calendar-alt me-2"></i>Frecuencias comunes:</h6>
                                <?php foreach (array_slice($frecuencias, 0, 3, true) as $frecuencia => $cantidad): ?>
                                    <div class="d-flex justify-content-between">
                                        <span><?php echo htmlspecialchars($frecuencia); ?></span>
                                        <span class="badge badge-purple"><?php echo $cantidad; ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>

<script>
    function limpiarBusqueda() {
        document.getElementById('criterio').value = '';
        document.getElementById('criterio').focus();
    }

    // Auto-focus en el campo de búsqueda al cargar
    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('criterio').focus();
    });

    // Buscar al presionar Enter
    document.getElementById('criterio').addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            this.form.submit();
        }
    });
</script>

<?php include 'views/footer.php'; ?>