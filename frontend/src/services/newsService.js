import api, { unwrap } from './api'

export default {
  // Public
  fetchAll: (params = {}) => api.get('/news', { params }).then(unwrap),

  // ID yoki SLUG orqali yangilikni olish
  fetchOne: (idOrSlug) => api.get(`/news/${idOrSlug}`).then(unwrap),

  // Admin (protected)
  create: (data) => api.post('/admin/news', data).then(unwrap),

  update: (idOrSlug, data) => {
    if (data instanceof FormData) {
      data.append('_method', 'PUT')
      return api.post(`/admin/news/${idOrSlug}`, data).then(unwrap)
    }
    return api.put(`/admin/news/${idOrSlug}`, data).then(unwrap)
  },

  remove: (idOrSlug) => api.delete(`/admin/news/${idOrSlug}`).then(unwrap),
}
