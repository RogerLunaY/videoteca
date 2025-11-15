# 📸 GUÍA VISUAL PASO A PASO - INSTALACIÓN EN XAMPP

## Sistema de Biblioteca Digital de Videos Educativos - U.E. San Francisco Xavier

---

## 🎯 PASO 1: INSTALAR Y CONFIGURAR XAMPP

### 1.1 Descargar XAMPP
```
1. Ir a: https://www.apachefriends.org/
2. Descargar: XAMPP para Windows (PHP 8.x)
3. Ejecutar el instalador
4. Instalar en: C:\xampp (ruta por defecto)
```

### 1.2 Iniciar XAMPP Control Panel
```
┌─────────────────────────────────────────────┐
│  XAMPP Control Panel v3.3.0                 │
├─────────────────────────────────────────────┤
│  Module    Status   Actions                 │
│  ────────  ──────   ─────────               │
│  Apache    [Stop]   [Start] [Admin]         │  ← Hacer clic en Start
│  MySQL     [Stop]   [Start] [Admin]         │  ← Hacer clic en Start
│  FileZilla [Stop]   [Start] [Admin]         │
│  Mercury   [Stop]   [Start] [Admin]         │
│  Tomcat    [Stop]   [Start] [Admin]         │
└─────────────────────────────────────────────┘
```

**Resultado esperado:**
```
┌─────────────────────────────────────────────┐
│  Apache    [Running ✓] [Stop] [Admin]       │  ← Verde
│  MySQL     [Running ✓] [Stop] [Admin]       │  ← Verde
└─────────────────────────────────────────────┘
```

---

## 🎯 PASO 2: EJECUTAR EL INSTALADOR AUTOMÁTICO

### 2.1 Abrir Terminal en el Proyecto
```
1. Abrir el Explorador de Windows
2. Navegar a: C:\Users\TuUsuario\...\videoteca
3. Hacer clic derecho en espacio vacío
4. Seleccionar: "Abrir en Terminal" o "Git Bash Here"
```

### 2.2 Ejecutar el Script de Instalación
```cmd
C:\...\videoteca> instalar-windows.bat
```

**Salida esperada:**
```
========================================================================
  INSTALADOR DEL SISTEMA DE VIDEOTECA EDUCATIVA
  U.E. San Francisco Xavier
========================================================================

[1/6] Verificando XAMPP... OK

[2/6] Copiando backend a XAMPP...
Backend copiado correctamente

[3/6] Configurando backend...
Carpetas de uploads creadas

[4/6] Configurando frontend...
Archivo .env del frontend creado

[5/6] Verificando Node.js...
Node.js detectado
Instalando dependencias del frontend...
✓ Dependencias instaladas

[6/6] Instalacion completada
```

---

## 🎯 PASO 3: CONFIGURAR LA BASE DE DATOS

### 3.1 Abrir phpMyAdmin
```
1. Abrir el navegador (Chrome, Firefox, Edge)
2. Escribir en la barra de direcciones:
   http://localhost/phpmyadmin
3. Presionar Enter
```

**Pantalla de phpMyAdmin:**
```
┌────────────────────────────────────────────────────────┐
│  phpMyAdmin                                            │
├────────────┬───────────────────────────────────────────┤
│ Bases de   │  Servidor de bases de datos              │
│ datos:     │  ────────────────────────────            │
│            │  Servidor: localhost via TCP/IP          │
│ ► Nueva    │  Tipo de servidor: MySQL                 │
│ ► phpmyadmin│ Versión: 8.0.x                          │
│            │                                           │
└────────────┴───────────────────────────────────────────┘
```

### 3.2 Crear la Base de Datos
```
1. Hacer clic en "Nueva" (panel izquierdo)
2. Nombre de la base de datos: videoteca_educativa
3. Cotejamiento: utf8mb4_unicode_ci
4. Hacer clic en "Crear"
```

**Formulario:**
```
┌─────────────────────────────────────┐
│ Crear base de datos                 │
├─────────────────────────────────────┤
│ Nombre: [videoteca_educativa]       │
│ Cotejamiento: [utf8mb4_unicode_ci ▼]│
│                                     │
│         [Crear]                     │
└─────────────────────────────────────┘
```

### 3.3 Importar el Schema
```
1. Seleccionar "videoteca_educativa" (panel izquierdo)
2. Hacer clic en la pestaña "SQL" (arriba)
3. Hacer clic en "Seleccionar archivo"
4. Navegar a: C:\...\videoteca\database\schema.sql
5. Seleccionar el archivo
6. Hacer clic en "Continuar" (abajo a la derecha)
7. Esperar 10-15 segundos
```

**Resultado exitoso:**
```
┌─────────────────────────────────────────┐
│ ✓ MySQL ha devuelto un resultado vacío │
│ (sin filas).                            │
│                                         │
│ 14 tablas creadas exitosamente         │
└─────────────────────────────────────────┘
```

### 3.4 Importar los Datos de Prueba
```
1. Hacer clic nuevamente en "SQL"
2. Hacer clic en "Seleccionar archivo"
3. Navegar a: C:\...\videoteca\database\seed_data.sql
4. Seleccionar el archivo
5. Hacer clic en "Continuar"
6. Esperar 5-10 segundos
```

**Verificar que se cargaron los datos:**
```
1. Panel izquierdo → videoteca_educativa → usuarios (10)
2. Panel izquierdo → videoteca_educativa → videos (21)
3. Panel izquierdo → videoteca_educativa → materias (10)
```

---

## 🎯 PASO 4: VERIFICAR EL BACKEND

### 4.1 Probar la API
```
1. Abrir el navegador
2. Ir a: http://localhost/videoteca-backend/api/videos
```

**Respuesta esperada (JSON):**
```json
{
  "success": false,
  "message": "Token de autenticación no proporcionado",
  "error": true
}
```

✓ **¡Perfecto! Esto significa que el backend funciona correctamente**

### 4.2 Verificar Archivos del Backend
```
Explorador de Windows:
C:\xampp\htdocs\videoteca-backend\

Deberías ver:
├── config/
├── controllers/
├── models/
├── middleware/
├── routes/
├── utils/
├── uploads/
│   ├── videos/
│   └── thumbnails/
├── .env          ← Este archivo debe existir
├── .htaccess
└── index.php
```

---

## 🎯 PASO 5: INICIAR EL FRONTEND

### 5.1 Abrir Terminal en Frontend
```
1. Explorador de Windows
2. Navegar a: C:\Users\TuUsuario\...\videoteca\frontend-web
3. Hacer clic derecho → "Abrir en Terminal"
```

### 5.2 Instalar Dependencias (si no se hizo automáticamente)
```cmd
C:\...\frontend-web> npm install
```

**Salida esperada:**
```
npm WARN deprecated ...
added 245 packages in 45s
```

### 5.3 Iniciar el Servidor de Desarrollo
```cmd
C:\...\frontend-web> npm run dev
```

**Salida esperada:**
```
  VITE v5.0.8  ready in 523 ms

  ➜  Local:   http://localhost:5173/
  ➜  Network: use --host to expose
  ➜  press h + enter to show help
```

✓ **¡El frontend está corriendo!**

---

## 🎯 PASO 6: ACCEDER AL SISTEMA

### 6.1 Abrir en el Navegador
```
URL: http://localhost:5173
```

**Pantalla de Login:**
```
┌──────────────────────────────────────────┐
│                                          │
│           🎬 (Ícono de Video)            │
│                                          │
│      Videoteca Educativa                 │
│   U.E. San Francisco Xavier              │
│                                          │
├──────────────────────────────────────────┤
│         Iniciar Sesión                   │
│                                          │
│  Correo Electrónico                      │
│  ┌────────────────────────────────────┐  │
│  │ 📧 correo@ejemplo.com              │  │
│  └────────────────────────────────────┘  │
│                                          │
│  Contraseña                              │
│  ┌────────────────────────────────────┐  │
│  │ 🔒 ••••••••                        │  │
│  └────────────────────────────────────┘  │
│                                          │
│     [    Iniciar Sesión    ]             │
│                                          │
│  ¿No tienes cuenta? Regístrate aquí      │
│                                          │
├──────────────────────────────────────────┤
│    Credenciales de Prueba:               │
│  Administrador: admin@sfxavier.edu.bo    │
│  Docente: maria.gonzalez@sfxavier.edu.bo │
│  Estudiante: juan.perez@estudiante.edu.bo│
│  Contraseña: password123                 │
└──────────────────────────────────────────┘
```

### 6.2 Iniciar Sesión como Estudiante
```
1. Email: juan.perez@estudiante.edu.bo
2. Contraseña: password123
3. Hacer clic en "Iniciar Sesión"
```

**Dashboard de Estudiante:**
```
┌────────────────────────────────────────────────────────┐
│  🎬 Videoteca SFX    Juan Pérez [Estudiante]  [Salir] │
├────────────────────────────────────────────────────────┤
│                                                        │
│  Bienvenido, Juan Pérez                                │
│  Explora los videos educativos disponibles            │
│                                                        │
│  ┌──────────────────────────────────────────────┐     │
│  │ 🔍 Buscar videos...            [Buscar]      │     │
│  └──────────────────────────────────────────────┘     │
│                                                        │
│  ┌─────────┐  ┌─────────┐  ┌─────────┐  ┌─────────┐ │
│  │ [IMG]   │  │ [IMG]   │  │ [IMG]   │  │ [IMG]   │ │
│  │ Video 1 │  │ Video 2 │  │ Video 3 │  │ Video 4 │ │
│  │ ★★★★★   │  │ ★★★★☆   │  │ ★★★★★   │  │ ★★★☆☆   │ │
│  │ 👁 45   │  │ 👁 67   │  │ 👁 89   │  │ 👁 34   │ │
│  │[Mate]   │  │[Lenguaje│  │[Ciencias│  │[Historia│ │
│  └─────────┘  └─────────┘  └─────────┘  └─────────┘ │
│                                                        │
└────────────────────────────────────────────────────────┘
```

### 6.3 Ver un Video
```
1. Hacer clic en cualquier video
2. Se abre el reproductor
```

**Pantalla del Reproductor:**
```
┌────────────────────────────────────────────────────────┐
│  🎬 Videoteca SFX    Juan Pérez [Estudiante]  [Salir] │
├────────────────────────────────────────────────────────┤
│  ← Volver                                              │
│                                                        │
│  ┌──────────────────────────────────────────────────┐ │
│  │                                                  │ │
│  │              ▶ VIDEO PLAYER                      │ │
│  │                                                  │ │
│  │  ────────●────────────────────                  │ │
│  │  🔊 ─●─  ⏸  ▶  🔇  ⛶                          │ │
│  └──────────────────────────────────────────────────┘ │
│                                                        │
│  Ecuaciones de Primer Grado - Introducción            │
│  ────────────────────────────────────────────         │
│  👁 45 visualizaciones  ★ 5.0 (12 calificaciones)     │
│  📅 15 de Noviembre, 2025                             │
│                                                        │
│  Descripción:                                          │
│  Aprende a resolver ecuaciones lineales de forma      │
│  sencilla. Este video cubre los conceptos básicos...  │
│                                                        │
│  Materia: Matemáticas                                 │
│  Grado: 1ro de Secundaria                             │
│  Docente: María González                              │
│                                                        │
└────────────────────────────────────────────────────────┘
```

---

## 🎯 PASO 7: PROBAR OTROS ROLES

### 7.1 Cerrar Sesión
```
1. Hacer clic en "Salir" (esquina superior derecha)
2. Vuelve a la pantalla de login
```

### 7.2 Iniciar como Docente
```
Email: maria.gonzalez@sfxavier.edu.bo
Contraseña: password123
```

**Dashboard de Docente:**
```
┌────────────────────────────────────────────────────────┐
│  Panel de Docente - María González                     │
│  Gestiona tus videos educativos                        │
│                                                        │
│  ┌───────────────┐  ┌───────────────┐  ┌───────────┐ │
│  │ Total Videos  │  │Visualizaciones│  │ 📤 Subir  │ │
│  │      4        │  │     245       │  │  Nuevo    │ │
│  │   🎬          │  │   📊          │  │  Video    │ │
│  └───────────────┘  └───────────────┘  └───────────┘ │
│                                                        │
│  Mis Videos                                            │
│  ┌─────────┐  ┌─────────┐  ┌─────────┐  ┌─────────┐ │
│  │ Video 1 │  │ Video 2 │  │ Video 3 │  │ Video 4 │ │
│  │ 👁 45   │  │ 👁 67   │  │ 👁 89   │  │ 👁 34   │ │
│  └─────────┘  └─────────┘  └─────────┘  └─────────┘ │
└────────────────────────────────────────────────────────┘
```

### 7.3 Iniciar como Administrador
```
Email: admin@sfxavier.edu.bo
Contraseña: password123
```

**Dashboard de Administrador:**
```
┌────────────────────────────────────────────────────────┐
│  Panel de Administración                               │
│  Bienvenido, Admin Sistema                             │
│                                                        │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐ │
│  │ Usuarios │ │  Videos  │ │ Visual.  │ │Almacen.  │ │
│  │    0     │ │    0     │ │    0     │ │  2.5 GB  │ │
│  │    👥    │ │    🎬    │ │    📈    │ │    💾    │ │
│  └──────────┘ └──────────┘ └──────────┘ └──────────┘ │
│                                                        │
│  Acciones Rápidas                                      │
│  ┌──────────────────────────────────────────────────┐ │
│  │  [Gestionar Usuarios]                            │ │
│  │  [Gestionar Videos]                              │ │
│  │  [Ver Estadísticas Completas]                    │ │
│  └──────────────────────────────────────────────────┘ │
└────────────────────────────────────────────────────────┘
```

---

## 🎯 RESUMEN DE URLS

```
┌──────────────────────────────────────────────────────┐
│  ACCESOS RÁPIDOS                                     │
├──────────────────────────────────────────────────────┤
│  Frontend (App Web)                                  │
│  → http://localhost:5173                             │
│                                                      │
│  Backend (API)                                       │
│  → http://localhost/videoteca-backend/api            │
│                                                      │
│  phpMyAdmin (Base de Datos)                          │
│  → http://localhost/phpmyadmin                       │
│                                                      │
│  Panel de Control XAMPP                              │
│  → C:\xampp\xampp-control.exe                        │
└──────────────────────────────────────────────────────┘
```

---

## 🎯 SOLUCIÓN DE PROBLEMAS VISUALES

### ❌ Problema: Pantalla blanca en el frontend

**Solución:**
```
1. Abrir Consola del Navegador (F12)
2. Ver si hay errores en "Console"
3. Verificar que el backend esté corriendo
4. Verificar archivo .env del frontend
```

### ❌ Problema: "Cannot connect to database"

**Pantalla de error:**
```
┌────────────────────────────────────────┐
│  ❌ Error                              │
│  Cannot connect to database            │
│  Access denied for user 'root'         │
└────────────────────────────────────────┘
```

**Solución:**
```
1. Abrir: C:\xampp\htdocs\videoteca-backend\.env
2. Verificar:
   DB_HOST=localhost
   DB_USER=root
   DB_PASS=          ← Vacío si XAMPP por defecto
   DB_NAME=videoteca_educativa
3. Guardar y recargar la página
```

### ❌ Problema: Videos no se reproducen

**Solución:**
```
1. Verificar que existan videos en la BD
2. Navegar a: C:\xampp\htdocs\videoteca-backend\uploads\videos\
3. Verificar que haya archivos .mp4
4. Si no hay, los videos de prueba son solo metadata
   (necesitas subir videos reales)
```

---

## ✅ VERIFICACIÓN FINAL

### Lista de Comprobación:

```
✓ XAMPP instalado y corriendo (Apache ✓ MySQL ✓)
✓ Backend copiado a htdocs
✓ Base de datos creada: videoteca_educativa
✓ Tablas creadas (14 tablas)
✓ Datos de prueba cargados (10 usuarios, 21 videos)
✓ Frontend con dependencias instaladas
✓ Frontend corriendo en: http://localhost:5173
✓ Login funciona con credenciales de prueba
✓ Puedes ver el dashboard según tu rol
✓ Puedes navegar entre videos
```

---

## 📞 SOPORTE

Si tienes problemas, verifica:
1. XAMPP Control Panel → Apache y MySQL en verde
2. phpMyAdmin → base de datos videoteca_educativa existe
3. Terminal del frontend → servidor corriendo sin errores
4. Consola del navegador (F12) → sin errores rojos

**Estudiante:** Roger Omar Luna Yujra
**Tutor:** Lic. Vladimir Mamani
**Institución:** Instituto Técnico "ATSI" Bolivia

---

¡Sistema instalado y listo para usar! 🎉
