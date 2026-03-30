// src/api/evaluaciones.js
import api from './axios';

// Obtener evaluaciones de un grupo
export async function getEvaluaciones(idGrupo) {
  const response = await api.get(`/evaluaciones/${idGrupo}`);
  return response.data;
}

// Guardar evaluaciones (una o varias)
export async function guardarEvaluaciones(data) {
  const response = await api.post('/evaluaciones/guardar', data);
  return response.data;
}