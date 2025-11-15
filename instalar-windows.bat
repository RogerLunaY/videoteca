@echo off
REM ========================================================================
REM Script de Instalacion Automatica para Windows
REM Sistema de Biblioteca Digital de Videos Educativos
REM U.E. San Francisco Xavier
REM ========================================================================

echo.
echo ========================================================================
echo   INSTALADOR DEL SISTEMA DE VIDEOTECA EDUCATIVA
echo   U.E. San Francisco Xavier
echo ========================================================================
echo.

REM Verificar si XAMPP esta instalado
if not exist "C:\xampp\htdocs\" (
    echo [ERROR] XAMPP no esta instalado en C:\xampp\
    echo Por favor, instala XAMPP primero desde: https://www.apachefriends.org/
    pause
    exit /b 1
)

echo [1/6] Verificando XAMPP... OK
echo.

REM Copiar backend a htdocs
echo [2/6] Copiando backend a XAMPP...
if exist "C:\xampp\htdocs\videoteca-backend\" (
    echo La carpeta videoteca-backend ya existe. Desea sobrescribirla? (S/N)
    set /p respuesta=
    if /i "%respuesta%"=="S" (
        rmdir /s /q "C:\xampp\htdocs\videoteca-backend"
    ) else (
        echo Instalacion cancelada.
        pause
        exit /b 0
    )
)

xcopy /E /I /Y backend "C:\xampp\htdocs\videoteca-backend"
echo Backend copiado correctamente
echo.

REM Crear archivo .env del backend
echo [3/6] Configurando backend...
copy /Y "C:\xampp\htdocs\videoteca-backend\.env.example" "C:\xampp\htdocs\videoteca-backend\.env"

REM Crear carpetas de uploads
if not exist "C:\xampp\htdocs\videoteca-backend\uploads\videos\" mkdir "C:\xampp\htdocs\videoteca-backend\uploads\videos"
if not exist "C:\xampp\htdocs\videoteca-backend\uploads\thumbnails\" mkdir "C:\xampp\htdocs\videoteca-backend\uploads\thumbnails"
echo Carpetas de uploads creadas
echo.

REM Configurar frontend
echo [4/6] Configurando frontend...
cd frontend-web
if not exist ".env" (
    copy /Y ".env.example" ".env"
    echo Archivo .env del frontend creado
)
cd ..
echo.

REM Verificar Node.js
echo [5/6] Verificando Node.js...
node --version >nul 2>&1
if errorlevel 1 (
    echo [ADVERTENCIA] Node.js no esta instalado
    echo Por favor, instala Node.js desde: https://nodejs.org/
    echo Luego ejecuta: cd frontend-web y luego: npm install
) else (
    echo Node.js detectado
    echo Instalando dependencias del frontend (esto puede tardar unos minutos)...
    cd frontend-web
    call npm install
    cd ..
    echo Dependencias instaladas
)
echo.

REM Instrucciones finales
echo [6/6] Instalacion completada
echo.
echo ========================================================================
echo   PROXIMOS PASOS:
echo ========================================================================
echo.
echo 1. Iniciar XAMPP Control Panel
echo 2. Iniciar Apache y MySQL
echo 3. Abrir phpMyAdmin: http://localhost/phpmyadmin
echo 4. Crear base de datos: videoteca_educativa
echo 5. Importar database\schema.sql
echo 6. Importar database\seed_data.sql
echo 7. Abrir terminal en frontend-web
echo 8. Ejecutar: npm run dev
echo 9. Abrir navegador: http://localhost:5173
echo.
echo ========================================================================
echo   CREDENCIALES DE PRUEBA:
echo ========================================================================
echo.
echo Administrador:
echo   Email: admin@sfxavier.edu.bo
echo   Contraseña: password123
echo.
echo Docente:
echo   Email: maria.gonzalez@sfxavier.edu.bo
echo   Contraseña: password123
echo.
echo Estudiante:
echo   Email: juan.perez@estudiante.edu.bo
echo   Contraseña: password123
echo.
echo ========================================================================
echo.
echo Presiona cualquier tecla para ver la guia completa...
pause >nul

REM Abrir guia de instalacion
if exist "INSTALACION_XAMPP.md" (
    notepad INSTALACION_XAMPP.md
)

echo.
echo Instalacion finalizada!
echo.
pause
