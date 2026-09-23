import api, { unwrap } from './api'

export default {
  login:  (username, password) => api.post('/auth/login', { username, password }).then(unwrap),
  logout: ()                   => api.post('/auth/logout'),
  me:     ()                   => api.get('/auth/me').then(unwrap),
}
