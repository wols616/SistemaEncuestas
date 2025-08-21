<?php

require_once 'Encuesta.php';

class EncuestaManager
{

    public function __construct()
    {
        // Inicializar la sesión si no está iniciada
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Inicializar el arreglo de encuestas si no existe
        if (!isset($_SESSION['encuestas'])) {
            $_SESSION['encuestas'] = [];
        }

        // Inicializar contador de encuestas fallidas
        if (!isset($_SESSION['encuestas_fallidas'])) {
            $_SESSION['encuestas_fallidas'] = 0;
        }
    }

    /**
     * Agrega una nueva encuesta a la sesión
     * @param Encuesta $encuesta
     * @return array con 'success' y 'message'
     */
    public function agregarEncuesta(Encuesta $encuesta)
    {
        try {
            // Verificar que el ID no exista
            if ($this->existeId($encuesta->getId())) {
                $this->incrementarEncuestasFallidas();
                return [
                    'success' => false,
                    'message' => 'Ya existe una encuesta con el ID: ' . $encuesta->getId()
                ];
            }

            // Agregar la encuesta al arreglo de sesión
            $_SESSION['encuestas'][] = $encuesta->toArray();

            return [
                'success' => true,
                'message' => 'Encuesta agregada exitosamente'
            ];

        } catch (Exception $e) {
            $this->incrementarEncuestasFallidas();
            return [
                'success' => false,
                'message' => 'Error al agregar encuesta: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Elimina una encuesta por ID
     * @param int $id
     * @return array con 'success' y 'message'
     */
    public function eliminarEncuesta($id)
    {
        if (!isset($_SESSION['encuestas']) || empty($_SESSION['encuestas'])) {
            return [
                'success' => false,
                'message' => 'No hay encuestas para eliminar'
            ];
        }

        $encontrada = false;
        foreach ($_SESSION['encuestas'] as $index => $encuesta) {
            if ($encuesta['id'] == $id) {
                unset($_SESSION['encuestas'][$index]);
                // Reindexar el arreglo
                $_SESSION['encuestas'] = array_values($_SESSION['encuestas']);
                $encontrada = true;
                break;
            }
        }

        if ($encontrada) {
            return [
                'success' => true,
                'message' => 'Encuesta eliminada exitosamente'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'No se encontró una encuesta con el ID: ' . $id
            ];
        }
    }

    /**
     * Busca encuestas por criterio (ID o nombre)
     * @param string $criterio
     * @return array de encuestas que coinciden
     */
    public function buscarEncuestas($criterio)
    {
        if (!isset($_SESSION['encuestas']) || empty($_SESSION['encuestas'])) {
            return [];
        }

        $criterio = trim($criterio);
        if (empty($criterio)) {
            return $_SESSION['encuestas'];
        }

        return array_filter($_SESSION['encuestas'], function ($encuesta) use ($criterio) {
            // Buscar por ID exacto
            if (is_numeric($criterio) && $encuesta['id'] == $criterio) {
                return true;
            }

            // Buscar por nombre (coincidencia parcial, insensible a mayúsculas)
            if (stripos($encuesta['nombre'], $criterio) !== false) {
                return true;
            }

            return false;
        });
    }

    /**
     * Obtiene todas las encuestas almacenadas
     * @return array
     */
    public function obtenerTodas()
    {
        return isset($_SESSION['encuestas']) ? $_SESSION['encuestas'] : [];
    }

    /**
     * Verifica si existe una encuesta con el ID especificado
     * @param int $id
     * @return bool
     */
    private function existeId($id)
    {
        if (!isset($_SESSION['encuestas']) || empty($_SESSION['encuestas'])) {
            return false;
        }

        foreach ($_SESSION['encuestas'] as $encuesta) {
            if ($encuesta['id'] == $id) {
                return true;
            }
        }

        return false;
    }

    /**
     * Obtiene una encuesta específica por ID
     * @param int $id
     * @return array|null
     */
    public function obtenerPorId($id)
    {
        if (!isset($_SESSION['encuestas']) || empty($_SESSION['encuestas'])) {
            return null;
        }

        foreach ($_SESSION['encuestas'] as $encuesta) {
            if ($encuesta['id'] == $id) {
                return $encuesta;
            }
        }

        return null;
    }

    /**
     * Elimina múltiples encuestas por sus IDs
     * @param array $ids
     * @return array con 'success', 'message' y contadores
     */
    public function eliminarMultiples($ids)
    {
        if (!is_array($ids) || empty($ids)) {
            return [
                'success' => false,
                'message' => 'No se proporcionaron IDs válidos'
            ];
        }

        $eliminadas = 0;
        $noEncontradas = 0;

        foreach ($ids as $id) {
            $resultado = $this->eliminarEncuesta($id);
            if ($resultado['success']) {
                $eliminadas++;
            } else {
                $noEncontradas++;
            }
        }

        if ($eliminadas > 0) {
            $message = "Se eliminaron {$eliminadas} encuesta(s)";
            if ($noEncontradas > 0) {
                $message .= ". {$noEncontradas} no se encontraron";
            }
            return [
                'success' => true,
                'message' => $message,
                'eliminadas' => $eliminadas,
                'no_encontradas' => $noEncontradas
            ];
        } else {
            return [
                'success' => false,
                'message' => 'No se pudo eliminar ninguna encuesta',
                'eliminadas' => 0,
                'no_encontradas' => $noEncontradas
            ];
        }
    }

    /**
     * Cuenta el total de encuestas
     * @return int
     */
    public function contarEncuestas()
    {
        return count($this->obtenerTodas());
    }

    /**
     * Verifica si el arreglo de entrada es válido
     * @param array $datos
     * @return bool
     */
    public function validarParametros($datos)
    {
        return is_array($datos) && !empty($datos);
    }

    /**
     * Limpia todas las encuestas de la sesión
     * @return array con 'success' y 'message'
     */
    public function limpiarTodas()
    {
        $total = $this->contarEncuestas();
        $_SESSION['encuestas'] = [];

        // También limpiar estadísticas de fallos
        $this->reiniciarContadorFallidas();

        return [
            'success' => true,
            'message' => "Se eliminaron {$total} encuesta(s) y se reiniciaron las estadísticas de fallos"
        ];
    }

    /**
     * Incrementa el contador de encuestas fallidas
     * @return void
     */
    private function incrementarEncuestasFallidas()
    {
        if (!isset($_SESSION['encuestas_fallidas'])) {
            $_SESSION['encuestas_fallidas'] = 0;
        }
        $_SESSION['encuestas_fallidas']++;
    }

    /**
     * Obtiene el total de encuestas fallidas
     * @return int
     */
    public function obtenerEncuestasFallidas()
    {
        return isset($_SESSION['encuestas_fallidas']) ? $_SESSION['encuestas_fallidas'] : 0;
    }

    /**
     * Reinicia el contador de encuestas fallidas
     * @return void
     */
    public function reiniciarContadorFallidas()
    {
        $_SESSION['encuestas_fallidas'] = 0;
        $_SESSION['historial_fallos'] = [];
    }

    /**
     * Registra un intento fallido de encuesta con motivo específico
     * @param string $motivo Razón del fallo
     * @return void
     */
    public function registrarIntenteFallido($motivo = 'Error de validación')
    {
        $this->incrementarEncuestasFallidas();

        // Opcional: guardar historial de fallos con timestamp y motivo
        if (!isset($_SESSION['historial_fallos'])) {
            $_SESSION['historial_fallos'] = [];
        }

        $_SESSION['historial_fallos'][] = [
            'timestamp' => date('Y-m-d H:i:s'),
            'motivo' => $motivo
        ];

        // Mantener solo los últimos 50 registros para no sobrecargar la sesión
        if (count($_SESSION['historial_fallos']) > 50) {
            $_SESSION['historial_fallos'] = array_slice($_SESSION['historial_fallos'], -50);
        }
    }

    /**
     * Obtiene estadísticas de intentos fallidos
     * @return array
     */
    public function obtenerEstadisticasFallos()
    {
        $totalFallos = $this->obtenerEncuestasFallidas();
        $totalExitosas = $this->contarEncuestas();
        $totalIntentos = $totalFallos + $totalExitosas;

        $tasaExito = $totalIntentos > 0 ? round(($totalExitosas / $totalIntentos) * 100, 1) : 0;
        $tasaFallo = $totalIntentos > 0 ? round(($totalFallos / $totalIntentos) * 100, 1) : 0;

        return [
            'total_intentos' => $totalIntentos,
            'total_exitosas' => $totalExitosas,
            'total_fallidas' => $totalFallos,
            'tasa_exito' => $tasaExito,
            'tasa_fallo' => $tasaFallo,
            'historial_fallos' => isset($_SESSION['historial_fallos']) ? $_SESSION['historial_fallos'] : []
        ];
    }
}
