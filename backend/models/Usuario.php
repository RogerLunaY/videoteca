<?php

/**
 * Modelo de Usuario
 *
 * Sistema de Biblioteca Digital de Videos Educativos
 * Unidad Educativa San Francisco Xavier
 *
 * @author Roger Omar Luna Yujra
 * @version 1.0
 */

declare(strict_types=1);

namespace Models;

use Config\Database;
use PDO;
use Exception;

/**
 * Clase Usuario
 *
 * Modelo para gestionar usuarios del sistema (Administradores, Docentes, Estudiantes)
 */
class Usuario
{
    private PDO $conn;

    public function __construct()
    {
        $this->conn = Database::getInstance()->getConnection();
    }

    /**
     * Obtiene un usuario por su ID
     *
     * @param int $id ID del usuario
     * @return array<string, mixed>|null Datos del usuario o null si no existe
     */
    public function obtenerPorId(int $id): ?array
    {
        $stmt = $this->conn->prepare(
            'SELECT u.*, r.nombre as rol FROM usuarios u
            INNER JOIN roles r ON u.rol_id = r.id
            WHERE u.id = ?'
        );

        $stmt->execute([$id]);
        $result = $stmt->fetch();

        return $result ?: null;
    }

    /**
     * Obtiene un usuario por su email
     *
     * @param string $email Email del usuario
     * @return array<string, mixed>|null Datos del usuario o null si no existe
     */
    public function obtenerPorEmail(string $email): ?array
    {
        $stmt = $this->conn->prepare(
            'SELECT u.*, r.nombre as rol FROM usuarios u
            INNER JOIN roles r ON u.rol_id = r.id
            WHERE u.email = ?'
        );

        $stmt->execute([$email]);
        $result = $stmt->fetch();

        return $result ?: null;
    }

    /**
     * Crea un nuevo usuario
     *
     * @param array<string, mixed> $datos Datos del usuario
     * @return int ID del usuario creado
     * @throws Exception Si hay un error al crear el usuario
     */
    public function crear(array $datos): int
    {
        $stmt = $this->conn->prepare(
            'INSERT INTO usuarios
            (nombre, apellido, ci, email, password_hash, rol_id, grado_id, materia_id, telefono, direccion)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );

        $passwordHash = password_hash($datos['password'], PASSWORD_BCRYPT, ['cost' => 12]);

        $stmt->execute([
            $datos['nombre'],
            $datos['apellido'],
            $datos['ci'],
            $datos['email'],
            $passwordHash,
            $datos['rol_id'],
            $datos['grado_id'] ?? null,
            $datos['materia_id'] ?? null,
            $datos['telefono'] ?? null,
            $datos['direccion'] ?? null,
        ]);

        return (int)$this->conn->lastInsertId();
    }

    /**
     * Actualiza un usuario
     *
     * @param int $id ID del usuario
     * @param array<string, mixed> $datos Datos a actualizar
     * @return bool True si se actualizó correctamente
     */
    public function actualizar(int $id, array $datos): bool
    {
        $campos = [];
        $valores = [];

        foreach ($datos as $campo => $valor) {
            if ($campo === 'password') {
                $campos[] = 'password_hash = ?';
                $valores[] = password_hash($valor, PASSWORD_BCRYPT, ['cost' => 12]);
            } elseif ($campo !== 'id') {
                $campos[] = "{$campo} = ?";
                $valores[] = $valor;
            }
        }

        if (empty($campos)) {
            return false;
        }

        $valores[] = $id;

        $sql = sprintf('UPDATE usuarios SET %s WHERE id = ?', implode(', ', $campos));
        $stmt = $this->conn->prepare($sql);

        return $stmt->execute($valores);
    }

    /**
     * Elimina un usuario
     *
     * @param int $id ID del usuario
     * @return bool True si se eliminó correctamente
     */
    public function eliminar(int $id): bool
    {
        $stmt = $this->conn->prepare('DELETE FROM usuarios WHERE id = ?');
        return $stmt->execute([$id]);
    }

    /**
     * Obtiene todos los usuarios con filtros opcionales
     *
     * @param array<string, mixed> $filtros Filtros a aplicar
     * @return array<array<string, mixed>> Lista de usuarios
     */
    public function obtenerTodos(array $filtros = []): array
    {
        $sql = 'SELECT u.*, r.nombre as rol, g.nombre as grado, m.nombre as materia
                FROM usuarios u
                INNER JOIN roles r ON u.rol_id = r.id
                LEFT JOIN grados g ON u.grado_id = g.id
                LEFT JOIN materias m ON u.materia_id = m.id
                WHERE 1=1';

        $params = [];

        if (isset($filtros['rol_id'])) {
            $sql .= ' AND u.rol_id = ?';
            $params[] = $filtros['rol_id'];
        }

        if (isset($filtros['grado_id'])) {
            $sql .= ' AND u.grado_id = ?';
            $params[] = $filtros['grado_id'];
        }

        if (isset($filtros['estado'])) {
            $sql .= ' AND u.estado = ?';
            $params[] = $filtros['estado'];
        }

        if (isset($filtros['busqueda'])) {
            $sql .= ' AND (u.nombre LIKE ? OR u.apellido LIKE ? OR u.email LIKE ? OR u.ci LIKE ?)';
            $busqueda = "%{$filtros['busqueda']}%";
            $params[] = $busqueda;
            $params[] = $busqueda;
            $params[] = $busqueda;
            $params[] = $busqueda;
        }

        $sql .= ' ORDER BY u.nombre, u.apellido';

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    /**
     * Verifica las credenciales de un usuario
     *
     * @param string $email Email del usuario
     * @param string $password Contraseña del usuario
     * @return array<string, mixed>|null Datos del usuario si las credenciales son correctas
     */
    public function verificarCredenciales(string $email, string $password): ?array
    {
        $usuario = $this->obtenerPorEmail($email);

        if (!$usuario) {
            return null;
        }

        if (!password_verify($password, $usuario['password_hash'])) {
            $this->incrementarIntentosLogin($usuario['id']);
            return null;
        }

        // Resetear intentos de login
        $this->resetearIntentosLogin($usuario['id']);

        return $usuario;
    }

    /**
     * Incrementa el contador de intentos de login fallidos
     *
     * @param int $usuarioId ID del usuario
     * @return void
     */
    private function incrementarIntentosLogin(int $usuarioId): void
    {
        $stmt = $this->conn->prepare(
            'UPDATE usuarios SET intentos_login = intentos_login + 1 WHERE id = ?'
        );
        $stmt->execute([$usuarioId]);

        // Bloquear usuario si supera los 5 intentos
        $stmt = $this->conn->prepare('SELECT intentos_login FROM usuarios WHERE id = ?');
        $stmt->execute([$usuarioId]);
        $intentos = $stmt->fetchColumn();

        if ($intentos >= 5) {
            $this->bloquearUsuario($usuarioId, 30); // Bloquear por 30 minutos
        }
    }

    /**
     * Resetea el contador de intentos de login
     *
     * @param int $usuarioId ID del usuario
     * @return void
     */
    private function resetearIntentosLogin(int $usuarioId): void
    {
        $stmt = $this->conn->prepare(
            'UPDATE usuarios SET intentos_login = 0, bloqueado_hasta = NULL WHERE id = ?'
        );
        $stmt->execute([$usuarioId]);
    }

    /**
     * Bloquea un usuario temporalmente
     *
     * @param int $usuarioId ID del usuario
     * @param int $minutos Minutos de bloqueo
     * @return void
     */
    private function bloquearUsuario(int $usuarioId, int $minutos): void
    {
        $stmt = $this->conn->prepare(
            'UPDATE usuarios SET bloqueado_hasta = DATE_ADD(NOW(), INTERVAL ? MINUTE) WHERE id = ?'
        );
        $stmt->execute([$minutos, $usuarioId]);
    }

    /**
     * Verifica si un usuario está bloqueado
     *
     * @param int $usuarioId ID del usuario
     * @return bool True si está bloqueado
     */
    public function estaBloqueado(int $usuarioId): bool
    {
        $stmt = $this->conn->prepare(
            'SELECT bloqueado_hasta FROM usuarios WHERE id = ?'
        );
        $stmt->execute([$usuarioId]);
        $bloqueadoHasta = $stmt->fetchColumn();

        if (!$bloqueadoHasta) {
            return false;
        }

        return strtotime($bloqueadoHasta) > time();
    }

    /**
     * Actualiza el último acceso del usuario
     *
     * @param int $usuarioId ID del usuario
     * @return void
     */
    public function actualizarUltimoAcceso(int $usuarioId): void
    {
        $stmt = $this->conn->prepare('UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = ?');
        $stmt->execute([$usuarioId]);
    }
}
