/**
 * Dashboard para Estudiantes
 */

import { useState, useEffect } from 'react';
import { useAuth } from '../../context/AuthContext';
import videoService from '../../services/videoService';
import VideoList from '../Video/VideoList';
import { Search, TrendingUp } from 'lucide-react';

const EstudianteDashboard = () => {
  const { user } = useAuth();
  const [videos, setVideos] = useState([]);
  const [loading, setLoading] = useState(true);
  const [searchQuery, setSearchQuery] = useState('');

  useEffect(() => {
    loadVideos();
  }, []);

  const loadVideos = async () => {
    try {
      const response = await videoService.getAll({ grado_id: user.grado_id });
      setVideos(response.data || []);
    } catch (error) {
      console.error('Error al cargar videos:', error);
    } finally {
      setLoading(false);
    }
  };

  const handleSearch = async (e) => {
    e.preventDefault();
    if (!searchQuery.trim()) {
      loadVideos();
      return;
    }

    try {
      const response = await videoService.search(searchQuery);
      setVideos(response.data || []);
    } catch (error) {
      console.error('Error en búsqueda:', error);
    }
  };

  return (
    <div className="max-w-7xl mx-auto px-4 py-8">
      <div className="mb-8">
        <h1 className="text-3xl font-bold text-gray-900 mb-2">
          Bienvenido, {user.nombre} {user.apellido}
        </h1>
        <p className="text-gray-600">Explora los videos educativos disponibles</p>
      </div>

      {/* Búsqueda */}
      <div className="mb-8">
        <form onSubmit={handleSearch} className="flex gap-2">
          <div className="flex-1 relative">
            <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 h-5 w-5" />
            <input
              type="text"
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              placeholder="Buscar videos..."
              className="input-field pl-10"
            />
          </div>
          <button type="submit" className="btn-primary">
            Buscar
          </button>
        </form>
      </div>

      {/* Lista de Videos */}
      {loading ? (
        <div className="text-center py-12">
          <div className="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600"></div>
          <p className="mt-4 text-gray-600">Cargando videos...</p>
        </div>
      ) : (
        <VideoList videos={videos} />
      )}
    </div>
  );
};

export default EstudianteDashboard;
