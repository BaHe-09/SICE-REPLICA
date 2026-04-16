import axios from 'axios'

const API = '${import.meta.env.VITE_API_URL}/api'

export const getEvaluaciones = async (id_grupo) => {
  const { data } = await axios.get(`${API}/evaluaciones/${id_grupo}`)
  return data
}
export const eliminarEvaluacion = async (id) => {
  const { data } = await axios.delete(`${API}/evaluaciones/${id}`)
  return data
}
export const guardarEvaluaciones = async (evaluacion, id_grupo = 1) => {
  const lista = Array.isArray(evaluacion) ? evaluacion : [evaluacion]
  const promesas = lista.map(e => {
    if (e.id_evaluacion) {
      return axios.put(`${API}/evaluaciones/${e.id_evaluacion}`, {
        nombre: e.nombre, porcentaje: e.porcentaje,
      })
    } else {
      return axios.post(`${API}/evaluaciones/guardar`, {
        id_grupo, nombre: e.nombre, porcentaje: e.porcentaje,
      })
    }
  })
  return Promise.all(promesas)
}