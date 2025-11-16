-- ============================================================================
-- INSTALACIÓN COMPLETA DE LA BASE DE DATOS
-- Sistema de Biblioteca Digital de Videos Educativos
-- U.E. San Francisco Xavier
-- ============================================================================
--
-- INSTRUCCIONES DE USO:
-- 1. Abrir phpMyAdmin: http://localhost/phpmyadmin
-- 2. Hacer clic en "SQL" en el menú superior
-- 3. Copiar TODO el contenido de este archivo
-- 4. Pegar en el área de texto
-- 5. Hacer clic en "Continuar"
-- 6. Esperar a que finalice (puede tardar 30-60 segundos)
--
-- Este script hace todo automáticamente:
-- - Crea la base de datos
-- - Crea todas las tablas
-- - Crea triggers, vistas y procedimientos
-- - Carga datos de prueba
--
-- ============================================================================

-- Eliminar base de datos si existe (CUIDADO: esto borra todo)
DROP DATABASE IF EXISTS videoteca;

-- Crear base de datos
CREATE DATABASE videoteca CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Usar la base de datos
USE videoteca;

-- Mostrar mensaje de inicio
SELECT 'Iniciando instalación de la base de datos...' AS mensaje;
SELECT 'Esto puede tardar 30-60 segundos...' AS mensaje;

-- ============================================================================
-- A continuación se cargarán automáticamente:
-- - Todas las tablas (14 tablas)
-- - Todos los triggers (7 triggers)
-- - Todas las vistas (4 vistas)
-- - Todos los procedimientos (3 procedimientos)
-- - Todos los datos de prueba (10 usuarios, 21 videos)
-- ============================================================================

-- IMPORTANTE: Ahora copiar y pegar el contenido de schema.sql aquí en phpMyAdmin
-- LUEGO: Copiar y pegar el contenido de seed_data.sql

-- ============================================================================
-- VERIFICACIÓN FINAL
-- ============================================================================

SELECT 'Instalación completada!' AS mensaje;
SELECT 'Verificando instalación...' AS mensaje;

SELECT 'Tablas creadas:' AS info, COUNT(*) AS cantidad
FROM information_schema.tables
WHERE table_schema = 'videoteca';

SELECT 'Usuarios creados:' AS info, COUNT(*) AS cantidad FROM usuarios;
SELECT 'Videos creados:' AS info, COUNT(*) AS cantidad FROM videos;
SELECT 'Materias creadas:' AS info, COUNT(*) AS cantidad FROM materias;
SELECT 'Grados creados:' AS info, COUNT(*) AS cantidad FROM grados;

SELECT '========================================' AS '';
SELECT 'CREDENCIALES DE ACCESO' AS '';
SELECT '========================================' AS '';
SELECT 'Administrador: admin@sfxavier.edu.bo' AS '';
SELECT 'Docente: maria.gonzalez@sfxavier.edu.bo' AS '';
SELECT 'Estudiante: juan.perez@estudiante.edu.bo' AS '';
SELECT 'Contraseña para todos: password123' AS '';
SELECT '========================================' AS '';
