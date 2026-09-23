import api, { unwrap } from './api'

export default {
  fetchAll:  ()     => api.get('/contacts').then(unwrap),
  submit:    (data) => api.post('/contacts', data).then(unwrap),
  markRead:  (id)   => api.patch(`/contacts/${id}/read`).then(unwrap),
  remove:    (id)   => api.delete(`/contacts/${id}`),
}
