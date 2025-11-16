# 🔄 Actualización de Base de Datos - Sistema Videoteca V2.0

## 📋 Resumen de Cambios

La versión 2.0 del sistema incluye cambios importantes en la base de datos:

### 🔸 Cambio de Nombre
- **Antes:** `videoteca_educativa`
- **Ahora:** `videoteca`
- **Motivo:** Nombre más simple y consistente

### 🔸 Cambios Arquitectónicos
- **Estudiantes:** Ya NO requieren login (acceso público)
- **Reproducciones:** Ahora soportan visualizaciones anónimas
- **Roles:** Actualizados para reflejar el nuevo modelo
- **Índices:** Optimizados para mejor rendimiento

## 🚀 ¿Qué Script Necesito?

### Escenario 1: Instalación Nueva (No tienes BD)
**Usa:** `instalar-base-datos.bat` o `instalar-todo.bat`

```bash
# Opción A: Solo BD
instalar-base-datos.bat

# Opción B: Todo el sistema
instalar-todo.bat
```

### Escenario 2: Tienes BD "videoteca_educativa"
**Usa:** `migrar-base-datos.bat`

Este script:
1. ✅ Exporta tu BD actual como backup
2. ✅ Crea nueva BD con nombre "videoteca"
3. ✅ Migra todos tus datos
4. ✅ Mantiene la BD antigua por seguridad

```bash
migrar-base-datos.bat
```

### Escenario 3: Tienes BD "videoteca" pero desactualizada
**Usa:** `actualizar-base-datos.bat`

Este script:
1. ✅ Crea backup automático
2. ✅ Actualiza estructura para V2.0
3. ✅ Optimiza índices y tablas
4. ✅ NO elimina tus datos

```bash
actualizar-base-datos.bat
```

## 📊 Comparación de Versiones

| Aspecto | V1.0 (Anterior) | V2.0 (Actual) |
|---------|----------------|---------------|
| **Nombre BD** | videoteca_educativa | videoteca |
| **Login Estudiantes** | Requerido | NO requerido |
| **Acceso Videos** | Autenticado | Público |
| **Reproducciones** | Siempre con usuario_id | Anónimas permitidas |
| **Roles Activos** | Admin, Docente, Estudiante | Admin, Docente |
| **Índices** | Básicos | Optimizados |

## 🛠️ Guía de Migración Paso a Paso

### Si Tienes BD "videoteca_educativa"

1. **Hacer Backup Manual (Recomendado)**
   ```
   http://localhost/phpmyadmin
   → Selecciona "videoteca_educativa"
   → Click en "Exportar"
   → Click en "Continuar"
   → Guarda el archivo .sql
   ```

2. **Ejecutar Migración**
   ```bash
   migrar-base-datos.bat
   ```

3. **Verificar**
   ```
   http://localhost/phpmyadmin
   → Deberías ver ambas BD:
     - videoteca (nueva)
     - videoteca_educativa (antigua, backup)
   ```

4. **Actualizar .env**
   ```
   C:\xampp\htdocs\videoteca-backend\.env

   Verificar:
   DB_NAME=videoteca    (no videoteca_educativa)
   ```

5. **Probar**
   ```
   http://localhost/videoteca-backend/api/public/grados
   ```

6. **Eliminar BD Antigua (Opcional)**
   - Solo después de verificar que todo funciona
   - Desde phpMyAdmin → Eliminar "videoteca_educativa"

### Si Solo Necesitas Actualizar "videoteca"

1. **Ejecutar Actualización**
   ```bash
   actualizar-base-datos.bat
   ```

2. **El script hará:**
   - ✅ Backup automático con fecha
   - ✅ Agregar índices faltantes
   - ✅ Actualizar descripciones de roles
   - ✅ Marcar estudiantes como inactivos
   - ✅ Optimizar tablas

3. **Verificar**
   ```
   http://localhost/videoteca-backend/
   ```

## 📝 Cambios Técnicos Detallados

### 1. Índices Agregados

```sql
-- Optimización de búsquedas
CREATE INDEX idx_videos_titulo ON videos(titulo);
CREATE INDEX idx_videos_estado ON videos(estado);
CREATE INDEX idx_videos_fecha ON videos(fecha_creacion);

-- Optimización de estadísticas
CREATE INDEX idx_reproducciones_video ON reproducciones(video_id);
CREATE INDEX idx_reproducciones_fecha ON reproducciones(fecha_reproduccion);

-- Optimización de filtros públicos
CREATE INDEX idx_grados_estado ON grados(estado);
CREATE INDEX idx_materias_estado ON materias(estado);
```

### 2. Cambios en Tabla de Reproducciones

```sql
-- Ahora permite NULL para reproducciones anónimas
ALTER TABLE reproducciones
MODIFY COLUMN usuario_id INT UNSIGNED NULL
COMMENT 'NULL para reproducciones anónimas (estudiantes en V2.0)';
```

### 3. Actualización de Roles

```sql
-- Roles actualizados para V2.0
UPDATE roles
SET descripcion = 'OBSOLETO en V2.0 - Los estudiantes ahora acceden públicamente sin login'
WHERE nombre = 'Estudiante';
```

### 4. Estudiantes Marcados como Inactivos

```sql
-- Los estudiantes ya no necesitan login
UPDATE usuarios
SET estado = 'inactivo'
WHERE rol_id = (SELECT id FROM roles WHERE nombre = 'Estudiante');
```

**Nota:** NO se eliminan para mantener el historial.

## 🔍 Verificación Post-Actualización

### Desde phpMyAdmin

```
http://localhost/phpmyadmin
```

1. **Verificar nombre:** `videoteca` debe existir
2. **Verificar tablas:** Deben ser 14 tablas
3. **Verificar grados:** Debe haber 12 grados
4. **Verificar materias:** Debe haber al menos 10 materias

### Desde la API

```bash
# Listar grados
http://localhost/videoteca-backend/api/public/grados

# Listar materias
http://localhost/videoteca-backend/api/public/materias

# Listar videos
http://localhost/videoteca-backend/api/public/videos
```

### Verificar Índices

```sql
-- En phpMyAdmin, ejecutar:
SHOW INDEX FROM videos;
SHOW INDEX FROM reproducciones;
SHOW INDEX FROM grados;
SHOW INDEX FROM materias;
```

## ⚠️ Problemas Comunes

### "Database 'videoteca' doesn't exist"

**Solución:**
```bash
# Si tienes videoteca_educativa:
migrar-base-datos.bat

# Si no tienes ninguna BD:
instalar-base-datos.bat
```

### "MySQL no está corriendo"

**Solución:**
1. Abre XAMPP Control Panel
2. Click START en MySQL
3. Espera a que se ponga verde
4. Ejecuta el script nuevamente

### "Access denied for user 'root'"

**Solución:**
Si tu usuario root tiene password:
1. Edita los scripts .bat
2. Cambia `-u root` por `-u root -p`
3. O mejor, actualiza `.env` con tu password

### Los índices ya existen

**Mensaje:**
```
Duplicate key name 'idx_videos_titulo'
```

**Solución:**
Este es un mensaje informativo, no un error. Significa que el índice ya existe (está bien).

## 📦 Archivos de Backup

Los scripts crean backups automáticos:

```
backup_videoteca_educativa.sql     (migración)
backup_videoteca_YYYYMMDD.sql      (actualización)
```

**Ubicación:** En la carpeta del proyecto

**Restaurar un backup:**
```bash
mysql -u root videoteca < backup_videoteca_YYYYMMDD.sql
```

## 🎯 Siguiente Paso

Después de actualizar la base de datos:

1. ✅ **Verificar .env**
   ```
   C:\xampp\htdocs\videoteca-backend\.env
   DB_NAME=videoteca
   ```

2. ✅ **Actualizar Backend**
   ```bash
   actualizar-xampp.bat
   ```

3. ✅ **Reiniciar Apache**
   - XAMPP Control Panel → Stop → Start

4. ✅ **Probar API**
   ```
   http://localhost/videoteca-backend/
   ```

5. ✅ **Probar Frontend**
   ```bash
   cd frontend-web
   npm run dev
   ```

---

## 📞 Soporte

Si encuentras problemas:

1. **Revisa los logs de MySQL:**
   - XAMPP Control Panel → Logs → MySQL error log

2. **Verifica los backups:**
   - Los scripts crean backups automáticos
   - Puedes restaurar si algo sale mal

3. **Consulta la documentación:**
   - `INSTALACION_BASE_DATOS.md` - Instalación desde cero
   - `INSTALACION_RAPIDA.md` - Guía rápida completa
   - `README.md` - Documentación general

---

**Versión:** 2.0
**Fecha:** 2024-11-16
**Autor:** Roger Omar Luna Yujra
