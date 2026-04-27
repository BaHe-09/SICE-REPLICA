import { createStore } from 'vuex'

const store = createStore({
  state() {
    return {
      // Datos que fluyen entre todos los módulos (tus alumnos, grupos y calificaciones)
      alumnos: [
        { noControl: '21456987', nombre: 'Sara Pérez', carrera: 'Ingeniería en Sistemas Computacionales', semestre: 6, estado: 'Activo' },
        { noControl: '21463254', nombre: 'Juan García', carrera: 'Ingeniería Industrial', semestre: 4, estado: 'Activo' },
        { noControl: '21454128', nombre: 'Mariela Gómez', carrera: 'Ingeniería Civil', semestre: 8, estado: 'Activo' },
        { noControl: '21454321', nombre: 'Ana Rodríguez', carrera: 'Lic. en Administración', semestre: 2, estado: 'Activo' }
      ],
      grupos: [], // aquí irán los grupos de Inscripción y Gestión
      calificaciones: {} // por grupo y alumno
    }
  },
  mutations: {
    agregarAlumno(state, alumno) { state.alumnos.push(alumno) },
    actualizarGrupo(state, grupo) { /* lógica */ }
  },
  getters: {
    alumnosActivos: state => state.alumnos.filter(a => a.estado === 'Activo')
  }
})

export default store