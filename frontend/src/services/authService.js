import api from './api.js'

export const authService = {
  register: (data)           => api.post('/auth/register', data),
  login:    (email, password) => api.post('/auth/login', { email, password }),
  profile:  ()               => api.get('/user/profile'),
}
