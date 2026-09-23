import api, { unwrap } from './api'

export default {
  fetchAll: () => api.get('/admin/specializations').then(unwrap),
  fetchOne: (id) => api.get(`/admin/specializations/${id}`).then(unwrap),
  create: (formData) => api.post('/admin/specializations', formData, {
    headers: { 'Content-Type': 'multipart/form-data' }
  }).then(unwrap),
  update: (id, formData) => {
    // If formData is FormData instance, attach _method PUT for Laravel multipart updates
    if (formData instanceof FormData) {
      if (!formData.has('_method')) {
        formData.append('_method', 'PUT')
      }
      return api.post(`/admin/specializations/${id}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      }).then(unwrap)
    }
    return api.put(`/admin/specializations/${id}`, formData).then(unwrap)
  },
  remove: (id) => api.delete(`/admin/specializations/${id}`),
}
