/**
 * Componente Dashboard Principal
 * Redirige al dashboard correspondiente según el rol
 */

import { useAuth } from '../../context/AuthContext';
import AdminDashboard from './AdminDashboard';
import DocenteDashboard from './DocenteDashboard';
import EstudianteDashboard from './EstudianteDashboard';

const Dashboard = () => {
  const { user } = useAuth();

  if (!user) return null;

  switch (user.rol) {
    case 'Administrador':
      return <AdminDashboard />;
    case 'Docente':
      return <DocenteDashboard />;
    case 'Estudiante':
      return <EstudianteDashboard />;
    default:
      return <div>Rol no reconocido</div>;
  }
};

export default Dashboard;
