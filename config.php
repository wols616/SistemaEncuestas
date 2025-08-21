<?php
/**
 * Archivo de configuración principal del sistema
 * Sistema de Encuestas Cinematográficas
 */

// Configuración de errores (para desarrollo)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuración de zona horaria
date_default_timezone_set('America/Mexico_City');

// Configuración de sesión (solo si no hay sesión activa)
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_lifetime', 3600); // 1 hora
    ini_set('session.gc_maxlifetime', 3600);
}

// Autoloader simple para las clases
spl_autoload_register(function ($clase) {
    $archivo = __DIR__ . '/clases/' . $clase . '.php';
    if (file_exists($archivo)) {
        require_once $archivo;
    }
});

// Constantes del sistema
define('SISTEMA_NOMBRE', 'Sistema de Encuestas Cinematográficas');
define('SISTEMA_VERSION', '1.0.0');
define('SISTEMA_AUTOR', 'Desarrollado con PHP POO');

// Configuración de validación
define('ID_MIN', 1);
define('ID_MAX', 999999);
define('NOMBRE_MIN_LENGTH', 2);
define('NOMBRE_MAX_LENGTH', 100);

// Configuración de la aplicación
$config = [
    'app' => [
        'name' => SISTEMA_NOMBRE,
        'version' => SISTEMA_VERSION,
        'author' => SISTEMA_AUTOR,
        'timezone' => 'America/Mexico_City'
    ],
    'session' => [
        'name' => 'encuestas_cinematograficas',
        'lifetime' => 3600,
        'path' => '/',
        'domain' => '',
        'secure' => false,
        'httponly' => true
    ],
    'validation' => [
        'id_min' => ID_MIN,
        'id_max' => ID_MAX,
        'nombre_min_length' => NOMBRE_MIN_LENGTH,
        'nombre_max_length' => NOMBRE_MAX_LENGTH
    ]
];

// Función para obtener configuración
function getConfig($key = null)
{
    global $config;

    if ($key === null) {
        return $config;
    }

    $keys = explode('.', $key);
    $value = $config;

    foreach ($keys as $k) {
        if (isset($value[$k])) {
            $value = $value[$k];
        } else {
            return null;
        }
    }

    return $value;
}

// Función para inicializar sesión de manera segura
function inicializarSesion()
{
    if (session_status() === PHP_SESSION_NONE) {
        $sessionConfig = getConfig('session');

        session_name($sessionConfig['name']);
        session_set_cookie_params(
            $sessionConfig['lifetime'],
            $sessionConfig['path'],
            $sessionConfig['domain'],
            $sessionConfig['secure'],
            $sessionConfig['httponly']
        );

        session_start();

        // Regenerar ID de sesión ocasionalmente para seguridad
        if (
            !isset($_SESSION['last_regeneration']) ||
            time() - $_SESSION['last_regeneration'] > 300
        ) {
            session_regenerate_id(true);
            $_SESSION['last_regeneration'] = time();
        }
    }
}

// Función para logging simple
function logear($mensaje, $nivel = 'INFO')
{
    $timestamp = date('Y-m-d H:i:s');
    $log = "[{$timestamp}] [{$nivel}] {$mensaje}" . PHP_EOL;

    // En un entorno de producción, esto se guardaría en un archivo
    error_log($log);
}

// Función para sanitizar datos de entrada
function sanitizarEntrada($data)
{
    if (is_array($data)) {
        return array_map('sanitizarEntrada', $data);
    }

    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

// Función para generar CSRF token (básico)
function generarCSRFToken()
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Función para verificar CSRF token
function verificarCSRFToken($token)
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Inicializar sesión al cargar el archivo
inicializarSesion();
?>