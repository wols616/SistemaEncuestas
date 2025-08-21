<?php

class EncuestaEstadisticas
{
    private $encuestas;

    public function __construct($encuestas = [])
    {
        $this->encuestas = $encuestas;
    }

    /**
     * Actualiza las encuestas para el cálculo
     * @param array $encuestas
     */
    public function setEncuestas($encuestas)
    {
        $this->encuestas = $encuestas;
    }

    /**
     * Retorna el total de encuestas
     * @return int
     */
    public function totalEncuestas()
    {
        return count($this->encuestas);
    }

    /**
     * Calcula el género más popular
     * @return array con información del género más popular
     */
    public function generoMasPopular()
    {
        if (empty($this->encuestas)) {
            return [
                'generos' => [],
                'cantidad' => 0,
                'mensaje' => 'No hay encuestas registradas'
            ];
        }

        // Extraer todos los géneros
        $generos = array_column($this->encuestas, 'genero');

        // Contar frecuencias
        $conteoGeneros = array_count_values($generos);

        // Ordenar de mayor a menor
        arsort($conteoGeneros);

        // Obtener la cantidad máxima
        $maxCantidad = max($conteoGeneros);

        // Encontrar todos los géneros con la cantidad máxima (en caso de empate)
        $generosMasPopulares = array_keys(array_filter($conteoGeneros, function ($cantidad) use ($maxCantidad) {
            return $cantidad === $maxCantidad;
        }));

        return [
            'generos' => $generosMasPopulares,
            'cantidad' => $maxCantidad,
            'todos_conteos' => $conteoGeneros,
            'mensaje' => count($generosMasPopulares) > 1 ? 'Empate entre géneros' : 'Género más popular'
        ];
    }

    /**
     * Calcula la plataforma más utilizada
     * @return array con información de la plataforma más utilizada
     */
    public function plataformaMasUsada()
    {
        if (empty($this->encuestas)) {
            return [
                'plataformas' => [],
                'cantidad' => 0,
                'mensaje' => 'No hay encuestas registradas'
            ];
        }

        $todasPlataformas = [];

        // Recopilar todas las plataformas seleccionadas
        foreach ($this->encuestas as $encuesta) {
            if (isset($encuesta['plataformas']) && is_array($encuesta['plataformas'])) {
                $todasPlataformas = array_merge($todasPlataformas, $encuesta['plataformas']);
            }
        }

        if (empty($todasPlataformas)) {
            return [
                'plataformas' => [],
                'cantidad' => 0,
                'mensaje' => 'No hay plataformas registradas'
            ];
        }

        // Contar frecuencias
        $conteoPlataformas = array_count_values($todasPlataformas);

        // Ordenar de mayor a menor
        arsort($conteoPlataformas);

        // Obtener la cantidad máxima
        $maxCantidad = max($conteoPlataformas);

        // Encontrar todas las plataformas con la cantidad máxima
        $plataformasMasUsadas = array_keys(array_filter($conteoPlataformas, function ($cantidad) use ($maxCantidad) {
            return $cantidad === $maxCantidad;
        }));

        return [
            'plataformas' => $plataformasMasUsadas,
            'cantidad' => $maxCantidad,
            'todos_conteos' => $conteoPlataformas,
            'mensaje' => count($plataformasMasUsadas) > 1 ? 'Empate entre plataformas' : 'Plataforma más utilizada'
        ];
    }

    /**
     * Calcula el país con mayor número de participantes
     * @return array con información del país con más participantes
     */
    public function paisConMasParticipantes()
    {
        if (empty($this->encuestas)) {
            return [
                'paises' => [],
                'cantidad' => 0,
                'mensaje' => 'No hay encuestas registradas'
            ];
        }

        // Extraer todos los países
        $paises = array_column($this->encuestas, 'pais');

        // Contar frecuencias
        $conteoPaises = array_count_values($paises);

        // Ordenar de mayor a menor
        arsort($conteoPaises);

        // Obtener la cantidad máxima
        $maxCantidad = max($conteoPaises);

        // Encontrar todos los países con la cantidad máxima
        $paisesMasParticipantes = array_keys(array_filter($conteoPaises, function ($cantidad) use ($maxCantidad) {
            return $cantidad === $maxCantidad;
        }));

        return [
            'paises' => $paisesMasParticipantes,
            'cantidad' => $maxCantidad,
            'todos_conteos' => $conteoPaises,
            'ranking' => $conteoPaises,
            'mensaje' => count($paisesMasParticipantes) > 1 ? 'Empate entre países' : 'País con más participantes'
        ];
    }

    /**
     * Calcula la distribución de frecuencias de consumo
     * @return array con porcentajes y conteos
     */
    public function distribucionFrecuencias()
    {
        if (empty($this->encuestas)) {
            return [
                'distribuciones' => [],
                'mensaje' => 'No hay encuestas registradas'
            ];
        }

        $frecuencias = array_column($this->encuestas, 'frecuencia');
        $conteoFrecuencias = array_count_values($frecuencias);
        $total = count($frecuencias);

        $distribuciones = [];
        foreach ($conteoFrecuencias as $frecuencia => $cantidad) {
            $porcentaje = round(($cantidad / $total) * 100, 2);
            $distribuciones[] = [
                'frecuencia' => $frecuencia,
                'cantidad' => $cantidad,
                'porcentaje' => $porcentaje
            ];
        }

        return [
            'distribuciones' => $distribuciones,
            'total' => $total,
            'todos_conteos' => $conteoFrecuencias
        ];
    }

    /**
     * Genera un resumen completo de estadísticas
     * @return array con todas las estadísticas
     */
    public function resumenCompleto()
    {
        return [
            'total_encuestas' => $this->totalEncuestas(),
            'genero_popular' => $this->generoMasPopular(),
            'plataforma_popular' => $this->plataformaMasUsada(),
            'pais_popular' => $this->paisConMasParticipantes(),
            'distribuciones_frecuencia' => $this->distribucionFrecuencias(),
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }

    /**
     * Obtiene estadísticas específicas para gráficos
     * @return array optimizado para Chart.js
     */
    public function datosParaGraficos()
    {
        $generos = $this->generoMasPopular();
        $plataformas = $this->plataformaMasUsada();
        $paises = $this->paisConMasParticipantes();
        $frecuencias = $this->distribucionFrecuencias();

        return [
            'generos' => [
                'labels' => array_keys($generos['todos_conteos'] ?? []),
                'data' => array_values($generos['todos_conteos'] ?? []),
                'titulo' => 'Distribución de Géneros Preferidos'
            ],
            'plataformas' => [
                'labels' => array_keys($plataformas['todos_conteos'] ?? []),
                'data' => array_values($plataformas['todos_conteos'] ?? []),
                'titulo' => 'Plataformas Más Utilizadas'
            ],
            'paises' => [
                'labels' => array_keys($paises['todos_conteos'] ?? []),
                'data' => array_values($paises['todos_conteos'] ?? []),
                'titulo' => 'Participación por País'
            ],
            'frecuencias' => [
                'labels' => array_column($frecuencias['distribuciones'] ?? [], 'frecuencia'),
                'data' => array_column($frecuencias['distribuciones'] ?? [], 'cantidad'),
                'porcentajes' => array_column($frecuencias['distribuciones'] ?? [], 'porcentaje'),
                'titulo' => 'Frecuencia de Consumo de Películas'
            ]
        ];
    }

    /**
     * Calcula relaciones entre datos (análisis avanzado)
     * @return array con análisis de relaciones
     */
    public function analisisRelaciones()
    {
        if (empty($this->encuestas)) {
            return ['mensaje' => 'No hay datos suficientes para análisis'];
        }

        $relacionGeneroPlataforma = [];
        $relacionPaisGenero = [];

        foreach ($this->encuestas as $encuesta) {
            $genero = $encuesta['genero'];
            $pais = $encuesta['pais'];

            // Relación género-plataforma
            if (isset($encuesta['plataformas']) && is_array($encuesta['plataformas'])) {
                foreach ($encuesta['plataformas'] as $plataforma) {
                    $clave = $genero . ' - ' . $plataforma;
                    $relacionGeneroPlataforma[$clave] = ($relacionGeneroPlataforma[$clave] ?? 0) + 1;
                }
            }

            // Relación país-género
            $clavePaisGenero = $pais . ' - ' . $genero;
            $relacionPaisGenero[$clavePaisGenero] = ($relacionPaisGenero[$clavePaisGenero] ?? 0) + 1;
        }

        arsort($relacionGeneroPlataforma);
        arsort($relacionPaisGenero);

        return [
            'genero_plataforma' => array_slice($relacionGeneroPlataforma, 0, 10, true),
            'pais_genero' => array_slice($relacionPaisGenero, 0, 10, true),
            'total_combinaciones_gp' => count($relacionGeneroPlataforma),
            'total_combinaciones_pg' => count($relacionPaisGenero)
        ];
    }

    /**
     * Calcula el promedio de películas vistas por semana
     * @return array con estadísticas del promedio semanal
     */
    public function promedioSemanal()
    {
        if (empty($this->encuestas)) {
            return [
                'promedio' => 0,
                'total_participantes' => 0,
                'mensaje' => 'No hay encuestas registradas'
            ];
        }

        // Mapeo de frecuencias a números de películas por semana (estimaciones)
        $frecuenciaANumero = [
            'Diario' => 7, // 1 película por día = 7 por semana
            'Semanal' => 1, // 1 película por semana
            'Mensual' => 0.25, // ~1 película al mes = 0.25 por semana
            'Rara vez' => 0.1 // Muy pocas películas
        ];

        $totalPeliculas = 0;
        $totalParticipantes = count($this->encuestas);
        $distribuciones = [];

        foreach ($this->encuestas as $encuesta) {
            $frecuencia = $encuesta['frecuencia'];

            if (isset($frecuenciaANumero[$frecuencia])) {
                $peliculasPorSemana = $frecuenciaANumero[$frecuencia];
                $totalPeliculas += $peliculasPorSemana;

                // Contar distribuciones
                if (!isset($distribuciones[$frecuencia])) {
                    $distribuciones[$frecuencia] = [
                        'cantidad' => 0,
                        'peliculas_semana' => $peliculasPorSemana
                    ];
                }
                $distribuciones[$frecuencia]['cantidad']++;
            }
        }

        $promedio = $totalParticipantes > 0 ? round($totalPeliculas / $totalParticipantes, 2) : 0;

        // Calcular porcentajes
        foreach ($distribuciones as $frecuencia => &$data) {
            $data['porcentaje'] = round(($data['cantidad'] / $totalParticipantes) * 100, 1);
        }

        // Encontrar la frecuencia más común
        $frecuenciaMasComun = '';
        $maxCantidad = 0;
        foreach ($distribuciones as $frecuencia => $data) {
            if ($data['cantidad'] > $maxCantidad) {
                $maxCantidad = $data['cantidad'];
                $frecuenciaMasComun = $frecuencia;
            }
        }

        return [
            'promedio' => $promedio,
            'total_participantes' => $totalParticipantes,
            'total_peliculas_semana' => round($totalPeliculas, 1),
            'distribuciones' => $distribuciones,
            'frecuencia_mas_comun' => $frecuenciaMasComun,
            'interpretacion' => $this->interpretarPromedio($promedio),
            'mensaje' => $totalParticipantes > 0 ? '' : 'No hay datos suficientes'
        ];
    }

    /**
     * Interpreta el promedio de películas por semana
     * @param float $promedio
     * @return string
     */
    private function interpretarPromedio($promedio)
    {
        if ($promedio >= 5) {
            return 'Consumidores muy activos';
        } elseif ($promedio >= 2) {
            return 'Consumidores regulares';
        } elseif ($promedio >= 1) {
            return 'Consumidores moderados';
        } elseif ($promedio >= 0.5) {
            return 'Consumidores ocasionales';
        } else {
            return 'Consumidores esporádicos';
        }
    }
}
