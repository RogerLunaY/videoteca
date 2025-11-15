<?php

/**
 * Sistema de Logging
 *
 * Sistema de Biblioteca Digital de Videos Educativos
 * Unidad Educativa San Francisco Xavier
 *
 * @author Roger Omar Luna Yujra
 * @version 1.0
 */

declare(strict_types=1);

namespace Utils;

use Config\Database;
use PDO;
use Exception;

/**
 * Clase Logger
 *
 * Registra actividades del sistema en la base de datos y archivos de log
 * Implementa diferentes niveles de logging (info, warning, error, critical)
 */
class Logger
{
    /** @var string Directorio de logs */
    private const LOG_DIR = __DIR__ . '/../logs';

    /** @var string Nivel: Información general */
    public const LEVEL_INFO = 'info';

    /** @var string Nivel: Advertencias */
    public const LEVEL_WARNING = 'warning';

    /** @var string Nivel: Errores */
    public const LEVEL_ERROR = 'error';

    /** @var string Nivel: Crítico */
    public const LEVEL_CRITICAL = 'critical';

    /**
     * Registra una actividad en la base de datos
     *
     * @param string $accion Acción realizada
     * @param string|null $descripcion Descripción de la acción
     * @param int|null $usuarioId ID del usuario que realizó la acción
     * @param string|null $entidadTipo Tipo de entidad afectada
     * @param int|null $entidadId ID de la entidad afectada
     * @param string $nivel Nivel del log
     * @param array<string, mixed>|null $datosAdicionales Datos adicionales
     * @return bool True si se registró correctamente
     */
    public static function log(
        string $accion,
        ?string $descripcion = null,
        ?int $usuarioId = null,
        ?string $entidadTipo = null,
        ?int $entidadId = null,
        string $nivel = self::LEVEL_INFO,
        ?array $datosAdicionales = null
    ): bool {
        try {
            $db = Database::getInstance();
            $conn = $db->getConnection();

            $stmt = $conn->prepare(
                'INSERT INTO logs_sistema
                (usuario_id, accion, descripcion, entidad_tipo, entidad_id, ip, user_agent, datos_adicionales, nivel)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
            );

            $ip = self::getClientIP();
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;
            $datosJson = $datosAdicionales ? json_encode($datosAdicionales) : null;

            $stmt->execute([
                $usuarioId,
                $accion,
                $descripcion,
                $entidadTipo,
                $entidadId,
                $ip,
                $userAgent,
                $datosJson,
                $nivel,
            ]);

            return true;
        } catch (Exception $e) {
            // Si falla el log en BD, registrar en archivo
            self::logToFile(
                $nivel,
                sprintf('Error al registrar en BD: %s | Acción: %s', $e->getMessage(), $accion)
            );

            return false;
        }
    }

    /**
     * Registra un log de información
     *
     * @param string $accion Acción realizada
     * @param string|null $descripcion Descripción
     * @param int|null $usuarioId ID del usuario
     * @return bool True si se registró correctamente
     */
    public static function info(
        string $accion,
        ?string $descripcion = null,
        ?int $usuarioId = null
    ): bool {
        return self::log($accion, $descripcion, $usuarioId, null, null, self::LEVEL_INFO);
    }

    /**
     * Registra un log de advertencia
     *
     * @param string $accion Acción realizada
     * @param string|null $descripcion Descripción
     * @param int|null $usuarioId ID del usuario
     * @return bool True si se registró correctamente
     */
    public static function warning(
        string $accion,
        ?string $descripcion = null,
        ?int $usuarioId = null
    ): bool {
        return self::log($accion, $descripcion, $usuarioId, null, null, self::LEVEL_WARNING);
    }

    /**
     * Registra un log de error
     *
     * @param string $accion Acción realizada
     * @param string|null $descripcion Descripción
     * @param int|null $usuarioId ID del usuario
     * @return bool True si se registró correctamente
     */
    public static function error(
        string $accion,
        ?string $descripcion = null,
        ?int $usuarioId = null
    ): bool {
        return self::log($accion, $descripcion, $usuarioId, null, null, self::LEVEL_ERROR);
    }

    /**
     * Registra un log crítico
     *
     * @param string $accion Acción realizada
     * @param string|null $descripcion Descripción
     * @param int|null $usuarioId ID del usuario
     * @return bool True si se registró correctamente
     */
    public static function critical(
        string $accion,
        ?string $descripcion = null,
        ?int $usuarioId = null
    ): bool {
        return self::log($accion, $descripcion, $usuarioId, null, null, self::LEVEL_CRITICAL);
    }

    /**
     * Registra un log en archivo
     *
     * @param string $nivel Nivel del log
     * @param string $mensaje Mensaje a registrar
     * @return void
     */
    private static function logToFile(string $nivel, string $mensaje): void
    {
        // Crear directorio de logs si no existe
        if (!is_dir(self::LOG_DIR)) {
            mkdir(self::LOG_DIR, 0755, true);
        }

        $fileName = sprintf('%s/app_%s.log', self::LOG_DIR, date('Y-m-d'));
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = sprintf("[%s] [%s] %s\n", $timestamp, strtoupper($nivel), $mensaje);

        file_put_contents($fileName, $logEntry, FILE_APPEND);
    }

    /**
     * Obtiene la dirección IP del cliente
     *
     * @return string Dirección IP
     */
    private static function getClientIP(): string
    {
        $ipKeys = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR',
        ];

        foreach ($ipKeys as $key) {
            if (isset($_SERVER[$key]) && filter_var($_SERVER[$key], FILTER_VALIDATE_IP)) {
                return $_SERVER[$key];
            }
        }

        return '0.0.0.0';
    }

    /**
     * Registra el login de un usuario
     *
     * @param int $usuarioId ID del usuario
     * @param bool $exitoso Si el login fue exitoso
     * @return bool True si se registró correctamente
     */
    public static function logLogin(int $usuarioId, bool $exitoso): bool
    {
        return self::log(
            $exitoso ? 'LOGIN_EXITOSO' : 'LOGIN_FALLIDO',
            $exitoso ? 'Inicio de sesión exitoso' : 'Intento de inicio de sesión fallido',
            $usuarioId,
            'usuario',
            $usuarioId,
            $exitoso ? self::LEVEL_INFO : self::LEVEL_WARNING
        );
    }

    /**
     * Registra el logout de un usuario
     *
     * @param int $usuarioId ID del usuario
     * @return bool True si se registró correctamente
     */
    public static function logLogout(int $usuarioId): bool
    {
        return self::log(
            'LOGOUT',
            'Cierre de sesión',
            $usuarioId,
            'usuario',
            $usuarioId,
            self::LEVEL_INFO
        );
    }

    /**
     * Registra la creación de un video
     *
     * @param int $videoId ID del video
     * @param int $docenteId ID del docente
     * @param string $titulo Título del video
     * @return bool True si se registró correctamente
     */
    public static function logVideoCreado(int $videoId, int $docenteId, string $titulo): bool
    {
        return self::log(
            'VIDEO_CREADO',
            sprintf('Video creado: %s', $titulo),
            $docenteId,
            'video',
            $videoId,
            self::LEVEL_INFO
        );
    }

    /**
     * Registra la reproducción de un video
     *
     * @param int $videoId ID del video
     * @param int $usuarioId ID del usuario
     * @return bool True si se registró correctamente
     */
    public static function logVideoReproducido(int $videoId, int $usuarioId): bool
    {
        return self::log(
            'VIDEO_REPRODUCIDO',
            'Reproducción de video',
            $usuarioId,
            'video',
            $videoId,
            self::LEVEL_INFO
        );
    }

    /**
     * Limpia logs antiguos de la base de datos
     *
     * @param int $dias Días de antigüedad a mantener (por defecto 90)
     * @return int Número de logs eliminados
     */
    public static function limpiarLogsAntiguos(int $dias = 90): int
    {
        try {
            $db = Database::getInstance();
            $conn = $db->getConnection();

            $stmt = $conn->prepare(
                'DELETE FROM logs_sistema WHERE fecha < DATE_SUB(NOW(), INTERVAL ? DAY)'
            );

            $stmt->execute([$dias]);

            return $stmt->rowCount();
        } catch (Exception $e) {
            self::logToFile(self::LEVEL_ERROR, 'Error al limpiar logs antiguos: ' . $e->getMessage());
            return 0;
        }
    }
}
