<?php declare(strict_types=1);

/**
 * Controlador de Autenticación
 *
 * Sistema de Biblioteca Digital de Videos Educativos
 * Unidad Educativa San Francisco Xavier
 *
 * @author Roger Omar Luna Yujra
 * @version 1.0
 */

namespace Controllers;

use Models\Usuario;
use Utils\JWTHandler;
use Utils\Logger;
use Middleware\ValidationMiddleware;
use Config\Database;
use PDO;
use Exception;

/**
 * Clase AuthController
 *
 * Maneja la autenticación de usuarios (login, registro, logout, refresh)
 */
class AuthController
{
    private Usuario $usuarioModel;
    private PDO $conn;

    public function __construct()
    {
        $this->usuarioModel = new Usuario();
        $this->conn = Database::getInstance()->getConnection();
    }

    /**
     * Inicia sesión de usuario
     *
     * POST /api/auth/login
     * Body: { "email": "user@example.com", "password": "password123" }
     *
     * @return void
     */
    public function login(): void
    {
        try {
            $datos = $this->obtenerDatosJSON();

            // Validar campos requeridos
            ValidationMiddleware::validarCamposRequeridos($datos, ['email', 'password']);
            ValidationMiddleware::validarEmail($datos['email']);

            // Verificar credenciales
            $usuario = $this->usuarioModel->verificarCredenciales(
                $datos['email'],
                $datos['password']
            );

            if (!$usuario) {
                Logger::warning('LOGIN_FALLIDO', 'Intento de login con credenciales inválidas');
                $this->enviarRespuesta(false, 'Credenciales inválidas', null, 401);
            }

            // Verificar si el usuario está bloqueado
            if ($this->usuarioModel->estaBloqueado($usuario['id'])) {
                $this->enviarRespuesta(
                    false,
                    'Usuario bloqueado temporalmente por múltiples intentos fallidos',
                    null,
                    403
                );
            }

            // Generar tokens
            $accessToken = JWTHandler::generateAccessToken($usuario);
            $refreshToken = JWTHandler::generateRefreshToken($usuario['id']);

            // Guardar refresh token en BD
            $this->guardarRefreshToken($usuario['id'], $refreshToken);

            // Log de login exitoso
            Logger::logLogin($usuario['id'], true);

            // Preparar respuesta
            $respuesta = [
                'access_token' => $accessToken,
                'refresh_token' => $refreshToken,
                'token_type' => 'Bearer',
                'expires_in' => \Config\JWTConfig::getAccessTokenExpiration(),
                'usuario' => [
                    'id' => $usuario['id'],
                    'nombre' => $usuario['nombre'],
                    'apellido' => $usuario['apellido'],
                    'email' => $usuario['email'],
                    'rol' => $usuario['rol'],
                    'grado_id' => $usuario['grado_id'],
                    'materia_id' => $usuario['materia_id'],
                ],
            ];

            $this->enviarRespuesta(true, 'Inicio de sesión exitoso', $respuesta);
        } catch (Exception $e) {
            $this->manejarError($e);
        }
    }

    /**
     * Registra un nuevo usuario
     *
     * POST /api/auth/register
     *
     * @return void
     */
    public function register(): void
    {
        try {
            $datos = $this->obtenerDatosJSON();

            // Validar campos requeridos
            ValidationMiddleware::validarCamposRequeridos(
                $datos,
                ['nombre', 'apellido', 'ci', 'email', 'password', 'rol_id']
            );

            ValidationMiddleware::validarEmail($datos['email']);
            ValidationMiddleware::validarPassword($datos['password']);

            // Verificar que el email no esté en uso
            if ($this->usuarioModel->obtenerPorEmail($datos['email'])) {
                $this->enviarRespuesta(false, 'El email ya está registrado', null, 409);
            }

            // Sanitizar datos
            $datos = ValidationMiddleware::sanitizarDatos($datos);

            // Crear usuario
            $usuarioId = $this->usuarioModel->crear($datos);

            Logger::info(
                'USUARIO_CREADO',
                sprintf('Nuevo usuario registrado: %s %s', $datos['nombre'], $datos['apellido']),
                $usuarioId
            );

            $this->enviarRespuesta(
                true,
                'Usuario registrado exitosamente',
                ['id' => $usuarioId],
                201
            );
        } catch (Exception $e) {
            $this->manejarError($e);
        }
    }

    /**
     * Refresca el access token usando el refresh token
     *
     * POST /api/auth/refresh-token
     * Body: { "refresh_token": "..." }
     *
     * @return void
     */
    public function refreshToken(): void
    {
        try {
            $datos = $this->obtenerDatosJSON();

            ValidationMiddleware::validarCamposRequeridos($datos, ['refresh_token']);

            $refreshToken = $datos['refresh_token'];

            // Validar refresh token
            $payload = JWTHandler::validateToken($refreshToken);

            // Verificar que sea un refresh token
            if (!isset($payload['type']) || $payload['type'] !== 'refresh') {
                $this->enviarRespuesta(false, 'Token inválido', null, 401);
            }

            // Verificar que el token esté en la BD
            if (!$this->verificarRefreshToken($payload['user_id'], $refreshToken)) {
                $this->enviarRespuesta(false, 'Token de refresco inválido', null, 401);
            }

            // Obtener usuario
            $usuario = $this->usuarioModel->obtenerPorId($payload['user_id']);

            if (!$usuario || $usuario['estado'] !== 'activo') {
                $this->enviarRespuesta(false, 'Usuario no encontrado o inactivo', null, 401);
            }

            // Generar nuevo access token
            $nuevoAccessToken = JWTHandler::generateAccessToken($usuario);

            $respuesta = [
                'access_token' => $nuevoAccessToken,
                'token_type' => 'Bearer',
                'expires_in' => \Config\JWTConfig::getAccessTokenExpiration(),
            ];

            $this->enviarRespuesta(true, 'Token refrescado exitosamente', $respuesta);
        } catch (Exception $e) {
            $this->manejarError($e);
        }
    }

    /**
     * Cierra sesión del usuario
     *
     * POST /api/auth/logout
     *
     * @return void
     */
    public function logout(): void
    {
        try {
            // Verificar autenticación
            $usuario = \Middleware\AuthMiddleware::verificar();

            // Eliminar refresh tokens del usuario
            $this->eliminarRefreshTokens($usuario['user_id']);

            Logger::logLogout($usuario['user_id']);

            $this->enviarRespuesta(true, 'Sesión cerrada exitosamente');
        } catch (Exception $e) {
            $this->manejarError($e);
        }
    }

    /**
     * Obtiene información del usuario autenticado
     *
     * GET /api/auth/me
     *
     * @return void
     */
    public function me(): void
    {
        try {
            $usuario = \Middleware\AuthMiddleware::verificar();

            // Obtener datos completos del usuario
            $usuarioCompleto = $this->usuarioModel->obtenerPorId($usuario['user_id']);

            if (!$usuarioCompleto) {
                $this->enviarRespuesta(false, 'Usuario no encontrado', null, 404);
            }

            // Eliminar datos sensibles
            unset($usuarioCompleto['password_hash']);
            unset($usuarioCompleto['intentos_login']);
            unset($usuarioCompleto['bloqueado_hasta']);

            $this->enviarRespuesta(true, 'Datos del usuario', $usuarioCompleto);
        } catch (Exception $e) {
            $this->manejarError($e);
        }
    }

    /**
     * Guarda un refresh token en la base de datos
     *
     * @param int $usuarioId ID del usuario
     * @param string $token Token a guardar
     * @return void
     */
    private function guardarRefreshToken(int $usuarioId, string $token): void
    {
        $stmt = $this->conn->prepare(
            'INSERT INTO tokens_refresh (usuario_id, token, ip, user_agent, expira_en)
            VALUES (?, ?, ?, ?, FROM_UNIXTIME(?))'
        );

        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;
        $expiraEn = \Config\JWTConfig::getRefreshTokenExpirationTimestamp();

        $stmt->execute([$usuarioId, $token, $ip, $userAgent, $expiraEn]);
    }

    /**
     * Verifica si un refresh token es válido
     *
     * @param int $usuarioId ID del usuario
     * @param string $token Token a verificar
     * @return bool True si el token es válido
     */
    private function verificarRefreshToken(int $usuarioId, string $token): bool
    {
        $stmt = $this->conn->prepare(
            'SELECT COUNT(*) FROM tokens_refresh
            WHERE usuario_id = ? AND token = ? AND expira_en > NOW()'
        );

        $stmt->execute([$usuarioId, $token]);

        return $stmt->fetchColumn() > 0;
    }

    /**
     * Elimina los refresh tokens de un usuario
     *
     * @param int $usuarioId ID del usuario
     * @return void
     */
    private function eliminarRefreshTokens(int $usuarioId): void
    {
        $stmt = $this->conn->prepare('DELETE FROM tokens_refresh WHERE usuario_id = ?');
        $stmt->execute([$usuarioId]);
    }

    /**
     * Obtiene datos JSON del cuerpo de la petición
     *
     * @return array<string, mixed> Datos decodificados
     */
    private function obtenerDatosJSON(): array
    {
        $json = file_get_contents('php://input');
        $datos = json_decode($json, true);

        return is_array($datos) ? $datos : [];
    }

    /**
     * Envía una respuesta JSON
     *
     * @param bool $success Si la operación fue exitosa
     * @param string $message Mensaje de respuesta
     * @param mixed $data Datos adicionales
     * @param int $code Código HTTP
     * @return void
     */
    private function enviarRespuesta(
        bool $success,
        string $message,
        $data = null,
        int $code = 200
    ): void {
        http_response_code($code);
        header('Content-Type: application/json');

        $respuesta = [
            'success' => $success,
            'message' => $message,
        ];

        if ($data !== null) {
            $respuesta['data'] = $data;
        }

        echo json_encode($respuesta);
        exit;
    }

    /**
     * Maneja errores y envía respuesta apropiada
     *
     * @param Exception $e Excepción a manejar
     * @return void
     */
    private function manejarError(Exception $e): void
    {
        Logger::error('ERROR_AUTH', $e->getMessage());

        $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;

        $this->enviarRespuesta(false, $e->getMessage(), null, $code);
    }
}
