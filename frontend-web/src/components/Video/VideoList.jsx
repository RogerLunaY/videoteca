/**
 * Lista de Videos
 */

import VideoCard from './VideoCard';

const VideoList = ({ videos, showActions = false }) => {
  if (!videos || videos.length === 0) {
    return (
      <div className="text-center py-12">
        <p className="text-gray-600">No se encontraron videos</p>
      </div>
    );
  }

  return (
    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      {videos.map((video) => (
        <VideoCard key={video.id} video={video} showActions={showActions} />
      ))}
    </div>
  );
};

export default VideoList;
