<?php

class Encuesta
{
    private $id;
    private $nombre;
    private $pais;
    private $genero;
    private $frecuencia;
    private $plataformas;

    // Lista de países válidos
    private static $paisesValidos = [
        'México',
        'España',
        'Argentina',
        'Colombia',
        'Chile',
        'Perú',
        'Venezuela',
        'Ecuador',
        'Uruguay',
        'Paraguay',
        'Bolivia',
        'Costa Rica',
        'Panamá',
        'Guatemala',
        'Honduras',
        'El Salvador',
        'Nicaragua',
        'República Dominicana',
        'Cuba',
        'Puerto Rico',
        'Estados Unidos',
        'Canadá',
        'Brasil'
    ];

    // Lista de géneros válidos
    private static $generosValidos = [
        'Acción',
        'Comedia',
        'Drama',
        'Terror',
        'Ciencia Ficción',
        'Romance',
        'Aventura',
        'Animación',
        'Documental',
        'Musical',
        'Suspenso',
        'Fantasía'
    ];

    // Lista de frecuencias válidas
    private static $frecuenciasValidas = [
        'Diario',
        'Semanal',
        'Mensual',
        'Rara vez'
    ];

    // Lista de plataformas válidas
    private static $plataformasValidas = [
        'Netflix',
        'Disney+',
        'HBO Max',
        'Amazon Prime Video',
        'Apple TV+',
        'Paramount+',
        'Hulu',
        'Star+',
        'Crunchyroll',
        'YouTube Premium',
        'Otros'
    ];

    public function __construct($id, $nombre, $pais, $genero, $frecuencia, $plataformas = [])
    {
        $this->setId($id);
        $this->setNombre($nombre);
        $this->setPais($pais);
        $this->setGenero($genero);
        $this->setFrecuencia($frecuencia);
        $this->setPlataformas($plataformas);
    }

    // Getters
    public function getId()
    {
        return $this->id;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function getPais()
    {
        return $this->pais;
    }

    public function getGenero()
    {
        return $this->genero;
    }

    public function getFrecuencia()
    {
        return $this->frecuencia;
    }

    public function getPlataformas()
    {
        return $this->plataformas;
    }

    // Setters con validación
    public function setId($id)
    {
        if (!is_numeric($id) || $id <= 0 || !is_int((int) $id)) {
            throw new InvalidArgumentException("El ID debe ser un número entero positivo");
        }
        $this->id = (int) $id;
    }

    public function setNombre($nombre)
    {
        if (empty(trim($nombre))) {
            throw new InvalidArgumentException("El nombre no puede estar vacío");
        }
        if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/", trim($nombre))) {
            throw new InvalidArgumentException("El nombre solo puede contener letras y espacios");
        }
        $this->nombre = trim($nombre);
    }

    public function setPais($pais)
    {
        if (!in_array($pais, self::$paisesValidos)) {
            throw new InvalidArgumentException("País no válido");
        }
        $this->pais = $pais;
    }

    public function setGenero($genero)
    {
        if (!in_array($genero, self::$generosValidos)) {
            throw new InvalidArgumentException("Género no válido");
        }
        $this->genero = $genero;
    }

    public function setFrecuencia($frecuencia)
    {
        if (!in_array($frecuencia, self::$frecuenciasValidas)) {
            throw new InvalidArgumentException("Frecuencia no válida");
        }
        $this->frecuencia = $frecuencia;
    }

    public function setPlataformas($plataformas)
    {
        if (!is_array($plataformas)) {
            $plataformas = [];
        }

        // Si no se seleccionó ninguna plataforma, asignar "Otros"
        if (empty($plataformas)) {
            $plataformas = ['Otros'];
        }

        foreach ($plataformas as $plataforma) {
            if (!in_array($plataforma, self::$plataformasValidas)) {
                throw new InvalidArgumentException("Plataforma no válida: " . $plataforma);
            }
        }
        $this->plataformas = $plataformas;
    }

    // Método para convertir el objeto a arreglo asociativo
    public function toArray()
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'pais' => $this->pais,
            'genero' => $this->genero,
            'frecuencia' => $this->frecuencia,
            'plataformas' => $this->plataformas
        ];
    }

    // Métodos estáticos para obtener las listas válidas
    public static function getPaisesValidos()
    {
        return self::$paisesValidos;
    }

    public static function getGenerosValidos()
    {
        return self::$generosValidos;
    }

    public static function getFrecuenciasValidas()
    {
        return self::$frecuenciasValidas;
    }

    public static function getPlataformasValidas()
    {
        return self::$plataformasValidas;
    }

    // Método para validar todos los campos antes de crear la encuesta
    public static function validarDatos($datos)
    {
        $errores = [];

        // Validar ID
        if (!isset($datos['id']) || empty($datos['id'])) {
            $errores[] = "El ID es obligatorio";
        } elseif (!is_numeric($datos['id']) || $datos['id'] <= 0) {
            $errores[] = "El ID debe ser un número entero positivo";
        }

        // Validar nombre
        if (!isset($datos['nombre']) || empty(trim($datos['nombre']))) {
            $errores[] = "El nombre es obligatorio";
        } elseif (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/", trim($datos['nombre']))) {
            $errores[] = "El nombre solo puede contener letras y espacios";
        }

        // Validar país
        if (!isset($datos['pais']) || !in_array($datos['pais'], self::$paisesValidos)) {
            $errores[] = "Debe seleccionar un país válido";
        }

        // Validar género
        if (!isset($datos['genero']) || !in_array($datos['genero'], self::$generosValidos)) {
            $errores[] = "Debe seleccionar un género válido";
        }

        // Validar frecuencia
        if (!isset($datos['frecuencia']) || !in_array($datos['frecuencia'], self::$frecuenciasValidas)) {
            $errores[] = "Debe seleccionar una frecuencia válida";
        }

        // Validar plataformas (opcional)
        if (isset($datos['plataformas']) && is_array($datos['plataformas'])) {
            foreach ($datos['plataformas'] as $plataforma) {
                if (!in_array($plataforma, self::$plataformasValidas)) {
                    $errores[] = "Plataforma no válida: " . $plataforma;
                    break;
                }
            }
        }

        return $errores;
    }
}
