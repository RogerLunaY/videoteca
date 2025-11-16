<?php

/**
 * Entry Point - API REST
 *
 * Sistema de Biblioteca Digital de Videos Educativos
 * Unidad Educativa San Francisco Xavier
 *
 * @author Roger Omar Luna Yujra
 * @version 1.0
 */

declare(strict_types=1);

// Autoloader de clases
spl_autoload_register(function ($class) {
    $baseDir = __DIR__ . '/';

    // Mapeo específico para clases con nombres de archivo personalizados
    $classMap = [
        'Config\\CORSConfig' => 'config/cors.php',
        'Config\\JWTConfig' => 'config/jwt.php',
        'Config\\Database' => 'config/database.php',
    ];

    // Si la clase está en el mapa, cargarla directamente
    if (isset($classMap[$class])) {
        $file = $baseDir . $classMap[$class];
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }

    // Mapeo de namespaces a directorios (para el resto de las clases)
    $namespaceMap = [
        'Config\\' => 'config/',
        'Controllers\\' => 'controllers/',
        'Models\\' => 'models/',
        'Middleware\\' => 'middleware/',
        'Utils\\' => 'utils/',
    ];

    // Buscar el namespace en el mapa
    foreach ($namespaceMap as $namespace => $directory) {
        if (strpos($class, $namespace) === 0) {
            $className = substr($class, strlen($namespace));
            $file = $baseDir . $directory . $className . '.php';

            if (file_exists($file)) {
                require_once $file;
                return;
            }
        }
    }

    // Fallback al método original
    $file = $baseDir . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Cargar variables de entorno
if (file_exists(__DIR__ . '/.env')) {
    $lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        if (strpos($line, '=') !== false) {
            [$key, $value] = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
}

// Configurar zona horaria
date_default_timezone_set($_ENV['TIMEZONE'] ?? 'America/La_Paz');

// Configurar reporte de errores
if (isset($_ENV['DEBUG']) && $_ENV['DEBUG'] === 'true') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// Aplicar headers CORS y seguridad
Config\CORSConfig::applyAll();

// Obtener método HTTP y URI
$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Cargar rutas
$routes = require __DIR__ . '/routes/api.php';

// Encontrar ruta coincidente
$routeFound = false;
$params = [];

foreach ($routes as $route) {
    [$routeMethod, $routePath, $controller, $action] = $route;

    // Verificar método HTTP
    if ($method !== $routeMethod) {
        continue;
    }

    // Convertir ruta con parámetros a expresión regular
    $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([a-zA-Z0-9_-]+)', $routePath);
    $pattern = '#^' . $pattern . '$#';

    // Verificar coincidencia
    if (preg_match($pattern, $uri, $matches)) {
        $routeFound = true;

        // Extraer parámetros
        preg_match_all('/\{([a-zA-Z0-9_]+)\}/', $routePath, $paramNames);
        foreach ($paramNames[1] as $index => $paramName) {
            $params[$paramName] = $matches[$index + 1];
        }

        // Ejecutar controlador
        try {
            $controllerClass = "Controllers\\{$controller}";

            if (!class_exists($controllerClass)) {
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => 'Controlador no encontrado',
                ]);
                exit;
            }

            $controllerInstance = new $controllerClass();

            if (!method_exists($controllerInstance, $action)) {
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => 'Acción no encontrada en el controlador',
                ]);
                exit;
            }

            // Llamar al método del controlador con los parámetros
            if (!empty($params)) {
                call_user_func_array([$controllerInstance, $action], array_values($params));
            } else {
                $controllerInstance->{$action}();
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => $_ENV['DEBUG'] === 'true' ? $e->getMessage() : 'Error interno del servidor',
                'error' => $_ENV['DEBUG'] === 'true' ? [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                ] : null,
            ]);
        }

        break;
    }
}

// Si no se encontró la ruta
if (!$routeFound) {
    http_response_code(404);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Ruta no encontrada',
        'path' => $uri,
    ]);
}
