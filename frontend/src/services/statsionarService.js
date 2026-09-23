import api, { unwrap } from './api'

export default {
  fetchAll: () => api.get('/admin/statsionar').then(unwrap),
  fetchOne: (id) => api.get(`/admin/statsionar/${id}`).then(unwrap),
  create: (formData) =>
    api.post('/admin/statsionar', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    }).then(unwrap),
  update: (id, formData) => {
    formData.append('_method', 'PUT')
    return api.post(`/admin/statsionar/${id}`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    }).then(unwrap)
  },
  remove: (id) => api.delete(`/admin/statsionar/${id}`),
}