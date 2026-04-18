import api from './api.js'

export const appointmentService = {
  getAll: ()           => api.get('/appointments'),
  book:   (data)       => api.post('/appointments', data),
  update: (id, data)   => api.put(`/appointments/${id}`, data),
  cancel: (id)         => api.delete(`/appointments/${id}`),
}
