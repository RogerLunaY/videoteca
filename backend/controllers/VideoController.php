<?php

/**
 * Controlador de Videos
 *
 * Sistema de Biblioteca Digital de Videos Educativos
 * Unidad Educativa San Francisco Xavier
 *
 * @author Roger Omar Luna Yujra
 * @version 1.0
 */

declare(strict_types=1);

namespace Controllers;

use Models\Video;
use Utils\FileHandler;
use Utils\VideoProcessor;
use Utils\Logger;
use Middleware\AuthMiddleware;
use Middleware\RoleMiddleware;
use Middleware\ValidationMiddleware;
use Exception;

/**
 * Clase VideoController
 *
 * Maneja las operaciones CRUD de videos educativos
 */
class VideoController
{
    private Video $videoModel;

    public function __construct()
    {
        $this->videoModel = new Video();
    }

    /**
     * Obtiene todos los videos
     *
     * GET /api/videos
     *
     * @return void
     */
    public function obtenerTodos(): void
    {
        try {
            // Verificar autenticación
            $usuario = AuthMiddleware::verificar();

            // Obtener filtros de query params
            $filtros = [
                'materia_id' => $_GET['materia_id'] ?? null,
                'grado_id' => $_GET['grado_id'] ?? null,
                'busqueda' => $_GET['q'] ?? null,
                'limit' => isset($_GET['limit']) ? (int)$_GET['limit'] : null,
            ];

            // Si es estudiante, filtrar por su grado
            if ($usuario['rol'] === 'Estudiante' && isset($usuario['grado_id'])) {
                $filtros['grado_id'] = $usuario['grado_id'];
            }

            // Si es docente, puede ver todos o solo los suyos
            if ($usuario['rol'] === 'Docente' && isset($_GET['mis_videos'])) {
                $filtros['docente_id'] = $usuario['user_id'];
            }

            $videos = $this->videoModel->obtenerTodos(array_filter($filtros));

            $this->enviarRespuesta(true, 'Videos obtenidos correctamente', $videos);
        } catch (Exception $e) {
            $this->manejarError($e);
        }
    }

    /**
     * Obtiene un video por ID
     *
     * GET /api/videos/{id}
     *
     * @param int $id ID del video
     * @return void
     */
    public function obtenerPorId(int $id): void
    {
        try {
            $usuario = AuthMiddleware::verificar();

            $video = $this->videoModel->obtenerPorId($id);

            if (!$video) {
                $this->enviarRespuesta(false, 'Video no encontrado', null, 404);
            }

            $this->enviarRespuesta(true, 'Video obtenido correctamente', $video);
        } catch (Exception $e) {
            $this->manejarError($e);
        }
    }

    /**
     * Crea un nuevo video
     *
     * POST /api/videos
     *
     * @return void
     */
    public function crear(): void
    {
        try {
            $usuario = AuthMiddleware::verificar();
            RoleMiddleware::esDocenteOAdministrador($usuario);

            // Validar que se haya subido un archivo
            if (!isset($_FILES['video']) || $_FILES['video']['error'] !== UPLOAD_ERR_OK) {
                $this->enviarRespuesta(false, 'No se recibió ningún archivo de video', null, 400);
            }

            // Validar campos requeridos
            ValidationMiddleware::validarCamposRequeridos($_POST, [
                'titulo',
                'materia_id',
                'grado_id',
            ]);

            // Subir video
            $uploadDir = __DIR__ . '/../uploads/videos';
            $videoInfo = FileHandler::uploadVideo($_FILES['video'], $uploadDir);

            // Extraer metadatos del video
            $metadata = VideoProcessor::extractMetadata($videoInfo['path']);

            // Generar thumbnail
            $thumbnailDir = __DIR__ . '/../uploads/thumbnails';
            $thumbnailPath = $thumbnailDir . '/' . pathinfo($videoInfo['filename'], PATHINFO_FILENAME) . '.jpg';
            VideoProcessor::generateThumbnail($videoInfo['path'], $thumbnailPath);

            // Preparar datos del video
            $datosVideo = [
                'titulo' => ValidationMiddleware::sanitizarTexto($_POST['titulo']),
                'descripcion' => $_POST['descripcion'] ?? null,
                'archivo_path' => '/uploads/videos/' . $videoInfo['filename'],
                'thumbnail_path' => file_exists($thumbnailPath) ? '/uploads/thumbnails/' . basename($thumbnailPath) : null,
                'duracion' => $metadata['duracion'],
                'tamaño' => $metadata['tamaño'],
                'formato' => $metadata['formato'],
                'resolucion' => $metadata['resolucion'],
                'codec' => $metadata['codec'],
                'materia_id' => ValidationMiddleware::validarEntero($_POST['materia_id']),
                'grado_id' => ValidationMiddleware::validarEntero($_POST['grado_id']),
                'docente_id' => $usuario['user_id'],
            ];

            // Crear video en BD
            $videoId = $this->videoModel->crear($datosVideo);

            // Asignar categorías si se proporcionaron
            if (isset($_POST['categorias'])) {
                $categorias = json_decode($_POST['categorias'], true);
                if (is_array($categorias)) {
                    $this->videoModel->asignarCategorias($videoId, $categorias);
                }
            }

            Logger::logVideoCreado($videoId, $usuario['user_id'], $datosVideo['titulo']);

            $this->enviarRespuesta(
                true,
                'Video creado exitosamente',
                ['id' => $videoId],
                201
            );
        } catch (Exception $e) {
            $this->manejarError($e);
        }
    }

    /**
     * Transmite un video (streaming)
     *
     * GET /api/videos/{id}/stream
     *
     * @param int $id ID del video
     * @return void
     */
    public function stream(int $id): void
    {
        try {
            $usuario = AuthMiddleware::verificar();

            $video = $this->videoModel->obtenerPorId($id);

            if (!$video || $video['estado'] !== 'activo') {
                http_response_code(404);
                exit('Video no encontrado');
            }

            $filePath = __DIR__ . '/..' . $video['archivo_path'];

            if (!file_exists($filePath)) {
                http_response_code(404);
                exit('Archivo de video no encontrado');
            }

            // Registrar reproducción
            Logger::logVideoReproducido($id, $usuario['user_id']);

            // Enviar archivo con soporte para range requests (streaming)
            $this->enviarVideoStream($filePath);
        } catch (Exception $e) {
            http_response_code(500);
            exit('Error al transmitir el video');
        }
    }

    /**
     * Busca videos
     *
     * GET /api/videos/buscar?q={query}
     *
     * @return void
     */
    public function buscar(): void
    {
        try {
            $usuario = AuthMiddleware::verificar();

            $query = $_GET['q'] ?? '';

            if (strlen($query) < 3) {
                $this->enviarRespuesta(
                    false,
                    'La búsqueda debe tener al menos 3 caracteres',
                    null,
                    400
                );
            }

            $videos = $this->videoModel->buscarTextoCompleto($query);

            $this->enviarRespuesta(true, 'Resultados de búsqueda', $videos);
        } catch (Exception $e) {
            $this->manejarError($e);
        }
    }

    /**
     * Envía un video con soporte para streaming (range requests)
     *
     * @param string $filePath Ruta del archivo de video
     * @return void
     */
    private function enviarVideoStream(string $filePath): void
    {
        $fileSize = filesize($filePath);
        $start = 0;
        $end = $fileSize - 1;

        header('Content-Type: video/mp4');
        header('Accept-Ranges: bytes');

        if (isset($_SERVER['HTTP_RANGE'])) {
            if (preg_match('/bytes=(\d+)-(\d*)/', $_SERVER['HTTP_RANGE'], $matches)) {
                $start = (int)$matches[1];
                $end = !empty($matches[2]) ? (int)$matches[2] : $end;
            }

            header('HTTP/1.1 206 Partial Content');
            header("Content-Range: bytes $start-$end/$fileSize");
        }

        $length = $end - $start + 1;
        header("Content-Length: $length");

        $file = fopen($filePath, 'rb');
        fseek($file, $start);

        $buffer = 8192;
        $bytesLeft = $length;

        while ($bytesLeft > 0 && !feof($file)) {
            $bytesToRead = min($buffer, $bytesLeft);
            echo fread($file, $bytesToRead);
            $bytesLeft -= $bytesToRead;
            flush();
        }

        fclose($file);
        exit;
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
        Logger::error('ERROR_VIDEO', $e->getMessage());

        $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;

        $this->enviarRespuesta(false, $e->getMessage(), null, $code);
    }
}
