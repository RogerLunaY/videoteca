/**
 * Servicio de Videos
 * Sistema de Biblioteca Digital de Videos Educativos
 */

import api from './api';

const videoService = {
  /**
   * Obtiene todos los videos
   * @param {Object} filters - Filtros opcionales
   * @returns {Promise}
   */
  async getAll(filters = {}) {
    const params = new URLSearchParams();

    Object.keys(filters).forEach(key => {
      if (filters[key]) {
        params.append(key, filters[key]);
      }
    });

    const response = await api.get(`/api/videos?${params.toString()}`);
    return response.data;
  },

  /**
   * Obtiene un video por ID
   * @param {number} id
   * @returns {Promise}
   */
  async getById(id) {
    const response = await api.get(`/api/videos/${id}`);
    return response.data;
  },

  /**
   * Crea un nuevo video
   * @param {FormData} formData
   * @returns {Promise}
   */
  async create(formData) {
    const response = await api.post('/api/videos', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
      timeout: 300000, // 5 minutos para subida de videos
    });
    return response.data;
  },

  /**
   * Actualiza un video
   * @param {number} id
   * @param {Object} data
   * @returns {Promise}
   */
  async update(id, data) {
    const response = await api.put(`/api/videos/${id}`, data);
    return response.data;
  },

  /**
   * Elimina un video
   * @param {number} id
   * @returns {Promise}
   */
  async delete(id) {
    const response = await api.delete(`/api/videos/${id}`);
    return response.data;
  },

  /**
   * Busca videos
   * @param {string} query
   * @returns {Promise}
   */
  async search(query) {
    const response = await api.get(`/api/videos/buscar?q=${encodeURIComponent(query)}`);
    return response.data;
  },

  /**
   * Obtiene la URL de streaming de un video
   * @param {number} id
   * @returns {string}
   */
  getStreamUrl(id) {
    const baseUrl = import.meta.env.VITE_API_URL || 'http://localhost/backend';
    return `${baseUrl}/api/videos/${id}/stream`;
  },

  /**
   * Obtiene videos populares
   * @returns {Promise}
   */
  async getPopular() {
    const response = await api.get('/api/videos/populares');
    return response.data;
  },

  /**
   * Obtiene videos recomendados
   * @returns {Promise}
   */
  async getRecommended() {
    const response = await api.get('/api/videos/recomendados');
    return response.data;
  },
};

export default videoService;
