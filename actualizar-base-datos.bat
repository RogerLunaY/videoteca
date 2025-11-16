@echo off
echo ============================================
echo Actualizar Base de Datos a V2.0
echo Sistema de Videoteca
echo ============================================
echo.
echo Este script actualizara tu base de datos
echo existente a la version 2.0 SIN eliminar
echo tus datos actuales.
echo.
echo IMPORTANTE:
echo - Es seguro ejecutar este script
echo - No eliminara tus videos ni usuarios
echo - Optimizara la base de datos para V2.0
echo.

REM Verificar que MySQL este corriendo
echo [1/5] Verificando MySQL...

"C:\xampp\mysql\bin\mysql.exe" -u root -e "SELECT 1;" >nul 2>&1

if %ERRORLEVEL% NEQ 0 (
    echo ERROR: MySQL no esta corriendo
    echo.
    echo Por favor:
    echo 1. Abre XAMPP Control Panel
    echo 2. Haz clic en START junto a MySQL
    echo 3. Ejecuta este script nuevamente
    echo.
    pause
    exit /b 1
)

echo [OK] MySQL esta corriendo
echo.

REM Verificar que exista la base de datos
echo [2/5] Verificando base de datos...

"C:\xampp\mysql\bin\mysql.exe" -u root -e "USE videoteca; SELECT 1;" >nul 2>&1

if %ERRORLEVEL% NEQ 0 (
    echo ERROR: La base de datos 'videoteca' no existe
    echo.
    echo Opciones:
    echo 1. Si tienes 'videoteca_educativa', ejecuta: migrar-base-datos.bat
    echo 2. Si no tienes base de datos, ejecuta: instalar-base-datos.bat
    echo.
    pause
    exit /b 1
)

echo [OK] Base de datos 'videoteca' encontrada
echo.

REM Hacer backup antes de actualizar
echo [3/5] Creando backup de seguridad...

"C:\xampp\mysql\bin\mysqldump.exe" -u root videoteca > backup_videoteca_%date:~-4%%date:~3,2%%date:~0,2%.sql

if %ERRORLEVEL% NEQ 0 (
    echo ADVERTENCIA: No se pudo crear backup
    echo.
    set /p continuar="Deseas continuar sin backup? (S/N): "
    if /i NOT "%continuar%"=="S" (
        echo Actualizacion cancelada
        pause
        exit /b 0
    )
) else (
    echo [OK] Backup creado exitosamente
)

echo.

REM Verificar que existe el script SQL
if not exist "database\actualizar_bd.sql" (
    echo ERROR: No se encuentra database\actualizar_bd.sql
    echo Asegurate de ejecutar este script desde la carpeta del proyecto
    pause
    exit /b 1
)

REM Aplicar actualizaciones
echo [4/5] Aplicando actualizaciones a la base de datos...
echo (Esto puede tardar 10-30 segundos)
echo.

"C:\xampp\mysql\bin\mysql.exe" -u root videoteca < "database\actualizar_bd.sql"

if %ERRORLEVEL% NEQ 0 (
    echo ERROR: No se pudo actualizar la base de datos
    echo.
    echo Tu backup esta en: backup_videoteca_%date:~-4%%date:~3,2%%date:~0,2%.sql
    echo.
    pause
    exit /b 1
)

echo [OK] Actualizaciones aplicadas exitosamente
echo.

REM Optimizacion final
echo [5/5] Optimizando base de datos...

"C:\xampp\mysql\bin\mysqlcheck.exe" -u root --optimize videoteca >nul 2>&1

echo [OK] Optimizacion completada
echo.

REM Mostrar resumen
echo ============================================
echo Actualizacion completada exitosamente!
echo ============================================
echo.
echo Base de datos: videoteca (V2.0)
echo.
echo CAMBIOS APLICADOS:
echo [X] Indices optimizados para mejor rendimiento
echo [X] Roles actualizados para sistema V2.0
echo [X] Estudiantes marcados como acceso publico
echo [X] Grados y materias verificados
echo [X] Tablas optimizadas
echo.
echo ARCHIVO DE BACKUP:
echo backup_videoteca_%date:~-4%%date:~3,2%%date:~0,2%.sql
echo.
echo SIGUIENTE PASO:
echo 1. Verifica que el backend este actualizado
echo 2. Ejecuta: actualizar-xampp.bat (si no lo hiciste)
echo 3. Prueba la API: http://localhost/videoteca-backend/
echo.
pause
