-- ============================================================================
-- SCRIPT DE ACTUALIZACION DE BASE DE DATOS
-- Sistema de Biblioteca Digital de Videos Educativos V2.0
-- U.E. San Francisco Xavier
-- ============================================================================
--
-- Este script actualiza una base de datos existente sin eliminar datos
-- Es SEGURO ejecutarlo en una base de datos con información
--
-- INSTRUCCIONES:
-- 1. Hacer backup de tu base de datos antes (por seguridad)
-- 2. Ejecutar este script en phpMyAdmin o desde línea de comandos
--
-- ============================================================================

USE videoteca;

-- ============================================================================
-- VERIFICACION Y ACTUALIZACION DE TABLAS
-- ============================================================================

-- Mostrar mensaje
SELECT 'Iniciando actualización de base de datos...' AS mensaje;

-- Verificar y actualizar tabla de roles
-- Asegurar que solo existan los roles necesarios para V2.0
-- (Administrador y Docente - los estudiantes ya no necesitan login)

-- Actualizar descripción de roles para V2.0
UPDATE roles
SET descripcion = 'Administrador del sistema con acceso completo'
WHERE nombre = 'Administrador';

UPDATE roles
SET descripcion = 'Docente con capacidad de subir y gestionar videos'
WHERE nombre = 'Docente';

UPDATE roles
SET descripcion = 'OBSOLETO en V2.0 - Los estudiantes ahora acceden públicamente sin login'
WHERE nombre = 'Estudiante';

-- ============================================================================
-- INDICES ADICIONALES PARA OPTIMIZACION
-- ============================================================================

-- Agregar índices si no existen (para mejorar rendimiento)

-- Índice para búsquedas de videos
CREATE INDEX IF NOT EXISTS idx_videos_titulo ON videos(titulo);
CREATE INDEX IF NOT EXISTS idx_videos_estado ON videos(estado);
CREATE INDEX IF NOT EXISTS idx_videos_fecha ON videos(fecha_creacion);

-- Índices para reproducciones (importante para estadísticas anónimas)
CREATE INDEX IF NOT EXISTS idx_reproducciones_video ON reproducciones(video_id);
CREATE INDEX IF NOT EXISTS idx_reproducciones_fecha ON reproducciones(fecha_reproduccion);

-- Índices para grados y materias (usado en filtros públicos)
CREATE INDEX IF NOT EXISTS idx_grados_estado ON grados(estado);
CREATE INDEX IF NOT EXISTS idx_materias_estado ON materias(estado);

-- ============================================================================
-- ACTUALIZACIONES PARA V2.0
-- ============================================================================

-- Marcar a los estudiantes como "migrados" a acceso público
-- No los eliminamos para mantener el historial, pero su rol ya no se usa
UPDATE usuarios
SET estado = 'inactivo'
WHERE rol_id = (SELECT id FROM roles WHERE nombre = 'Estudiante');

-- Actualizar comentario en tabla de reproducciones
ALTER TABLE reproducciones
MODIFY COLUMN usuario_id INT UNSIGNED NULL
COMMENT 'NULL para reproducciones anónimas (estudiantes en V2.0)';

-- ============================================================================
-- VERIFICACION DE DATOS ESENCIALES
-- ============================================================================

-- Asegurar que existan grados (primaria y secundaria)
INSERT IGNORE INTO grados (nombre, nivel, descripcion, estado) VALUES
('1° de Primaria', 'Primaria', 'Primer año de educación primaria', 'activo'),
('2° de Primaria', 'Primaria', 'Segundo año de educación primaria', 'activo'),
('3° de Primaria', 'Primaria', 'Tercer año de educación primaria', 'activo'),
('4° de Primaria', 'Primaria', 'Cuarto año de educación primaria', 'activo'),
('5° de Primaria', 'Primaria', 'Quinto año de educación primaria', 'activo'),
('6° de Primaria', 'Primaria', 'Sexto año de educación primaria', 'activo'),
('1° de Secundaria', 'Secundaria', 'Primer año de educación secundaria', 'activo'),
('2° de Secundaria', 'Secundaria', 'Segundo año de educación secundaria', 'activo'),
('3° de Secundaria', 'Secundaria', 'Tercer año de educación secundaria', 'activo'),
('4° de Secundaria', 'Secundaria', 'Cuarto año de educación secundaria', 'activo'),
('5° de Secundaria', 'Secundaria', 'Quinto año de educación secundaria', 'activo'),
('6° de Secundaria', 'Secundaria', 'Sexto año de educación secundaria', 'activo');

-- Asegurar que existan materias básicas
INSERT IGNORE INTO materias (nombre, codigo, descripcion, estado) VALUES
('Matemáticas', 'MAT', 'Matemáticas y cálculo', 'activo'),
('Lenguaje', 'LENG', 'Lenguaje y comunicación', 'activo'),
('Ciencias Naturales', 'CNAT', 'Biología, Química y Física', 'activo'),
('Ciencias Sociales', 'CSOC', 'Historia, Geografía y Cívica', 'activo'),
('Inglés', 'ING', 'Lengua extranjera Inglés', 'activo'),
('Educación Física', 'EDFIS', 'Deportes y actividad física', 'activo'),
('Artes Plásticas', 'ARTES', 'Artes visuales y plásticas', 'activo'),
('Música', 'MUS', 'Educación musical', 'activo'),
('Computación', 'COMP', 'Informática y tecnología', 'activo'),
('Valores', 'VAL', 'Formación en valores', 'activo');

-- ============================================================================
-- LIMPIEZA Y OPTIMIZACION
-- ============================================================================

-- Optimizar tablas
OPTIMIZE TABLE videos;
OPTIMIZE TABLE usuarios;
OPTIMIZE TABLE reproducciones;
OPTIMIZE TABLE grados;
OPTIMIZE TABLE materias;

-- ============================================================================
-- VERIFICACION FINAL
-- ============================================================================

SELECT 'Actualización completada!' AS mensaje;
SELECT '=====================================';

-- Mostrar estadísticas
SELECT
    'Usuarios activos (Docentes y Admin)' AS tipo,
    COUNT(*) AS cantidad
FROM usuarios
WHERE estado = 'activo' AND rol_id IN (
    SELECT id FROM roles WHERE nombre IN ('Administrador', 'Docente')
);

SELECT
    'Videos disponibles' AS tipo,
    COUNT(*) AS cantidad
FROM videos
WHERE estado IN ('activo', 'publicado');

SELECT
    'Grados escolares' AS tipo,
    COUNT(*) AS cantidad
FROM grados
WHERE estado = 'activo';

SELECT
    'Materias disponibles' AS tipo,
    COUNT(*) AS cantidad
FROM materias
WHERE estado = 'activo';

SELECT '=====================================' AS mensaje;
SELECT 'Base de datos actualizada a V2.0' AS mensaje;
SELECT 'Sistema listo para acceso público de estudiantes' AS mensaje;
SELECT '=====================================' AS mensaje;
