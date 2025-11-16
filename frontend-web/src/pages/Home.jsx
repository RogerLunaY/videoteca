/**
 * Página Principal Pública
 * Los estudiantes pueden ver videos sin necesidad de login
 */

import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import axios from 'axios';
import { Video, BookOpen, GraduationCap, Search } from 'lucide-react';

const API_URL = import.meta.env.VITE_API_URL || 'http://localhost/backend';

const Home = () => {
  const [videos, setVideos] = useState([]);
  const [grados, setGrados] = useState([]);
  const [materias, setMaterias] = useState([]);
  const [loading, setLoading] = useState(true);
  const [selectedGrado, setSelectedGrado] = useState('');
  const [selectedMateria, setSelectedMateria] = useState('');
  const [searchQuery, setSearchQuery] = useState('');

  useEffect(() => {
    loadInitialData();
  }, []);

  useEffect(() => {
    loadVideos();
  }, [selectedGrado, selectedMateria]);

  const loadInitialData = async () => {
    try {
      const [videosRes, gradosRes, materiasRes] = await Promise.all([
        axios.get(`${API_URL}/api/public/videos`),
        axios.get(`${API_URL}/api/public/grados`),
        axios.get(`${API_URL}/api/public/materias`)
      ]);

      setVideos(videosRes.data.data || []);
      setGrados(gradosRes.data.data || []);
      setMaterias(materiasRes.data.data || []);
    } catch (error) {
      console.error('Error:', error);
    } finally {
      setLoading(false);
    }
  };

  const loadVideos = async () => {
    try {
      const params = new URLSearchParams();
      if (selectedGrado) params.append('grado_id', selectedGrado);
      if (selectedMateria) params.append('materia_id', selectedMateria);

      const response = await axios.get(`${API_URL}/api/public/videos?${params.toString()}`);
      setVideos(response.data.data || []);
    } catch (error) {
      console.error('Error:', error);
    }
  };

  const handleSearch = async (e) => {
    e.preventDefault();
    if (!searchQuery.trim()) {
      loadVideos();
      return;
    }

    try {
      const response = await axios.get(`${API_URL}/api/public/videos/search?q=${encodeURIComponent(searchQuery)}`);
      setVideos(response.data.data || []);
    } catch (error) {
      console.error('Error:', error);
    }
  };

  const formatDuration = (seconds) => {
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${mins}:${secs.toString().padStart(2, '0')}`;
  };

  return (
    <div className="min-h-screen bg-gray-50">
      {/* Header */}
      <header className="bg-white shadow-md">
        <div className="max-w-7xl mx-auto px-4 py-6">
          <div className="flex justify-between items-center">
            <div className="flex items-center gap-3">
              <Video className="h-10 w-10 text-primary-600" />
              <div>
                <h1 className="text-2xl font-bold text-gray-900">
                  Videoteca Educativa
                </h1>
                <p className="text-sm text-gray-600">
                  U.E. San Francisco Xavier
                </p>
              </div>
            </div>
            <Link
              to="/login"
              className="btn-primary"
            >
              Acceso Docentes
            </Link>
          </div>
        </div>
      </header>

      {/* Filtros */}
      <div className="bg-white border-b">
        <div className="max-w-7xl mx-auto px-4 py-4">
          <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
            {/* Búsqueda */}
            <form onSubmit={handleSearch} className="md:col-span-3">
              <div className="flex gap-2">
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
              </div>
            </form>

            {/* Filtro por Grado */}
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">
                <GraduationCap className="inline h-4 w-4 mr-1" />
                Filtrar por Grado
              </label>
              <select
                value={selectedGrado}
                onChange={(e) => setSelectedGrado(e.target.value)}
                className="input-field"
              >
                <option value="">Todos los grados</option>
                {grados.map(grado => (
                  <option key={grado.id} value={grado.id}>
                    {grado.nombre}
                  </option>
                ))}
              </select>
            </div>

            {/* Filtro por Materia */}
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">
                <BookOpen className="inline h-4 w-4 mr-1" />
                Filtrar por Materia
              </label>
              <select
                value={selectedMateria}
                onChange={(e) => setSelectedMateria(e.target.value)}
                className="input-field"
              >
                <option value="">Todas las materias</option>
                {materias.map(materia => (
                  <option key={materia.id} value={materia.id}>
                    {materia.nombre}
                  </option>
                ))}
              </select>
            </div>

            {/* Botón limpiar filtros */}
            {(selectedGrado || selectedMateria || searchQuery) && (
              <div className="flex items-end">
                <button
                  onClick={() => {
                    setSelectedGrado('');
                    setSelectedMateria('');
                    setSearchQuery('');
                    loadInitialData();
                  }}
                  className="btn-secondary w-full"
                >
                  Limpiar Filtros
                </button>
              </div>
            )}
          </div>
        </div>
      </div>

      {/* Lista de Videos */}
      <main className="max-w-7xl mx-auto px-4 py-8">
        {loading ? (
          <div className="text-center py-12">
            <div className="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600"></div>
            <p className="mt-4 text-gray-600">Cargando videos...</p>
          </div>
        ) : videos.length === 0 ? (
          <div className="text-center py-12">
            <Video className="h-16 w-16 text-gray-400 mx-auto mb-4" />
            <p className="text-gray-600">No se encontraron videos</p>
          </div>
        ) : (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            {videos.map((video) => (
              <Link
                key={video.id}
                to={`/watch/${video.id}`}
                className="card hover:shadow-lg transition-shadow duration-200"
              >
                {/* Thumbnail */}
                <div className="relative aspect-video bg-gray-200 rounded-lg overflow-hidden mb-3">
                  {video.thumbnail_path ? (
                    <img
                      src={`${API_URL}${video.thumbnail_path}`}
                      alt={video.titulo}
                      className="w-full h-full object-cover"
                    />
                  ) : (
                    <div className="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary-400 to-primary-600">
                      <Video className="h-16 w-16 text-white opacity-50" />
                    </div>
                  )}
                  {video.duracion && (
                    <div className="absolute bottom-2 right-2 bg-black bg-opacity-75 text-white text-xs px-2 py-1 rounded">
                      {formatDuration(video.duracion)}
                    </div>
                  )}
                </div>

                {/* Info */}
                <div className="space-y-2">
                  <h3 className="font-semibold text-gray-900 line-clamp-2">
                    {video.titulo}
                  </h3>
                  <p className="text-sm text-gray-600 line-clamp-2">
                    {video.descripcion || 'Sin descripción'}
                  </p>
                  <div className="flex flex-wrap gap-2">
                    {video.materia_nombre && (
                      <span className="text-xs bg-primary-100 text-primary-700 px-2 py-1 rounded">
                        {video.materia_nombre}
                      </span>
                    )}
                    {video.grado_nombre && (
                      <span className="text-xs bg-green-100 text-green-700 px-2 py-1 rounded">
                        {video.grado_nombre}
                      </span>
                    )}
                  </div>
                  <p className="text-xs text-gray-500">
                    👁 {video.visualizaciones || 0} visualizaciones
                  </p>
                </div>
              </Link>
            ))}
          </div>
        )}
      </main>

      {/* Footer */}
      <footer className="bg-white border-t mt-12">
        <div className="max-w-7xl mx-auto px-4 py-6 text-center text-gray-600">
          <p>© 2024 U.E. San Francisco Xavier - Okinawa Uno, Bolivia</p>
          <p className="text-sm mt-1">Sistema de Videoteca Educativa</p>
        </div>
      </footer>
    </div>
  );
};

export default Home;
