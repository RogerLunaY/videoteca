<?php declare(strict_types=1);

/**
 * Manejador de Archivos
 *
 * Sistema de Biblioteca Digital de Videos Educativos
 * Unidad Educativa San Francisco Xavier
 *
 * @author Roger Omar Luna Yujra
 * @version 1.0
 */

namespace Utils;

use Exception;

/**
 * Clase FileHandler
 *
 * Maneja la subida, validación y gestión de archivos del sistema
 * Implementa seguridad para prevenir ataques de subida de archivos maliciosos
 */
class FileHandler
{
    /** @var int Tamaño máximo de archivo en bytes (500MB por defecto) */
    private const MAX_FILE_SIZE = 524288000;

    /** @var array<string> Formatos de video permitidos */
    private const ALLOWED_VIDEO_FORMATS = ['mp4', 'avi', 'mkv', 'webm', 'mov'];

    /** @var array<string> MIME types de video permitidos */
    private const ALLOWED_VIDEO_MIME_TYPES = [
        'video/mp4',
        'video/x-msvideo',
        'video/x-matroska',
        'video/webm',
        'video/quicktime',
    ];

    /** @var array<string> Formatos de imagen permitidos para thumbnails */
    private const ALLOWED_IMAGE_FORMATS = ['jpg', 'jpeg', 'png', 'webp'];

    /** @var array<string> MIME types de imagen permitidos */
    private const ALLOWED_IMAGE_MIME_TYPES = [
        'image/jpeg',
        'image/png',
        'image/webp',
    ];

    /**
     * Sube un archivo de video con validaciones de seguridad
     *
     * @param array<string, mixed> $file Archivo desde $_FILES
     * @param string $destinationPath Ruta de destino
     * @return array<string, mixed> Información del archivo subido
     * @throws Exception Si hay un error en la subida
     */
    public static function uploadVideo(array $file, string $destinationPath): array
    {
        // Validar que el archivo existe
        if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
            throw new Exception('No se recibió ningún archivo');
        }

        // Validar errores de subida
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception(self::getUploadErrorMessage($file['error']));
        }

        // Validar tamaño
        $maxSize = (int)($_ENV['MAX_VIDEO_SIZE'] ?? self::MAX_FILE_SIZE);
        if ($file['size'] > $maxSize) {
            $maxSizeMB = round($maxSize / 1024 / 1024);
            throw new Exception("El archivo excede el tamaño máximo permitido ({$maxSizeMB}MB)");
        }

        // Validar tipo MIME
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, self::ALLOWED_VIDEO_MIME_TYPES, true)) {
            throw new Exception('Formato de video no permitido');
        }

        // Validar extensión
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, self::ALLOWED_VIDEO_FORMATS, true)) {
            throw new Exception('Extensión de archivo no permitida');
        }

        // Generar nombre único y seguro
        $fileName = self::generateSecureFileName($file['name']);
        $fullPath = $destinationPath . '/' . $fileName;

        // Crear directorio si no existe
        if (!is_dir($destinationPath)) {
            if (!mkdir($destinationPath, 0755, true)) {
                throw new Exception('No se pudo crear el directorio de destino');
            }
        }

        // Mover archivo
        if (!move_uploaded_file($file['tmp_name'], $fullPath)) {
            throw new Exception('Error al mover el archivo a su destino');
        }

        return [
            'filename' => $fileName,
            'path' => $fullPath,
            'size' => $file['size'],
            'mime_type' => $mimeType,
            'extension' => $extension,
        ];
    }

    /**
     * Sube una imagen (thumbnail)
     *
     * @param array<string, mixed> $file Archivo desde $_FILES
     * @param string $destinationPath Ruta de destino
     * @return array<string, mixed> Información del archivo subido
     * @throws Exception Si hay un error en la subida
     */
    public static function uploadImage(array $file, string $destinationPath): array
    {
        // Validar que el archivo existe
        if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
            throw new Exception('No se recibió ningún archivo de imagen');
        }

        // Validar errores de subida
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception(self::getUploadErrorMessage($file['error']));
        }

        // Validar tamaño (máximo 5MB para imágenes)
        $maxImageSize = 5242880;
        if ($file['size'] > $maxImageSize) {
            throw new Exception('La imagen excede el tamaño máximo permitido (5MB)');
        }

        // Validar tipo MIME
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, self::ALLOWED_IMAGE_MIME_TYPES, true)) {
            throw new Exception('Formato de imagen no permitido');
        }

        // Validar extensión
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, self::ALLOWED_IMAGE_FORMATS, true)) {
            throw new Exception('Extensión de imagen no permitida');
        }

        // Generar nombre único
        $fileName = self::generateSecureFileName($file['name']);
        $fullPath = $destinationPath . '/' . $fileName;

        // Crear directorio si no existe
        if (!is_dir($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        // Mover archivo
        if (!move_uploaded_file($file['tmp_name'], $fullPath)) {
            throw new Exception('Error al mover la imagen a su destino');
        }

        return [
            'filename' => $fileName,
            'path' => $fullPath,
            'size' => $file['size'],
            'mime_type' => $mimeType,
            'extension' => $extension,
        ];
    }

    /**
     * Elimina un archivo del sistema
     *
     * @param string $filePath Ruta del archivo
     * @return bool True si se eliminó correctamente
     */
    public static function deleteFile(string $filePath): bool
    {
        if (file_exists($filePath)) {
            return unlink($filePath);
        }

        return false;
    }

    /**
     * Genera un nombre de archivo seguro y único
     *
     * @param string $originalName Nombre original del archivo
     * @return string Nombre seguro generado
     */
    private static function generateSecureFileName(string $originalName): string
    {
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $baseName = pathinfo($originalName, PATHINFO_FILENAME);

        // Sanitizar el nombre base
        $baseName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $baseName);
        $baseName = substr($baseName, 0, 50);

        // Generar un hash único
        $uniqueId = uniqid('', true);
        $timestamp = time();

        return sprintf('%s_%s_%s.%s', $baseName, $timestamp, $uniqueId, $extension);
    }

    /**
     * Obtiene el mensaje de error de subida
     *
     * @param int $errorCode Código de error
     * @return string Mensaje de error
     */
    private static function getUploadErrorMessage(int $errorCode): string
    {
        return match ($errorCode) {
            UPLOAD_ERR_INI_SIZE => 'El archivo excede el tamaño máximo permitido por el servidor',
            UPLOAD_ERR_FORM_SIZE => 'El archivo excede el tamaño máximo permitido',
            UPLOAD_ERR_PARTIAL => 'El archivo se subió parcialmente',
            UPLOAD_ERR_NO_FILE => 'No se subió ningún archivo',
            UPLOAD_ERR_NO_TMP_DIR => 'Falta el directorio temporal',
            UPLOAD_ERR_CANT_WRITE => 'Error al escribir el archivo en disco',
            UPLOAD_ERR_EXTENSION => 'Una extensión de PHP detuvo la subida',
            default => 'Error desconocido al subir el archivo',
        };
    }

    /**
     * Obtiene el tamaño de un archivo en formato legible
     *
     * @param int $bytes Tamaño en bytes
     * @return string Tamaño formateado
     */
    public static function formatFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1024 ** $pow);

        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Valida si un archivo es un video válido
     *
     * @param string $filePath Ruta del archivo
     * @return bool True si es un video válido
     */
    public static function isValidVideo(string $filePath): bool
    {
        if (!file_exists($filePath)) {
            return false;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $filePath);
        finfo_close($finfo);

        return in_array($mimeType, self::ALLOWED_VIDEO_MIME_TYPES, true);
    }

    /**
     * Crea un directorio si no existe
     *
     * @param string $path Ruta del directorio
     * @return bool True si se creó o ya existe
     */
    public static function ensureDirectoryExists(string $path): bool
    {
        if (is_dir($path)) {
            return true;
        }

        return mkdir($path, 0755, true);
    }
}
