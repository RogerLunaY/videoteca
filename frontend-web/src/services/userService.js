/**
 * Servicio de Usuarios
 * Sistema de Biblioteca Digital de Videos Educativos
 */

import api from './api';

const userService = {
  /**
   * Obtiene todos los usuarios
   * @returns {Promise}
   */
  async getAll() {
    const response = await api.get('/api/usuarios');
    return response.data;
  },

  /**
   * Obtiene un usuario por ID
   * @param {number} id
   * @returns {Promise}
   */
  async getById(id) {
    const response = await api.get(`/api/usuarios/${id}`);
    return response.data;
  },

  /**
   * Crea un nuevo usuario
   * @param {Object} userData
   * @returns {Promise}
   */
  async create(userData) {
    const response = await api.post('/api/usuarios', userData);
    return response.data;
  },

  /**
   * Actualiza un usuario
   * @param {number} id
   * @param {Object} userData
   * @returns {Promise}
   */
  async update(id, userData) {
    const response = await api.put(`/api/usuarios/${id}`, userData);
    return response.data;
  },

  /**
   * Elimina un usuario
   * @param {number} id
   * @returns {Promise}
   */
  async delete(id) {
    const response = await api.delete(`/api/usuarios/${id}`);
    return response.data;
  },
};

export default userService;
