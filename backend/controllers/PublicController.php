<?php

/**
 * Controlador Público
 * Endpoints sin autenticación para estudiantes
 *
 * Sistema de Biblioteca Digital de Videos Educativos
 * Unidad Educativa San Francisco Xavier
 *
 * @author Roger Omar Luna Yujra
 * @version 2.0
 */

declare(strict_types=1);

namespace Controllers;

use Models\Video;
use Config\Database;
use PDO;
use Exception;

/**
 * Clase PublicController
 *
 * Maneja endpoints públicos (sin JWT) para que estudiantes puedan
 * acceder a videos sin necesidad de login
 */
class PublicController
{
    private Video $videoModel;
    private PDO $conn;

    public function __construct()
    {
        $this->videoModel = new Video();
        $this->conn = Database::getInstance()->getConnection();
    }

    /**
     * Obtiene todos los videos (público)
     *
     * GET /api/public/videos
     *
     * @return void
     */
    public function getVideos(): void
    {
        try {
            $filtros = [
                'materia_id' => $_GET['materia_id'] ?? null,
                'grado_id' => $_GET['grado_id'] ?? null,
                'estado' => 'activo',
            ];

            $videos = $this->videoModel->obtenerTodos(array_filter($filtros));

            $this->enviarRespuesta(true, 'Videos obtenidos correctamente', $videos);
        } catch (Exception $e) {
            $this->manejarError($e);
        }
    }

    /**
     * Obtiene un video por ID (público)
     *
     * GET /api/public/videos/{id}
     *
     * @param int $id ID del video
     * @return void
     */
    public function getVideoById(int $id): void
    {
        try {
            $video = $this->videoModel->obtenerPorId($id);

            if (!$video || $video['estado'] !== 'activo') {
                $this->enviarRespuesta(false, 'Video no encontrado', null, 404);
            }

            $this->enviarRespuesta(true, 'Video obtenido correctamente', $video);
        } catch (Exception $e) {
            $this->manejarError($e);
        }
    }

    /**
     * Streaming de video (público)
     *
     * GET /api/public/videos/{id}/stream
     *
     * @param int $id ID del video
     * @return void
     */
    public function streamVideo(int $id): void
    {
        try {
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

            $this->enviarVideoStream($filePath);
        } catch (Exception $e) {
            http_response_code(500);
            exit('Error al transmitir el video');
        }
    }

    /**
     * Registra una visualización (anónima)
     *
     * POST /api/public/videos/{id}/view
     *
     * @param int $id ID del video
     * @return void
     */
    public function registrarVisualizacion(int $id): void
    {
        try {
            // Incrementar contador de visualizaciones
            $this->videoModel->incrementarVisualizaciones($id);

            // Opcional: Registrar en tabla de reproducciones anónimas
            $stmt = $this->conn->prepare(
                'INSERT INTO reproducciones (video_id, usuario_id, tiempo_reproducido, progreso)
                VALUES (?, NULL, 0, 0)'
            );
            $stmt->execute([$id]);

            $this->enviarRespuesta(true, 'Visualización registrada');
        } catch (Exception $e) {
            // No fallar si hay error, solo registrar
            $this->enviarRespuesta(true, 'Video disponible');
        }
    }

    /**
     * Busca videos (público)
     *
     * GET /api/public/videos/search?q={query}
     *
     * @return void
     */
    public function buscarVideos(): void
    {
        try {
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

            // Filtrar solo activos
            $videos = array_filter($videos, function($v) {
                return $v['estado'] === 'activo';
            });

            $this->enviarRespuesta(true, 'Resultados de búsqueda', array_values($videos));
        } catch (Exception $e) {
            $this->manejarError($e);
        }
    }

    /**
     * Obtiene todos los grados (público)
     *
     * GET /api/public/grados
     *
     * @return void
     */
    public function getGrados(): void
    {
        try {
            $stmt = $this->conn->prepare(
                'SELECT * FROM grados WHERE estado = "activo" ORDER BY id'
            );
            $stmt->execute();
            $grados = $stmt->fetchAll();

            $this->enviarRespuesta(true, 'Grados obtenidos correctamente', $grados);
        } catch (Exception $e) {
            $this->manejarError($e);
        }
    }

    /**
     * Obtiene todas las materias (público)
     *
     * GET /api/public/materias
     *
     * @return void
     */
    public function getMaterias(): void
    {
        try {
            $stmt = $this->conn->prepare(
                'SELECT * FROM materias WHERE estado = "activo" ORDER BY nombre'
            );
            $stmt->execute();
            $materias = $stmt->fetchAll();

            $this->enviarRespuesta(true, 'Materias obtenidas correctamente', $materias);
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
        error_log(sprintf(
            '[%s] Error en PublicController: %s',
            date('Y-m-d H:i:s'),
            $e->getMessage()
        ));

        $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;

        $this->enviarRespuesta(false, $e->getMessage(), null, $code);
    }
}
