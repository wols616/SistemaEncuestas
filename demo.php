<?php
// Incluir configuración y clases necesarias
require_once 'config.php';
require_once 'clases/EncuestaManager.php';
require_once 'clases/Encuesta.php';

// Configurar título de la página
$pageTitle = 'Datos de Demostración - Sistema de Encuestas Cinematográficas';

// Crear instancia del manager
$manager = new EncuestaManager();

// Datos de ejemplo para la demostración
$datosEjemplo = [
    [
        'id' => 1,
        'nombre' => 'María García',
        'pais' => 'México',
        'genero' => 'Drama',
        'frecuencia' => 'Semanal',
        'plataformas' => ['Netflix', 'Disney+']
    ],
    [
        'id' => 2,
        'nombre' => 'Carlos Rodríguez',
        'pais' => 'España',
        'genero' => 'Acción',
        'frecuencia' => 'Diario',
        'plataformas' => ['HBO Max', 'Amazon Prime Video']
    ],
    [
        'id' => 3,
        'nombre' => 'Ana Martínez',
        'pais' => 'Argentina',
        'genero' => 'Comedia',
        'frecuencia' => 'Mensual',
        'plataformas' => ['Netflix', 'Hulu', 'Star+']
    ],
    [
        'id' => 4,
        'nombre' => 'Luis González',
        'pais' => 'Colombia',
        'genero' => 'Ciencia Ficción',
        'frecuencia' => 'Semanal',
        'plataformas' => ['Disney+', 'Apple TV+']
    ],
    [
        'id' => 5,
        'nombre' => 'Carmen López',
        'pais' => 'Chile',
        'genero' => 'Romance',
        'frecuencia' => 'Rara vez',
        'plataformas' => ['Netflix']
    ],
    [
        'id' => 6,
        'nombre' => 'Pedro Sánchez',
        'pais' => 'Perú',
        'genero' => 'Terror',
        'frecuencia' => 'Mensual',
        'plataformas' => ['HBO Max', 'Paramount+']
    ],
    [
        'id' => 7,
        'nombre' => 'Isabel Fernández',
        'pais' => 'Venezuela',
        'genero' => 'Animación',
        'frecuencia' => 'Semanal',
        'plataformas' => ['Disney+', 'Netflix', 'Crunchyroll']
    ],
    [
        'id' => 8,
        'nombre' => 'Roberto Díaz',
        'pais' => 'Ecuador',
        'genero' => 'Aventura',
        'frecuencia' => 'Diario',
        'plataformas' => ['Amazon Prime Video', 'YouTube Premium']
    ],
    [
        'id' => 9,
        'nombre' => 'Sofía Ruiz',
        'pais' => 'Uruguay',
        'genero' => 'Documental',
        'frecuencia' => 'Mensual',
        'plataformas' => ['Netflix', 'HBO Max']
    ],
    [
        'id' => 10,
        'nombre' => 'Miguel Torres',
        'pais' => 'Paraguay',
        'genero' => 'Musical',
        'frecuencia' => 'Rara vez',
        'plataformas' => ['Disney+', 'Apple TV+']
    ],
    [
        'id' => 11,
        'nombre' => 'Elena Vargas',
        'pais' => 'Bolivia',
        'genero' => 'Suspenso',
        'frecuencia' => 'Semanal',
        'plataformas' => ['Netflix', 'HBO Max', 'Hulu']
    ],
    [
        'id' => 12,
        'nombre' => 'José Morales',
        'pais' => 'Costa Rica',
        'genero' => 'Fantasía',
        'frecuencia' => 'Diario',
        'plataformas' => ['Amazon Prime Video', 'Disney+']
    ],
    [
        'id' => 13,
        'nombre' => 'Laura Jiménez',
        'pais' => 'Panamá',
        'genero' => 'Drama',
        'frecuencia' => 'Mensual',
        'plataformas' => ['Netflix', 'Star+']
    ],
    [
        'id' => 14,
        'nombre' => 'Fernando Castro',
        'pais' => 'Guatemala',
        'genero' => 'Acción',
        'frecuencia' => 'Semanal',
        'plataformas' => ['HBO Max', 'Paramount+', 'YouTube Premium']
    ],
    [
        'id' => 15,
        'nombre' => 'Patricia Herrera',
        'pais' => 'Honduras',
        'genero' => 'Comedia',
        'frecuencia' => 'Rara vez',
        'plataformas' => ['Netflix', 'Disney+', 'Apple TV+']
    ]
];

$mensaje = '';
$errores = [];

// Procesar la acción solicitada
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'cargar_ejemplos':
                $exitos = 0;
                $fallos = 0;

                foreach ($datosEjemplo as $datos) {
                    try {
                        $encuesta = new Encuesta(
                            $datos['id'],
                            $datos['nombre'],
                            $datos['pais'],
                            $datos['genero'],
                            $datos['frecuencia'],
                            $datos['plataformas']
                        );

                        $resultado = $manager->agregarEncuesta($encuesta);

                        if ($resultado['success']) {
                            $exitos++;
                        } else {
                            $fallos++;
                        }
                    } catch (Exception $e) {
                        $fallos++;
                    }
                }

                $mensaje = "Se cargaron {$exitos} encuesta(s) de ejemplo exitosamente.";
                if ($fallos > 0) {
                    $mensaje .= " {$fallos} ya existían o tuvieron errores.";
                }
                break;

            case 'limpiar_datos':
                $resultado = $manager->limpiarTodas();
                $mensaje = $resultado['message'];
                break;
        }
    }
}

// Obtener estadísticas actuales
$totalActual = $manager->contarEncuestas();

// Incluir header
include 'views/header.php';
?>

<!-- Título de la página -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h2 class="title-gradient mb-0">
                    <i class="fas fa-database me-2"></i>
                    Datos de Demostración
                </h2>
                <p class="text-muted mt-2">Cargue datos de ejemplo para probar el sistema</p>
            </div>
            <div>
                <a href="index.php" class="btn btn-purple-outline">
                    <i class="fas fa-home me-1"></i>
                    Volver al Inicio
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Mostrar mensajes -->
<?php if (!empty($mensaje)): ?>
    <div class="alert alert-custom-success">
        <i class="fas fa-check-circle me-2"></i>
        <?php echo htmlspecialchars($mensaje); ?>
    </div>
<?php endif; ?>

<!-- Estado actual -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="stat-card">
            <div class="stat-number"><?php echo $totalActual; ?></div>
            <div class="stat-label">
                <i class="fas fa-poll me-1"></i>
                Encuestas Actuales
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="stat-card">
            <div class="stat-number"><?php echo count($datosEjemplo); ?></div>
            <div class="stat-label">
                <i class="fas fa-file-import me-1"></i>
                Datos de Ejemplo
            </div>
        </div>
    </div>
</div>

<!-- Acciones principales -->
<div class="row mb-5">
    <div class="col-md-6 mb-4">
        <div class="card-custom h-100">
            <div class="card-header-custom">
                <i class="fas fa-download me-2"></i>
                Cargar Datos de Ejemplo
            </div>
            <div class="card-body d-flex flex-column">
                <p class="flex-grow-1">
                    Cargue <?php echo count($datosEjemplo); ?> encuestas de demostración con datos variados para probar
                    todas las funcionalidades del sistema.
                </p>
                <div class="mb-3">
                    <h6>Los datos incluyen:</h6>
                    <ul class="small">
                        <li>Participantes de diferentes países latinoamericanos</li>
                        <li>Variedad de géneros cinematográficos</li>
                        <li>Diferentes frecuencias de consumo</li>
                        <li>Múltiples plataformas de streaming</li>
                    </ul>
                </div>
                <form method="POST" action="">
                    <input type="hidden" name="action" value="cargar_ejemplos">
                    <button type="submit" class="btn btn-purple w-100" <?php echo $totalActual >= count($datosEjemplo) ? 'disabled' : ''; ?>>
                        <i class="fas fa-download me-2"></i>
                        Cargar Datos de Ejemplo
                    </button>
                    <?php if ($totalActual >= count($datosEjemplo)): ?>
                        <small class="text-muted d-block mt-2">
                            <i class="fas fa-info-circle me-1"></i>
                            Ya se han cargado suficientes datos
                        </small>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="card-custom h-100">
            <div class="card-header-custom">
                <i class="fas fa-trash-alt me-2"></i>
                Limpiar Todos los Datos
            </div>
            <div class="card-body d-flex flex-column">
                <p class="flex-grow-1">
                    Elimine todas las encuestas almacenadas en la sesión actual para comenzar con datos limpios.
                </p>
                <div class="alert alert-custom-danger mb-3">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>¡Atención!</strong> Esta acción eliminará permanentemente todas las encuestas.
                </div>
                <form method="POST" action="" onsubmit="return confirmarLimpieza()">
                    <input type="hidden" name="action" value="limpiar_datos">
                    <button type="submit" class="btn btn-danger-custom w-100" <?php echo $totalActual === 0 ? 'disabled' : ''; ?>>
                        <i class="fas fa-trash-alt me-2"></i>
                        Limpiar Todos los Datos
                    </button>
                    <?php if ($totalActual === 0): ?>
                        <small class="text-muted d-block mt-2">
                            <i class="fas fa-info-circle me-1"></i>
                            No hay datos para limpiar
                        </small>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Vista previa de los datos de ejemplo -->
<div class="row">
    <div class="col-12">
        <div class="card-custom">
            <div class="card-header-custom">
                <i class="fas fa-eye me-2"></i>
                Vista Previa de Datos de Ejemplo
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-custom mb-0">
                        <thead>
                            <tr>
                                <th width="60">ID</th>
                                <th>Nombre</th>
                                <th>País</th>
                                <th>Género</th>
                                <th>Frecuencia</th>
                                <th>Plataformas</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($datosEjemplo, 0, 10) as $datos): ?>
                                <tr>
                                    <td><strong><?php echo $datos['id']; ?></strong></td>
                                    <td><?php echo htmlspecialchars($datos['nombre']); ?></td>
                                    <td>
                                        <i class="fas fa-flag me-1"></i>
                                        <?php echo htmlspecialchars($datos['pais']); ?>
                                    </td>
                                    <td>
                                        <span class="badge badge-purple">
                                            <?php echo htmlspecialchars($datos['genero']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($datos['frecuencia']); ?></td>
                                    <td>
                                        <?php foreach ($datos['plataformas'] as $plataforma): ?>
                                            <small class="badge bg-light text-dark me-1">
                                                <?php echo htmlspecialchars($plataforma); ?>
                                            </small>
                                        <?php endforeach; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (count($datosEjemplo) > 10): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted">
                                        <i class="fas fa-ellipsis-h me-1"></i>
                                        ... y <?php echo count($datosEjemplo) - 10; ?> encuesta(s) más
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Información adicional -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card-custom">
            <div class="card-header-custom">
                <i class="fas fa-lightbulb me-2"></i>
                Sugerencias de Uso
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-play icon-purple me-3 mt-1"></i>
                            <div>
                                <h6 class="mb-1">1. Cargar Datos</h6>
                                <small class="text-muted">Utilice el botón "Cargar Datos de Ejemplo" para llenar el
                                    sistema con encuestas de prueba.</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-chart-bar icon-purple me-3 mt-1"></i>
                            <div>
                                <h6 class="mb-1">2. Explorar Resultados</h6>
                                <small class="text-muted">Vaya a la sección "Resultados" para ver gráficos y
                                    estadísticas generadas.</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-search icon-purple me-3 mt-1"></i>
                            <div>
                                <h6 class="mb-1">3. Probar Búsquedas</h6>
                                <small class="text-muted">Use la función de búsqueda para filtrar encuestas por ID o
                                    nombre.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmarLimpieza() {
        return confirm('¿Está seguro de que desea eliminar TODAS las encuestas almacenadas?\n\nEsta acción no se puede deshacer.');
    }

    // Deshabilitar botón durante envío para evitar doble click
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function () {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn && !submitBtn.disabled) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Procesando...';
            }
        });
    });
</script>

<?php include 'views/footer.php'; ?>