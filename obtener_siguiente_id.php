<?php
/**
 * API endpoint para obtener el siguiente ID disponible
 * Devuelve JSON con el siguiente ID para usar en el formulario
 */

header('Content-Type: application/json');

try {
    // Incluir configuración y clases necesarias
    require_once 'config.php';
    require_once 'clases/EncuestaManager.php';

    // Crear instancia del manager
    $manager = new EncuestaManager();

    // Obtener el siguiente ID
    $siguienteId = $manager->obtenerSiguienteId();

    // Respuesta exitosa
    echo json_encode([
        'success' => true,
        'siguiente_id' => $siguienteId,
        'mensaje' => 'Siguiente ID obtenido correctamente'
    ]);

} catch (Exception $e) {
    // Respuesta de error
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'mensaje' => 'Error al obtener el siguiente ID'
    ]);
}
?>