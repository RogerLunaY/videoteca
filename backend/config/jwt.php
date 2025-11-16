<?php declare(strict_types=1);

/**
 * Configuración de JWT (JSON Web Tokens)
 *
 * Sistema de Biblioteca Digital de Videos Educativos
 * Unidad Educativa San Francisco Xavier
 *
 * @author Roger Omar Luna Yujra
 * @version 1.0
 */

namespace Config;

/**
 * Clase JWTConfig
 *
 * Gestiona la configuración de JSON Web Tokens para autenticación
 * Proporciona constantes y configuraciones para la generación y validación de tokens
 */
class JWTConfig
{
    /**
     * Clave secreta para firmar los tokens JWT
     * IMPORTANTE: En producción, debe ser una clave compleja y única almacenada en .env
     *
     * @var string
     */
    public static function getSecretKey(): string
    {
        return $_ENV['JWT_SECRET'] ?? 'videoteca_sfxavier_secret_key_2024_change_in_production';
    }

    /**
     * Algoritmo de cifrado para JWT
     * Algoritmos soportados: HS256, HS384, HS512, RS256, RS384, RS512
     *
     * @var string
     */
    public const ALGORITHM = 'HS256';

    /**
     * Tiempo de expiración del access token en segundos
     * Por defecto: 1 hora (3600 segundos)
     *
     * @var int
     */
    public static function getAccessTokenExpiration(): int
    {
        return (int)($_ENV['JWT_ACCESS_EXPIRATION'] ?? 3600);
    }

    /**
     * Tiempo de expiración del refresh token en segundos
     * Por defecto: 7 días (604800 segundos)
     *
     * @var int
     */
    public static function getRefreshTokenExpiration(): int
    {
        return (int)($_ENV['JWT_REFRESH_EXPIRATION'] ?? 604800);
    }

    /**
     * Emisor del token (issuer)
     * Identifica quién generó el token
     *
     * @var string
     */
    public static function getIssuer(): string
    {
        return $_ENV['JWT_ISSUER'] ?? 'videoteca.sfxavier.edu.bo';
    }

    /**
     * Audiencia del token (audience)
     * Identifica para quién es el token
     *
     * @var string
     */
    public static function getAudience(): string
    {
        return $_ENV['JWT_AUDIENCE'] ?? 'videoteca.sfxavier.edu.bo';
    }

    /**
     * Tiempo de tolerancia para la validación de tokens (en segundos)
     * Permite cierta flexibilidad en caso de desincronización de relojes
     *
     * @var int
     */
    public const LEEWAY = 60;

    /**
     * Nombre del header donde se envía el token
     *
     * @var string
     */
    public const HEADER_NAME = 'Authorization';

    /**
     * Prefijo del token en el header
     * Formato esperado: "Bearer {token}"
     *
     * @var string
     */
    public const TOKEN_PREFIX = 'Bearer';

    /**
     * Claims personalizados que se incluyen en el token
     *
     * @var array<string>
     */
    public const CUSTOM_CLAIMS = [
        'user_id',
        'rol',
        'nombre',
        'apellido',
        'email',
        'grado_id',
        'materia_id',
    ];

    /**
     * Obtiene la configuración completa de JWT
     *
     * @return array<string, mixed> Configuración de JWT
     */
    public static function getConfig(): array
    {
        return [
            'secret_key' => self::getSecretKey(),
            'algorithm' => self::ALGORITHM,
            'access_expiration' => self::getAccessTokenExpiration(),
            'refresh_expiration' => self::getRefreshTokenExpiration(),
            'issuer' => self::getIssuer(),
            'audience' => self::getAudience(),
            'leeway' => self::LEEWAY,
            'header_name' => self::HEADER_NAME,
            'token_prefix' => self::TOKEN_PREFIX,
            'custom_claims' => self::CUSTOM_CLAIMS,
        ];
    }

    /**
     * Valida que la configuración de JWT sea correcta
     *
     * @return bool True si la configuración es válida
     * @throws \Exception Si la configuración no es válida
     */
    public static function validateConfig(): bool
    {
        $secretKey = self::getSecretKey();

        // Verificar que la clave secreta tenga al menos 32 caracteres
        if (strlen($secretKey) < 32) {
            throw new \Exception(
                'La clave secreta JWT debe tener al menos 32 caracteres. Configure JWT_SECRET en .env'
            );
        }

        // Advertir si se está usando la clave por defecto en producción
        if (
            isset($_ENV['APP_ENV']) &&
            $_ENV['APP_ENV'] === 'production' &&
            strpos($secretKey, 'change_in_production') !== false
        ) {
            error_log('[WARNING] Se está usando la clave JWT por defecto en producción. Cambie JWT_SECRET en .env');
        }

        return true;
    }

    /**
     * Obtiene el tiempo actual en timestamp
     *
     * @return int Timestamp actual
     */
    public static function getCurrentTimestamp(): int
    {
        return time();
    }

    /**
     * Calcula el timestamp de expiración del access token
     *
     * @return int Timestamp de expiración
     */
    public static function getAccessTokenExpirationTimestamp(): int
    {
        return self::getCurrentTimestamp() + self::getAccessTokenExpiration();
    }

    /**
     * Calcula el timestamp de expiración del refresh token
     *
     * @return int Timestamp de expiración
     */
    public static function getRefreshTokenExpirationTimestamp(): int
    {
        return self::getCurrentTimestamp() + self::getRefreshTokenExpiration();
    }
}
