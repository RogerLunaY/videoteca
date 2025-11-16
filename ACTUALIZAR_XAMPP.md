# IMPORTANTE - Actualización Requerida

## ⚠️ Problema

Los archivos en tu repositorio YA ESTÁN CORREGIDOS, pero los archivos en XAMPP están desactualizados.

**Archivo correcto (repositorio)**: `backend/index.php`
**Archivo desactualizado (XAMPP)**: `C:\xampp\htdocs\videoteca-backend\index.php`

## ✅ Solución Rápida

Sigue estos pasos **EN ORDEN**:

### Paso 1: Copia TODO el backend actualizado

1. Abre el **Explorador de Archivos** de Windows
2. Navega a la carpeta de tu repositorio (donde descargaste/clonaste el proyecto)
3. Encuentra la carpeta `backend`
4. **COPIA** toda la carpeta `backend` (Ctrl+C)
5. Ve a `C:\xampp\htdocs\`
6. **ELIMINA** la carpeta `videoteca-backend` si existe
7. **PEGA** la carpeta `backend` (Ctrl+V)
8. **RENOMBRA** la carpeta de `backend` a `videoteca-backend`

### Paso 2: Reinicia Apache

1. Abre **XAMPP Control Panel**
2. Click en **Stop** junto a Apache
3. Espera 2 segundos
4. Click en **Start** junto a Apache

### Paso 3: Verifica que funcione

Abre en tu navegador:
```
http://localhost/videoteca-backend/
```

**✅ Debería mostrar**: JSON con información de la API (sin errores)

## 🔧 Método Alternativo: Copiar solo index.php

Si prefieres copiar solo el archivo principal:

1. Origen: `tu-repositorio/backend/index.php`
2. Destino: `C:\xampp\htdocs\videoteca-backend\index.php`
3. **SOBRESCRIBE** el archivo cuando te lo pregunte
4. Reinicia Apache en XAMPP

## 📋 Verificación

Después de copiar, verifica estos archivos en XAMPP:

```
C:\xampp\htdocs\videoteca-backend\index.php
C:\xampp\htdocs\videoteca-backend\config\cors.php
C:\xampp\htdocs\videoteca-backend\controllers\ApiInfoController.php
C:\xampp\htdocs\videoteca-backend\routes\api.php
```

Todos deben tener en la **PRIMERA LÍNEA**:
```php
<?php declare(strict_types=1);
```

## ❌ NO hagas esto

- ❌ NO edites los archivos manualmente en XAMPP
- ❌ NO uses versiones antiguas del repositorio
- ❌ NO olvides reiniciar Apache después de copiar

## ✅ Resumen

1. **COPIA** backend → C:\xampp\htdocs\videoteca-backend
2. **REINICIA** Apache en XAMPP
3. **PRUEBA** http://localhost/videoteca-backend/

Si sigues estos pasos, el error desaparecerá.
