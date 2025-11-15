# 🚀 INICIO RÁPIDO - 5 MINUTOS

## Sistema de Videoteca Educativa - U.E. San Francisco Xavier

---

## ⚡ INSTALACIÓN RÁPIDA (Windows + XAMPP)

### 1️⃣ Requisitos (Instalar primero):
- ✅ XAMPP 8.x → https://www.apachefriends.org/
- ✅ Node.js 16+ → https://nodejs.org/

### 2️⃣ Ejecutar Instalador Automático:
```cmd
# En la carpeta del proyecto ejecutar:
instalar-windows.bat
```

### 3️⃣ Configurar Base de Datos:
1. Abrir: http://localhost/phpmyadmin
2. Nueva base de datos: `videoteca_educativa`
3. Importar: `database/schema.sql`
4. Importar: `database/seed_data.sql`

### 4️⃣ Iniciar Frontend:
```cmd
cd frontend-web
npm run dev
```

### 5️⃣ Acceder:
- 🌐 Abrir: http://localhost:5173
- 📧 Email: `admin@sfxavier.edu.bo`
- 🔑 Contraseña: `password123`

---

## 📚 DOCUMENTACIÓN COMPLETA

- 📖 **INSTALACION_XAMPP.md** - Guía detallada paso a paso
- 🖼️ **GUIA_VISUAL_XAMPP.md** - Guía con capturas textuales
- 📋 **README.md** - Documentación completa del proyecto

---

## 🔑 CREDENCIALES DE PRUEBA

| Rol | Email | Contraseña |
|-----|-------|------------|
| **Admin** | admin@sfxavier.edu.bo | password123 |
| **Docente** | maria.gonzalez@sfxavier.edu.bo | password123 |
| **Estudiante** | juan.perez@estudiante.edu.bo | password123 |

---

## 🌐 URLS IMPORTANTES

- Frontend: http://localhost:5173
- Backend API: http://localhost/videoteca-backend/api
- phpMyAdmin: http://localhost/phpmyadmin

---

## ⚠️ PROBLEMAS COMUNES

### "Cannot connect to database"
→ Editar `backend/.env` y verificar DB_USER y DB_PASS

### "404 Not Found" en backend
→ Verificar que Apache esté corriendo en XAMPP

### Frontend no inicia
→ Ejecutar `npm install` en frontend-web

---

## ✅ VERIFICACIÓN RÁPIDA

```bash
# Backend funciona?
http://localhost/videoteca-backend/api/videos
# Debe mostrar: "Token de autenticación no proporcionado"

# Frontend funciona?
http://localhost:5173
# Debe mostrar: Pantalla de login
```

---

## 📞 ESTRUCTURA DEL PROYECTO

```
videoteca/
├── backend/              → C:\xampp\htdocs\videoteca-backend
├── frontend-web/         → Ejecutar con npm run dev
├── database/             → Importar en phpMyAdmin
├── instalar-windows.bat  → Script de instalación
└── README.md             → Documentación completa
```

---

**¡Listo para usar en 5 minutos!** 🎉

Para más detalles ver: **INSTALACION_XAMPP.md**
