<?php

/**
 * Configuración de Base de Datos
 *
 * Sistema de Biblioteca Digital de Videos Educativos
 * Unidad Educativa San Francisco Xavier
 *
 * @author Roger Omar Luna Yujra
 * @version 1.0
 */

declare(strict_types=1);

namespace Config;

use PDO;
use PDOException;
use Exception;

/**
 * Clase Database
 *
 * Implementa el patrón Singleton para gestionar la conexión a la base de datos MySQL
 * Proporciona una única instancia de conexión PDO para toda la aplicación
 */
class Database
{
    /** @var Database|null Instancia única de la clase (Singleton) */
    private static ?Database $instance = null;

    /** @var PDO|null Objeto de conexión PDO */
    private ?PDO $connection = null;

    /** @var string Host del servidor de base de datos */
    private string $host;

    /** @var string Nombre de la base de datos */
    private string $database;

    /** @var string Usuario de la base de datos */
    private string $username;

    /** @var string Contraseña de la base de datos */
    private string $password;

    /** @var string Charset de la conexión */
    private string $charset;

    /** @var int Puerto del servidor MySQL */
    private int $port;

    /**
     * Constructor privado para implementar Singleton
     * Inicializa los parámetros de conexión desde variables de entorno
     */
    private function __construct()
    {
        $this->host = $_ENV['DB_HOST'] ?? 'localhost';
        $this->database = $_ENV['DB_NAME'] ?? 'videoteca_educativa';
        $this->username = $_ENV['DB_USER'] ?? 'root';
        $this->password = $_ENV['DB_PASS'] ?? '';
        $this->charset = $_ENV['DB_CHARSET'] ?? 'utf8mb4';
        $this->port = (int)($_ENV['DB_PORT'] ?? 3306);
    }

    /**
     * Obtiene la instancia única de Database
     *
     * @return Database Instancia única de la clase
     */
    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Obtiene la conexión PDO a la base de datos
     *
     * Crea una nueva conexión si no existe. La conexión se configura con:
     * - Modo de error PDO::ERRMODE_EXCEPTION
     * - Fetch mode PDO::FETCH_ASSOC
     * - Emulación de prepared statements desactivada
     *
     * @return PDO Objeto de conexión PDO
     * @throws Exception Si no se puede establecer la conexión
     */
    public function getConnection(): PDO
    {
        if ($this->connection === null) {
            try {
                $dsn = sprintf(
                    'mysql:host=%s;port=%d;dbname=%s;charset=%s',
                    $this->host,
                    $this->port,
                    $this->database,
                    $this->charset
                );

                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_PERSISTENT => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$this->charset}",
                    PDO::ATTR_STRINGIFY_FETCHES => false,
                ];

                $this->connection = new PDO($dsn, $this->username, $this->password, $options);

                // Log de conexión exitosa en modo debug
                if (isset($_ENV['DEBUG']) && $_ENV['DEBUG'] === 'true') {
                    error_log(sprintf(
                        '[%s] Conexión a base de datos establecida: %s@%s:%d/%s',
                        date('Y-m-d H:i:s'),
                        $this->username,
                        $this->host,
                        $this->port,
                        $this->database
                    ));
                }
            } catch (PDOException $e) {
                // Log del error
                error_log(sprintf(
                    '[%s] Error de conexión a base de datos: %s',
                    date('Y-m-d H:i:s'),
                    $e->getMessage()
                ));

                throw new Exception(
                    'Error al conectar con la base de datos. Por favor, contacte al administrador.',
                    500
                );
            }
        }

        return $this->connection;
    }

    /**
     * Cierra la conexión a la base de datos
     *
     * @return void
     */
    public function closeConnection(): void
    {
        $this->connection = null;

        if (isset($_ENV['DEBUG']) && $_ENV['DEBUG'] === 'true') {
            error_log(sprintf(
                '[%s] Conexión a base de datos cerrada',
                date('Y-m-d H:i:s')
            ));
        }
    }

    /**
     * Inicia una transacción
     *
     * @return bool True si la transacción se inició correctamente
     * @throws Exception Si hay un error al iniciar la transacción
     */
    public function beginTransaction(): bool
    {
        try {
            return $this->getConnection()->beginTransaction();
        } catch (PDOException $e) {
            error_log(sprintf(
                '[%s] Error al iniciar transacción: %s',
                date('Y-m-d H:i:s'),
                $e->getMessage()
            ));

            throw new Exception('Error al iniciar la transacción', 500);
        }
    }

    /**
     * Confirma una transacción
     *
     * @return bool True si la transacción se confirmó correctamente
     * @throws Exception Si hay un error al confirmar la transacción
     */
    public function commit(): bool
    {
        try {
            return $this->getConnection()->commit();
        } catch (PDOException $e) {
            error_log(sprintf(
                '[%s] Error al confirmar transacción: %s',
                date('Y-m-d H:i:s'),
                $e->getMessage()
            ));

            throw new Exception('Error al confirmar la transacción', 500);
        }
    }

    /**
     * Revierte una transacción
     *
     * @return bool True si la transacción se revirtió correctamente
     * @throws Exception Si hay un error al revertir la transacción
     */
    public function rollback(): bool
    {
        try {
            return $this->getConnection()->rollBack();
        } catch (PDOException $e) {
            error_log(sprintf(
                '[%s] Error al revertir transacción: %s',
                date('Y-m-d H:i:s'),
                $e->getMessage()
            ));

            throw new Exception('Error al revertir la transacción', 500);
        }
    }

    /**
     * Verifica si hay una transacción activa
     *
     * @return bool True si hay una transacción activa
     */
    public function inTransaction(): bool
    {
        return $this->getConnection()->inTransaction();
    }

    /**
     * Obtiene el ID del último registro insertado
     *
     * @return string ID del último registro insertado
     */
    public function lastInsertId(): string
    {
        return $this->getConnection()->lastInsertId();
    }

    /**
     * Previene la clonación del objeto (Singleton)
     */
    private function __clone()
    {
        // Prevenir clonación
    }

    /**
     * Previene la deserialización del objeto (Singleton)
     *
     * @throws Exception Siempre lanza una excepción
     */
    public function __wakeup()
    {
        throw new Exception('No se puede deserializar un Singleton');
    }

    /**
     * Cierra la conexión al destruir el objeto
     */
    public function __destruct()
    {
        $this->closeConnection();
    }
}
