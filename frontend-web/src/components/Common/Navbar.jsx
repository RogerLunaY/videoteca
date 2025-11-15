/**
 * Navbar Principal
 */

import { Link } from 'react-router-dom';
import { useAuth } from '../../context/AuthContext';
import { Video, LogOut, User } from 'lucide-react';

const Navbar = () => {
  const { user, logout, isAuthenticated } = useAuth();

  const handleLogout = async () => {
    await logout();
    window.location.href = '/login';
  };

  if (!isAuthenticated()) return null;

  return (
    <nav className="bg-white shadow-md">
      <div className="max-w-7xl mx-auto px-4">
        <div className="flex justify-between items-center h-16">
          <Link to="/dashboard" className="flex items-center gap-2">
            <Video className="h-8 w-8 text-primary-600" />
            <span className="text-xl font-bold text-gray-900">Videoteca SFX</span>
          </Link>

          <div className="flex items-center gap-4">
            <div className="flex items-center gap-2">
              <User className="h-5 w-5 text-gray-600" />
              <span className="text-sm text-gray-700">
                {user?.nombre} {user?.apellido}
              </span>
              <span className="text-xs bg-primary-100 text-primary-700 px-2 py-1 rounded">
                {user?.rol}
              </span>
            </div>

            <button
              onClick={handleLogout}
              className="btn-secondary flex items-center gap-2"
            >
              <LogOut className="h-4 w-4" />
              Salir
            </button>
          </div>
        </div>
      </div>
    </nav>
  );
};

export default Navbar;
