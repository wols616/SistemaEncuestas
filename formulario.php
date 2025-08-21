<?php
// Incluir configuración y clases necesarias
require_once 'config.php';
require_once 'clases/EncuestaManager.php';
require_once 'clases/Encuesta.php';

// Configurar título de la página
$pageTitle = 'Nueva Encuesta - Sistema de Encuestas Cinematográficas';

// Variables para el formulario
$errores = [];
$mensaje = '';
$datos = [];
$mostrarExito = false;

// Crear instancia del manager
$manager = new EncuestaManager();

// Procesar formulario si se envió
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = $_POST;
    
    try {
        // Validar los datos
        $errores = Encuesta::validarDatos($datos);
        
        if (empty($errores)) {
            // Crear nueva encuesta
            $encuesta = new Encuesta(
                $datos['id'],
                $datos['nombre'],
                $datos['pais'],
                $datos['genero'],
                $datos['frecuencia'],
                isset($datos['plataformas']) ? $datos['plataformas'] : []
            );
            
            // Intentar agregar la encuesta
            $resultado = $manager->agregarEncuesta($encuesta);
            
            if ($resultado['success']) {
                $mensaje = $resultado['message'];
                $mostrarExito = true;
                $datos = []; // Limpiar formulario
            } else {
                $errores[] = $resultado['message'];
                // Nota: el contador de fallos ya se incrementa en agregarEncuesta()
            }
        } else {
            // Registrar intento fallido por errores de validación
            $manager->registrarIntenteFallido('Errores de validación en formulario');
        }
    } catch (Exception $e) {
        // Registrar intento fallido por excepción
        $manager->registrarIntenteFallido('Excepción: ' . $e->getMessage());
        $errores[] = $e->getMessage();
    }
}

// Obtener listas válidas para los select
$paisesValidos = Encuesta::getPaisesValidos();
$generosValidos = Encuesta::getGenerosValidos();
$frecuenciasValidas = Encuesta::getFrecuenciasValidas();
$plataformasValidas = Encuesta::getPlataformasValidas();

// Obtener el siguiente ID disponible si no hay datos previos
$siguienteId = !isset($datos['id']) ? $manager->obtenerSiguienteId() : $datos['id'];

// Incluir header
include 'views/header.php';
?>

<!-- Título de la página -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex align-items-center">
            <h2 class="title-gradient mb-0">
                <i class="fas fa-plus-circle me-2"></i>
                Nueva Encuesta Cinematográfica
            </h2>
            <div class="ms-auto">
                <a href="index.php" class="btn btn-purple-outline">
                    <i class="fas fa-arrow-left me-1"></i>
                    Volver al Inicio
                </a>
            </div>
        </div>
        <p class="text-muted mt-2">Complete todos los campos obligatorios para registrar una nueva encuesta</p>
    </div>
</div>

<!-- Mostrar mensajes -->
<?php if ($mostrarExito): ?>
    <div class="alert alert-custom-success">
        <i class="fas fa-check-circle me-2"></i>
        <?php echo htmlspecialchars($mensaje); ?>
    </div>
<?php endif; ?>

<?php if (!empty($errores)): ?>
    <div class="alert alert-custom-danger">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Se encontraron los siguientes errores:</strong>
        <ul class="mb-0 mt-2">
            <?php foreach ($errores as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<!-- Formulario -->
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card-custom">
            <div class="card-header-custom">
                <i class="fas fa-form me-2"></i>
                Datos de la Encuesta
            </div>
            <div class="card-body p-4">
                <form method="POST" action="" id="formularioEncuesta">
                    
                    <!-- ID y Nombre -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="id" class="form-label fw-bold">
                                <i class="fas fa-hashtag me-1"></i>
                                ID de Participante *
                            </label>
                            <div class="input-group">
                                <input type="number" 
                                       class="form-control form-control-custom" 
                                       id="id" 
                                       name="id" 
                                       value="<?php echo htmlspecialchars($siguienteId ?? ''); ?>"
                                       required
                                       min="1"
                                       placeholder="Ej: <?php echo $siguienteId; ?>">
                                <button type="button" 
                                        class="btn btn-outline-secondary" 
                                        id="btnSiguienteId"
                                        title="Generar siguiente ID">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </div>
                            <div class="form-text">
                                Número único e irrepetible. 
                                <span class="text-success">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Sugerido: <?php echo $siguienteId; ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="nombre" class="form-label fw-bold">
                                <i class="fas fa-user me-1"></i>
                                Nombre Completo *
                            </label>
                            <input type="text" 
                                   class="form-control form-control-custom" 
                                   id="nombre" 
                                   name="nombre" 
                                   value="<?php echo htmlspecialchars($datos['nombre'] ?? ''); ?>"
                                   required
                                   placeholder="Ej: Juan Pérez">
                            <div class="form-text">Solo letras y espacios permitidos</div>
                        </div>
                    </div>
                    
                    <!-- País y Género -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="pais" class="form-label fw-bold">
                                <i class="fas fa-flag me-1"></i>
                                País de Origen *
                            </label>
                            <select class="form-select form-select-custom" id="pais" name="pais" required>
                                <option value="">Seleccione un país</option>
                                <?php foreach ($paisesValidos as $pais): ?>
                                    <option value="<?php echo htmlspecialchars($pais); ?>" 
                                            <?php echo (isset($datos['pais']) && $datos['pais'] === $pais) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($pais); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="genero" class="form-label fw-bold">
                                <i class="fas fa-theater-masks me-1"></i>
                                Género Cinematográfico Favorito *
                            </label>
                            <select class="form-select form-select-custom" id="genero" name="genero" required>
                                <option value="">Seleccione un género</option>
                                <?php foreach ($generosValidos as $genero): ?>
                                    <option value="<?php echo htmlspecialchars($genero); ?>" 
                                            <?php echo (isset($datos['genero']) && $datos['genero'] === $genero) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($genero); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Frecuencia -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <label for="frecuencia" class="form-label fw-bold">
                                <i class="fas fa-calendar-alt me-1"></i>
                                Frecuencia de Consumo de Películas *
                            </label>
                            <select class="form-select form-select-custom" id="frecuencia" name="frecuencia" required>
                                <option value="">Seleccione la frecuencia</option>
                                <?php foreach ($frecuenciasValidas as $frecuencia): ?>
                                    <option value="<?php echo htmlspecialchars($frecuencia); ?>" 
                                            <?php echo (isset($datos['frecuencia']) && $datos['frecuencia'] === $frecuencia) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($frecuencia); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">¿Con qué frecuencia ve películas?</div>
                        </div>
                    </div>
                    
                    <!-- Plataformas -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <label class="form-label fw-bold">
                                <i class="fas fa-tv me-1"></i>
                                Plataformas de Streaming Utilizadas
                            </label>
                            <div class="form-text mb-3">
                                Seleccione todas las plataformas que utiliza. 
                                <span class="text-info">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Si no utiliza ninguna de las listadas o no marca ninguna, se registrará automáticamente como "Otros"
                                </span>
                            </div>
                            
                            <div class="row">
                                <?php 
                                $plataformasSeleccionadas = isset($datos['plataformas']) ? $datos['plataformas'] : [];
                                foreach ($plataformasValidas as $index => $plataforma): 
                                ?>
                                    <div class="col-md-6 col-lg-4 mb-3">
                                        <div class="form-check form-check-custom">
                                            <input class="form-check-input form-check-input-custom" 
                                                   type="checkbox" 
                                                   id="plataforma_<?php echo $index; ?>" 
                                                   name="plataformas[]" 
                                                   value="<?php echo htmlspecialchars($plataforma); ?>"
                                                   <?php echo in_array($plataforma, $plataformasSeleccionadas) ? 'checked' : ''; ?>>
                                            <label class="form-check-label form-check-label-custom" 
                                                   for="plataforma_<?php echo $index; ?>">
                                                <?php echo htmlspecialchars($plataforma); ?>
                                            </label>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Botones -->
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Los campos marcados con * son obligatorios
                                </small>
                                <div>
                                    <button type="reset" class="btn btn-purple-outline me-2">
                                        <i class="fas fa-undo me-1"></i>
                                        Limpiar
                                    </button>
                                    <button type="submit" class="btn btn-purple">
                                        <i class="fas fa-save me-1"></i>
                                        Guardar Encuesta
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Información adicional -->
<div class="row mt-5">
    <div class="col-12">
        <div class="card-custom">
            <div class="card-header-custom">
                <i class="fas fa-info-circle me-2"></i>
                Información Importante
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-shield-alt icon-purple me-3 mt-1"></i>
                            <div>
                                <h6 class="mb-1">Validación de Datos</h6>
                                <small class="text-muted">Todos los datos son validados antes del almacenamiento</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-ban icon-purple me-3 mt-1"></i>
                            <div>
                                <h6 class="mb-1">ID Único</h6>
                                <small class="text-muted">No se pueden registrar dos encuestas con el mismo ID</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-database icon-purple me-3 mt-1"></i>
                            <div>
                                <h6 class="mb-1">Almacenamiento</h6>
                                <small class="text-muted">Los datos se guardan en la sesión actual del navegador</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Validación en tiempo real
document.addEventListener('DOMContentLoaded', function() {
    const idInput = document.getElementById('id');
    const nombreInput = document.getElementById('nombre');
    const btnSiguienteId = document.getElementById('btnSiguienteId');
    
    // Botón para generar siguiente ID
    btnSiguienteId.addEventListener('click', function() {
        // Hacer una petición AJAX para obtener el siguiente ID
        fetch('obtener_siguiente_id.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    idInput.value = data.siguiente_id;
                    idInput.classList.remove('is-invalid');
                    idInput.classList.add('is-valid');
                    
                    // Mostrar notificación
                    showNotification('ID actualizado: ' + data.siguiente_id, 'success');
                } else {
                    showNotification('Error al obtener siguiente ID', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error de conexión', 'error');
            });
    });
    
    // Validar ID
    idInput.addEventListener('input', function() {
        const valor = this.value;
        if (valor && (!Number.isInteger(Number(valor)) || Number(valor) <= 0)) {
            this.classList.add('is-invalid');
        } else {
            this.classList.remove('is-invalid');
            if (valor) this.classList.add('is-valid');
        }
    });
    
    // Validar nombre
    nombreInput.addEventListener('input', function() {
        const patron = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/;
        if (this.value && !patron.test(this.value)) {
            this.classList.add('is-invalid');
        } else {
            this.classList.remove('is-invalid');
            if (this.value) this.classList.add('is-valid');
        }
    });
    
    // Confirmar antes de limpiar
    document.querySelector('button[type="reset"]').addEventListener('click', function(e) {
        if (!confirm('¿Está seguro de que desea limpiar todos los campos?')) {
            e.preventDefault();
        }
    });
});

// Función para mostrar notificaciones
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    // Auto-eliminar después de 3 segundos
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 3000);
}
</script>

<?php include 'views/footer.php'; ?>
