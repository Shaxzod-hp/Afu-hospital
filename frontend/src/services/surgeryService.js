import api, { unwrap } from './api'

export default {
  // Admin (protected)
  fetchAll: () => api.get('/admin/surgeries').then(unwrap),
  fetchOne: (id) => api.get(`/admin/surgeries/${id}`).then(unwrap),
  create: (formData) =>
    api.post('/admin/surgeries', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    }).then(unwrap),
  update: (id, formData) => {
    formData.append('_method', 'PUT')
    return api.post(`/admin/surgeries/${id}`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    }).then(unwrap)
  },
  remove: (id) => api.delete(`/admin/surgeries/${id}`).then(unwrap),

  // Public (open)
  fetchAllPublic: () => api.get('/surgeries').then(unwrap),
  fetchOnePublic: (id) => api.get(`/surgeries/${id}`).then(unwrap),
}