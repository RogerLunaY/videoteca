@echo off
echo ============================================
echo Instalacion de Base de Datos - Videoteca
echo ============================================
echo.
echo Este script creara la base de datos MySQL
echo para el sistema de videoteca.
echo.

REM Verificar que existe el directorio database
if not exist "database" (
    echo ERROR: No se encuentra la carpeta 'database'
    echo Asegurate de ejecutar este script desde la carpeta del proyecto
    echo.
    pause
    exit /b 1
)

REM Verificar que existe el archivo SQL
if not exist "database\instalacion_completa.sql" (
    echo ERROR: No se encuentra el archivo instalacion_completa.sql
    echo.
    pause
    exit /b 1
)

echo IMPORTANTE:
echo - Asegurate de que MySQL este corriendo en XAMPP
echo - Se usara el usuario 'root' sin password (configuracion por defecto de XAMPP)
echo - Se creara la base de datos 'videoteca'
echo.
echo Presiona cualquier tecla para continuar o Ctrl+C para cancelar...
pause >nul
echo.

echo [1/3] Conectando a MySQL...
echo.

REM Intentar conectar a MySQL y crear la base de datos
"C:\xampp\mysql\bin\mysql.exe" -u root -e "CREATE DATABASE IF NOT EXISTS videoteca CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

if %ERRORLEVEL% NEQ 0 (
    echo.
    echo ERROR: No se pudo conectar a MySQL
    echo.
    echo Verifica que:
    echo 1. MySQL este corriendo en XAMPP Control Panel
    echo 2. XAMPP este instalado en C:\xampp
    echo 3. El usuario root no tenga password
    echo.
    pause
    exit /b 1
)

echo [2/3] Base de datos 'videoteca' creada exitosamente
echo.

echo [3/3] Importando schema y datos de prueba...
echo.

REM Importar el archivo SQL completo
"C:\xampp\mysql\bin\mysql.exe" -u root videoteca < "database\instalacion_completa.sql"

if %ERRORLEVEL% NEQ 0 (
    echo.
    echo ERROR: No se pudo importar el schema
    echo.
    pause
    exit /b 1
)

echo.
echo ============================================
echo Instalacion completada exitosamente!
echo ============================================
echo.
echo Base de datos: videoteca
echo Usuario: root
echo Password: (vacio)
echo.
echo La base de datos incluye:
echo - 14 tablas creadas
echo - Triggers automaticos
echo - Vistas optimizadas
echo - Procedimientos almacenados
echo - Datos de prueba (10 usuarios, 21 videos)
echo.
echo CREDENCIALES DE PRUEBA:
echo.
echo Administrador:
echo   Email: admin@sanfranciscoxavier.edu.bo
echo   Password: password123
echo.
echo Docente:
echo   Email: maria.lopez@sanfranciscoxavier.edu.bo
echo   Password: password123
echo.
echo SIGUIENTE PASO:
echo 1. Verifica el archivo .env en C:\xampp\htdocs\videoteca-backend\
echo 2. Asegurate de que tenga estas credenciales:
echo    DB_HOST=localhost
echo    DB_DATABASE=videoteca
echo    DB_USERNAME=root
echo    DB_PASSWORD=
echo.
echo Luego prueba la API en:
echo   http://localhost/videoteca-backend/
echo.
pause
