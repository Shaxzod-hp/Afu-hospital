// doctorsService.js
import api, { unwrap } from './api'

export default {
  fetchAll: (specializationId = null) => {
    const params = specializationId ? { specialization_id: specializationId } : {}
    return api.get('/doctors', { params }).then(unwrap)
  },

  fetchOne: (idOrSlug) => api.get(`/doctors/${idOrSlug}`).then(unwrap),

  fetchLogs: (slugOrId) => api.get(`/doctors/${slugOrId}/treatment-logs`).then(unwrap),

  adminFetchAll: (specializationId = null) => {
    const params = specializationId ? { specialization_id: specializationId } : {}
    return api.get('/admin/doctors', { params }).then(unwrap)
  },
  adminFetchOne: (id) => api.get(`/admin/doctors/${id}`).then(unwrap),
  create: (formData) =>
    api.post('/admin/doctors', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    }).then(unwrap),
  update: (id, formData) => {
    formData.append('_method', 'PUT')
    return api.post(`/admin/doctors/${id}`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    }).then(unwrap)
  },
  remove: (id) => api.delete(`/admin/doctors/${id}`),
}