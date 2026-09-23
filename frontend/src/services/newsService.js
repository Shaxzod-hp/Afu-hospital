import api, { unwrap } from './api'

export default {
  fetchAll: (params = {}) => api.get('/news', { params }).then(unwrap),
  
  // ID yoki SLUG orqali yangilikni olish
  fetchOne: (idOrSlug) => api.get(`/news/${idOrSlug}`).then(unwrap),
  
  create: (data) => api.post('/news', data).then(unwrap),
  
  update: (idOrSlug, data) => {
    if (data instanceof FormData) {
      data.append('_method', 'PUT')
      return api.post(`/news/${idOrSlug}`, data).then(unwrap)
    }
    return api.put(`/news/${idOrSlug}`, data).then(unwrap)
  },
  
  remove: (idOrSlug) => api.delete(`/news/${idOrSlug}`).then(unwrap),
}