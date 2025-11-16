@echo off
color 0A
echo.
echo ============================================
echo   INSTALACION COMPLETA - SISTEMA VIDEOTECA
echo   U.E. San Francisco Xavier
echo ============================================
echo.
echo Este script instalara AUTOMATICAMENTE:
echo [1] Backend en XAMPP
echo [2] Base de datos MySQL
echo [3] Archivo de configuracion .env
echo.
echo Presiona cualquier tecla para continuar...
pause >nul
cls

REM ============================================================================
REM PASO 1: COPIAR BACKEND A XAMPP
REM ============================================================================

echo.
echo ============================================
echo PASO 1 DE 3: Copiando Backend a XAMPP
echo ============================================
echo.

if not exist "backend" (
    echo ERROR: No se encuentra la carpeta 'backend'
    echo Asegurate de ejecutar este script desde la carpeta del proyecto
    pause
    exit /b 1
)

echo Copiando archivos del backend...
xcopy /E /I /Y backend C:\xampp\htdocs\videoteca-backend >nul

if %ERRORLEVEL% NEQ 0 (
    echo ERROR: No se pudo copiar los archivos
    echo Verifica que XAMPP este instalado en C:\xampp
    pause
    exit /b 1
)

echo [OK] Backend copiado exitosamente
echo.

REM ============================================================================
REM PASO 2: CREAR ARCHIVO .env
REM ============================================================================

echo ============================================
echo PASO 2 DE 3: Configurando archivo .env
echo ============================================
echo.

if not exist "C:\xampp\htdocs\videoteca-backend\.env.example" (
    echo ERROR: No se encuentra .env.example en el backend
    pause
    exit /b 1
)

echo Creando archivo .env desde .env.example...
copy /Y C:\xampp\htdocs\videoteca-backend\.env.example C:\xampp\htdocs\videoteca-backend\.env >nul

if %ERRORLEVEL% NEQ 0 (
    echo ERROR: No se pudo crear el archivo .env
    pause
    exit /b 1
)

echo [OK] Archivo .env creado exitosamente
echo.

REM ============================================================================
REM PASO 3: INSTALAR BASE DE DATOS
REM ============================================================================

echo ============================================
echo PASO 3 DE 3: Instalando Base de Datos
echo ============================================
echo.

if not exist "database\instalacion_completa.sql" (
    echo ERROR: No se encuentra instalacion_completa.sql
    pause
    exit /b 1
)

echo Verificando que MySQL este corriendo...

REM Intentar conectar a MySQL
"C:\xampp\mysql\bin\mysql.exe" -u root -e "SELECT 1;" >nul 2>&1

if %ERRORLEVEL% NEQ 0 (
    echo.
    echo ERROR: MySQL no esta corriendo
    echo.
    echo Por favor:
    echo 1. Abre XAMPP Control Panel
    echo 2. Haz clic en START junto a MySQL
    echo 3. Espera a que se ponga verde
    echo 4. Ejecuta este script nuevamente
    echo.
    pause
    exit /b 1
)

echo [OK] MySQL esta corriendo
echo.

echo Creando base de datos 'videoteca'...
"C:\xampp\mysql\bin\mysql.exe" -u root -e "CREATE DATABASE IF NOT EXISTS videoteca CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" >nul 2>&1

if %ERRORLEVEL% NEQ 0 (
    echo ERROR: No se pudo crear la base de datos
    pause
    exit /b 1
)

echo [OK] Base de datos creada
echo.

echo Importando schema y datos (esto puede tardar 30-60 segundos)...
"C:\xampp\mysql\bin\mysql.exe" -u root videoteca < "database\instalacion_completa.sql"

if %ERRORLEVEL% NEQ 0 (
    echo ERROR: No se pudo importar el schema
    pause
    exit /b 1
)

echo [OK] Schema importado exitosamente
echo.

REM ============================================================================
REM INSTALACION COMPLETADA
REM ============================================================================

cls
color 0A
echo.
echo ================================================
echo   INSTALACION COMPLETADA EXITOSAMENTE!
echo ================================================
echo.
echo [X] Backend instalado en:
echo     C:\xampp\htdocs\videoteca-backend
echo.
echo [X] Base de datos creada:
echo     Nombre: videoteca
echo     Usuario: root
echo     Password: (vacio)
echo.
echo [X] Archivo .env configurado
echo.
echo ================================================
echo   CREDENCIALES DE PRUEBA
echo ================================================
echo.
echo Administrador:
echo   Email: admin@sanfranciscoxavier.edu.bo
echo   Password: password123
echo.
echo Docente:
echo   Email: maria.lopez@sanfranciscoxavier.edu.bo
echo   Password: password123
echo.
echo ================================================
echo   SIGUIENTE PASO
echo ================================================
echo.
echo 1. Verifica que Apache este corriendo (XAMPP)
echo 2. Abre tu navegador
echo 3. Ve a: http://localhost/videoteca-backend/
echo.
echo Deberia mostrar un JSON con informacion de la API
echo.
echo ================================================
echo   INSTALAR FRONTEND
echo ================================================
echo.
echo Para instalar el frontend web:
echo.
echo 1. Abre una terminal (CMD o PowerShell)
echo 2. Ejecuta: cd frontend-web
echo 3. Ejecuta: npm install
echo 4. Ejecuta: npm run dev
echo 5. Abre: http://localhost:5173
echo.
echo ================================================
echo.
pause
