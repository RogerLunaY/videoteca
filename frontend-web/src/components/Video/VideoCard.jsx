/**
 * Tarjeta de Video
 */

import { Link } from 'react-router-dom';
import { Play, Eye, Star, Clock } from 'lucide-react';

const VideoCard = ({ video, showActions }) => {
  const formatDuration = (seconds) => {
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${mins}:${secs.toString().padStart(2, '0')}`;
  };

  return (
    <Link to={`/video/${video.id}`} className="card hover:shadow-lg transition-shadow duration-200">
      {/* Thumbnail */}
      <div className="relative aspect-video bg-gray-200 rounded-lg overflow-hidden mb-3">
        {video.thumbnail_path ? (
          <img
            src={`${import.meta.env.VITE_API_URL}${video.thumbnail_path}`}
            alt={video.titulo}
            className="w-full h-full object-cover"
          />
        ) : (
          <div className="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary-400 to-primary-600">
            <Play className="h-16 w-16 text-white opacity-50" />
          </div>
        )}

        {/* Duración */}
        {video.duracion && (
          <div className="absolute bottom-2 right-2 bg-black bg-opacity-75 text-white text-xs px-2 py-1 rounded">
            {formatDuration(video.duracion)}
          </div>
        )}
      </div>

      {/* Información */}
      <div className="space-y-2">
        <h3 className="font-semibold text-gray-900 line-clamp-2 hover:text-primary-600">
          {video.titulo}
        </h3>

        <p className="text-sm text-gray-600 line-clamp-2">
          {video.descripcion || 'Sin descripción'}
        </p>

        <div className="flex items-center gap-4 text-xs text-gray-500">
          <span className="flex items-center gap-1">
            <Eye className="h-3 w-3" />
            {video.visualizaciones || 0}
          </span>

          {video.calificacion_promedio > 0 && (
            <span className="flex items-center gap-1">
              <Star className="h-3 w-3 fill-yellow-400 text-yellow-400" />
              {video.calificacion_promedio.toFixed(1)}
            </span>
          )}
        </div>

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
      </div>
    </Link>
  );
};

export default VideoCard;
