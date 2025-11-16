@echo off
echo ============================================
echo Actualizando Backend en XAMPP
echo ============================================
echo.

REM Copiar backend completo a XAMPP
echo Copiando archivos del backend...
xcopy /E /I /Y backend C:\xampp\htdocs\videoteca-backend

echo.
echo ============================================
echo Actualizacion completada!
echo ============================================
echo.
echo El backend ha sido actualizado en:
echo C:\xampp\htdocs\videoteca-backend
echo.
echo Ahora puedes probar la API en:
echo http://localhost/videoteca-backend/api/public/videos
echo.
pause
