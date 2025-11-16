@echo off
echo ============================================
echo Actualizando Backend en XAMPP
echo ============================================
echo.
echo Este script copiara todos los archivos
echo corregidos del backend a XAMPP.
echo.

REM Verificar que existe el directorio backend
if not exist "backend" (
    echo ERROR: No se encuentra la carpeta 'backend'
    echo Asegurate de ejecutar este script desde la carpeta del proyecto
    echo.
    pause
    exit /b 1
)

REM Copiar backend completo a XAMPP
echo [1/2] Copiando archivos del backend a XAMPP...
xcopy /E /I /Y backend C:\xampp\htdocs\videoteca-backend

if %ERRORLEVEL% NEQ 0 (
    echo.
    echo ERROR: No se pudo copiar los archivos
    echo Verifica que XAMPP este instalado en C:\xampp
    echo.
    pause
    exit /b 1
)

echo [2/2] Archivos copiados exitosamente!
echo.
echo ============================================
echo Actualizacion completada!
echo ============================================
echo.
echo El backend ha sido actualizado en:
echo   C:\xampp\htdocs\videoteca-backend
echo.
echo SIGUIENTE PASO:
echo 1. Abre XAMPP Control Panel
echo 2. Haz clic en STOP en Apache (si esta corriendo)
echo 3. Haz clic en START en Apache
echo.
echo Luego prueba la API en tu navegador:
echo   http://localhost/videoteca-backend/
echo.
echo Deberia mostrar un JSON con informacion de la API
echo.
pause
