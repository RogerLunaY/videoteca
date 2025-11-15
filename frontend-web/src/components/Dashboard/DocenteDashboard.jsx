/**
 * Dashboard para Docentes
 */

import { useState, useEffect } from 'react';
import { useAuth } from '../../context/AuthContext';
import { Link } from 'react-router-dom';
import videoService from '../../services/videoService';
import VideoList from '../Video/VideoList';
import { Upload, Video, BarChart } from 'lucide-react';

const DocenteDashboard = () => {
  const { user } = useAuth();
  const [videos, setVideos] = useState([]);
  const [stats, setStats] = useState({ total: 0, views: 0 });

  useEffect(() => {
    loadVideos();
  }, []);

  const loadVideos = async () => {
    try {
      const response = await videoService.getAll({ docente_id: user.id });
      const videosData = response.data || [];
      setVideos(videosData);

      const totalViews = videosData.reduce((sum, v) => sum + (v.visualizaciones || 0), 0);
      setStats({ total: videosData.length, views: totalViews });
    } catch (error) {
      console.error('Error:', error);
    }
  };

  return (
    <div className="max-w-7xl mx-auto px-4 py-8">
      <div className="mb-8">
        <h1 className="text-3xl font-bold text-gray-900 mb-2">
          Panel de Docente - {user.nombre} {user.apellido}
        </h1>
        <p className="text-gray-600">Gestiona tus videos educativos</p>
      </div>

      {/* Estadísticas */}
      <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div className="card">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-gray-600 text-sm">Total Videos</p>
              <p className="text-3xl font-bold text-primary-600">{stats.total}</p>
            </div>
            <Video className="h-12 w-12 text-primary-600 opacity-20" />
          </div>
        </div>
        <div className="card">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-gray-600 text-sm">Visualizaciones</p>
              <p className="text-3xl font-bold text-green-600">{stats.views}</p>
            </div>
            <BarChart className="h-12 w-12 text-green-600 opacity-20" />
          </div>
        </div>
        <div className="card">
          <Link to="/upload" className="flex items-center justify-center gap-2 btn-primary w-full">
            <Upload className="h-5 w-5" />
            Subir Nuevo Video
          </Link>
        </div>
      </div>

      {/* Mis Videos */}
      <div>
        <h2 className="text-2xl font-bold mb-4">Mis Videos</h2>
        <VideoList videos={videos} showActions />
      </div>
    </div>
  );
};

export default DocenteDashboard;
