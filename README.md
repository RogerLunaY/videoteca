# Sistema de Biblioteca Digital de Videos Educativos

## Unidad Educativa San Francisco Xavier - Okinawa Uno, Bolivia

### Información del Proyecto

- **Estudiante:** Roger Omar Luna Yujra (CI: 6734278 LP)
- **Tutor:** Lic. Vladimir Mamani
- **Institución:** Instituto Técnico "ATSI" Bolivia
- **Beneficiario:** U.E. San Francisco Xavier (Directora: Sor Grethel Alvarez Mamani)
- **Modalidad:** Técnico Humanístico Completo
- **Versión:** 1.0
- **Fecha:** 2024

## Descripción del Proyecto

Sistema web integral con aplicación móvil complementaria para streaming de videos educativos en servidor local (intranet) sin dependencia de internet. El sistema sirve a 142 estudiantes y 20 docentes de la Unidad Educativa San Francisco Xavier.

## Stack Tecnológico

### Backend
- **Lenguaje:** PHP 8.x
- **Base de Datos:** MySQL 8.x
- **Servidor Web:** Apache 2.4 (XAMPP/LAMP)
- **Autenticación:** JWT (JSON Web Tokens)
- **Streaming:** HTTP Live Streaming (HLS) / Range Requests

### Frontend Web
- **Framework:** React 18+
- **Build Tool:** Vite
- **Estilos:** TailwindCSS / Material-UI
- **Estado:** Context API
- **HTTP Client:** Axios
- **Formularios:** React Hook Form
- **Rutas:** React Router

### Frontend Móvil
- **Framework:** React Native
- **Plataforma:** Expo
- **Navegación:** React Navigation

## Características Principales

### Sistema de Roles
- **Administrador:** Acceso completo al sistema
- **Docente:** Gestión de videos propios y visualización de estadísticas
- **Estudiante:** Visualización y reproducción de videos según su grado

### Funcionalidades Core
- ✅ Autenticación y autorización con JWT
- ✅ Subida y gestión de videos educativos
- ✅ Streaming de videos con buffer adaptativo
- ✅ Sistema de categorías y búsqueda avanzada
- ✅ Comentarios y calificaciones
- ✅ Videos favoritos
- ✅ Historial de reproducciones
- ✅ Estadísticas y reportes
- ✅ Sistema de notificaciones
- ✅ Logging de actividades

## Requisitos del Sistema

### Servidor
- **Sistema Operativo:** Linux / Windows / macOS
- **PHP:** >= 8.0
- **MySQL:** >= 8.0
- **Apache:** >= 2.4 (con mod_rewrite habilitado)
- **FFmpeg:** Para procesamiento de videos
- **Espacio en Disco:** Mínimo 100GB para videos
- **RAM:** Mínimo 4GB (recomendado 8GB)

### Cliente
- **Navegadores:** Chrome, Firefox, Safari, Edge (últimas 2 versiones)
- **Dispositivos Móviles:** Android 6.0+ / iOS 11+

## Instalación

### 1. Preparar el Entorno

#### En Linux (Ubuntu/Debian)
```bash
# Actualizar sistema
sudo apt update && sudo apt upgrade -y

# Instalar Apache, MySQL y PHP
sudo apt install apache2 mysql-server php8.1 php8.1-mysql php8.1-mbstring php8.1-xml php8.1-curl -y

# Instalar FFmpeg para procesamiento de videos
sudo apt install ffmpeg -y

# Habilitar mod_rewrite de Apache
sudo a2enmod rewrite

# Reiniciar Apache
sudo systemctl restart apache2
```

#### En Windows (con XAMPP)
1. Descargar e instalar [XAMPP 8.x](https://www.apachefriends.org/)
2. Descargar e instalar [FFmpeg](https://ffmpeg.org/download.html)
3. Agregar FFmpeg al PATH del sistema
4. Iniciar Apache y MySQL desde el panel de control de XAMPP

### 2. Clonar el Repositorio

```bash
# Clonar el proyecto
git clone https://github.com/RogerLunaY/videoteca.git
cd videoteca
```

### 3. Configurar la Base de Datos

```bash
# Acceder a MySQL
mysql -u root -p

# Ejecutar el script de creación de base de datos
mysql -u root -p < database/schema.sql

# Cargar datos de prueba
mysql -u root -p < database/seed_data.sql
```

### 4. Configurar el Backend PHP

```bash
# Navegar al directorio del backend
cd backend

# Copiar archivo de configuración
cp .env.example .env

# Editar configuración (ajustar según tu entorno)
nano .env
```

**Configuración del archivo `.env`:**

```env
# Entorno
APP_ENV=development
DEBUG=true
TIMEZONE=America/La_Paz

# Base de Datos
DB_HOST=localhost
DB_PORT=3306
DB_NAME=videoteca_educativa
DB_USER=root
DB_PASS=
DB_CHARSET=utf8mb4

# JWT
JWT_SECRET=tu_clave_secreta_muy_segura_aqui_minimo_32_caracteres
JWT_ISSUER=videoteca.sfxavier.edu.bo
JWT_AUDIENCE=videoteca.sfxavier.edu.bo
JWT_ACCESS_EXPIRATION=3600
JWT_REFRESH_EXPIRATION=604800

# CORS
CORS_ALLOWED_ORIGINS=http://localhost:5173,http://localhost:3000

# Uploads
MAX_VIDEO_SIZE=524288000
```

### 5. Configurar Apache

#### Opción A: Virtual Host (Recomendado)

```apache
<VirtualHost *:80>
    ServerName videoteca.local
    DocumentRoot /ruta/completa/al/proyecto/videoteca/backend

    <Directory /ruta/completa/al/proyecto/videoteca/backend>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/videoteca-error.log
    CustomLog ${APACHE_LOG_DIR}/videoteca-access.log combined
</VirtualHost>
```

**Agregar al archivo hosts:**
```bash
sudo nano /etc/hosts

# Agregar línea:
127.0.0.1   videoteca.local
```

#### Opción B: Copiar a htdocs (XAMPP)

```bash
# En Windows con XAMPP
# Copiar la carpeta backend a C:\xampp\htdocs\videoteca
```

### 6. Configurar el Frontend Web

```bash
# Navegar al directorio del frontend
cd frontend-web

# Instalar dependencias (requiere Node.js 16+)
npm install

# Copiar archivo de configuración
cp .env.example .env

# Editar configuración
nano .env
```

**Configuración del archivo `.env` del frontend:**

```env
VITE_API_URL=http://videoteca.local/api
VITE_APP_NAME=Videoteca San Francisco Xavier
```

### 7. Iniciar el Frontend

```bash
# Modo desarrollo
npm run dev

# El frontend estará disponible en http://localhost:5173
```

### 8. Configurar la Aplicación Móvil (Opcional)

```bash
# Navegar al directorio de la app móvil
cd mobile-app

# Instalar dependencias
npm install

# Iniciar Expo
npx expo start

# Escanear el código QR con la app Expo Go en tu dispositivo
```

## Credenciales de Acceso (Datos de Prueba)

### Administrador
- **Email:** admin@sfxavier.edu.bo
- **Contraseña:** password123

### Docente
- **Email:** maria.gonzalez@sfxavier.edu.bo
- **Contraseña:** password123

### Estudiante
- **Email:** juan.perez@estudiante.edu.bo
- **Contraseña:** password123

## Estructura del Proyecto

```
videoteca/
├── backend/                    # Backend PHP
│   ├── config/                 # Configuraciones
│   │   ├── database.php        # Conexión a BD
│   │   ├── jwt.php             # Configuración JWT
│   │   └── cors.php            # Configuración CORS
│   ├── controllers/            # Controladores MVC
│   │   ├── AuthController.php
│   │   ├── VideoController.php
│   │   └── UsuarioController.php
│   ├── models/                 # Modelos de datos
│   │   ├── Usuario.php
│   │   └── Video.php
│   ├── middleware/             # Middleware
│   │   ├── AuthMiddleware.php
│   │   ├── RoleMiddleware.php
│   │   └── ValidationMiddleware.php
│   ├── routes/                 # Definición de rutas
│   │   └── api.php
│   ├── utils/                  # Utilidades
│   │   ├── JWTHandler.php
│   │   ├── FileHandler.php
│   │   ├── VideoProcessor.php
│   │   └── Logger.php
│   ├── uploads/                # Archivos subidos
│   │   ├── videos/
│   │   └── thumbnails/
│   ├── .env                    # Variables de entorno
│   ├── .htaccess               # Configuración Apache
│   └── index.php               # Entry point
│
├── frontend-web/               # Frontend React
│   ├── public/
│   ├── src/
│   │   ├── components/         # Componentes React
│   │   │   ├── Auth/           # Login, Register
│   │   │   ├── Dashboard/      # Dashboards por rol
│   │   │   ├── Video/          # Componentes de video
│   │   │   ├── Common/         # Navbar, Sidebar, Footer
│   │   │   └── Statistics/     # Gráficos y estadísticas
│   │   ├── pages/              # Páginas de la app
│   │   ├── services/           # Servicios API
│   │   │   ├── api.js
│   │   │   ├── authService.js
│   │   │   └── videoService.js
│   │   ├── context/            # Context API
│   │   │   └── AuthContext.jsx
│   │   ├── App.jsx             # Componente principal
│   │   └── main.jsx            # Entry point
│   ├── .env                    # Variables de entorno
│   ├── package.json
│   └── vite.config.js
│
├── mobile-app/                 # App móvil React Native
│   ├── src/
│   │   ├── screens/            # Pantallas
│   │   ├── components/         # Componentes
│   │   ├── navigation/         # Navegación
│   │   └── services/           # Servicios API
│   ├── App.js
│   └── package.json
│
├── database/                   # Scripts SQL
│   ├── schema.sql              # Estructura de BD
│   └── seed_data.sql           # Datos de prueba
│
└── README.md                   # Este archivo
```

## API Endpoints

### Autenticación

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| POST | /api/auth/login | Iniciar sesión |
| POST | /api/auth/register | Registrar usuario |
| POST | /api/auth/logout | Cerrar sesión |
| POST | /api/auth/refresh-token | Refrescar token |
| GET | /api/auth/me | Obtener usuario actual |

### Videos

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | /api/videos | Listar videos |
| GET | /api/videos/{id} | Obtener video por ID |
| POST | /api/videos | Crear video |
| PUT | /api/videos/{id} | Actualizar video |
| DELETE | /api/videos/{id} | Eliminar video |
| GET | /api/videos/{id}/stream | Transmitir video |
| GET | /api/videos/buscar?q={query} | Buscar videos |

### Usuarios

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | /api/usuarios | Listar usuarios |
| GET | /api/usuarios/{id} | Obtener usuario por ID |
| POST | /api/usuarios | Crear usuario |
| PUT | /api/usuarios/{id} | Actualizar usuario |
| DELETE | /api/usuarios/{id} | Eliminar usuario |

### Estadísticas

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | /api/estadisticas/generales | Estadísticas generales |
| GET | /api/estadisticas/videos-populares | Videos más populares |
| GET | /api/estadisticas/actividad-usuarios | Actividad de usuarios |

## Uso del Sistema

### Para Estudiantes

1. **Iniciar Sesión:** Acceder con credenciales proporcionadas
2. **Explorar Videos:** Navegar por categorías, materias o usar el buscador
3. **Reproducir Videos:** Click en un video para reproducirlo
4. **Comentar:** Dejar comentarios y calificaciones
5. **Favoritos:** Guardar videos favoritos para acceso rápido
6. **Historial:** Ver el historial de videos vistos

### Para Docentes

1. **Subir Videos:**
   - Click en "Subir Video"
   - Completar formulario (título, descripción, materia, grado)
   - Seleccionar archivo de video
   - El sistema procesa automáticamente el video y genera thumbnail

2. **Gestionar Videos:**
   - Ver lista de videos propios
   - Editar información de videos
   - Eliminar videos

3. **Ver Estadísticas:**
   - Visualizaciones de cada video
   - Calificaciones y comentarios
   - Tendencias de consumo

### Para Administradores

1. **Gestionar Usuarios:**
   - Crear, editar, eliminar usuarios
   - Asignar roles y permisos
   - Ver actividad de usuarios

2. **Gestionar Contenido:**
   - Aprobar/rechazar videos
   - Gestionar categorías y materias
   - Moderar comentarios

3. **Estadísticas Completas:**
   - Dashboard con métricas generales
   - Reportes exportables
   - Análisis de uso del sistema

## Mantenimiento

### Limpieza de Logs

```bash
# Acceder a MySQL
mysql -u root -p videoteca_educativa

# Limpiar logs antiguos (mantener últimos 90 días)
CALL sp_limpiar_tokens_expirados();
DELETE FROM logs_sistema WHERE fecha < DATE_SUB(NOW(), INTERVAL 90 DAY);
```

### Backup de Base de Datos

```bash
# Backup completo
mysqldump -u root -p videoteca_educativa > backup_$(date +%Y%m%d).sql

# Backup solo estructura
mysqldump -u root -p --no-data videoteca_educativa > estructura_$(date +%Y%m%d).sql
```

### Limpieza de Videos

```bash
# Verificar espacio en disco
df -h

# Limpiar videos eliminados de la BD pero que permanecen en disco
# (crear script personalizado según necesidades)
```

## Solución de Problemas

### El backend no responde

```bash
# Verificar que Apache esté corriendo
sudo systemctl status apache2

# Verificar logs de error
tail -f /var/log/apache2/error.log

# Verificar permisos
sudo chown -R www-data:www-data /ruta/al/proyecto/backend/uploads
sudo chmod -R 755 /ruta/al/proyecto/backend/uploads
```

### Error de conexión a MySQL

```bash
# Verificar que MySQL esté corriendo
sudo systemctl status mysql

# Verificar credenciales en .env
# Verificar que la BD existe
mysql -u root -p -e "SHOW DATABASES;"
```

### Videos no se reproducen

```bash
# Verificar que FFmpeg esté instalado
ffmpeg -version

# Verificar permisos en directorio uploads
ls -la backend/uploads/videos

# Verificar ruta del archivo en la BD
mysql -u root -p videoteca_educativa -e "SELECT id, titulo, archivo_path FROM videos LIMIT 5;"
```

## Seguridad

### Mejores Prácticas Implementadas

- ✅ Contraseñas hasheadas con bcrypt (costo 12)
- ✅ Tokens JWT con expiración
- ✅ Validación de inputs (prevención XSS, SQL Injection)
- ✅ CORS configurado
- ✅ Headers de seguridad
- ✅ Limitación de intentos de login
- ✅ Logs de actividad
- ✅ Sanitización de nombres de archivos
- ✅ Validación de tipos MIME de archivos

### Recomendaciones para Producción

1. **Cambiar clave JWT:** Generar una clave única y compleja
2. **Deshabilitar debug:** Establecer `DEBUG=false` en .env
3. **HTTPS:** Configurar certificado SSL
4. **Firewall:** Configurar firewall del servidor
5. **Backups automáticos:** Programar backups diarios
6. **Actualizar dependencias:** Mantener PHP, MySQL y librerías actualizadas

## Contribuir

Este proyecto es parte de un trabajo de grado. Para sugerencias o mejoras:

1. Fork el proyecto
2. Crear una rama (`git checkout -b feature/mejora`)
3. Commit cambios (`git commit -m 'Agregar mejora'`)
4. Push a la rama (`git push origin feature/mejora`)
5. Abrir Pull Request

## Licencia

Este proyecto es propiedad de la Unidad Educativa San Francisco Xavier y se desarrolla bajo licencia educativa.

## Contacto

- **Estudiante:** Roger Omar Luna Yujra
- **Email:** roger.luna@estudiante.edu.bo
- **Institución:** Instituto Técnico "ATSI" Bolivia
- **Tutor:** Lic. Vladimir Mamani

## Agradecimientos

- Unidad Educativa San Francisco Xavier
- Instituto Técnico "ATSI" Bolivia
- Lic. Vladimir Mamani (Tutor)
- Sor Grethel Alvarez Mamani (Directora U.E.)

---

**Desarrollado con ❤️ para la educación boliviana**

*Última actualización: 2024*
