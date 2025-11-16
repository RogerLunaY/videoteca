<?php declare(strict_types=1);

/**
 * Manejador de JSON Web Tokens (JWT)
 *
 * Sistema de Biblioteca Digital de Videos Educativos
 * Unidad Educativa San Francisco Xavier
 *
 * @author Roger Omar Luna Yujra
 * @version 1.0
 */

namespace Utils;

use Config\JWTConfig;
use Exception;

/**
 * Clase JWTHandler
 *
 * Maneja la generación, validación y decodificación de JSON Web Tokens
 * Implementa el algoritmo HS256 para firmar tokens
 */
class JWTHandler
{
    /**
     * Genera un token JWT con los datos proporcionados
     *
     * @param array<string, mixed> $payload Datos a incluir en el token
     * @param bool $isRefreshToken Si es un refresh token (tiempo de expiración mayor)
     * @return string Token JWT generado
     * @throws Exception Si hay un error al generar el token
     */
    public static function generateToken(array $payload, bool $isRefreshToken = false): string
    {
        try {
            $header = [
                'typ' => 'JWT',
                'alg' => JWTConfig::ALGORITHM,
            ];

            $currentTime = JWTConfig::getCurrentTimestamp();

            // Establecer claims estándar
            $claims = [
                'iss' => JWTConfig::getIssuer(),
                'aud' => JWTConfig::getAudience(),
                'iat' => $currentTime,
                'nbf' => $currentTime,
                'exp' => $isRefreshToken
                    ? JWTConfig::getRefreshTokenExpirationTimestamp()
                    : JWTConfig::getAccessTokenExpirationTimestamp(),
            ];

            // Agregar payload personalizado
            $claims = array_merge($claims, $payload);

            // Codificar header y payload
            $headerEncoded = self::base64UrlEncode(json_encode($header));
            $payloadEncoded = self::base64UrlEncode(json_encode($claims));

            // Crear firma
            $signature = self::createSignature($headerEncoded . '.' . $payloadEncoded);

            // Construir token
            return $headerEncoded . '.' . $payloadEncoded . '.' . $signature;
        } catch (Exception $e) {
            error_log(sprintf(
                '[%s] Error al generar token JWT: %s',
                date('Y-m-d H:i:s'),
                $e->getMessage()
            ));

            throw new Exception('Error al generar el token de autenticación', 500);
        }
    }

    /**
     * Valida y decodifica un token JWT
     *
     * @param string $token Token JWT a validar
     * @return array<string, mixed> Payload del token si es válido
     * @throws Exception Si el token no es válido
     */
    public static function validateToken(string $token): array
    {
        try {
            // Dividir el token en sus partes
            $parts = explode('.', $token);

            if (count($parts) !== 3) {
                throw new Exception('Formato de token inválido');
            }

            [$headerEncoded, $payloadEncoded, $signature] = $parts;

            // Verificar firma
            $expectedSignature = self::createSignature($headerEncoded . '.' . $payloadEncoded);

            if (!hash_equals($signature, $expectedSignature)) {
                throw new Exception('Firma de token inválida');
            }

            // Decodificar payload
            $payload = json_decode(self::base64UrlDecode($payloadEncoded), true);

            if (!is_array($payload)) {
                throw new Exception('Payload de token inválido');
            }

            // Validar claims estándar
            self::validateClaims($payload);

            return $payload;
        } catch (Exception $e) {
            error_log(sprintf(
                '[%s] Error al validar token JWT: %s',
                date('Y-m-d H:i:s'),
                $e->getMessage()
            ));

            throw new Exception('Token de autenticación inválido o expirado', 401);
        }
    }

    /**
     * Extrae el token del header Authorization
     *
     * @return string|null Token extraído o null si no existe
     */
    public static function extractTokenFromHeader(): ?string
    {
        $headers = getallheaders();

        if (!isset($headers['Authorization']) && !isset($headers['authorization'])) {
            return null;
        }

        $authHeader = $headers['Authorization'] ?? $headers['authorization'];

        // Verificar formato "Bearer {token}"
        if (!preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            return null;
        }

        return $matches[1] ?? null;
    }

    /**
     * Decodifica un token sin validarlo (útil para debug)
     *
     * @param string $token Token a decodificar
     * @return array<string, mixed> Payload del token
     * @throws Exception Si hay un error al decodificar
     */
    public static function decodeToken(string $token): array
    {
        $parts = explode('.', $token);

        if (count($parts) !== 3) {
            throw new Exception('Formato de token inválido');
        }

        $payload = json_decode(self::base64UrlDecode($parts[1]), true);

        if (!is_array($payload)) {
            throw new Exception('Payload de token inválido');
        }

        return $payload;
    }

    /**
     * Verifica si un token ha expirado
     *
     * @param string $token Token a verificar
     * @return bool True si el token ha expirado
     */
    public static function isTokenExpired(string $token): bool
    {
        try {
            $payload = self::decodeToken($token);
            $currentTime = JWTConfig::getCurrentTimestamp();

            return isset($payload['exp']) && $payload['exp'] < $currentTime;
        } catch (Exception $e) {
            return true;
        }
    }

    /**
     * Crea la firma del token usando HMAC SHA256
     *
     * @param string $data Datos a firmar
     * @return string Firma codificada
     */
    private static function createSignature(string $data): string
    {
        $signature = hash_hmac(
            'sha256',
            $data,
            JWTConfig::getSecretKey(),
            true
        );

        return self::base64UrlEncode($signature);
    }

    /**
     * Valida los claims estándar del token
     *
     * @param array<string, mixed> $payload Payload del token
     * @return void
     * @throws Exception Si los claims no son válidos
     */
    private static function validateClaims(array $payload): void
    {
        $currentTime = JWTConfig::getCurrentTimestamp();

        // Validar issuer
        if (
            isset($payload['iss']) &&
            $payload['iss'] !== JWTConfig::getIssuer()
        ) {
            throw new Exception('Emisor del token inválido');
        }

        // Validar audience
        if (
            isset($payload['aud']) &&
            $payload['aud'] !== JWTConfig::getAudience()
        ) {
            throw new Exception('Audiencia del token inválida');
        }

        // Validar expiración (con leeway)
        if (
            isset($payload['exp']) &&
            $payload['exp'] < ($currentTime - JWTConfig::LEEWAY)
        ) {
            throw new Exception('Token expirado');
        }

        // Validar not before (con leeway)
        if (
            isset($payload['nbf']) &&
            $payload['nbf'] > ($currentTime + JWTConfig::LEEWAY)
        ) {
            throw new Exception('Token no válido todavía');
        }
    }

    /**
     * Codifica en Base64 URL-safe
     *
     * @param string $data Datos a codificar
     * @return string Datos codificados
     */
    private static function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Decodifica de Base64 URL-safe
     *
     * @param string $data Datos a decodificar
     * @return string Datos decodificados
     */
    private static function base64UrlDecode(string $data): string
    {
        $remainder = strlen($data) % 4;

        if ($remainder) {
            $data .= str_repeat('=', 4 - $remainder);
        }

        return base64_decode(strtr($data, '-_', '+/'));
    }

    /**
     * Genera un token de acceso para un usuario
     *
     * @param array<string, mixed> $userData Datos del usuario
     * @return string Token de acceso
     */
    public static function generateAccessToken(array $userData): string
    {
        $payload = [
            'user_id' => $userData['id'],
            'rol' => $userData['rol'],
            'nombre' => $userData['nombre'],
            'apellido' => $userData['apellido'],
            'email' => $userData['email'],
        ];

        // Agregar grado_id si existe (para estudiantes)
        if (isset($userData['grado_id'])) {
            $payload['grado_id'] = $userData['grado_id'];
        }

        // Agregar materia_id si existe (para docentes)
        if (isset($userData['materia_id'])) {
            $payload['materia_id'] = $userData['materia_id'];
        }

        return self::generateToken($payload, false);
    }

    /**
     * Genera un token de refresco para un usuario
     *
     * @param int $userId ID del usuario
     * @return string Token de refresco
     */
    public static function generateRefreshToken(int $userId): string
    {
        $payload = [
            'user_id' => $userId,
            'type' => 'refresh',
        ];

        return self::generateToken($payload, true);
    }
}
