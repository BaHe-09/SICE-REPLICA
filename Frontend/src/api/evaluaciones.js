import axios from 'axios'

const API = 'http://localhost:8000/api'

export const getEvaluaciones = async (id_grupo) => {
  const { data } = await axios.get(`${API}/evaluaciones/${id_grupo}`)
  return data
}

export const guardarEvaluaciones = async (evaluacion) => {
  const lista = Array.isArray(evaluacion) ? evaluacion : [evaluacion]

  const promesas = lista.map(e => {
    if (e.id_evaluacion) {
      // Si ya existe, actualizar
      return axios.put(`${API}/evaluaciones/${e.id_evaluacion}`, {
        nombre:     e.nombre,
        porcentaje: e.porcentaje,
      })
    } else {
      // Si es nueva, crear
      return axios.post(`${API}/evaluaciones/guardar`, {
        id_grupo:   1, // ← aquí deberías pasar el grupo real
        nombre:     e.nombre,
        porcentaje: e.porcentaje,
      })
    }
  })

  return Promise.all(promesas)
}