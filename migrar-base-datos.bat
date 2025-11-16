@echo off
echo ============================================
echo Migracion de Base de Datos
echo De: videoteca_educativa
echo A:  videoteca
echo ============================================
echo.

REM Verificar que MySQL este corriendo
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

echo [1/4] Verificando si existe la base de datos antigua...

REM Verificar si existe videoteca_educativa
"C:\xampp\mysql\bin\mysql.exe" -u root -e "SHOW DATABASES LIKE 'videoteca_educativa';" > temp_check.txt

findstr /C:"videoteca_educativa" temp_check.txt >nul

if %ERRORLEVEL% EQU 0 (
    echo [OK] Se encontro la base de datos 'videoteca_educativa'
    echo.
    echo [2/4] Exportando datos de la base de datos antigua...

    REM Exportar la base de datos antigua
    "C:\xampp\mysql\bin\mysqldump.exe" -u root videoteca_educativa > backup_videoteca_educativa.sql

    if %ERRORLEVEL% NEQ 0 (
        echo ERROR: No se pudo hacer backup de la base de datos
        del temp_check.txt
        pause
        exit /b 1
    )

    echo [OK] Backup creado: backup_videoteca_educativa.sql
    echo.

    echo [3/4] Renombrando base de datos...

    REM Crear nueva base de datos
    "C:\xampp\mysql\bin\mysql.exe" -u root -e "CREATE DATABASE IF NOT EXISTS videoteca CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

    REM Importar datos en la nueva base de datos
    "C:\xampp\mysql\bin\mysql.exe" -u root videoteca < backup_videoteca_educativa.sql

    if %ERRORLEVEL% NEQ 0 (
        echo ERROR: No se pudo migrar los datos
        del temp_check.txt
        pause
        exit /b 1
    )

    echo [OK] Datos migrados exitosamente
    echo.

    echo [4/4] Limpiando...
    echo.
    echo IMPORTANTE: La base de datos antigua 'videoteca_educativa' NO se ha eliminado
    echo Se mantiene como backup de seguridad.
    echo El archivo backup_videoteca_educativa.sql tambien se mantiene.
    echo.
    echo Si todo funciona correctamente, puedes eliminar manualmente:
    echo - La base de datos: videoteca_educativa (desde phpMyAdmin)
    echo - El archivo: backup_videoteca_educativa.sql
    echo.
) else (
    echo [OK] No se encontro base de datos antigua
    echo.
    echo [2/4] Verificando si existe 'videoteca'...

    "C:\xampp\mysql\bin\mysql.exe" -u root -e "SHOW DATABASES LIKE 'videoteca';" > temp_check2.txt

    findstr /C:"videoteca" temp_check2.txt >nul

    if %ERRORLEVEL% EQU 0 (
        echo [OK] La base de datos 'videoteca' ya existe
        echo.
        echo No se requiere migracion.
        echo.
    ) else (
        echo [INFO] La base de datos 'videoteca' no existe
        echo.
        echo Ejecuta 'instalar-base-datos.bat' para crear la base de datos
        echo.
    )

    del temp_check2.txt
)

del temp_check.txt

echo ============================================
echo Migracion completada
echo ============================================
echo.
pause
