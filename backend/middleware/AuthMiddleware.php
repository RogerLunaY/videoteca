<?php

/**
 * Middleware de Autenticación
 *
 * Sistema de Biblioteca Digital de Videos Educativos
 * Unidad Educativa San Francisco Xavier
 *
 * @author Roger Omar Luna Yujra
 * @version 1.0
 */

declare(strict_types=1);

namespace Middleware;

use Utils\JWTHandler;
use Models\Usuario;
use Exception;

/**
 * Clase AuthMiddleware
 *
 * Verifica que las peticiones incluyan un token JWT válido
 * Extrae la información del usuario autenticado del token
 */
class AuthMiddleware
{
    /**
     * Verifica la autenticación del usuario
     *
     * @return array<string, mixed> Datos del usuario autenticado
     * @throws Exception Si no hay token o es inválido
     */
    public static function verificar(): array
    {
        // Extraer token del header Authorization
        $token = JWTHandler::extractTokenFromHeader();

        if (!$token) {
            self::enviarError('Token de autenticación no proporcionado', 401);
        }

        try {
            // Validar y decodificar el token
            $payload = JWTHandler::validateToken($token);

            // Verificar que el usuario existe y está activo
            $usuarioModel = new Usuario();
            $usuario = $usuarioModel->obtenerPorId($payload['user_id']);

            if (!$usuario) {
                self::enviarError('Usuario no encontrado', 401);
            }

            if ($usuario['estado'] !== 'activo') {
                self::enviarError('Usuario inactivo o suspendido', 403);
            }

            // Verificar si el usuario está bloqueado
            if ($usuarioModel->estaBloqueado($usuario['id'])) {
                self::enviarError('Usuario bloqueado temporalmente por múltiples intentos fallidos', 403);
            }

            // Actualizar último acceso
            $usuarioModel->actualizarUltimoAcceso($usuario['id']);

            return $payload;
        } catch (Exception $e) {
            self::enviarError($e->getMessage(), 401);
        }
    }

    /**
     * Verifica la autenticación y retorna los datos del usuario (opcional)
     *
     * @return array<string, mixed>|null Datos del usuario o null si no está autenticado
     */
    public static function verificarOpcional(): ?array
    {
        try {
            return self::verificar();
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Envía una respuesta de error y termina la ejecución
     *
     * @param string $mensaje Mensaje de error
     * @param int $codigo Código HTTP
     * @return never
     */
    private static function enviarError(string $mensaje, int $codigo): never
    {
        http_response_code($codigo);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => $mensaje,
            'error' => true,
        ]);
        exit;
    }
}
