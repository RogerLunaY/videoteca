/**
 * Reproductor de Video
 */

import { useState, useEffect } from 'react';
import { useParams, Link } from 'react-router-dom';
import videoService from '../../services/videoService';
import { ArrowLeft, Star, Eye, Calendar } from 'lucide-react';
import { format } from 'date-fns';
import { es } from 'date-fns/locale';

const VideoPlayer = () => {
  const { id } = useParams();
  const [video, setVideo] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    loadVideo();
  }, [id]);

  const loadVideo = async () => {
    try {
      const response = await videoService.getById(id);
      setVideo(response.data);
    } catch (error) {
      console.error('Error:', error);
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

  if (!video) {
    return <div className="text-center py-12">Video no encontrado</div>;
  }

  return (
    <div className="max-w-6xl mx-auto px-4 py-8">
      <Link to="/dashboard" className="inline-flex items-center gap-2 text-primary-600 hover:text-primary-700 mb-6">
        <ArrowLeft className="h-5 w-5" />
        Volver
      </Link>

      {/* Reproductor */}
      <div className="bg-black rounded-lg overflow-hidden mb-6">
        <video
          controls
          className="w-full aspect-video"
          src={videoService.getStreamUrl(video.id)}
        >
          Tu navegador no soporta la reproducción de videos.
        </video>
      </div>

      {/* Información */}
      <div className="card">
        <h1 className="text-3xl font-bold text-gray-900 mb-4">{video.titulo}</h1>

        <div className="flex items-center gap-6 mb-6 text-sm text-gray-600">
          <span className="flex items-center gap-1">
            <Eye className="h-4 w-4" />
            {video.visualizaciones || 0} visualizaciones
          </span>

          {video.calificacion_promedio > 0 && (
            <span className="flex items-center gap-1">
              <Star className="h-4 w-4 fill-yellow-400 text-yellow-400" />
              {video.calificacion_promedio.toFixed(1)} ({video.total_calificaciones} calificaciones)
            </span>
          )}

          {video.fecha_subida && (
            <span className="flex items-center gap-1">
              <Calendar className="h-4 w-4" />
              {format(new Date(video.fecha_subida), 'PPP', { locale: es })}
            </span>
          )}
        </div>

        <div className="space-y-4">
          {video.descripcion && (
            <div>
              <h3 className="font-semibold text-gray-900 mb-2">Descripción</h3>
              <p className="text-gray-700">{video.descripcion}</p>
            </div>
          )}

          <div className="flex gap-4">
            <div>
              <span className="text-sm font-medium text-gray-600">Materia:</span>
              <span className="ml-2 text-sm text-gray-900">{video.materia_nombre}</span>
            </div>
            <div>
              <span className="text-sm font-medium text-gray-600">Grado:</span>
              <span className="ml-2 text-sm text-gray-900">{video.grado_nombre}</span>
            </div>
            <div>
              <span className="text-sm font-medium text-gray-600">Docente:</span>
              <span className="ml-2 text-sm text-gray-900">{video.docente_nombre}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default VideoPlayer;
