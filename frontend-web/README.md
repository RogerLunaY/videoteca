# Frontend Web - Videoteca San Francisco Xavier

## Descripción

Frontend web desarrollado con React 18+ y Vite para el Sistema de Biblioteca Digital de Videos Educativos de la U.E. San Francisco Xavier.

## Tecnologías

- **React** 18.2.0
- **Vite** 5.0.8
- **React Router** 6.20.0
- **Axios** 1.6.2
- **TailwindCSS** 3.3.6
- **Lucide React** (iconos)
- **date-fns** (manejo de fechas)

## Instalación

1. **Instalar Node.js** (versión 16 o superior)

2. **Instalar dependencias:**
```bash
cd frontend-web
npm install
```

3. **Configurar variables de entorno:**
```bash
cp .env.example .env
```

Editar `.env`:
```env
VITE_API_URL=http://localhost/backend
VITE_APP_NAME=Videoteca San Francisco Xavier
VITE_ENV=development
```

4. **Iniciar servidor de desarrollo:**
```bash
npm run dev
```

El frontend estará disponible en: **http://localhost:5173**

## Construcción para Producción

```bash
npm run build
```

Los archivos compilados estarán en el directorio `dist/`.

## Estructura del Proyecto

```
frontend-web/
├── src/
│   ├── components/
│   │   ├── Auth/              # Login y Register
│   │   ├── Dashboard/         # Dashboards por rol
│   │   ├── Video/             # Componentes de video
│   │   └── Common/            # Navbar, etc.
│   ├── context/
│   │   └── AuthContext.jsx    # Contexto de autenticación
│   ├── services/
│   │   ├── api.js             # Cliente Axios
│   │   ├── authService.js     # Servicios de auth
│   │   ├── videoService.js    # Servicios de videos
│   │   └── userService.js     # Servicios de usuarios
│   ├── App.jsx                # App principal con rutas
│   ├── main.jsx               # Entry point
│   └── index.css              # Estilos Tailwind
├── public/
├── index.html
├── package.json
├── vite.config.js
└── tailwind.config.js
```

## Rutas Disponibles

- `/` - Redirección automática
- `/login` - Inicio de sesión
- `/register` - Registro de usuario
- `/dashboard` - Dashboard según rol
- `/video/:id` - Reproductor de video

## Roles y Permisos

### Estudiante
- Ver videos de su grado
- Buscar videos
- Reproducir videos
- Ver detalles de videos

### Docente
- Todo lo de estudiante
- Subir videos propios
- Gestionar videos propios
- Ver estadísticas de sus videos

### Administrador
- Acceso completo al sistema
- Gestionar usuarios
- Gestionar todos los videos
- Ver estadísticas globales

## Credenciales de Prueba

**Administrador:**
- Email: `admin@sfxavier.edu.bo`
- Contraseña: `password123`

**Docente:**
- Email: `maria.gonzalez@sfxavier.edu.bo`
- Contraseña: `password123`

**Estudiante:**
- Email: `juan.perez@estudiante.edu.bo`
- Contraseña: `password123`

## Características Implementadas

✅ Autenticación con JWT
✅ Auto-refresh de tokens
✅ Rutas protegidas por rol
✅ Dashboards diferenciados
✅ Búsqueda de videos
✅ Reproductor HTML5 con streaming
✅ Diseño responsivo
✅ Manejo de errores
✅ Loading states
✅ Logout con limpieza de sesión

## Scripts Disponibles

```bash
# Desarrollo
npm run dev

# Construcción
npm run build

# Preview de producción
npm run preview

# Linting
npm run lint
```

## Configuración de Proxy

El archivo `vite.config.js` incluye configuración de proxy para desarrollo:

```javascript
proxy: {
  '/api': {
    target: 'http://localhost',
    changeOrigin: true,
    rewrite: (path) => path.replace(/^\/api/, '/backend/api')
  }
}
```

## Personalización

### Colores (TailwindCSS)

Editar `tailwind.config.js`:

```javascript
colors: {
  primary: {
    // Personalizar colores aquí
  }
}
```

### URL de la API

Editar `.env`:

```env
VITE_API_URL=http://tu-servidor/backend
```

## Soporte de Navegadores

- Chrome (últimas 2 versiones)
- Firefox (últimas 2 versiones)
- Safari (últimas 2 versiones)
- Edge (últimas 2 versiones)

## Autor

**Roger Omar Luna Yujra**
- CI: 6734278 LP
- Institución: Instituto Técnico "ATSI" Bolivia
- Tutor: Lic. Vladimir Mamani

## Licencia

Proyecto educativo - U.E. San Francisco Xavier
