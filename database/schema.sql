-- ============================================================================
-- BASE DE DATOS: SISTEMA DE BIBLIOTECA DIGITAL DE VIDEOS EDUCATIVOS
-- Unidad Educativa San Francisco Xavier
-- Estudiante: Roger Omar Luna Yujra
-- Tutor: Lic. Vladimir Mamani
-- Instituto: ATSI Bolivia
-- ============================================================================

-- Crear base de datos
DROP DATABASE IF EXISTS videoteca;
CREATE DATABASE videoteca CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE videoteca;

-- ============================================================================
-- TABLAS PRINCIPALES
-- ============================================================================

-- Tabla: roles
-- Descripción: Define los roles del sistema (Administrador, Docente, Estudiante)
CREATE TABLE roles (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    permisos JSON NOT NULL COMMENT 'Permisos del rol en formato JSON',
    descripcion TEXT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_nombre (nombre)
) ENGINE=InnoDB COMMENT='Roles del sistema con sus permisos';

-- Tabla: grados
-- Descripción: Grados escolares de la unidad educativa
CREATE TABLE grados (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    nivel VARCHAR(50) NOT NULL COMMENT 'Primaria, Secundaria',
    descripcion TEXT,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_nivel (nivel),
    INDEX idx_estado (estado)
) ENGINE=InnoDB COMMENT='Grados escolares del sistema';

-- Tabla: materias
-- Descripción: Materias educativas del sistema
CREATE TABLE materias (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    codigo VARCHAR(20) UNIQUE,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_nombre (nombre),
    INDEX idx_codigo (codigo),
    INDEX idx_estado (estado)
) ENGINE=InnoDB COMMENT='Materias educativas del sistema';

-- Tabla: usuarios
-- Descripción: Usuarios del sistema (Administradores, Docentes, Estudiantes)
CREATE TABLE usuarios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    ci VARCHAR(20) NOT NULL UNIQUE COMMENT 'Cédula de Identidad',
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL COMMENT 'Contraseña hasheada con bcrypt',
    rol_id INT UNSIGNED NOT NULL,
    grado_id INT UNSIGNED NULL COMMENT 'Solo para estudiantes',
    materia_id INT UNSIGNED NULL COMMENT 'Solo para docentes (materia principal)',
    avatar_path VARCHAR(255) DEFAULT NULL,
    telefono VARCHAR(20),
    direccion TEXT,
    estado ENUM('activo', 'inactivo', 'suspendido') DEFAULT 'activo',
    ultimo_acceso TIMESTAMP NULL,
    intentos_login INT DEFAULT 0 COMMENT 'Contador de intentos fallidos de login',
    bloqueado_hasta TIMESTAMP NULL COMMENT 'Fecha hasta la que está bloqueado el usuario',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (rol_id) REFERENCES roles(id) ON DELETE RESTRICT,
    FOREIGN KEY (grado_id) REFERENCES grados(id) ON DELETE SET NULL,
    FOREIGN KEY (materia_id) REFERENCES materias(id) ON DELETE SET NULL,
    INDEX idx_ci (ci),
    INDEX idx_email (email),
    INDEX idx_rol (rol_id),
    INDEX idx_grado (grado_id),
    INDEX idx_estado (estado),
    INDEX idx_nombre_apellido (nombre, apellido)
) ENGINE=InnoDB COMMENT='Usuarios del sistema educativo';

-- Tabla: categorias
-- Descripción: Categorías para clasificar videos
CREATE TABLE categorias (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    descripcion TEXT,
    color VARCHAR(7) DEFAULT '#3498db' COMMENT 'Color en formato hexadecimal',
    icono VARCHAR(50) DEFAULT NULL,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_nombre (nombre),
    INDEX idx_estado (estado)
) ENGINE=InnoDB COMMENT='Categorías para clasificar videos educativos';

-- Tabla: videos
-- Descripción: Videos educativos del sistema
CREATE TABLE videos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    descripcion TEXT,
    archivo_path VARCHAR(500) NOT NULL COMMENT 'Ruta del archivo de video',
    thumbnail_path VARCHAR(500) DEFAULT NULL COMMENT 'Ruta de la miniatura',
    duracion INT UNSIGNED NOT NULL COMMENT 'Duración en segundos',
    tamaño BIGINT UNSIGNED NOT NULL COMMENT 'Tamaño del archivo en bytes',
    formato VARCHAR(20) NOT NULL COMMENT 'MP4, AVI, MKV, WEBM',
    resolucion VARCHAR(20) COMMENT '720p, 1080p, etc',
    codec VARCHAR(50),
    materia_id INT UNSIGNED NOT NULL,
    grado_id INT UNSIGNED NOT NULL,
    docente_id INT UNSIGNED NOT NULL COMMENT 'Docente que subió el video',
    visualizaciones INT UNSIGNED DEFAULT 0,
    calificacion_promedio DECIMAL(3,2) DEFAULT 0.00,
    total_calificaciones INT UNSIGNED DEFAULT 0,
    estado ENUM('activo', 'inactivo', 'procesando', 'error') DEFAULT 'activo',
    fecha_subida TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (materia_id) REFERENCES materias(id) ON DELETE CASCADE,
    FOREIGN KEY (grado_id) REFERENCES grados(id) ON DELETE CASCADE,
    FOREIGN KEY (docente_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_titulo (titulo),
    INDEX idx_materia (materia_id),
    INDEX idx_grado (grado_id),
    INDEX idx_docente (docente_id),
    INDEX idx_estado (estado),
    INDEX idx_fecha_subida (fecha_subida),
    INDEX idx_visualizaciones (visualizaciones),
    INDEX idx_calificacion (calificacion_promedio),
    FULLTEXT INDEX idx_fulltext_busqueda (titulo, descripcion)
) ENGINE=InnoDB COMMENT='Videos educativos del sistema';

-- Tabla: video_categorias
-- Descripción: Relación muchos a muchos entre videos y categorías
CREATE TABLE video_categorias (
    video_id INT UNSIGNED NOT NULL,
    categoria_id INT UNSIGNED NOT NULL,
    fecha_asignacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (video_id, categoria_id),
    FOREIGN KEY (video_id) REFERENCES videos(id) ON DELETE CASCADE,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE CASCADE,
    INDEX idx_video (video_id),
    INDEX idx_categoria (categoria_id)
) ENGINE=InnoDB COMMENT='Relación entre videos y categorías';

-- Tabla: reproducciones
-- Descripción: Registro de reproducciones de videos por usuarios
CREATE TABLE reproducciones (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    video_id INT UNSIGNED NOT NULL,
    usuario_id INT UNSIGNED NOT NULL,
    tiempo_reproducido INT UNSIGNED DEFAULT 0 COMMENT 'Tiempo reproducido en segundos',
    progreso DECIMAL(5,2) DEFAULT 0.00 COMMENT 'Porcentaje de progreso (0-100)',
    completado BOOLEAN DEFAULT FALSE,
    fecha_inicio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_ultima_reproduccion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (video_id) REFERENCES videos(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_video (video_id),
    INDEX idx_usuario (usuario_id),
    INDEX idx_completado (completado),
    INDEX idx_fecha_ultima (fecha_ultima_reproduccion),
    UNIQUE KEY unique_usuario_video (usuario_id, video_id)
) ENGINE=InnoDB COMMENT='Registro de reproducciones de videos';

-- Tabla: comentarios
-- Descripción: Comentarios de usuarios en videos
CREATE TABLE comentarios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    video_id INT UNSIGNED NOT NULL,
    usuario_id INT UNSIGNED NOT NULL,
    contenido TEXT NOT NULL,
    estado ENUM('activo', 'oculto', 'eliminado') DEFAULT 'activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (video_id) REFERENCES videos(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_video (video_id),
    INDEX idx_usuario (usuario_id),
    INDEX idx_estado (estado),
    INDEX idx_fecha (fecha_creacion)
) ENGINE=InnoDB COMMENT='Comentarios de usuarios en videos';

-- Tabla: favoritos
-- Descripción: Videos marcados como favoritos por usuarios
CREATE TABLE favoritos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    video_id INT UNSIGNED NOT NULL,
    usuario_id INT UNSIGNED NOT NULL,
    fecha_agregado TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (video_id) REFERENCES videos(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    UNIQUE KEY unique_favorito (usuario_id, video_id),
    INDEX idx_video (video_id),
    INDEX idx_usuario (usuario_id),
    INDEX idx_fecha (fecha_agregado)
) ENGINE=InnoDB COMMENT='Videos favoritos de usuarios';

-- Tabla: calificaciones
-- Descripción: Calificaciones de videos por usuarios
CREATE TABLE calificaciones (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    video_id INT UNSIGNED NOT NULL,
    usuario_id INT UNSIGNED NOT NULL,
    puntuacion TINYINT UNSIGNED NOT NULL CHECK (puntuacion >= 1 AND puntuacion <= 5),
    comentario_calificacion TEXT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (video_id) REFERENCES videos(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    UNIQUE KEY unique_calificacion (usuario_id, video_id),
    INDEX idx_video (video_id),
    INDEX idx_usuario (usuario_id),
    INDEX idx_puntuacion (puntuacion)
) ENGINE=InnoDB COMMENT='Calificaciones de videos por usuarios';

-- Tabla: estadisticas
-- Descripción: Estadísticas generales del sistema
CREATE TABLE estadisticas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tipo VARCHAR(100) NOT NULL COMMENT 'Tipo de estadística (diaria, semanal, mensual)',
    datos_json JSON NOT NULL COMMENT 'Datos de la estadística en formato JSON',
    periodo_inicio DATE NOT NULL,
    periodo_fin DATE NOT NULL,
    fecha_generacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_tipo (tipo),
    INDEX idx_periodo (periodo_inicio, periodo_fin)
) ENGINE=InnoDB COMMENT='Estadísticas del sistema';

-- Tabla: logs_sistema
-- Descripción: Registro de actividades del sistema
CREATE TABLE logs_sistema (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT UNSIGNED NULL,
    accion VARCHAR(100) NOT NULL COMMENT 'Tipo de acción realizada',
    descripcion TEXT,
    entidad_tipo VARCHAR(50) COMMENT 'Tipo de entidad afectada (video, usuario, etc)',
    entidad_id INT UNSIGNED COMMENT 'ID de la entidad afectada',
    ip VARCHAR(45) NOT NULL COMMENT 'Dirección IP del usuario',
    user_agent TEXT COMMENT 'Navegador y sistema operativo',
    datos_adicionales JSON COMMENT 'Datos adicionales en formato JSON',
    nivel ENUM('info', 'warning', 'error', 'critical') DEFAULT 'info',
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_usuario (usuario_id),
    INDEX idx_accion (accion),
    INDEX idx_entidad (entidad_tipo, entidad_id),
    INDEX idx_nivel (nivel),
    INDEX idx_fecha (fecha)
) ENGINE=InnoDB COMMENT='Registro de actividades del sistema';

-- Tabla: tokens_refresh
-- Descripción: Tokens de refresco JWT para autenticación
CREATE TABLE tokens_refresh (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT UNSIGNED NOT NULL,
    token VARCHAR(500) NOT NULL UNIQUE,
    ip VARCHAR(45) NOT NULL,
    user_agent TEXT,
    expira_en TIMESTAMP NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_usuario (usuario_id),
    INDEX idx_token (token),
    INDEX idx_expira (expira_en)
) ENGINE=InnoDB COMMENT='Tokens de refresco para autenticación JWT';

-- Tabla: notificaciones
-- Descripción: Notificaciones para usuarios
CREATE TABLE notificaciones (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT UNSIGNED NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    mensaje TEXT NOT NULL,
    tipo VARCHAR(50) NOT NULL COMMENT 'nuevo_video, comentario, sistema, etc',
    entidad_tipo VARCHAR(50) COMMENT 'Tipo de entidad relacionada',
    entidad_id INT UNSIGNED COMMENT 'ID de la entidad relacionada',
    leida BOOLEAN DEFAULT FALSE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_usuario (usuario_id),
    INDEX idx_leida (leida),
    INDEX idx_fecha (fecha_creacion)
) ENGINE=InnoDB COMMENT='Notificaciones para usuarios';

-- ============================================================================
-- TRIGGERS
-- ============================================================================

-- Trigger: Actualizar contador de visualizaciones
DELIMITER //
CREATE TRIGGER tr_actualizar_visualizaciones
AFTER INSERT ON reproducciones
FOR EACH ROW
BEGIN
    UPDATE videos
    SET visualizaciones = visualizaciones + 1
    WHERE id = NEW.video_id;
END;//
DELIMITER ;

-- Trigger: Actualizar calificación promedio de video
DELIMITER //
CREATE TRIGGER tr_actualizar_calificacion_promedio_insert
AFTER INSERT ON calificaciones
FOR EACH ROW
BEGIN
    UPDATE videos v
    SET
        calificacion_promedio = (
            SELECT AVG(puntuacion)
            FROM calificaciones
            WHERE video_id = NEW.video_id
        ),
        total_calificaciones = (
            SELECT COUNT(*)
            FROM calificaciones
            WHERE video_id = NEW.video_id
        )
    WHERE v.id = NEW.video_id;
END;//
DELIMITER ;

DELIMITER //
CREATE TRIGGER tr_actualizar_calificacion_promedio_update
AFTER UPDATE ON calificaciones
FOR EACH ROW
BEGIN
    UPDATE videos v
    SET
        calificacion_promedio = (
            SELECT AVG(puntuacion)
            FROM calificaciones
            WHERE video_id = NEW.video_id
        ),
        total_calificaciones = (
            SELECT COUNT(*)
            FROM calificaciones
            WHERE video_id = NEW.video_id
        )
    WHERE v.id = NEW.video_id;
END;//
DELIMITER ;

DELIMITER //
CREATE TRIGGER tr_actualizar_calificacion_promedio_delete
AFTER DELETE ON calificaciones
FOR EACH ROW
BEGIN
    UPDATE videos v
    SET
        calificacion_promedio = COALESCE((
            SELECT AVG(puntuacion)
            FROM calificaciones
            WHERE video_id = OLD.video_id
        ), 0),
        total_calificaciones = (
            SELECT COUNT(*)
            FROM calificaciones
            WHERE video_id = OLD.video_id
        )
    WHERE v.id = OLD.video_id;
END;//
DELIMITER ;

-- Trigger: Log automático al crear video
DELIMITER //
CREATE TRIGGER tr_log_crear_video
AFTER INSERT ON videos
FOR EACH ROW
BEGIN
    INSERT INTO logs_sistema (
        usuario_id,
        accion,
        descripcion,
        entidad_tipo,
        entidad_id,
        ip,
        nivel
    ) VALUES (
        NEW.docente_id,
        'VIDEO_CREADO',
        CONCAT('Video creado: ', NEW.titulo),
        'video',
        NEW.id,
        '127.0.0.1',
        'info'
    );
END;//
DELIMITER ;

-- Trigger: Log automático al eliminar video
DELIMITER //
CREATE TRIGGER tr_log_eliminar_video
BEFORE DELETE ON videos
FOR EACH ROW
BEGIN
    INSERT INTO logs_sistema (
        usuario_id,
        accion,
        descripcion,
        entidad_tipo,
        entidad_id,
        ip,
        nivel
    ) VALUES (
        OLD.docente_id,
        'VIDEO_ELIMINADO',
        CONCAT('Video eliminado: ', OLD.titulo),
        'video',
        OLD.id,
        '127.0.0.1',
        'warning'
    );
END;//
DELIMITER ;

-- ============================================================================
-- VISTAS
-- ============================================================================

-- Vista: Videos con información completa
CREATE OR REPLACE VIEW v_videos_completos AS
SELECT
    v.id,
    v.titulo,
    v.descripcion,
    v.archivo_path,
    v.thumbnail_path,
    v.duracion,
    v.tamaño,
    v.formato,
    v.resolucion,
    v.visualizaciones,
    v.calificacion_promedio,
    v.total_calificaciones,
    v.estado,
    v.fecha_subida,
    m.id AS materia_id,
    m.nombre AS materia_nombre,
    g.id AS grado_id,
    g.nombre AS grado_nombre,
    g.nivel AS grado_nivel,
    u.id AS docente_id,
    CONCAT(u.nombre, ' ', u.apellido) AS docente_nombre,
    (SELECT COUNT(*) FROM comentarios WHERE video_id = v.id AND estado = 'activo') AS total_comentarios,
    (SELECT COUNT(*) FROM favoritos WHERE video_id = v.id) AS total_favoritos,
    GROUP_CONCAT(c.nombre SEPARATOR ', ') AS categorias
FROM videos v
INNER JOIN materias m ON v.materia_id = m.id
INNER JOIN grados g ON v.grado_id = g.id
INNER JOIN usuarios u ON v.docente_id = u.id
LEFT JOIN video_categorias vc ON v.id = vc.video_id
LEFT JOIN categorias c ON vc.categoria_id = c.id
GROUP BY v.id;

-- Vista: Estadísticas de usuarios
CREATE OR REPLACE VIEW v_estadisticas_usuarios AS
SELECT
    u.id,
    CONCAT(u.nombre, ' ', u.apellido) AS nombre_completo,
    u.email,
    r.nombre AS rol,
    COUNT(DISTINCT rep.video_id) AS videos_vistos,
    SUM(rep.tiempo_reproducido) AS tiempo_total_reproducido,
    COUNT(DISTINCT f.video_id) AS videos_favoritos,
    COUNT(DISTINCT c.id) AS comentarios_realizados,
    COUNT(DISTINCT cal.id) AS calificaciones_dadas,
    u.ultimo_acceso,
    u.fecha_creacion
FROM usuarios u
INNER JOIN roles r ON u.rol_id = r.id
LEFT JOIN reproducciones rep ON u.id = rep.usuario_id
LEFT JOIN favoritos f ON u.id = f.usuario_id
LEFT JOIN comentarios c ON u.id = c.usuario_id AND c.estado = 'activo'
LEFT JOIN calificaciones cal ON u.id = cal.usuario_id
GROUP BY u.id;

-- Vista: Videos más populares
CREATE OR REPLACE VIEW v_videos_populares AS
SELECT
    v.id,
    v.titulo,
    v.visualizaciones,
    v.calificacion_promedio,
    v.total_calificaciones,
    m.nombre AS materia,
    g.nombre AS grado,
    CONCAT(u.nombre, ' ', u.apellido) AS docente,
    COUNT(DISTINCT f.id) AS total_favoritos,
    COUNT(DISTINCT c.id) AS total_comentarios
FROM videos v
INNER JOIN materias m ON v.materia_id = m.id
INNER JOIN grados g ON v.grado_id = g.id
INNER JOIN usuarios u ON v.docente_id = u.id
LEFT JOIN favoritos f ON v.id = f.video_id
LEFT JOIN comentarios c ON v.id = c.video_id AND c.estado = 'activo'
WHERE v.estado = 'activo'
GROUP BY v.id
ORDER BY v.visualizaciones DESC, v.calificacion_promedio DESC;

-- Vista: Actividad reciente del sistema
CREATE OR REPLACE VIEW v_actividad_reciente AS
SELECT
    l.id,
    CONCAT(u.nombre, ' ', u.apellido) AS usuario,
    l.accion,
    l.descripcion,
    l.entidad_tipo,
    l.nivel,
    l.fecha
FROM logs_sistema l
LEFT JOIN usuarios u ON l.usuario_id = u.id
ORDER BY l.fecha DESC
LIMIT 100;

-- ============================================================================
-- PROCEDIMIENTOS ALMACENADOS
-- ============================================================================

-- Procedimiento: Obtener estadísticas generales del sistema
DELIMITER //
CREATE PROCEDURE sp_estadisticas_generales()
BEGIN
    SELECT
        (SELECT COUNT(*) FROM usuarios WHERE estado = 'activo') AS total_usuarios_activos,
        (SELECT COUNT(*) FROM usuarios WHERE rol_id = (SELECT id FROM roles WHERE nombre = 'Estudiante')) AS total_estudiantes,
        (SELECT COUNT(*) FROM usuarios WHERE rol_id = (SELECT id FROM roles WHERE nombre = 'Docente')) AS total_docentes,
        (SELECT COUNT(*) FROM videos WHERE estado = 'activo') AS total_videos,
        (SELECT SUM(tamaño) FROM videos WHERE estado = 'activo') AS espacio_total_videos,
        (SELECT SUM(visualizaciones) FROM videos) AS total_visualizaciones,
        (SELECT AVG(calificacion_promedio) FROM videos WHERE total_calificaciones > 0) AS calificacion_promedio_general,
        (SELECT COUNT(*) FROM comentarios WHERE estado = 'activo') AS total_comentarios,
        (SELECT COUNT(*) FROM reproducciones WHERE completado = TRUE) AS total_videos_completados;
END;//
DELIMITER ;

-- Procedimiento: Obtener videos recomendados para un usuario
DELIMITER //
CREATE PROCEDURE sp_videos_recomendados(IN p_usuario_id INT)
BEGIN
    DECLARE v_grado_id INT;

    -- Obtener el grado del usuario
    SELECT grado_id INTO v_grado_id FROM usuarios WHERE id = p_usuario_id;

    -- Obtener videos recomendados basados en el grado y popularidad
    SELECT DISTINCT
        v.id,
        v.titulo,
        v.descripcion,
        v.thumbnail_path,
        v.duracion,
        v.visualizaciones,
        v.calificacion_promedio,
        m.nombre AS materia,
        CONCAT(u.nombre, ' ', u.apellido) AS docente
    FROM videos v
    INNER JOIN materias m ON v.materia_id = m.id
    INNER JOIN usuarios u ON v.docente_id = u.id
    WHERE v.grado_id = v_grado_id
        AND v.estado = 'activo'
        AND v.id NOT IN (
            SELECT video_id FROM reproducciones
            WHERE usuario_id = p_usuario_id AND completado = TRUE
        )
    ORDER BY v.calificacion_promedio DESC, v.visualizaciones DESC
    LIMIT 10;
END;//
DELIMITER ;

-- Procedimiento: Limpiar tokens expirados
DELIMITER //
CREATE PROCEDURE sp_limpiar_tokens_expirados()
BEGIN
    DELETE FROM tokens_refresh WHERE expira_en < NOW();

    SELECT ROW_COUNT() AS tokens_eliminados;
END;//
DELIMITER ;

-- ============================================================================
-- EVENTOS PROGRAMADOS
-- ============================================================================

-- Habilitar el scheduler de eventos
SET GLOBAL event_scheduler = ON;

-- Evento: Limpiar tokens expirados cada día
CREATE EVENT IF NOT EXISTS ev_limpiar_tokens
ON SCHEDULE EVERY 1 DAY
STARTS CURRENT_TIMESTAMP
DO
    CALL sp_limpiar_tokens_expirados();

-- Evento: Generar estadísticas diarias
DELIMITER //
CREATE EVENT IF NOT EXISTS ev_generar_estadisticas_diarias
ON SCHEDULE EVERY 1 DAY
STARTS CURRENT_TIMESTAMP + INTERVAL 1 HOUR
DO
BEGIN
    INSERT INTO estadisticas (tipo, datos_json, periodo_inicio, periodo_fin)
    SELECT
        'estadistica_diaria',
        JSON_OBJECT(
            'total_reproducciones', COUNT(DISTINCT r.id),
            'total_usuarios_activos', COUNT(DISTINCT r.usuario_id),
            'total_videos_vistos', COUNT(DISTINCT r.video_id),
            'tiempo_total_reproducido', SUM(r.tiempo_reproducido)
        ),
        CURDATE() - INTERVAL 1 DAY,
        CURDATE()
    FROM reproducciones r
    WHERE DATE(r.fecha_inicio) = CURDATE() - INTERVAL 1 DAY;
END;//
DELIMITER ;

-- ============================================================================
-- ÍNDICES ADICIONALES PARA OPTIMIZACIÓN
-- ============================================================================

-- Índice compuesto para búsquedas de videos por materia y grado
CREATE INDEX idx_videos_materia_grado ON videos(materia_id, grado_id, estado);

-- Índice para búsquedas de reproducciones por usuario y fecha
CREATE INDEX idx_reproducciones_usuario_fecha ON reproducciones(usuario_id, fecha_ultima_reproduccion);

-- Índice para búsquedas de favoritos por usuario
CREATE INDEX idx_favoritos_usuario_fecha ON favoritos(usuario_id, fecha_agregado);

-- ============================================================================
-- CONFIGURACIONES FINALES
-- ============================================================================

-- Mensaje de confirmación
SELECT 'Base de datos creada exitosamente' AS mensaje;
SELECT 'Total de tablas creadas: ' AS info, COUNT(*) AS cantidad
FROM information_schema.tables
WHERE table_schema = 'videoteca';

SELECT 'Total de triggers creados: ' AS info, COUNT(*) AS cantidad
FROM information_schema.triggers
WHERE trigger_schema = 'videoteca';

SELECT 'Total de vistas creadas: ' AS info, COUNT(*) AS cantidad
FROM information_schema.views
WHERE table_schema = 'videoteca';

SELECT 'Total de procedimientos creados: ' AS info, COUNT(*) AS cantidad
FROM information_schema.routines
WHERE routine_schema = 'videoteca' AND routine_type = 'PROCEDURE';
