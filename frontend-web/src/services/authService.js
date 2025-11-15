/**
 * Servicio de Autenticación
 * Sistema de Biblioteca Digital de Videos Educativos
 */

import api from './api';

const authService = {
  /**
   * Inicia sesión
   * @param {string} email
   * @param {string} password
   * @returns {Promise}
   */
  async login(email, password) {
    const response = await api.post('/api/auth/login', { email, password });

    if (response.data.success) {
      const { access_token, refresh_token, usuario } = response.data.data;

      localStorage.setItem('access_token', access_token);
      localStorage.setItem('refresh_token', refresh_token);
      localStorage.setItem('user', JSON.stringify(usuario));
    }

    return response.data;
  },

  /**
   * Registra un nuevo usuario
   * @param {Object} userData
   * @returns {Promise}
   */
  async register(userData) {
    const response = await api.post('/api/auth/register', userData);
    return response.data;
  },

  /**
   * Cierra sesión
   * @returns {Promise}
   */
  async logout() {
    try {
      await api.post('/api/auth/logout');
    } finally {
      localStorage.removeItem('access_token');
      localStorage.removeItem('refresh_token');
      localStorage.removeItem('user');
    }
  },

  /**
   * Obtiene el usuario actual del localStorage
   * @returns {Object|null}
   */
  getCurrentUser() {
    const userStr = localStorage.getItem('user');
    return userStr ? JSON.parse(userStr) : null;
  },

  /**
   * Obtiene el usuario actual desde la API
   * @returns {Promise}
   */
  async me() {
    const response = await api.get('/api/auth/me');
    return response.data;
  },

  /**
   * Verifica si el usuario está autenticado
   * @returns {boolean}
   */
  isAuthenticated() {
    return !!localStorage.getItem('access_token');
  },

  /**
   * Obtiene el rol del usuario actual
   * @returns {string|null}
   */
  getUserRole() {
    const user = this.getCurrentUser();
    return user?.rol || null;
  },
};

export default authService;
