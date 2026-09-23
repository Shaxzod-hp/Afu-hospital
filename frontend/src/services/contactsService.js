import api, { unwrap } from './api'

export default {
  // Public
  submit:    (data) => api.post('/contacts', data).then(unwrap),

  // Admin (protected)
  fetchAll:  (page = 1) => api.get('/admin/contacts', { params: { page } }).then(res => res.data),
  markRead:  (id)   => api.put(`/admin/contacts/${id}`, { read: true }).then(unwrap),
  remove:    (id)   => api.delete(`/admin/contacts/${id}`),
}
