<?php declare(strict_types=1);

/**
 * Modelo de Video
 *
 * Sistema de Biblioteca Digital de Videos Educativos
 * Unidad Educativa San Francisco Xavier
 *
 * @author Roger Omar Luna Yujra
 * @version 1.0
 */

namespace Models;

use Config\Database;
use PDO;
use Exception;

/**
 * Clase Video
 *
 * Modelo para gestionar videos educativos del sistema
 */
class Video
{
    private PDO $conn;

    public function __construct()
    {
        $this->conn = Database::getInstance()->getConnection();
    }

    /**
     * Obtiene un video por su ID con información completa
     *
     * @param int $id ID del video
     * @return array<string, mixed>|null Datos del video o null si no existe
     */
    public function obtenerPorId(int $id): ?array
    {
        $stmt = $this->conn->prepare(
            'SELECT v.*, m.nombre as materia_nombre, g.nombre as grado_nombre,
            CONCAT(u.nombre, " ", u.apellido) as docente_nombre,
            (SELECT COUNT(*) FROM comentarios WHERE video_id = v.id AND estado = "activo") as total_comentarios,
            (SELECT COUNT(*) FROM favoritos WHERE video_id = v.id) as total_favoritos
            FROM videos v
            INNER JOIN materias m ON v.materia_id = m.id
            INNER JOIN grados g ON v.grado_id = g.id
            INNER JOIN usuarios u ON v.docente_id = u.id
            WHERE v.id = ?'
        );

        $stmt->execute([$id]);
        $result = $stmt->fetch();

        if ($result) {
            $result['categorias'] = $this->obtenerCategorias($id);
        }

        return $result ?: null;
    }

    /**
     * Crea un nuevo video
     *
     * @param array<string, mixed> $datos Datos del video
     * @return int ID del video creado
     * @throws Exception Si hay un error al crear el video
     */
    public function crear(array $datos): int
    {
        $stmt = $this->conn->prepare(
            'INSERT INTO videos
            (titulo, descripcion, archivo_path, thumbnail_path, duracion, tamaño, formato,
            resolucion, codec, materia_id, grado_id, docente_id, estado)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );

        $stmt->execute([
            $datos['titulo'],
            $datos['descripcion'] ?? null,
            $datos['archivo_path'],
            $datos['thumbnail_path'] ?? null,
            $datos['duracion'],
            $datos['tamaño'],
            $datos['formato'],
            $datos['resolucion'] ?? null,
            $datos['codec'] ?? null,
            $datos['materia_id'],
            $datos['grado_id'],
            $datos['docente_id'],
            $datos['estado'] ?? 'activo',
        ]);

        return (int)$this->conn->lastInsertId();
    }

    /**
     * Actualiza un video
     *
     * @param int $id ID del video
     * @param array<string, mixed> $datos Datos a actualizar
     * @return bool True si se actualizó correctamente
     */
    public function actualizar(int $id, array $datos): bool
    {
        $campos = [];
        $valores = [];

        foreach ($datos as $campo => $valor) {
            if ($campo !== 'id') {
                $campos[] = "{$campo} = ?";
                $valores[] = $valor;
            }
        }

        if (empty($campos)) {
            return false;
        }

        $valores[] = $id;

        $sql = sprintf('UPDATE videos SET %s WHERE id = ?', implode(', ', $campos));
        $stmt = $this->conn->prepare($sql);

        return $stmt->execute($valores);
    }

    /**
     * Elimina un video
     *
     * @param int $id ID del video
     * @return bool True si se eliminó correctamente
     */
    public function eliminar(int $id): bool
    {
        $stmt = $this->conn->prepare('DELETE FROM videos WHERE id = ?');
        return $stmt->execute([$id]);
    }

    /**
     * Obtiene todos los videos con filtros opcionales
     *
     * @param array<string, mixed> $filtros Filtros a aplicar
     * @return array<array<string, mixed>> Lista de videos
     */
    public function obtenerTodos(array $filtros = []): array
    {
        $sql = 'SELECT v.*, m.nombre as materia_nombre, g.nombre as grado_nombre,
                CONCAT(u.nombre, " ", u.apellido) as docente_nombre
                FROM videos v
                INNER JOIN materias m ON v.materia_id = m.id
                INNER JOIN grados g ON v.grado_id = g.id
                INNER JOIN usuarios u ON v.docente_id = u.id
                WHERE 1=1';

        $params = [];

        if (isset($filtros['materia_id'])) {
            $sql .= ' AND v.materia_id = ?';
            $params[] = $filtros['materia_id'];
        }

        if (isset($filtros['grado_id'])) {
            $sql .= ' AND v.grado_id = ?';
            $params[] = $filtros['grado_id'];
        }

        if (isset($filtros['docente_id'])) {
            $sql .= ' AND v.docente_id = ?';
            $params[] = $filtros['docente_id'];
        }

        if (isset($filtros['estado'])) {
            $sql .= ' AND v.estado = ?';
            $params[] = $filtros['estado'];
        } else {
            $sql .= ' AND v.estado = "activo"';
        }

        if (isset($filtros['busqueda'])) {
            $sql .= ' AND (v.titulo LIKE ? OR v.descripcion LIKE ?)';
            $busqueda = "%{$filtros['busqueda']}%";
            $params[] = $busqueda;
            $params[] = $busqueda;
        }

        $sql .= ' ORDER BY v.fecha_subida DESC';

        if (isset($filtros['limit'])) {
            $sql .= ' LIMIT ?';
            $params[] = (int)$filtros['limit'];
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    /**
     * Obtiene las categorías de un video
     *
     * @param int $videoId ID del video
     * @return array<array<string, mixed>> Categorías del video
     */
    public function obtenerCategorias(int $videoId): array
    {
        $stmt = $this->conn->prepare(
            'SELECT c.* FROM categorias c
            INNER JOIN video_categorias vc ON c.id = vc.categoria_id
            WHERE vc.video_id = ?'
        );

        $stmt->execute([$videoId]);

        return $stmt->fetchAll();
    }

    /**
     * Asigna categorías a un video
     *
     * @param int $videoId ID del video
     * @param array<int> $categoriaIds IDs de las categorías
     * @return bool True si se asignaron correctamente
     */
    public function asignarCategorias(int $videoId, array $categoriaIds): bool
    {
        // Eliminar categorías actuales
        $stmt = $this->conn->prepare('DELETE FROM video_categorias WHERE video_id = ?');
        $stmt->execute([$videoId]);

        // Insertar nuevas categorías
        $stmt = $this->conn->prepare(
            'INSERT INTO video_categorias (video_id, categoria_id) VALUES (?, ?)'
        );

        foreach ($categoriaIds as $categoriaId) {
            $stmt->execute([$videoId, $categoriaId]);
        }

        return true;
    }

    /**
     * Incrementa el contador de visualizaciones
     *
     * @param int $videoId ID del video
     * @return bool True si se incrementó correctamente
     */
    public function incrementarVisualizaciones(int $videoId): bool
    {
        $stmt = $this->conn->prepare(
            'UPDATE videos SET visualizaciones = visualizaciones + 1 WHERE id = ?'
        );

        return $stmt->execute([$videoId]);
    }

    /**
     * Obtiene los videos más populares
     *
     * @param int $limit Número de videos a obtener
     * @return array<array<string, mixed>> Videos más populares
     */
    public function obtenerMasPopulares(int $limit = 10): array
    {
        $stmt = $this->conn->prepare(
            'SELECT * FROM v_videos_populares LIMIT ?'
        );

        $stmt->execute([$limit]);

        return $stmt->fetchAll();
    }

    /**
     * Busca videos por texto completo
     *
     * @param string $busqueda Texto a buscar
     * @param int $limit Número máximo de resultados
     * @return array<array<string, mixed>> Videos encontrados
     */
    public function buscarTextoCompleto(string $busqueda, int $limit = 50): array
    {
        $stmt = $this->conn->prepare(
            'SELECT v.*, m.nombre as materia_nombre, g.nombre as grado_nombre,
            CONCAT(u.nombre, " ", u.apellido) as docente_nombre,
            MATCH(v.titulo, v.descripcion) AGAINST(? IN NATURAL LANGUAGE MODE) as relevancia
            FROM videos v
            INNER JOIN materias m ON v.materia_id = m.id
            INNER JOIN grados g ON v.grado_id = g.id
            INNER JOIN usuarios u ON v.docente_id = u.id
            WHERE MATCH(v.titulo, v.descripcion) AGAINST(? IN NATURAL LANGUAGE MODE)
            AND v.estado = "activo"
            ORDER BY relevancia DESC
            LIMIT ?'
        );

        $stmt->execute([$busqueda, $busqueda, $limit]);

        return $stmt->fetchAll();
    }

    /**
     * Obtiene videos recomendados para un usuario
     *
     * @param int $usuarioId ID del usuario
     * @param int $limit Número de videos a obtener
     * @return array<array<string, mixed>> Videos recomendados
     */
    public function obtenerRecomendados(int $usuarioId, int $limit = 10): array
    {
        $stmt = $this->conn->prepare('CALL sp_videos_recomendados(?)');
        $stmt->execute([$usuarioId]);

        $videos = $stmt->fetchAll();

        // Limitar resultados
        return array_slice($videos, 0, $limit);
    }
}
