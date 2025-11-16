<?php declare(strict_types=1);

/**
 * Middleware de Roles
 *
 * Sistema de Biblioteca Digital de Videos Educativos
 * Unidad Educativa San Francisco Xavier
 *
 * @author Roger Omar Luna Yujra
 * @version 1.0
 */

namespace Middleware;

use Exception;

/**
 * Clase RoleMiddleware
 *
 * Verifica que el usuario autenticado tenga los permisos necesarios
 * basándose en su rol
 */
class RoleMiddleware
{
    /**
     * Verifica que el usuario tenga uno de los roles permitidos
     *
     * @param array<string, mixed> $usuario Datos del usuario autenticado
     * @param array<string> $rolesPermitidos Roles permitidos
     * @return void
     * @throws Exception Si el usuario no tiene permiso
     */
    public static function verificar(array $usuario, array $rolesPermitidos): void
    {
        if (!isset($usuario['rol'])) {
            self::enviarError('Rol de usuario no especificado', 403);
        }

        if (!in_array($usuario['rol'], $rolesPermitidos, true)) {
            self::enviarError('No tiene permisos para realizar esta acción', 403);
        }
    }

    /**
     * Verifica que el usuario sea administrador
     *
     * @param array<string, mixed> $usuario Datos del usuario autenticado
     * @return void
     */
    public static function esAdministrador(array $usuario): void
    {
        self::verificar($usuario, ['Administrador']);
    }

    /**
     * Verifica que el usuario sea docente o administrador
     *
     * @param array<string, mixed> $usuario Datos del usuario autenticado
     * @return void
     */
    public static function esDocenteOAdministrador(array $usuario): void
    {
        self::verificar($usuario, ['Administrador', 'Docente']);
    }

    /**
     * Verifica que el usuario sea estudiante
     *
     * @param array<string, mixed> $usuario Datos del usuario autenticado
     * @return void
     */
    public static function esEstudiante(array $usuario): void
    {
        self::verificar($usuario, ['Estudiante']);
    }

    /**
     * Verifica que el usuario sea el propietario del recurso o administrador
     *
     * @param array<string, mixed> $usuario Datos del usuario autenticado
     * @param int $propietarioId ID del propietario del recurso
     * @return void
     */
    public static function esPropietarioOAdministrador(array $usuario, int $propietarioId): void
    {
        if ($usuario['rol'] === 'Administrador') {
            return;
        }

        if ($usuario['user_id'] !== $propietarioId) {
            self::enviarError('No tiene permisos para realizar esta acción', 403);
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
