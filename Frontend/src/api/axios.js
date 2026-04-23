// src/api/axios.js
import axios from 'axios'

const api = axios.create({
  baseURL: 'https://sice-backend.up.railway.app/api',   // 👈 usa el puerto de Laravel
  timeout: 10000,
  headers: {
    'Content-Type': 'application/json'
  }
})

// Interceptor para manejar errores comunes
api.interceptors.response.use(
  response => response,
  error => {
    console.error('Error en la petición:', error.response?.data || error.message)
    return Promise.reject(error)
  }
)

export default api