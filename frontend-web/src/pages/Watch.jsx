/**
 * Reproductor de Video Público
 * Los estudiantes pueden ver videos sin login
 */

import { useState, useEffect } from 'react';
import { useParams, Link } from 'react-router-dom';
import axios from 'axios';
import { ArrowLeft, Video, Eye, Calendar } from 'lucide-react';
import { format } from 'date-fns';
import { es } from 'date-fns/locale';

const API_URL = import.meta.env.VITE_API_URL || 'http://localhost/backend';

const Watch = () => {
  const { id } = useParams();
  const [video, setVideo] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    loadVideo();
  }, [id]);

  const loadVideo = async () => {
    try {
      const response = await axios.get(`${API_URL}/api/public/videos/${id}`);
      setVideo(response.data.data);

      // Registrar reproducción (anónima)
      axios.post(`${API_URL}/api/public/videos/${id}/view`).catch(() => {});
    } catch (error) {
      console.error('Error:', error);
      setError('Video no encontrado');
    } finally {
      setLoading(false);
    }
  };

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600"></div>
      </div>
    );
  }

  if (error || !video) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="text-center">
          <Video className="h-16 w-16 text-gray-400 mx-auto mb-4" />
          <p className="text-gray-600">{error || 'Video no encontrado'}</p>
          <Link to="/" className="btn-primary mt-4 inline-block">
            Volver al inicio
          </Link>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gray-50">
      {/* Header simple */}
      <header className="bg-white shadow-md">
        <div className="max-w-6xl mx-auto px-4 py-4">
          <Link to="/" className="inline-flex items-center gap-2 text-primary-600 hover:text-primary-700">
            <ArrowLeft className="h-5 w-5" />
            <span className="font-medium">Volver al inicio</span>
          </Link>
        </div>
      </header>

      <main className="max-w-6xl mx-auto px-4 py-8">
        {/* Reproductor */}
        <div className="bg-black rounded-lg overflow-hidden mb-6">
          <video
            controls
            autoPlay
            className="w-full aspect-video"
            src={`${API_URL}/api/public/videos/${video.id}/stream`}
          >
            Tu navegador no soporta la reproducción de videos.
          </video>
        </div>

        {/* Información del video */}
        <div className="card">
          <h1 className="text-3xl font-bold text-gray-900 mb-4">
            {video.titulo}
          </h1>

          <div className="flex flex-wrap items-center gap-6 mb-6 text-sm text-gray-600">
            <span className="flex items-center gap-1">
              <Eye className="h-4 w-4" />
              {video.visualizaciones || 0} visualizaciones
            </span>

            {video.fecha_subida && (
              <span className="flex items-center gap-1">
                <Calendar className="h-4 w-4" />
                {format(new Date(video.fecha_subida), 'PPP', { locale: es })}
              </span>
            )}
          </div>

          {video.descripcion && (
            <div className="mb-6">
              <h3 className="font-semibold text-gray-900 mb-2">Descripción</h3>
              <p className="text-gray-700 whitespace-pre-wrap">{video.descripcion}</p>
            </div>
          )}

          <div className="grid grid-cols-1 md:grid-cols-3 gap-4 pt-4 border-t">
            <div>
              <span className="text-sm font-medium text-gray-600">Materia:</span>
              <p className="text-gray-900">{video.materia_nombre}</p>
            </div>
            <div>
              <span className="text-sm font-medium text-gray-600">Grado:</span>
              <p className="text-gray-900">{video.grado_nombre}</p>
            </div>
            <div>
              <span className="text-sm font-medium text-gray-600">Docente:</span>
              <p className="text-gray-900">{video.docente_nombre}</p>
            </div>
          </div>
        </div>
      </main>
    </div>
  );
};

export default Watch;
