# ========================================================================
# GUÍA DE INSTALACIÓN EN XAMPP
# Sistema de Biblioteca Digital de Videos Educativos
# U.E. San Francisco Xavier
# ========================================================================

## REQUISITOS PREVIOS

1. XAMPP 8.x instalado (con PHP 8.x, MySQL 8.x, Apache)
   Descargar de: https://www.apachefriends.org/

2. Node.js 16+ instalado (para el frontend)
   Descargar de: https://nodejs.org/

3. Git instalado (ya lo tienes)

4. FFmpeg instalado (opcional, para procesamiento de videos)
   Descargar de: https://ffmpeg.org/download.html

## ========================================================================
## PASO 1: PREPARAR XAMPP
## ========================================================================

1. Abrir el Panel de Control de XAMPP
2. Iniciar Apache y MySQL (hacer clic en "Start")
3. Verificar que estén corriendo (indicador verde)

## ========================================================================
## PASO 2: COPIAR EL BACKEND A XAMPP
## ========================================================================

### En Windows:

1. Abrir una terminal en el directorio del proyecto
   cd C:\ruta\a\videoteca

2. Copiar la carpeta backend a htdocs de XAMPP:

   xcopy /E /I backend "C:\xampp\htdocs\videoteca-backend"

   O manualmente:
   - Copiar la carpeta "backend" completa
   - Pegarla en: C:\xampp\htdocs\
   - Renombrarla a: videoteca-backend

### En Linux/Mac:

cp -r backend /opt/lampp/htdocs/videoteca-backend

## ========================================================================
## PASO 3: CONFIGURAR LA BASE DE DATOS
## ========================================================================

### Opción A: Usando phpMyAdmin (Recomendado para principiantes)

1. Abrir el navegador
2. Ir a: http://localhost/phpmyadmin
3. Hacer clic en "Nueva" (en el panel izquierdo)
4. Nombre de la base de datos: videoteca_educativa
5. Cotejamiento: utf8mb4_unicode_ci
6. Hacer clic en "Crear"

7. Seleccionar la base de datos creada (panel izquierdo)
8. Hacer clic en la pestaña "SQL"
9. Abrir el archivo: database/schema.sql
10. Copiar TODO el contenido del archivo
11. Pegarlo en el área de texto de phpMyAdmin
12. Hacer clic en "Continuar" (abajo a la derecha)
13. Esperar a que se ejecute (puede tardar unos segundos)

14. Repetir el proceso para cargar datos de prueba:
    - Pestaña "SQL"
    - Abrir database/seed_data.sql
    - Copiar y pegar todo el contenido
    - Hacer clic en "Continuar"

### Opción B: Usando la línea de comandos

1. Abrir terminal/CMD
2. Navegar al directorio de MySQL de XAMPP:

   Windows:
   cd C:\xampp\mysql\bin

   Linux/Mac:
   cd /opt/lampp/bin

3. Ejecutar los siguientes comandos:

   # Crear y poblar la base de datos
   mysql -u root -p < C:\ruta\a\videoteca\database\schema.sql
   mysql -u root -p < C:\ruta\a\videoteca\database\seed_data.sql

   Nota: Si no tiene contraseña, omitir el -p

## ========================================================================
## PASO 4: CONFIGURAR EL BACKEND
## ========================================================================

1. Ir a la carpeta del backend en XAMPP:
   cd C:\xampp\htdocs\videoteca-backend

2. Crear el archivo .env desde .env.example:

   Windows:
   copy .env.example .env

   Linux/Mac:
   cp .env.example .env

3. Editar el archivo .env con un editor de texto:

   notepad .env

   O usar tu editor favorito (VSCode, Sublime, etc.)

4. Configurar las variables (IMPORTANTE):

   # Entorno
   APP_ENV=development
   DEBUG=true
   TIMEZONE=America/La_Paz

   # Base de Datos (AJUSTAR SEGÚN TU CONFIGURACIÓN)
   DB_HOST=localhost
   DB_PORT=3306
   DB_NAME=videoteca_educativa
   DB_USER=root
   DB_PASS=
   DB_CHARSET=utf8mb4

   # JWT - CAMBIAR EN PRODUCCIÓN
   JWT_SECRET=videoteca_sfxavier_mi_clave_super_secreta_2024_cambiar
   JWT_ISSUER=videoteca.sfxavier.edu.bo
   JWT_AUDIENCE=videoteca.sfxavier.edu.bo
   JWT_ACCESS_EXPIRATION=3600
   JWT_REFRESH_EXPIRATION=604800

   # CORS
   CORS_ALLOWED_ORIGINS=http://localhost:5173,http://localhost:3000

   # Uploads
   MAX_VIDEO_SIZE=524288000

5. Guardar y cerrar el archivo

6. Crear las carpetas de uploads con permisos:

   mkdir uploads\videos
   mkdir uploads\thumbnails

   En Linux/Mac también dar permisos:
   chmod 755 uploads
   chmod 755 uploads/videos
   chmod 755 uploads/thumbnails

## ========================================================================
## PASO 5: VERIFICAR QUE EL BACKEND FUNCIONA
## ========================================================================

1. Abrir el navegador
2. Ir a: http://localhost/videoteca-backend/

3. Si ves un error 404, ir a:
   http://localhost/videoteca-backend/api/auth/login

4. Deberías ver un mensaje JSON como:
   {
     "success": false,
     "message": "Campos requeridos faltantes: email, password"
   }

   ¡Esto significa que el backend funciona! ✅

## ========================================================================
## PASO 6: CONFIGURAR EL FRONTEND
## ========================================================================

1. Abrir una terminal en el directorio del frontend:
   cd C:\ruta\a\videoteca\frontend-web

2. Instalar las dependencias de Node.js:
   npm install

   Nota: Esto puede tardar 2-5 minutos

3. Crear el archivo .env:

   Windows:
   copy .env.example .env

   Linux/Mac:
   cp .env.example .env

4. Editar el archivo .env del frontend:

   notepad .env

5. Configurar la URL del backend:

   # API Backend URL (IMPORTANTE: sin /api al final)
   VITE_API_URL=http://localhost/videoteca-backend

   # Nombre de la aplicación
   VITE_APP_NAME=Videoteca San Francisco Xavier

   # Entorno
   VITE_ENV=development

6. Guardar y cerrar

## ========================================================================
## PASO 7: INICIAR EL FRONTEND
## ========================================================================

1. En la terminal del frontend, ejecutar:
   npm run dev

2. Deberías ver algo como:

   VITE v5.0.8  ready in 500 ms

   ➜  Local:   http://localhost:5173/
   ➜  Network: use --host to expose
   ➜  press h to show help

3. Abrir el navegador en: http://localhost:5173

## ========================================================================
## PASO 8: USAR EL SISTEMA
## ========================================================================

### Primera vez - Pantalla de Login

1. Verás la pantalla de login con el logo y el formulario

2. Usar las credenciales de prueba (están visibles en la pantalla):

   ADMINISTRADOR:
   Email: admin@sfxavier.edu.bo
   Contraseña: password123

   DOCENTE:
   Email: maria.gonzalez@sfxavier.edu.bo
   Contraseña: password123

   ESTUDIANTE:
   Email: juan.perez@estudiante.edu.bo
   Contraseña: password123

3. Hacer clic en "Iniciar Sesión"

4. Serás redirigido al Dashboard correspondiente según tu rol

### Como Estudiante:

1. Dashboard muestra:
   - Barra de búsqueda
   - Lista de videos disponibles para tu grado
   - Cards de videos con thumbnail, título, descripción

2. Para ver un video:
   - Hacer clic en cualquier card de video
   - Se abre el reproductor con el video
   - Controles: Play/Pause, Volumen, Pantalla completa

3. Para buscar:
   - Escribir en la barra de búsqueda
   - Hacer clic en "Buscar"
   - Se filtran los videos

### Como Docente:

1. Dashboard muestra:
   - Estadísticas: Total videos, Visualizaciones
   - Botón "Subir Nuevo Video"
   - Lista de tus videos

2. Para subir un video (pendiente de implementar UI):
   - Actualmente solo está el backend
   - Se puede hacer con Postman o curl

3. Ver estadísticas de tus videos

### Como Administrador:

1. Dashboard muestra:
   - Estadísticas generales
   - Total de usuarios, videos, visualizaciones
   - Acciones rápidas:
     * Gestionar Usuarios
     * Gestionar Videos
     * Ver Estadísticas

## ========================================================================
## PASO 9: PROBAR EL BACKEND CON POSTMAN (Opcional)
## ========================================================================

Si quieres probar el backend directamente:

1. Descargar Postman: https://www.postman.com/downloads/

2. Abrir Postman

3. Crear una nueva petición:

   ### Login:
   - Método: POST
   - URL: http://localhost/videoteca-backend/api/auth/login
   - Headers: Content-Type: application/json
   - Body (raw, JSON):
     {
       "email": "admin@sfxavier.edu.bo",
       "password": "password123"
     }
   - Hacer clic en "Send"
   - Copiar el "access_token" de la respuesta

   ### Obtener Videos:
   - Método: GET
   - URL: http://localhost/videoteca-backend/api/videos
   - Headers:
     * Content-Type: application/json
     * Authorization: Bearer {pegar_el_token_aquí}
   - Hacer clic en "Send"
   - Ver la lista de videos

## ========================================================================
## SOLUCIÓN DE PROBLEMAS COMUNES
## ========================================================================

### Problema 1: "Access denied for user 'root'@'localhost'"

Solución:
- El usuario de MySQL es incorrecto o tiene contraseña
- Editar .env del backend
- Cambiar DB_USER y DB_PASS según tu configuración de XAMPP
- Por defecto XAMPP: usuario=root, contraseña=vacía

### Problema 2: "404 Not Found" en el backend

Solución:
- Verificar que la carpeta esté en: C:\xampp\htdocs\videoteca-backend
- Verificar que Apache esté corriendo en XAMPP
- Verificar que el archivo .htaccess exista
- Habilitar mod_rewrite en Apache:
  * Abrir: C:\xampp\apache\conf\httpd.conf
  * Buscar: LoadModule rewrite_module
  * Quitar el # al inicio si está comentado
  * Reiniciar Apache

### Problema 3: Frontend no conecta con el backend

Solución:
- Verificar CORS en backend/config/cors.php
- Verificar URL en frontend/.env
- Verificar que sea: VITE_API_URL=http://localhost/videoteca-backend
- Sin /api al final
- Reiniciar el servidor de Vite (Ctrl+C y npm run dev)

### Problema 4: "Cannot find module" en frontend

Solución:
- Eliminar carpeta node_modules
- Eliminar package-lock.json
- Ejecutar: npm install
- Intentar de nuevo: npm run dev

### Problema 5: Videos no se reproducen

Solución:
- Verificar que existan videos en la BD
- Verificar que los archivos estén en: backend/uploads/videos/
- Verificar permisos de la carpeta uploads
- Verificar que el navegador soporte MP4

## ========================================================================
## ESTRUCTURA DE ARCHIVOS EN XAMPP
## ========================================================================

C:\xampp\htdocs\videoteca-backend\
├── config\
├── controllers\
├── models\
├── middleware\
├── routes\
├── utils\
├── uploads\
│   ├── videos\        ← Los videos se guardan aquí
│   └── thumbnails\    ← Los thumbnails se guardan aquí
├── .env               ← Configuración (CREAR ESTE ARCHIVO)
├── .htaccess
└── index.php

C:\ruta\a\tu\proyecto\videoteca\frontend-web\
├── src\
├── public\
├── .env               ← Configuración (CREAR ESTE ARCHIVO)
├── package.json
└── index.html

## ========================================================================
## COMANDOS ÚTILES
## ========================================================================

# Ver si Apache está corriendo (Windows)
netstat -an | findstr :80

# Ver si MySQL está corriendo (Windows)
netstat -an | findstr :3306

# Reiniciar Apache desde terminal
C:\xampp\apache\bin\httpd.exe -k restart

# Ver logs de Apache (si hay errores)
type C:\xampp\apache\logs\error.log

# Ver logs de PHP
type C:\xampp\php\logs\php_error_log

## ========================================================================
## SIGUIENTES PASOS
## ========================================================================

1. ✅ Sistema instalado y corriendo
2. Probar todas las funcionalidades
3. Subir videos reales (usando Postman por ahora)
4. Crear más usuarios de prueba
5. Personalizar el sistema según necesidades
6. Implementar componente de subida de videos en el frontend
7. Agregar más estadísticas y gráficos

## ========================================================================
## ACCESOS RÁPIDOS
## ========================================================================

Frontend:        http://localhost:5173
Backend API:     http://localhost/videoteca-backend/api
phpMyAdmin:      http://localhost/phpmyadmin
Panel XAMPP:     C:\xampp\xampp-control.exe

## ========================================================================
## CONTACTO Y SOPORTE
## ========================================================================

Estudiante: Roger Omar Luna Yujra
CI: 6734278 LP
Tutor: Lic. Vladimir Mamani
Institución: Instituto Técnico "ATSI" Bolivia

========================================================================
