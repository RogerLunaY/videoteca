<?php

/**
 * Sistema de Rutas API
 *
 * Sistema de Biblioteca Digital de Videos Educativos
 * Unidad Educativa San Francisco Xavier
 *
 * @author Roger Omar Luna Yujra
 * @version 1.0
 */

declare(strict_types=1);

/**
 * Define las rutas de la API y las asocia con sus controladores
 *
 * Formato de rutas:
 * [método HTTP, ruta, controlador, función]
 *
 * Parámetros en rutas se definen con {nombre}
 */

return [
    // ============================================================================
    // ENDPOINTS PÚBLICOS (SIN AUTENTICACIÓN) - Para estudiantes
    // ============================================================================
    ['GET', '/api/public/videos', 'PublicController', 'getVideos'],
    ['GET', '/api/public/videos/{id}', 'PublicController', 'getVideoById'],
    ['GET', '/api/public/videos/{id}/stream', 'PublicController', 'streamVideo'],
    ['POST', '/api/public/videos/{id}/view', 'PublicController', 'registrarVisualizacion'],
    ['GET', '/api/public/videos/search', 'PublicController', 'buscarVideos'],
    ['GET', '/api/public/grados', 'PublicController', 'getGrados'],
    ['GET', '/api/public/materias', 'PublicController', 'getMaterias'],

    // ============================================================================
    // AUTENTICACIÓN (Solo para Docentes y Administradores)
    // ============================================================================
    ['POST', '/api/auth/login', 'AuthController', 'login'],
    ['POST', '/api/auth/logout', 'AuthController', 'logout'],
    ['POST', '/api/auth/refresh-token', 'AuthController', 'refreshToken'],
    ['GET', '/api/auth/me', 'AuthController', 'me'],

    // ============================================================================
    // VIDEOS
    // ============================================================================
    ['GET', '/api/videos', 'VideoController', 'obtenerTodos'],
    ['GET', '/api/videos/{id}', 'VideoController', 'obtenerPorId'],
    ['POST', '/api/videos', 'VideoController', 'crear'],
    ['PUT', '/api/videos/{id}', 'VideoController', 'actualizar'],
    ['DELETE', '/api/videos/{id}', 'VideoController', 'eliminar'],
    ['GET', '/api/videos/{id}/stream', 'VideoController', 'stream'],
    ['GET', '/api/videos/buscar', 'VideoController', 'buscar'],
    ['GET', '/api/videos/populares', 'VideoController', 'obtenerPopulares'],
    ['GET', '/api/videos/recomendados', 'VideoController', 'obtenerRecomendados'],

    // ============================================================================
    // USUARIOS
    // ============================================================================
    ['GET', '/api/usuarios', 'UsuarioController', 'obtenerTodos'],
    ['GET', '/api/usuarios/{id}', 'UsuarioController', 'obtenerPorId'],
    ['POST', '/api/usuarios', 'UsuarioController', 'crear'],
    ['PUT', '/api/usuarios/{id}', 'UsuarioController', 'actualizar'],
    ['DELETE', '/api/usuarios/{id}', 'UsuarioController', 'eliminar'],

    // ============================================================================
    // REPRODUCCIONES
    // ============================================================================
    ['POST', '/api/reproducciones', 'ReproduccionController', 'crear'],
    ['GET', '/api/reproducciones/usuario/{id}', 'ReproduccionController', 'obtenerPorUsuario'],
    ['PUT', '/api/reproducciones/{id}/progreso', 'ReproduccionController', 'actualizarProgreso'],

    // ============================================================================
    // COMENTARIOS
    // ============================================================================
    ['GET', '/api/videos/{id}/comentarios', 'ComentarioController', 'obtenerPorVideo'],
    ['POST', '/api/comentarios', 'ComentarioController', 'crear'],
    ['DELETE', '/api/comentarios/{id}', 'ComentarioController', 'eliminar'],

    // ============================================================================
    // FAVORITOS
    // ============================================================================
    ['GET', '/api/favoritos/usuario/{id}', 'FavoritoController', 'obtenerPorUsuario'],
    ['POST', '/api/favoritos', 'FavoritoController', 'crear'],
    ['DELETE', '/api/favoritos/{id}', 'FavoritoController', 'eliminar'],

    // ============================================================================
    // CALIFICACIONES
    // ============================================================================
    ['POST', '/api/calificaciones', 'CalificacionController', 'crear'],
    ['PUT', '/api/calificaciones/{id}', 'CalificacionController', 'actualizar'],

    // ============================================================================
    // ESTADÍSTICAS
    // ============================================================================
    ['GET', '/api/estadisticas/generales', 'EstadisticaController', 'generales'],
    ['GET', '/api/estadisticas/videos-populares', 'EstadisticaController', 'videosPopulares'],
    ['GET', '/api/estadisticas/actividad-usuarios', 'EstadisticaController', 'actividadUsuarios'],

    // ============================================================================
    // CATEGORÍAS
    // ============================================================================
    ['GET', '/api/categorias', 'CategoriaController', 'obtenerTodas'],
    ['GET', '/api/categorias/{id}', 'CategoriaController', 'obtenerPorId'],
    ['POST', '/api/categorias', 'CategoriaController', 'crear'],
    ['PUT', '/api/categorias/{id}', 'CategoriaController', 'actualizar'],
    ['DELETE', '/api/categorias/{id}', 'CategoriaController', 'eliminar'],

    // ============================================================================
    // MATERIAS
    // ============================================================================
    ['GET', '/api/materias', 'MateriaController', 'obtenerTodas'],
    ['GET', '/api/materias/{id}', 'MateriaController', 'obtenerPorId'],

    // ============================================================================
    // GRADOS
    // ============================================================================
    ['GET', '/api/grados', 'GradoController', 'obtenerTodos'],
    ['GET', '/api/grados/{id}', 'GradoController', 'obtenerPorId'],
];
