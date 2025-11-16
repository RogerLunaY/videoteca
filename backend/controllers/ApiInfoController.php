<?php declare(strict_types=1);

/**
 * Controlador de Información de la API
 *
 * Sistema de Biblioteca Digital de Videos Educativos
 * Unidad Educativa San Francisco Xavier
 *
 * @author Roger Omar Luna Yujra
 * @version 2.0
 */

namespace Controllers;

/**
 * Clase ApiInfoController
 *
 * Muestra información general de la API y endpoints disponibles
 */
class ApiInfoController
{
    /**
     * Muestra información de bienvenida de la API
     *
     * @return void
     */
    public function index(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(200);

        $info = [
            'success' => true,
            'nombre' => 'API - Sistema de Biblioteca Digital de Videos Educativos',
            'institucion' => 'Unidad Educativa San Francisco Xavier',
            'ubicacion' => 'Okinawa Uno, Bolivia',
            'version' => '2.0',
            'autor' => 'Roger Omar Luna Yujra',
            'descripcion' => 'Sistema de gestión de videos educativos con acceso público para estudiantes',

            'caracteristicas' => [
                'Acceso público para estudiantes (sin login)',
                'Sistema de autenticación para docentes y administradores',
                'Streaming de video con HTTP Range Requests',
                'Filtrado por grado y materia',
                'Búsqueda de videos',
                'Contador de visualizaciones anónimas',
            ],

            'endpoints_publicos' => [
                'GET /api/public/videos' => 'Listar todos los videos (puede filtrar por grado_id, materia_id)',
                'GET /api/public/videos/{id}' => 'Obtener detalles de un video específico',
                'GET /api/public/videos/{id}/stream' => 'Reproducir video',
                'POST /api/public/videos/{id}/view' => 'Registrar visualización anónima',
                'GET /api/public/videos/search?q={query}' => 'Buscar videos por título',
                'GET /api/public/grados' => 'Listar todos los grados',
                'GET /api/public/materias' => 'Listar todas las materias',
            ],

            'endpoints_autenticados' => [
                'autenticacion' => [
                    'POST /api/auth/login' => 'Iniciar sesión (docentes y administradores)',
                    'POST /api/auth/logout' => 'Cerrar sesión',
                    'POST /api/auth/refresh-token' => 'Renovar token de acceso',
                    'GET /api/auth/me' => 'Obtener información del usuario autenticado',
                ],
                'videos' => [
                    'GET /api/videos' => 'Listar videos (con autenticación)',
                    'POST /api/videos' => 'Crear nuevo video (solo docentes/admin)',
                    'PUT /api/videos/{id}' => 'Actualizar video',
                    'DELETE /api/videos/{id}' => 'Eliminar video',
                ],
            ],

            'roles' => [
                'Estudiantes' => 'Acceso público sin login - Solo visualización de videos',
                'Docentes' => 'Login requerido - Pueden subir y gestionar sus videos',
                'Administradores' => 'Login requerido - Control total del sistema',
            ],

            'tecnologias' => [
                'Backend' => 'PHP 8.x con arquitectura MVC',
                'Base de datos' => 'MySQL 8.x',
                'Servidor web' => 'Apache 2.4 (XAMPP)',
                'Autenticación' => 'JWT (JSON Web Tokens)',
                'Frontend Web' => 'React 18 + Vite',
            ],

            'estado' => 'Activo',
            'fecha' => date('Y-m-d H:i:s'),
            'timezone' => date_default_timezone_get(),

            'enlaces_utiles' => [
                'Documentación' => 'README.md',
                'Instalación XAMPP' => 'INSTALACION_XAMPP.md',
                'Sistema actualizado' => 'SISTEMA_ACTUALIZADO.md',
                'Frontend Web' => 'http://localhost:5173',
                'API Base URL' => 'http://localhost/videoteca-backend',
            ],

            'ejemplos' => [
                'Listar videos' => 'GET http://localhost/videoteca-backend/api/public/videos',
                'Buscar videos' => 'GET http://localhost/videoteca-backend/api/public/videos/search?q=matemáticas',
                'Filtrar por grado' => 'GET http://localhost/videoteca-backend/api/public/videos?grado_id=1',
                'Obtener grados' => 'GET http://localhost/videoteca-backend/api/public/grados',
            ],

            'soporte' => [
                'Desarrollador' => 'Roger Omar Luna Yujra',
                'Institución educativa' => 'Instituto Técnico ATSI, Bolivia',
                'Proyecto para' => 'U.E. San Francisco Xavier',
            ],
        ];

        echo json_encode($info, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
