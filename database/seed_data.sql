-- ============================================================================
-- DATOS DE PRUEBA: SISTEMA DE BIBLIOTECA DIGITAL DE VIDEOS EDUCATIVOS
-- Unidad Educativa San Francisco Xavier
-- ============================================================================

USE videoteca_educativa;

-- ============================================================================
-- INSERTAR ROLES
-- ============================================================================

INSERT INTO roles (nombre, permisos, descripcion) VALUES
(
    'Administrador',
    JSON_OBJECT(
        'usuarios', JSON_ARRAY('crear', 'leer', 'actualizar', 'eliminar'),
        'videos', JSON_ARRAY('crear', 'leer', 'actualizar', 'eliminar'),
        'categorias', JSON_ARRAY('crear', 'leer', 'actualizar', 'eliminar'),
        'materias', JSON_ARRAY('crear', 'leer', 'actualizar', 'eliminar'),
        'grados', JSON_ARRAY('crear', 'leer', 'actualizar', 'eliminar'),
        'estadisticas', JSON_ARRAY('leer'),
        'logs', JSON_ARRAY('leer')
    ),
    'Acceso completo al sistema'
),
(
    'Docente',
    JSON_OBJECT(
        'videos', JSON_ARRAY('crear', 'leer', 'actualizar_propio', 'eliminar_propio'),
        'comentarios', JSON_ARRAY('leer', 'moderar'),
        'estadisticas', JSON_ARRAY('leer_propio'),
        'categorias', JSON_ARRAY('leer'),
        'materias', JSON_ARRAY('leer'),
        'grados', JSON_ARRAY('leer')
    ),
    'Gestión de contenido educativo propio'
),
(
    'Estudiante',
    JSON_OBJECT(
        'videos', JSON_ARRAY('leer', 'reproducir'),
        'comentarios', JSON_ARRAY('crear', 'leer', 'actualizar_propio', 'eliminar_propio'),
        'calificaciones', JSON_ARRAY('crear', 'actualizar_propio'),
        'favoritos', JSON_ARRAY('crear', 'leer', 'eliminar'),
        'perfil', JSON_ARRAY('leer', 'actualizar_propio')
    ),
    'Acceso a contenido educativo según su grado'
);

-- ============================================================================
-- INSERTAR GRADOS
-- ============================================================================

INSERT INTO grados (nombre, nivel, descripcion) VALUES
('1ro de Primaria', 'Primaria', 'Primer año de educación primaria'),
('2do de Primaria', 'Primaria', 'Segundo año de educación primaria'),
('3ro de Primaria', 'Primaria', 'Tercer año de educación primaria'),
('4to de Primaria', 'Primaria', 'Cuarto año de educación primaria'),
('5to de Primaria', 'Primaria', 'Quinto año de educación primaria'),
('6to de Primaria', 'Primaria', 'Sexto año de educación primaria'),
('1ro de Secundaria', 'Secundaria', 'Primer año de educación secundaria'),
('2do de Secundaria', 'Secundaria', 'Segundo año de educación secundaria'),
('3ro de Secundaria', 'Secundaria', 'Tercer año de educación secundaria'),
('4to de Secundaria', 'Secundaria', 'Cuarto año de educación secundaria'),
('5to de Secundaria', 'Secundaria', 'Quinto año de educación secundaria'),
('6to de Secundaria', 'Secundaria', 'Sexto año de educación secundaria');

-- ============================================================================
-- INSERTAR MATERIAS
-- ============================================================================

INSERT INTO materias (nombre, descripcion, codigo) VALUES
('Matemáticas', 'Ciencia que estudia las propiedades de los números y las relaciones', 'MAT'),
('Lenguaje', 'Estudio de la lengua española, gramática y literatura', 'LEN'),
('Ciencias Naturales', 'Estudio de la naturaleza, biología, química y física', 'CN'),
('Ciencias Sociales', 'Historia, geografía y educación cívica', 'CS'),
('Inglés', 'Idioma extranjero inglés', 'ING'),
('Educación Física', 'Desarrollo físico y deportivo', 'EF'),
('Artes Plásticas', 'Expresión artística y creatividad visual', 'ART'),
('Música', 'Educación musical y apreciación artística', 'MUS'),
('Computación', 'Tecnologías de información y comunicación', 'COM'),
('Valores y Religión', 'Formación ética y religiosa', 'VAL');

-- ============================================================================
-- INSERTAR CATEGORÍAS
-- ============================================================================

INSERT INTO categorias (nombre, descripcion, color, icono) VALUES
('Tutorial', 'Videos instructivos paso a paso', '#3498db', 'book-open'),
('Experimento', 'Demostraciones prácticas y experimentos', '#e74c3c', 'flask'),
('Explicación', 'Conceptos teóricos explicados', '#2ecc71', 'lightbulb'),
('Ejercicios', 'Resolución de ejercicios y problemas', '#f39c12', 'edit'),
('Historia', 'Contenido histórico y cultural', '#9b59b6', 'clock'),
('Documental', 'Videos documentales educativos', '#1abc9c', 'film'),
('Gramática', 'Reglas y estructura del lenguaje', '#34495e', 'type'),
('Laboratorio', 'Prácticas de laboratorio', '#e67e22', 'test-tube'),
('Geografía', 'Contenido geográfico y mapas', '#16a085', 'globe'),
('Cultura', 'Aspectos culturales y tradiciones', '#8e44ad', 'users');

-- ============================================================================
-- INSERTAR USUARIOS
-- ============================================================================

-- Contraseña para todos los usuarios de prueba: "password123"
-- Hash bcrypt con costo 12: $2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5lXg3qRjLq8q6

-- Administrador
INSERT INTO usuarios (nombre, apellido, ci, email, password_hash, rol_id, estado) VALUES
('Admin', 'Sistema', '1234567', 'admin@sfxavier.edu.bo', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5lXg3qRjLq8q6', 1, 'activo');

-- Docentes (con materias asignadas)
INSERT INTO usuarios (nombre, apellido, ci, email, password_hash, rol_id, materia_id, telefono, estado) VALUES
('María', 'González López', '2345678', 'maria.gonzalez@sfxavier.edu.bo', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5lXg3qRjLq8q6', 2, 1, '70123456', 'activo'),
('Carlos', 'Mamani Quispe', '3456789', 'carlos.mamani@sfxavier.edu.bo', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5lXg3qRjLq8q6', 2, 2, '70234567', 'activo'),
('Ana', 'Flores Condori', '4567890', 'ana.flores@sfxavier.edu.bo', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5lXg3qRjLq8q6', 2, 3, '70345678', 'activo'),
('Roberto', 'Choque Vargas', '5678901', 'roberto.choque@sfxavier.edu.bo', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5lXg3qRjLq8q6', 2, 4, '70456789', 'activo'),
('Patricia', 'Luna Cruz', '6789012', 'patricia.luna@sfxavier.edu.bo', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5lXg3qRjLq8q6', 2, 5, '70567890', 'activo');

-- Estudiantes (con grados asignados)
INSERT INTO usuarios (nombre, apellido, ci, email, password_hash, rol_id, grado_id, estado) VALUES
('Juan', 'Pérez Mamani', '7890123', 'juan.perez@estudiante.edu.bo', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5lXg3qRjLq8q6', 3, 7, 'activo'),
('Sofía', 'Quispe Torres', '8901234', 'sofia.quispe@estudiante.edu.bo', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5lXg3qRjLq8q6', 3, 7, 'activo'),
('Miguel', 'Condori Apaza', '9012345', 'miguel.condori@estudiante.edu.bo', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5lXg3qRjLq8q6', 3, 8, 'activo'),
('Laura', 'Vargas Nina', '0123456', 'laura.vargas@estudiante.edu.bo', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5lXg3qRjLq8q6', 3, 8, 'activo'),
('Diego', 'Huanca Limachi', '1234568', 'diego.huanca@estudiante.edu.bo', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5lXg3qRjLq8q6', 3, 9, 'activo'),
('Valentina', 'Chura Flores', '2345679', 'valentina.chura@estudiante.edu.bo', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5lXg3qRjLq8q6', 3, 9, 'activo'),
('Andrés', 'Poma Quisbert', '3456780', 'andres.poma@estudiante.edu.bo', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5lXg3qRjLq8q6', 3, 10, 'activo'),
('Camila', 'Yujra Callisaya', '4567891', 'camila.yujra@estudiante.edu.bo', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5lXg3qRjLq8q6', 3, 10, 'activo'),
('Gabriel', 'Marca Ticona', '5678902', 'gabriel.marca@estudiante.edu.bo', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5lXg3qRjLq8q6', 3, 11, 'activo'),
('Isabella', 'Colque Mamani', '6789013', 'isabella.colque@estudiante.edu.bo', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5lXg3qRjLq8q6', 3, 11, 'activo');

-- ============================================================================
-- INSERTAR VIDEOS
-- ============================================================================

-- Videos de Matemáticas (Docente: María González - ID 2)
INSERT INTO videos (titulo, descripcion, archivo_path, thumbnail_path, duracion, tamaño, formato, resolucion, codec, materia_id, grado_id, docente_id, visualizaciones, estado) VALUES
(
    'Ecuaciones de Primer Grado - Introducción',
    'Aprende a resolver ecuaciones lineales de forma sencilla. Este video cubre los conceptos básicos y ejemplos prácticos.',
    '/uploads/videos/matematicas/ecuaciones_primer_grado.mp4',
    '/uploads/thumbnails/ecuaciones_primer_grado.jpg',
    1200,
    45000000,
    'MP4',
    '720p',
    'H.264',
    1,
    7,
    2,
    45,
    'activo'
),
(
    'Teorema de Pitágoras Explicado',
    'Demostración visual del teorema de Pitágoras con aplicaciones prácticas en geometría.',
    '/uploads/videos/matematicas/teorema_pitagoras.mp4',
    '/uploads/thumbnails/teorema_pitagoras.jpg',
    900,
    38000000,
    'MP4',
    '720p',
    'H.264',
    1,
    8,
    2,
    67,
    'activo'
),
(
    'Fracciones y Operaciones Básicas',
    'Todo lo que necesitas saber sobre fracciones: suma, resta, multiplicación y división.',
    '/uploads/videos/matematicas/fracciones_operaciones.mp4',
    '/uploads/thumbnails/fracciones_operaciones.jpg',
    1500,
    52000000,
    'MP4',
    '1080p',
    'H.264',
    1,
    7,
    2,
    89,
    'activo'
),
(
    'Sistemas de Ecuaciones - Método de Sustitución',
    'Aprende a resolver sistemas de ecuaciones lineales usando el método de sustitución.',
    '/uploads/videos/matematicas/sistemas_ecuaciones.mp4',
    '/uploads/thumbnails/sistemas_ecuaciones.jpg',
    1350,
    48000000,
    'MP4',
    '720p',
    'H.264',
    1,
    9,
    2,
    34,
    'activo'
);

-- Videos de Lenguaje (Docente: Carlos Mamani - ID 3)
INSERT INTO videos (titulo, descripcion, archivo_path, thumbnail_path, duracion, tamaño, formato, resolucion, codec, materia_id, grado_id, docente_id, visualizaciones, estado) VALUES
(
    'Análisis Sintáctico de Oraciones',
    'Aprende a identificar sujeto, predicado y complementos en diferentes tipos de oraciones.',
    '/uploads/videos/lenguaje/analisis_sintactico.mp4',
    '/uploads/thumbnails/analisis_sintactico.jpg',
    1100,
    42000000,
    'MP4',
    '720p',
    'H.264',
    2,
    8,
    3,
    56,
    'activo'
),
(
    'Reglas de Acentuación',
    'Domina las reglas de acentuación en español: agudas, graves, esdrújulas y sobresdrújulas.',
    '/uploads/videos/lenguaje/reglas_acentuacion.mp4',
    '/uploads/thumbnails/reglas_acentuacion.jpg',
    800,
    32000000,
    'MP4',
    '720p',
    'H.264',
    2,
    7,
    3,
    78,
    'activo'
),
(
    'Figuras Literarias en la Poesía',
    'Descubre las principales figuras literarias: metáfora, símil, hipérbole y más.',
    '/uploads/videos/lenguaje/figuras_literarias.mp4',
    '/uploads/thumbnails/figuras_literarias.jpg',
    1250,
    46000000,
    'MP4',
    '1080p',
    'H.264',
    2,
    9,
    3,
    43,
    'activo'
),
(
    'Comprensión Lectora - Técnicas Avanzadas',
    'Mejora tu comprensión de textos con estrategias efectivas de lectura.',
    '/uploads/videos/lenguaje/comprension_lectora.mp4',
    '/uploads/thumbnails/comprension_lectora.jpg',
    1400,
    50000000,
    'MP4',
    '720p',
    'H.264',
    2,
    10,
    3,
    61,
    'activo'
);

-- Videos de Ciencias Naturales (Docente: Ana Flores - ID 4)
INSERT INTO videos (titulo, descripcion, archivo_path, thumbnail_path, duracion, tamaño, formato, resolucion, codec, materia_id, grado_id, docente_id, visualizaciones, estado) VALUES
(
    'El Ciclo del Agua',
    'Conoce las etapas del ciclo hidrológico: evaporación, condensación, precipitación y escorrentía.',
    '/uploads/videos/ciencias/ciclo_agua.mp4',
    '/uploads/thumbnails/ciclo_agua.jpg',
    950,
    40000000,
    'MP4',
    '1080p',
    'H.264',
    3,
    7,
    4,
    92,
    'activo'
),
(
    'La Célula y sus Partes',
    'Explora la estructura celular: membrana, citoplasma, núcleo y organelos.',
    '/uploads/videos/ciencias/celula_partes.mp4',
    '/uploads/thumbnails/celula_partes.jpg',
    1300,
    47000000,
    'MP4',
    '720p',
    'H.264',
    3,
    8,
    4,
    75,
    'activo'
),
(
    'Leyes de Newton - Física Básica',
    'Comprende las tres leyes fundamentales del movimiento de Newton con ejemplos cotidianos.',
    '/uploads/videos/ciencias/leyes_newton.mp4',
    '/uploads/thumbnails/leyes_newton.jpg',
    1450,
    51000000,
    'MP4',
    '1080p',
    'H.264',
    3,
    9,
    4,
    58,
    'activo'
),
(
    'Fotosíntesis en las Plantas',
    'Descubre cómo las plantas producen su alimento a través de la fotosíntesis.',
    '/uploads/videos/ciencias/fotosintesis.mp4',
    '/uploads/thumbnails/fotosintesis.jpg',
    1050,
    43000000,
    'MP4',
    '720p',
    'H.264',
    3,
    7,
    4,
    84,
    'activo'
),
(
    'El Sistema Solar y sus Planetas',
    'Un recorrido por nuestro sistema solar: características de cada planeta.',
    '/uploads/videos/ciencias/sistema_solar.mp4',
    '/uploads/thumbnails/sistema_solar.jpg',
    1600,
    55000000,
    'MP4',
    '1080p',
    'H.264',
    3,
    8,
    4,
    103,
    'activo'
);

-- Videos de Ciencias Sociales (Docente: Roberto Choque - ID 5)
INSERT INTO videos (titulo, descripcion, archivo_path, thumbnail_path, duracion, tamaño, formato, resolucion, codec, materia_id, grado_id, docente_id, visualizaciones, estado) VALUES
(
    'Independencia de Bolivia',
    'Historia de la independencia boliviana y sus principales protagonistas.',
    '/uploads/videos/sociales/independencia_bolivia.mp4',
    '/uploads/thumbnails/independencia_bolivia.jpg',
    1350,
    49000000,
    'MP4',
    '720p',
    'H.264',
    4,
    9,
    5,
    71,
    'activo'
),
(
    'Geografía de Bolivia - Regiones',
    'Conoce las tres regiones geográficas de Bolivia: Altiplano, Valles y Llanos.',
    '/uploads/videos/sociales/geografia_bolivia.mp4',
    '/uploads/thumbnails/geografia_bolivia.jpg',
    1200,
    44000000,
    'MP4',
    '1080p',
    'H.264',
    4,
    7,
    5,
    86,
    'activo'
),
(
    'Culturas Precolombinas de Bolivia',
    'Tiwanaku, cultura Inca y otras civilizaciones que habitaron nuestro territorio.',
    '/uploads/videos/sociales/culturas_precolombinas.mp4',
    '/uploads/thumbnails/culturas_precolombinas.jpg',
    1500,
    53000000,
    'MP4',
    '720p',
    'H.264',
    4,
    8,
    5,
    64,
    'activo'
),
(
    'La Constitución Política del Estado',
    'Principios básicos de la Constitución boliviana y derechos fundamentales.',
    '/uploads/videos/sociales/constitucion_bolivia.mp4',
    '/uploads/thumbnails/constitucion_bolivia.jpg',
    1100,
    41000000,
    'MP4',
    '720p',
    'H.264',
    4,
    10,
    5,
    48,
    'activo'
);

-- Videos de Inglés (Docente: Patricia Luna - ID 6)
INSERT INTO videos (titulo, descripcion, archivo_path, thumbnail_path, duracion, tamaño, formato, resolucion, codec, materia_id, grado_id, docente_id, visualizaciones, estado) VALUES
(
    'Present Simple vs Present Continuous',
    'Aprende las diferencias entre estos dos tiempos verbales con ejemplos prácticos.',
    '/uploads/videos/ingles/present_tenses.mp4',
    '/uploads/thumbnails/present_tenses.jpg',
    1000,
    39000000,
    'MP4',
    '720p',
    'H.264',
    5,
    8,
    6,
    55,
    'activo'
),
(
    'Phrasal Verbs Más Comunes',
    'Los phrasal verbs esenciales que debes conocer para conversaciones cotidianas.',
    '/uploads/videos/ingles/phrasal_verbs.mp4',
    '/uploads/thumbnails/phrasal_verbs.jpg',
    1250,
    45000000,
    'MP4',
    '1080p',
    'H.264',
    5,
    9,
    6,
    42,
    'activo'
),
(
    'Pronunciación del Inglés - Sonidos Vocálicos',
    'Mejora tu pronunciación aprendiendo los sonidos vocálicos del inglés.',
    '/uploads/videos/ingles/pronunciacion_vocales.mp4',
    '/uploads/thumbnails/pronunciacion_vocales.jpg',
    900,
    36000000,
    'MP4',
    '720p',
    'H.264',
    5,
    7,
    6,
    69,
    'activo'
),
(
    'Vocabulario: La Familia en Inglés',
    'Aprende el vocabulario relacionado con la familia y las relaciones.',
    '/uploads/videos/ingles/vocabulario_familia.mp4',
    '/uploads/thumbnails/vocabulario_familia.jpg',
    750,
    30000000,
    'MP4',
    '720p',
    'H.264',
    5,
    7,
    6,
    77,
    'activo'
);

-- ============================================================================
-- INSERTAR RELACIONES VIDEO-CATEGORÍAS
-- ============================================================================

INSERT INTO video_categorias (video_id, categoria_id) VALUES
-- Matemáticas
(1, 1), (1, 3), -- Ecuaciones: Tutorial, Explicación
(2, 3), (2, 4), -- Pitágoras: Explicación, Ejercicios
(3, 1), (3, 4), -- Fracciones: Tutorial, Ejercicios
(4, 1), (4, 4), -- Sistemas: Tutorial, Ejercicios

-- Lenguaje
(5, 3), (5, 7), -- Análisis: Explicación, Gramática
(6, 1), (6, 7), -- Acentuación: Tutorial, Gramática
(7, 3), (7, 10), -- Figuras: Explicación, Cultura
(8, 1), (8, 3), -- Comprensión: Tutorial, Explicación

-- Ciencias Naturales
(9, 3), (9, 6), -- Agua: Explicación, Documental
(10, 3), (10, 8), -- Célula: Explicación, Laboratorio
(11, 2), (11, 3), -- Newton: Experimento, Explicación
(12, 2), (12, 8), -- Fotosíntesis: Experimento, Laboratorio
(13, 6), (13, 3), -- Sistema Solar: Documental, Explicación

-- Ciencias Sociales
(14, 5), (14, 6), -- Independencia: Historia, Documental
(15, 9), (15, 3), -- Geografía: Geografía, Explicación
(16, 5), (16, 10), -- Culturas: Historia, Cultura
(17, 3), (17, 5), -- Constitución: Explicación, Historia

-- Inglés
(18, 1), (18, 3), -- Present: Tutorial, Explicación
(19, 1), (19, 4), -- Phrasal: Tutorial, Ejercicios
(20, 1), (20, 3), -- Pronunciación: Tutorial, Explicación
(21, 1), (21, 3); -- Vocabulario: Tutorial, Explicación

-- ============================================================================
-- INSERTAR REPRODUCCIONES
-- ============================================================================

-- Reproducciones de estudiantes
INSERT INTO reproducciones (video_id, usuario_id, tiempo_reproducido, progreso, completado) VALUES
-- Juan Pérez (ID 7)
(1, 7, 1200, 100.00, TRUE),
(3, 7, 900, 60.00, FALSE),
(6, 7, 800, 100.00, TRUE),
(9, 7, 950, 100.00, TRUE),

-- Sofía Quispe (ID 8)
(1, 8, 1200, 100.00, TRUE),
(2, 8, 900, 100.00, TRUE),
(5, 8, 750, 68.18, FALSE),
(9, 8, 950, 100.00, TRUE),

-- Miguel Condori (ID 9)
(2, 9, 900, 100.00, TRUE),
(5, 9, 1100, 100.00, TRUE),
(10, 9, 1300, 100.00, TRUE),
(15, 9, 800, 66.67, FALSE),

-- Laura Vargas (ID 10)
(2, 10, 450, 50.00, FALSE),
(10, 10, 1300, 100.00, TRUE),
(18, 10, 1000, 100.00, TRUE),

-- Diego Huanca (ID 11)
(4, 11, 1350, 100.00, TRUE),
(7, 11, 1250, 100.00, TRUE),
(11, 11, 1450, 100.00, TRUE),
(14, 11, 900, 66.67, FALSE),

-- Valentina Chura (ID 12)
(4, 12, 800, 59.26, FALSE),
(11, 12, 1200, 82.76, FALSE),
(19, 12, 1250, 100.00, TRUE),

-- Andrés Poma (ID 13)
(8, 13, 1400, 100.00, TRUE),
(17, 13, 1100, 100.00, TRUE),

-- Camila Yujra (ID 14)
(8, 14, 700, 50.00, FALSE),
(17, 14, 1100, 100.00, TRUE),

-- Gabriel Marca (ID 15)
(19, 15, 625, 50.00, FALSE),

-- Isabella Colque (ID 16)
(19, 16, 1250, 100.00, TRUE);

-- ============================================================================
-- INSERTAR COMENTARIOS
-- ============================================================================

INSERT INTO comentarios (video_id, usuario_id, contenido, estado) VALUES
(1, 7, 'Excelente explicación, ahora entiendo mucho mejor las ecuaciones.', 'activo'),
(1, 8, 'Muy claro el video, gracias profesora María!', 'activo'),
(2, 9, 'Me ayudó mucho para el examen de geometría.', 'activo'),
(9, 7, 'El ciclo del agua nunca había sido tan fácil de entender.', 'activo'),
(10, 9, 'Las animaciones de la célula están muy bien hechas.', 'activo'),
(5, 8, 'Podrían hacer más videos sobre análisis sintáctico?', 'activo'),
(11, 11, 'Los ejemplos de las leyes de Newton son muy buenos.', 'activo'),
(18, 10, 'Este video me sirvió mucho para practicar los tiempos verbales.', 'activo'),
(14, 11, 'Interesante la historia de nuestra independencia.', 'activo'),
(3, 7, 'Las fracciones ya no me parecen tan difíciles.', 'activo'),
(6, 7, 'Ahora sé cómo acentuar correctamente las palabras.', 'activo'),
(19, 12, 'Los phrasal verbs son complicados pero este video ayuda.', 'activo'),
(7, 11, 'Me encantan las figuras literarias, especialmente las metáforas.', 'activo'),
(13, 9, 'El sistema solar es fascinante!', 'activo'),
(2, 8, 'Buen video sobre el teorema de Pitágoras.', 'activo');

-- ============================================================================
-- INSERTAR FAVORITOS
-- ============================================================================

INSERT INTO favoritos (video_id, usuario_id) VALUES
(1, 7),
(9, 7),
(6, 7),
(1, 8),
(2, 8),
(9, 8),
(2, 9),
(10, 9),
(11, 11),
(4, 11),
(14, 11),
(18, 10),
(10, 10),
(19, 12),
(8, 13),
(17, 13),
(19, 16);

-- ============================================================================
-- INSERTAR CALIFICACIONES
-- ============================================================================

INSERT INTO calificaciones (video_id, usuario_id, puntuacion, comentario_calificacion) VALUES
(1, 7, 5, 'Perfecto para aprender ecuaciones'),
(1, 8, 5, 'Excelente contenido'),
(2, 9, 5, 'Muy bien explicado'),
(2, 8, 4, 'Buen video'),
(3, 7, 5, 'Me ayudó mucho'),
(5, 8, 4, 'Buena explicación'),
(6, 7, 5, 'Ahora entiendo la acentuación'),
(9, 7, 5, 'Muy educativo'),
(9, 8, 5, 'Excelente'),
(10, 9, 5, 'Las animaciones son geniales'),
(11, 11, 5, 'Perfecta explicación de las leyes de Newton'),
(14, 11, 4, 'Buen contenido histórico'),
(18, 10, 5, 'Me ayudó mucho con el inglés'),
(4, 11, 4, 'Buen método de resolución'),
(7, 11, 5, 'Me encantan las figuras literarias'),
(19, 12, 4, 'Útil para aprender phrasal verbs'),
(8, 13, 5, 'Excelentes técnicas de comprensión'),
(17, 13, 5, 'Muy importante conocer nuestra constitución'),
(19, 16, 4, 'Buen contenido');

-- ============================================================================
-- INSERTAR NOTIFICACIONES
-- ============================================================================

INSERT INTO notificaciones (usuario_id, titulo, mensaje, tipo, entidad_tipo, entidad_id) VALUES
(7, 'Nuevo video disponible', 'La profesora María González ha subido un nuevo video de Matemáticas', 'nuevo_video', 'video', 1),
(8, 'Nuevo video disponible', 'La profesora María González ha subido un nuevo video de Matemáticas', 'nuevo_video', 'video', 1),
(9, 'Nuevo video disponible', 'La profesora Ana Flores ha subido un nuevo video de Ciencias Naturales', 'nuevo_video', 'video', 10),
(7, 'Bienvenido al sistema', 'Te damos la bienvenida a la Videoteca Educativa de San Francisco Xavier', 'sistema', NULL, NULL);

-- ============================================================================
-- INSERTAR LOGS DEL SISTEMA
-- ============================================================================

INSERT INTO logs_sistema (usuario_id, accion, descripcion, entidad_tipo, entidad_id, ip, nivel) VALUES
(1, 'LOGIN', 'Inicio de sesión exitoso', 'usuario', 1, '192.168.1.100', 'info'),
(2, 'VIDEO_CREADO', 'Video creado: Ecuaciones de Primer Grado', 'video', 1, '192.168.1.101', 'info'),
(7, 'LOGIN', 'Inicio de sesión exitoso', 'usuario', 7, '192.168.1.102', 'info'),
(7, 'VIDEO_REPRODUCIDO', 'Reproducción de video: Ecuaciones de Primer Grado', 'video', 1, '192.168.1.102', 'info'),
(8, 'LOGIN', 'Inicio de sesión exitoso', 'usuario', 8, '192.168.1.103', 'info'),
(3, 'VIDEO_CREADO', 'Video creado: Análisis Sintáctico de Oraciones', 'video', 5, '192.168.1.104', 'info'),
(4, 'VIDEO_CREADO', 'Video creado: El Ciclo del Agua', 'video', 9, '192.168.1.105', 'info');

-- ============================================================================
-- VERIFICACIÓN DE DATOS
-- ============================================================================

-- Contar registros insertados
SELECT 'Roles insertados:' AS info, COUNT(*) AS cantidad FROM roles;
SELECT 'Grados insertados:' AS info, COUNT(*) AS cantidad FROM grados;
SELECT 'Materias insertadas:' AS info, COUNT(*) AS cantidad FROM materias;
SELECT 'Categorías insertadas:' AS info, COUNT(*) AS cantidad FROM categorias;
SELECT 'Usuarios insertados:' AS info, COUNT(*) AS cantidad FROM usuarios;
SELECT 'Videos insertados:' AS info, COUNT(*) AS cantidad FROM videos;
SELECT 'Reproducciones insertadas:' AS info, COUNT(*) AS cantidad FROM reproducciones;
SELECT 'Comentarios insertados:' AS info, COUNT(*) AS cantidad FROM comentarios;
SELECT 'Favoritos insertados:' AS info, COUNT(*) AS cantidad FROM favoritos;
SELECT 'Calificaciones insertadas:' AS info, COUNT(*) AS cantidad FROM calificaciones;

-- Mostrar credenciales de acceso
SELECT '========== CREDENCIALES DE ACCESO ==========' AS '';
SELECT 'ADMINISTRADOR:' AS '';
SELECT '  Email: admin@sfxavier.edu.bo' AS '';
SELECT '  Contraseña: password123' AS '';
SELECT '' AS '';
SELECT 'DOCENTE (Ejemplo):' AS '';
SELECT '  Email: maria.gonzalez@sfxavier.edu.bo' AS '';
SELECT '  Contraseña: password123' AS '';
SELECT '' AS '';
SELECT 'ESTUDIANTE (Ejemplo):' AS '';
SELECT '  Email: juan.perez@estudiante.edu.bo' AS '';
SELECT '  Contraseña: password123' AS '';
SELECT '===========================================' AS '';
