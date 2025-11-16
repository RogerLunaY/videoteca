# Corrección del Autoloader - Sistema de Videoteca

## 🐛 Problema Detectado

```
Fatal error: Uncaught Error: Class "Config\CORSConfig" not found
in C:\xampp\htdocs\videoteca-backend\index.php:53
```

### Causa del Error

El autoloader original no mapeaba correctamente los **namespaces** de PHP a la estructura real de archivos:

- **Namespace esperado**: `Config\CORSConfig`
- **Archivo real**: `backend/config/cors.php`

El autoloader buscaba:
- ❌ `backend/Config/CORSConfig.php` (NO EXISTE)

En lugar de:
- ✅ `backend/config/cors.php` (EXISTE)

## ✅ Solución Implementada

Se actualizó el autoloader en `backend/index.php` con:

1. **Mapeo explícito** de clases de configuración:
   ```php
   $classMap = [
       'Config\\CORSConfig' => 'config/cors.php',
       'Config\\JWTConfig' => 'config/jwt.php',
       'Config\\Database' => 'config/database.php',
   ];
   ```

2. **Mapeo de namespaces** para el resto de las clases:
   ```php
   $namespaceMap = [
       'Config\\' => 'config/',
       'Controllers\\' => 'controllers/',
       'Models\\' => 'models/',
       'Middleware\\' => 'middleware/',
       'Utils\\' => 'utils/',
   ];
   ```

3. **Fallback** para cualquier caso no contemplado

## 🔄 Cómo Actualizar en XAMPP

### Opción 1: Script Automático (RECOMENDADO)

1. **Ejecuta** el archivo `actualizar-xampp.bat` (doble clic)
2. Espera a que copie todos los archivos
3. ¡Listo!

### Opción 2: Manual

1. **Copia** el archivo `backend/index.php` actualizado
2. **Pégalo** en `C:\xampp\htdocs\videoteca-backend\index.php`
3. **Sobrescribe** el archivo anterior

## 🧪 Verificación

Después de actualizar, verifica que funcione:

### 1. Probar endpoint público
Abre en tu navegador:
```
http://localhost/videoteca-backend/api/public/videos
```

**Resultado esperado**: JSON con la lista de videos (o array vacío si no hay videos)

### 2. Probar CORS
En la consola del navegador (F12), verifica que NO aparezcan errores de CORS

### 3. Probar frontend
```bash
cd frontend-web
npm run dev
```

Abre http://localhost:5173 y verifica que cargue sin errores

## 📝 Archivos Modificados

- ✏️ `backend/index.php` - Autoloader corregido
- 📄 `actualizar-xampp.bat` - Script de actualización

## 🎯 Próximos Pasos

Una vez actualizado y verificado:

1. Asegúrate de que Apache esté corriendo en XAMPP
2. Asegúrate de que MySQL esté corriendo en XAMPP
3. Verifica que la base de datos `videoteca` esté creada
4. Prueba el frontend en http://localhost:5173
5. Prueba el backend en http://localhost/videoteca-backend/api/public/videos

## ❓ Problemas Comunes

### Error 404 al acceder a la API
- **Causa**: Apache no está corriendo
- **Solución**: Abre XAMPP Control Panel e inicia Apache

### Error de conexión a base de datos
- **Causa**: MySQL no está corriendo o credenciales incorrectas
- **Solución**:
  1. Inicia MySQL en XAMPP Control Panel
  2. Verifica el archivo `.env` en `videoteca-backend/`

### Error CORS persiste
- **Causa**: Caché del navegador
- **Solución**:
  1. Presiona Ctrl+Shift+R para limpiar caché
  2. O abre en ventana de incógnito

## 📞 Soporte

Si el error persiste después de actualizar, verifica:

1. ✅ Apache corriendo en puerto 80
2. ✅ Archivo copiado correctamente
3. ✅ Ruta correcta: `C:\xampp\htdocs\videoteca-backend\`
4. ✅ No hay otros servidores usando el puerto 80

---

**Fecha de corrección**: 2024-11-16
**Versión del sistema**: 2.0.1
