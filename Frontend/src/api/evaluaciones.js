import api from './axios'

export const getEvaluaciones = async (id_grupo) => {
  const { data } = await api.get(`/evaluaciones/${id_grupo}`)
  return data
}

export const eliminarEvaluacion = async (id) => {
  const { data } = await api.delete(`/evaluaciones/${id}`)
  return data
}

export const guardarEvaluaciones = async (evaluacion, id_grupo = 1) => {
  const lista = Array.isArray(evaluacion) ? evaluacion : [evaluacion]
  const promesas = lista.map(e => {
    if (e.id_evaluacion) {
      return api.put(`/evaluaciones/${e.id_evaluacion}`, {
        nombre: e.nombre, porcentaje: e.porcentaje,
      })
    } else {
      return api.post('/evaluaciones/guardar', {
        id_grupo, nombre: e.nombre, porcentaje: e.porcentaje,
      })
    }
  })
  return Promise.all(promesas)
}