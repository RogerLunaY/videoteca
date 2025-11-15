/**
 * Dashboard para Administradores
 */

import { useState, useEffect } from 'react';
import { useAuth } from '../../context/AuthContext';
import { Link } from 'react-router-dom';
import { Users, Video, TrendingUp, Database } from 'lucide-react';

const AdminDashboard = () => {
  const { user } = useAuth();
  const [stats, setStats] = useState({
    usuarios: 0,
    videos: 0,
    visualizaciones: 0,
  });

  return (
    <div className="max-w-7xl mx-auto px-4 py-8">
      <div className="mb-8">
        <h1 className="text-3xl font-bold text-gray-900 mb-2">
          Panel de Administración
        </h1>
        <p className="text-gray-600">Bienvenido, {user.nombre} {user.apellido}</p>
      </div>

      {/* Estadísticas */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div className="card">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-gray-600 text-sm">Usuarios</p>
              <p className="text-3xl font-bold text-primary-600">{stats.usuarios}</p>
            </div>
            <Users className="h-12 w-12 text-primary-600 opacity-20" />
          </div>
        </div>
        <div className="card">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-gray-600 text-sm">Videos</p>
              <p className="text-3xl font-bold text-green-600">{stats.videos}</p>
            </div>
            <Video className="h-12 w-12 text-green-600 opacity-20" />
          </div>
        </div>
        <div className="card">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-gray-600 text-sm">Visualizaciones</p>
              <p className="text-3xl font-bold text-purple-600">{stats.visualizaciones}</p>
            </div>
            <TrendingUp className="h-12 w-12 text-purple-600 opacity-20" />
          </div>
        </div>
        <div className="card">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-gray-600 text-sm">Almacenamiento</p>
              <p className="text-2xl font-bold text-orange-600">2.5 GB</p>
            </div>
            <Database className="h-12 w-12 text-orange-600 opacity-20" />
          </div>
        </div>
      </div>

      {/* Acciones Rápidas */}
      <div className="card">
        <h2 className="text-xl font-bold mb-4">Acciones Rápidas</h2>
        <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
          <Link to="/admin/usuarios" className="btn-primary text-center">
            Gestionar Usuarios
          </Link>
          <Link to="/admin/videos" className="btn-primary text-center">
            Gestionar Videos
          </Link>
          <Link to="/admin/estadisticas" className="btn-primary text-center">
            Ver Estadísticas Completas
          </Link>
        </div>
      </div>
    </div>
  );
};

export default AdminDashboard;
