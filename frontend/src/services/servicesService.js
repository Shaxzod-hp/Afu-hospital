import api, { unwrap } from './api'

export default {
  fetchAll: () => api.get('/admin/services').then(unwrap),
  fetchOne: (id) => api.get(`/admin/services/${id}`).then(unwrap),
  create: (formData) =>
    api.post('/admin/services', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    }).then(unwrap),
  update: (id, formData) => {
    formData.append('_method', 'PUT')
    return api.post(`/admin/services/${id}`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    }).then(unwrap)
  },
  remove: (id) => api.delete(`/admin/services/${id}`),
}