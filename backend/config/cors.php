<?php

/**
 * Configuración de CORS (Cross-Origin Resource Sharing)
 *
 * Sistema de Biblioteca Digital de Videos Educativos
 * Unidad Educativa San Francisco Xavier
 *
 * @author Roger Omar Luna Yujra
 * @version 1.0
 */

declare(strict_types=1);

namespace Config;

/**
 * Clase CORSConfig
 *
 * Gestiona la configuración de CORS para permitir peticiones desde el frontend
 * Implementa políticas de seguridad para acceso desde diferentes orígenes
 */
class CORSConfig
{
    /**
     * Orígenes permitidos para hacer peticiones a la API
     * En producción, solo permitir dominios específicos
     *
     * @var array<string>
     */
    private static function getAllowedOrigins(): array
    {
        $origins = $_ENV['CORS_ALLOWED_ORIGINS'] ?? 'http://localhost:5173,http://localhost:3000';
        return explode(',', $origins);
    }

    /**
     * Métodos HTTP permitidos
     *
     * @var array<string>
     */
    private const ALLOWED_METHODS = [
        'GET',
        'POST',
        'PUT',
        'DELETE',
        'OPTIONS',
        'PATCH',
    ];

    /**
     * Headers permitidos
     *
     * @var array<string>
     */
    private const ALLOWED_HEADERS = [
        'Content-Type',
        'Authorization',
        'X-Requested-With',
        'Accept',
        'Origin',
        'Access-Control-Request-Method',
        'Access-Control-Request-Headers',
    ];

    /**
     * Headers expuestos al cliente
     *
     * @var array<string>
     */
    private const EXPOSED_HEADERS = [
        'Content-Length',
        'Content-Type',
        'Authorization',
    ];

    /**
     * Tiempo de caché para las preflight requests (en segundos)
     * Por defecto: 24 horas (86400 segundos)
     *
     * @var int
     */
    private const MAX_AGE = 86400;

    /**
     * Permitir credenciales (cookies, headers de autorización)
     *
     * @var bool
     */
    private const ALLOW_CREDENTIALS = true;

    /**
     * Aplica los headers CORS a la respuesta
     *
     * @return void
     */
    public static function apply(): void
    {
        // Obtener el origen de la petición
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';

        // Verificar si el origen está permitido
        if (self::isOriginAllowed($origin)) {
            header('Access-Control-Allow-Origin: ' . $origin);
        } else {
            // En desarrollo, permitir cualquier origen
            if (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'development') {
                header('Access-Control-Allow-Origin: *');
            }
        }

        // Aplicar otros headers CORS
        header('Access-Control-Allow-Methods: ' . implode(', ', self::ALLOWED_METHODS));
        header('Access-Control-Allow-Headers: ' . implode(', ', self::ALLOWED_HEADERS));
        header('Access-Control-Expose-Headers: ' . implode(', ', self::EXPOSED_HEADERS));
        header('Access-Control-Max-Age: ' . self::MAX_AGE);

        if (self::ALLOW_CREDENTIALS) {
            header('Access-Control-Allow-Credentials: true');
        }

        // Si es una petición OPTIONS (preflight), terminar aquí
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(204);
            exit;
        }
    }

    /**
     * Verifica si un origen está permitido
     *
     * @param string $origin Origen a verificar
     * @return bool True si el origen está permitido
     */
    private static function isOriginAllowed(string $origin): bool
    {
        if (empty($origin)) {
            return false;
        }

        $allowedOrigins = self::getAllowedOrigins();

        // Verificar si el origen está en la lista de permitidos
        return in_array($origin, $allowedOrigins, true);
    }

    /**
     * Obtiene la configuración completa de CORS
     *
     * @return array<string, mixed> Configuración de CORS
     */
    public static function getConfig(): array
    {
        return [
            'allowed_origins' => self::getAllowedOrigins(),
            'allowed_methods' => self::ALLOWED_METHODS,
            'allowed_headers' => self::ALLOWED_HEADERS,
            'exposed_headers' => self::EXPOSED_HEADERS,
            'max_age' => self::MAX_AGE,
            'allow_credentials' => self::ALLOW_CREDENTIALS,
        ];
    }

    /**
     * Establece headers de seguridad adicionales
     *
     * @return void
     */
    public static function setSecurityHeaders(): void
    {
        // Prevenir ataques XSS
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: DENY');
        header('X-XSS-Protection: 1; mode=block');

        // Política de referencia
        header('Referrer-Policy: strict-origin-when-cross-origin');

        // Content Security Policy (CSP)
        // En producción, ajustar según las necesidades específicas
        if (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'production') {
            header("Content-Security-Policy: default-src 'self'");
        }

        // Prevenir que el navegador almacene datos sensibles en caché
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
    }

    /**
     * Establece headers para permitir streaming de video
     *
     * @return void
     */
    public static function setStreamingHeaders(): void
    {
        header('Accept-Ranges: bytes');
        header('Content-Type: video/mp4');
    }

    /**
     * Aplica headers completos (CORS + Seguridad)
     *
     * @return void
     */
    public static function applyAll(): void
    {
        self::apply();
        self::setSecurityHeaders();
    }
}
