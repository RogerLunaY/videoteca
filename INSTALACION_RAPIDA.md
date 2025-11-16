# 🚀 Instalación Rápida - Sistema de Videoteca

## ⚡ Instalación en 1 Paso (RECOMENDADO)

### Opción 1: Todo en Uno

**Instala TODO automáticamente** (backend + base de datos + configuración):

1. Abre XAMPP Control Panel
2. Inicia **Apache** y **MySQL** (botón START en ambos)
3. Haz **doble clic** en: `instalar-todo.bat`
4. Espera 1-2 minutos
5. ¡Listo!

**Prueba que funcione:**
- Abre: `http://localhost/videoteca-backend/`
- Deberías ver un JSON con información de la API

---

## 📦 Instalación Paso a Paso (Alternativa)

Si prefieres instalar cada componente por separado:

### 1️⃣ Instalar Backend

Doble clic en: `actualizar-xampp.bat`

**Qué hace:**
- Copia todos los archivos del backend a XAMPP
- Los coloca en `C:\xampp\htdocs\videoteca-backend\`

### 2️⃣ Instalar Base de Datos

Doble clic en: `instalar-base-datos.bat`

**Qué hace:**
- Crea la base de datos `videoteca` en MySQL
- Importa el schema completo (14 tablas)
- Importa datos de prueba (10 usuarios, 21 videos)

**Nota:** MySQL debe estar corriendo en XAMPP

### 3️⃣ Configurar .env

**Manual:**
1. Ve a: `C:\xampp\htdocs\videoteca-backend\`
2. Copia `.env.example`
3. Renómbralo a `.env`

**Automático:**
El script `instalar-todo.bat` hace esto automáticamente.

---

## 🧪 Verificación

Después de instalar, verifica cada componente:

### ✅ Backend

```
http://localhost/videoteca-backend/
```
**Esperado:** JSON con información de la API

### ✅ Base de Datos

```
http://localhost/phpmyadmin
```
1. Usuario: `root`, Password: *(vacío)*
2. Verifica que existe la base de datos `videoteca`
3. Debería tener 14 tablas

### ✅ Endpoints Públicos

```
http://localhost/videoteca-backend/api/public/grados
http://localhost/videoteca-backend/api/public/materias
```
**Esperado:** JSON con datos de grados y materias

---

## 👤 Credenciales de Prueba

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

**Nota:** Los estudiantes NO necesitan login en el sistema V2.0

---

## 🎨 Instalar Frontend (Opcional)

Una vez que el backend esté funcionando:

### 1. Instalar Dependencias

```bash
cd frontend-web
npm install
```

### 2. Iniciar Servidor de Desarrollo

```bash
npm run dev
```

### 3. Abrir en el Navegador

```
http://localhost:5173
```

**Esperado:**
- Página principal con filtros de grado y materia
- Lista de videos educativos
- Botón "Acceso Docentes" para login

---

## 📋 Requisitos Previos

Antes de instalar, asegúrate de tener:

- ✅ **XAMPP** instalado en `C:\xampp`
- ✅ **Apache** corriendo (puerto 80)
- ✅ **MySQL** corriendo (puerto 3306)
- ✅ **Node.js** instalado (para el frontend)
- ✅ **Git** instalado (si clonaste el repositorio)

---

## ❓ Problemas Comunes

### "Not Found" al acceder a la API

**Causa:** Los archivos no se copiaron a XAMPP

**Solución:**
1. Ejecuta `actualizar-xampp.bat` nuevamente
2. Verifica que existe: `C:\xampp\htdocs\videoteca-backend\index.php`

### "MySQL no está corriendo"

**Causa:** MySQL no está iniciado

**Solución:**
1. Abre XAMPP Control Panel
2. Click en START junto a MySQL
3. Espera a que se ponga verde

### "strict_types must be first statement"

**Causa:** Archivos desactualizados en XAMPP

**Solución:**
1. Ejecuta `actualizar-xampp.bat` para copiar versiones corregidas
2. Reinicia Apache

### "Access denied for user 'root'"

**Causa:** El usuario root tiene password en tu instalación de MySQL

**Solución:**
1. Edita el archivo `.env` en `C:\xampp\htdocs\videoteca-backend\.env`
2. Cambia la línea: `DB_PASS=tu_password_aqui`

---

## 📞 Siguiente Paso

Después de la instalación exitosa:

1. ✅ **Backend funcionando**: `http://localhost/videoteca-backend/`
2. ✅ **Base de datos creada**: Verificado en phpMyAdmin
3. ✅ **Frontend corriendo**: `http://localhost:5173` (opcional)

**¡El sistema está listo para usar!** 🎉

### Documentación Adicional

- `README.md` - Documentación completa del proyecto
- `INSTALACION_XAMPP.md` - Guía detallada de XAMPP
- `INSTALACION_BASE_DATOS.md` - Guía detallada de base de datos
- `SISTEMA_ACTUALIZADO.md` - Información sobre el sistema V2.0

---

**Desarrollado por:** Roger Omar Luna Yujra
**Institución:** Instituto Técnico ATSI, Bolivia
**Para:** U.E. San Francisco Xavier, Okinawa Uno
