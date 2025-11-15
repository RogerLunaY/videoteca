<?php

/**
 * Procesador de Videos
 *
 * Sistema de Biblioteca Digital de Videos Educativos
 * Unidad Educativa San Francisco Xavier
 *
 * @author Roger Omar Luna Yujra
 * @version 1.0
 */

declare(strict_types=1);

namespace Utils;

use Exception;

/**
 * Clase VideoProcessor
 *
 * Procesa videos para extraer metadatos y generar thumbnails
 * Requiere FFmpeg instalado en el sistema
 */
class VideoProcessor
{
    /** @var string Ruta al ejecutable de FFmpeg */
    private const FFMPEG_PATH = 'ffmpeg';

    /** @var string Ruta al ejecutable de FFprobe */
    private const FFPROBE_PATH = 'ffprobe';

    /**
     * Extrae los metadatos de un video usando FFprobe
     *
     * @param string $videoPath Ruta del video
     * @return array<string, mixed> Metadatos del video
     * @throws Exception Si no se pueden extraer los metadatos
     */
    public static function extractMetadata(string $videoPath): array
    {
        if (!file_exists($videoPath)) {
            throw new Exception('El archivo de video no existe');
        }

        try {
            // Comando FFprobe para obtener información en formato JSON
            $command = sprintf(
                '%s -v quiet -print_format json -show_format -show_streams "%s" 2>&1',
                self::FFPROBE_PATH,
                escapeshellarg($videoPath)
            );

            $output = shell_exec($command);

            if ($output === null) {
                throw new Exception('No se pudo ejecutar FFprobe');
            }

            $data = json_decode($output, true);

            if (!is_array($data)) {
                throw new Exception('Respuesta inválida de FFprobe');
            }

            // Extraer información relevante
            $videoStream = self::findVideoStream($data['streams'] ?? []);
            $format = $data['format'] ?? [];

            return [
                'duracion' => (int)($format['duration'] ?? 0),
                'tamaño' => (int)($format['size'] ?? 0),
                'formato' => pathinfo($videoPath, PATHINFO_EXTENSION),
                'resolucion' => self::getResolution($videoStream),
                'codec' => $videoStream['codec_name'] ?? 'unknown',
                'bitrate' => (int)($format['bit_rate'] ?? 0),
                'ancho' => (int)($videoStream['width'] ?? 0),
                'alto' => (int)($videoStream['height'] ?? 0),
            ];
        } catch (Exception $e) {
            error_log(sprintf(
                '[%s] Error al extraer metadatos: %s',
                date('Y-m-d H:i:s'),
                $e->getMessage()
            ));

            // Retornar metadatos básicos si falla FFprobe
            return [
                'duracion' => 0,
                'tamaño' => filesize($videoPath),
                'formato' => pathinfo($videoPath, PATHINFO_EXTENSION),
                'resolucion' => 'unknown',
                'codec' => 'unknown',
                'bitrate' => 0,
                'ancho' => 0,
                'alto' => 0,
            ];
        }
    }

    /**
     * Genera un thumbnail del video
     *
     * @param string $videoPath Ruta del video
     * @param string $thumbnailPath Ruta donde guardar el thumbnail
     * @param int $timeSeconds Segundo del video para capturar (por defecto 5)
     * @return bool True si se generó correctamente
     */
    public static function generateThumbnail(
        string $videoPath,
        string $thumbnailPath,
        int $timeSeconds = 5
    ): bool {
        if (!file_exists($videoPath)) {
            return false;
        }

        try {
            // Asegurar que el directorio existe
            $thumbnailDir = dirname($thumbnailPath);
            if (!is_dir($thumbnailDir)) {
                mkdir($thumbnailDir, 0755, true);
            }

            // Comando FFmpeg para generar thumbnail
            $command = sprintf(
                '%s -i "%s" -ss %d -vframes 1 -vf "scale=640:-1" "%s" 2>&1',
                self::FFMPEG_PATH,
                escapeshellarg($videoPath),
                $timeSeconds,
                escapeshellarg($thumbnailPath)
            );

            exec($command, $output, $returnCode);

            if ($returnCode !== 0) {
                error_log(sprintf(
                    '[%s] Error al generar thumbnail: %s',
                    date('Y-m-d H:i:s'),
                    implode("\n", $output)
                ));
                return false;
            }

            return file_exists($thumbnailPath);
        } catch (Exception $e) {
            error_log(sprintf(
                '[%s] Error al generar thumbnail: %s',
                date('Y-m-d H:i:s'),
                $e->getMessage()
            ));

            return false;
        }
    }

    /**
     * Busca el stream de video en los metadatos
     *
     * @param array<array<string, mixed>> $streams Streams del video
     * @return array<string, mixed> Stream de video encontrado
     */
    private static function findVideoStream(array $streams): array
    {
        foreach ($streams as $stream) {
            if (isset($stream['codec_type']) && $stream['codec_type'] === 'video') {
                return $stream;
            }
        }

        return [];
    }

    /**
     * Obtiene la resolución del video en formato legible
     *
     * @param array<string, mixed> $videoStream Stream de video
     * @return string Resolución (ej: 1080p, 720p)
     */
    private static function getResolution(array $videoStream): string
    {
        if (!isset($videoStream['height'])) {
            return 'unknown';
        }

        $height = (int)$videoStream['height'];

        return match (true) {
            $height >= 2160 => '4K',
            $height >= 1440 => '1440p',
            $height >= 1080 => '1080p',
            $height >= 720 => '720p',
            $height >= 480 => '480p',
            $height >= 360 => '360p',
            default => "{$height}p",
        };
    }

    /**
     * Convierte segundos a formato HH:MM:SS
     *
     * @param int $seconds Segundos
     * @return string Tiempo formateado
     */
    public static function formatDuration(int $seconds): string
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $seconds = $seconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }

    /**
     * Verifica si FFmpeg está disponible
     *
     * @return bool True si FFmpeg está disponible
     */
    public static function isFFmpegAvailable(): bool
    {
        $output = shell_exec(self::FFMPEG_PATH . ' -version 2>&1');
        return $output !== null && strpos($output, 'ffmpeg version') !== false;
    }

    /**
     * Verifica si FFprobe está disponible
     *
     * @return bool True si FFprobe está disponible
     */
    public static function isFFprobeAvailable(): bool
    {
        $output = shell_exec(self::FFPROBE_PATH . ' -version 2>&1');
        return $output !== null && strpos($output, 'ffprobe version') !== false;
    }
}
