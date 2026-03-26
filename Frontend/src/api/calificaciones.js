// src/api/calificaciones.js
import api from './axios';

// Obtener calificaciones de un grupo
export async function getCalificacionesGrupo(params = {}) {
  const response = await api.get('/calificaciones-grupo', { params });
  return response.data;
}

// Guardar calificaciones (una o varias)
export async function guardarCalificaciones(data) {
  const response = await api.post('/guardar-calificaciones', data);
  return response.data;
}