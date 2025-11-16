# Instalación de Base de Datos - Sistema de Videoteca

## 📋 Requisitos Previos

Antes de instalar la base de datos, asegúrate de que:

1. ✅ **XAMPP esté instalado** en `C:\xampp`
2. ✅ **MySQL esté corriendo** (verde en XAMPP Control Panel)
3. ✅ **Apache esté corriendo** (verde en XAMPP Control Panel)

## 🚀 Método 1: Instalación Automática (RECOMENDADO)

### Paso 1: Ejecutar el Script

1. Ve a la carpeta del proyecto
2. **Doble clic** en `instalar-base-datos.bat`
3. Presiona **cualquier tecla** para continuar cuando te lo pida
4. Espera a que termine (30-60 segundos)

### Resultado Esperado

```
============================================
Instalacion completada exitosamente!
============================================

Base de datos: videoteca
Usuario: root
Password: (vacio)

La base de datos incluye:
- 14 tablas creadas
- Triggers automaticos
- Vistas optimizadas
- Procedimientos almacenados
- Datos de prueba (10 usuarios, 21 videos)
```

## 🔧 Método 2: Instalación Manual

Si el script automático no funciona, sigue estos pasos:

### Paso 1: Abrir phpMyAdmin

1. Abre tu navegador
2. Ve a: `http://localhost/phpmyadmin`
3. Usuario: `root`
4. Password: *(dejar vacío)*

### Paso 2: Crear la Base de Datos

1. Click en **"Nueva"** en el panel izquierdo
2. Nombre: `videoteca`
3. Cotejamiento: `utf8mb4_unicode_ci`
4. Click en **"Crear"**

### Paso 3: Importar el Schema

1. Click en la base de datos `videoteca` (panel izquierdo)
2. Click en la pestaña **"Importar"**
3. Click en **"Seleccionar archivo"**
4. Navega a: `tu-proyecto/database/instalacion_completa.sql`
5. Click en **"Continuar"** al final de la página
6. Espera a que termine (puede tardar 30-60 segundos)

### Paso 4: Verificar

Deberías ver:

```
Importación finalizada correctamente,
se han ejecutado XXX consultas
```

En el panel izquierdo deberías ver estas tablas:
- usuarios
- videos
- grados
- materias
- categorias
- roles
- reproducciones
- comentarios
- favoritos
- calificaciones
- video_categorias
- historial_cambios
- registros_logs
- notificaciones

## 📝 Configurar el Archivo .env

Después de instalar la base de datos:

### Paso 1: Crear el Archivo .env

1. Ve a: `C:\xampp\htdocs\videoteca-backend\`
2. Si existe `.env.example`, **cópialo** y renómbralo a `.env`
3. Si no existe, créalo con el siguiente contenido:

```env
# Configuración de Base de Datos
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=videoteca
DB_USERNAME=root
DB_PASSWORD=

# Configuración de JWT
JWT_SECRET=tu_clave_secreta_muy_segura_cambiar_en_produccion_2024
JWT_ISSUER=videoteca-api
JWT_AUDIENCE=videoteca-app
JWT_ACCESS_TOKEN_EXPIRATION=3600
JWT_REFRESH_TOKEN_EXPIRATION=604800

# Configuración de CORS
CORS_ALLOWED_ORIGINS=http://localhost:5173,http://localhost:3000

# Configuración de la Aplicación
APP_ENV=development
DEBUG=true
TIMEZONE=America/La_Paz

# Configuración de Videos
UPLOAD_MAX_SIZE=524288000
VIDEO_STORAGE_PATH=storage/videos
THUMBNAIL_STORAGE_PATH=storage/thumbnails
```

### Paso 2: Guardar el Archivo

1. **Guarda** el archivo como `.env` (con el punto al inicio)
2. Asegúrate de que esté en: `C:\xampp\htdocs\videoteca-backend\.env`

## 🧪 Verificar la Instalación

### Opción 1: Desde el Navegador

Abre en tu navegador:
```
http://localhost/videoteca-backend/api/public/grados
```

**✅ Deberías ver:** Un JSON con los grados escolares (1° a 6° de primaria, 1° a 6° de secundaria)

### Opción 2: Desde phpMyAdmin

1. Ve a: `http://localhost/phpmyadmin`
2. Click en `videoteca` (panel izquierdo)
3. Click en tabla `usuarios`
4. Click en pestaña **"Examinar"**
5. Deberías ver 10 usuarios (1 admin, 5 docentes, 4 estudiantes)

## 👥 Credenciales de Prueba

Después de la instalación, puedes usar estas credenciales para probar:

### Administrador
```
Email: admin@sanfranciscoxavier.edu.bo
Password: password123
```

### Docente
```
Email: maria.lopez@sanfranciscoxavier.edu.bo
Password: password123
```

### Otros Docentes
```
juan.perez@sanfranciscoxavier.edu.bo - password123
carlos.rodriguez@sanfranciscoxavier.edu.bo - password123
ana.martinez@sanfranciscoxavier.edu.bo - password123
luis.garcia@sanfranciscoxavier.edu.bo - password123
sofia.fernandez@sanfranciscoxavier.edu.bo - password123
```

**Nota:** En el sistema V2.0, los **estudiantes NO necesitan login**. Pueden ver videos directamente desde la página principal.

## 📊 Estructura de la Base de Datos

La base de datos incluye:

### Tablas Principales
- **usuarios** - Docentes y administradores (sin estudiantes)
- **videos** - Videos educativos
- **grados** - Niveles escolares (primaria y secundaria)
- **materias** - Asignaturas (matemáticas, ciencias, etc.)
- **categorias** - Categorías de videos

### Tablas de Relación
- **video_categorias** - Relación videos-categorías
- **reproducciones** - Historial de visualizaciones (anónimas)
- **comentarios** - Comentarios de docentes
- **favoritos** - Videos favoritos de docentes
- **calificaciones** - Calificaciones de videos

### Tablas del Sistema
- **roles** - Roles del sistema (admin, docente)
- **historial_cambios** - Auditoría de cambios
- **registros_logs** - Logs del sistema
- **notificaciones** - Sistema de notificaciones

### Funcionalidades Automáticas
- **Triggers** - Actualización automática de contadores
- **Vistas** - Consultas optimizadas precalculadas
- **Procedimientos** - Estadísticas y reportes
- **Eventos** - Tareas programadas (limpieza, etc.)

## 🔍 Solución de Problemas

### "MySQL no está corriendo"
**Solución:**
1. Abre XAMPP Control Panel
2. Click en **"Start"** junto a MySQL
3. Espera a que se ponga verde
4. Ejecuta el script nuevamente

### "Access denied for user 'root'"
**Solución:**
1. Abre phpMyAdmin
2. Ve a "Cuentas de usuario"
3. Verifica que el usuario `root` no tenga password
4. Si tiene password, modifica el script o `.env` con el password correcto

### "Table already exists"
**Solución:**
Esta es una advertencia normal si ya ejecutaste el script antes. La base de datos ya está instalada.

### "Can't connect to MySQL server"
**Solución:**
1. Verifica que XAMPP esté instalado en `C:\xampp`
2. Verifica que MySQL esté corriendo
3. Intenta reiniciar MySQL en XAMPP Control Panel

## 📞 Siguiente Paso

Después de instalar la base de datos:

1. ✅ Verifica el archivo `.env`
2. ✅ Prueba la API: `http://localhost/videoteca-backend/`
3. ✅ Inicia el frontend: `cd frontend-web && npm run dev`
4. ✅ Accede a: `http://localhost:5173`

---

**¡La base de datos está lista para usar!** 🎉
