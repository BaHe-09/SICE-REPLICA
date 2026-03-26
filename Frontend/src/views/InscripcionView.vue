<template>
  <MainLayout v-slot="{ busquedaGlobal }">
    <div class="inscripcion-page">

      <div class="breadcrumb">
        Servicios Escolares <span class="arrow">›</span> Inscripción
      </div>

      <h1 class="page-title">Inscripción</h1>
      <p class="subtitle">Seleccione al alumno y asígnelo a un grupo disponible para el periodo vigente.</p>

      <div v-if="notification.message" class="toast" :class="notification.type">
        {{ notification.message }}
      </div>

      <div class="content-card">

        <div class="buscar-alumno-section">
          <h3>Buscar Alumno</h3>
          <div class="search-row">
            <div class="selected-student-wrapper">
              <input 
                type="text" 
                v-model="busquedaAlumno"
                placeholder="Nombre o número de control"
                @keyup.enter="seleccionarAlumno"
              />
              <button class="btn-buscar" @click="seleccionarAlumno">Buscar</button>
            </div>
            <select v-model="periodo" class="period-select">
              <option value="Ago/Dic 2024">Ago/Dic 2024</option>
              <option value="Ene/Jun 2025">Ene/Jun 2025</option>
            </select>
          </div>

          <div v-if="alumnoSeleccionado" class="selected-student">
            {{ alumnoSeleccionado.nombre }} • {{ alumnoSeleccionado.noControl }} • 
            {{ alumnoSeleccionado.carrera }} ({{ alumnoSeleccionado.semestre }} Semestre)
          </div>
        </div>

        <div class="seleccionar-grupo-section">
          <h3>Seleccionar Grupo</h3>
          <div class="group-search-bar">
            <input 
              type="text" 
              v-model="busquedaGrupo"
              placeholder="Buscar grupo..."
              class="group-input"
            />
            <button class="btn-filtrar" @click="filtrarGrupos">Filtrar</button>
          </div>

          <div class="table-container">
            <table class="inscripcion-table">
              <thead>
                <tr>
                  <th>Materia</th>
                  <th>Docente</th>
                  <th>Aula</th>
                  <th>Capacidad</th>
                  <th>Inscritos</th>
                  <th class="text-center">Acciones</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="grupo in gruposFiltrados" :key="grupo.id">
                  <td>{{ grupo.materia }}</td>
                  <td>{{ grupo.docente }}</td>
                  <td>{{ grupo.aula }}</td>
                  <td class="text-center">{{ grupo.capacidad }}</td>
                  <td class="text-center">
                    <span class="inscritos-badge">{{ grupo.inscritos }} / {{ grupo.capacidad }}</span>
                  </td>
                  <td class="text-center">
                    <button
                      class="btn-inscribir"
                      @click="inscribirAlumno(grupo)"
                      :disabled="!alumnoSeleccionado || grupo.inscritos >= grupo.capacidad"
                    >
                      {{ grupo.inscritos >= grupo.capacidad ? 'Sin cupo' : 'Inscribir' }}
                    </button>
                    
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="pagination">
            <span>Página 1 de 4</span>
            <div class="pagination-buttons">
              <button class="page-btn" disabled>‹</button>
              <button class="page-btn active">1</button>
              <button class="page-btn">2</button>
              <button class="page-btn">3</button>
              <button class="page-btn">4</button>
              <button class="page-btn">›</button>
            </div>
            <span class="mostrando">Mostrando 3 de 10 grupos disponibles.</span>
          </div>
        </div>

      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import { ref, computed, reactive, onMounted } from 'vue'
import MainLayout from '@/layouts/MainLayout.vue'
import axios from 'axios'

const busquedaAlumno = ref('')
const busquedaGrupo = ref('')
const periodo = ref('Ago/Dic 2024')
const alumnoSeleccionado = ref(null)

const grupos = ref([])

const notification = reactive({ message: '', type: '' })

const gruposFiltrados = computed(() => {
  if (!busquedaGrupo.value.trim()) return grupos.value

  const texto = busquedaGrupo.value.toLowerCase()
  return grupos.value.filter(grupo =>
    grupo.materia.toLowerCase().includes(texto) ||
    grupo.docente.toLowerCase().includes(texto) ||
    grupo.aula.toLowerCase().includes(texto)
  )
})

const seleccionarAlumno = async () => {
  const termino = busquedaAlumno.value.trim()

  if (!termino) {
    showNotification('Escribe un nombre o número de control', 'error')
    return
  }

  try {
    const response = await axios.get('http://127.0.0.1:8000/api/buscar-alumno', {
      params: { q: termino }
    })

    alumnoSeleccionado.value = {
      id_alumno: response.data.id_alumno,
      noControl: response.data.noControl,
      nombre: response.data.nombre,
      carrera: response.data.carrera,
      semestre: response.data.semestre
    }

    showNotification('Alumno encontrado correctamente', 'success')
  } catch (error) {
    console.error('Error al buscar alumno:', error)
    alumnoSeleccionado.value = null

    if (error.response?.status === 404) {
      showNotification('Alumno no encontrado', 'error')
    } else {
      showNotification('Error al buscar alumno', 'error')
    }
  }
}

const inscribirAlumno = async (grupo) => {
  if (!alumnoSeleccionado.value || !alumnoSeleccionado.value.id_alumno) {
    showNotification('Primero debes buscar y seleccionar un alumno', 'error')
    return
  }

  try {
    const response = await axios.post('http://127.0.0.1:8000/api/inscribir', {
      id_alumno: alumnoSeleccionado.value.id_alumno,
      id_grupo: grupo.id
    })

    showNotification(response.data.message || `Inscripción realizada en ${grupo.materia}`, 'success')

    await cargarGrupos()
  } catch (error) {
    console.error('Error al inscribir:', error)

    if (error.response?.data?.error) {
      showNotification(error.response.data.error, 'error')
    } else {
      showNotification('Error al realizar la inscripción', 'error')
    }
  }
}

const filtrarGrupos = () => {}

const showNotification = (message, type) => {
  notification.message = message
  notification.type = type
  setTimeout(() => { notification.message = '' }, 3000)
}

const cargarGrupos = async () => {
  try {
    const response = await axios.get('http://127.0.0.1:8000/api/grupos-disponibles')
    grupos.value = response.data
  } catch (error) {
    console.error('Error al cargar grupos:', error)
    showNotification('Error al cargar grupos desde la BD', 'error')
  }
}

onMounted(() => {
  cargarGrupos()
})
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap');

.inscripcion-page {
  width: 100%;
  padding: 2rem 2.5rem;
  background: #F5F5F5;
}

.breadcrumb {
  color: #6B7280;
  font-size: 0.95rem;
  margin-bottom: 1rem;
}
.arrow { color: #1A1A1A; font-weight: bold; }

.page-title {
  text-align: left;
  font-size: 2.6rem;
  font-weight: 700;
  color: #1A1A1A;
  margin-bottom: 0.5rem;
}
.subtitle {
  text-align: left;
  color: #6B7280;
  margin-bottom: 2rem;
}

.content-card {
  background: #FFFFFF;
  border-radius: 16px;
  box-shadow: 0 12px 35px rgba(0,0,0,0.09);
  padding: 3rem;
  max-width: 1100px;
  margin: 0 auto;
  border: 1px solid #E5E7EB;
}

.buscar-alumno-section h3 {
  color: #1A1A1A;
  margin-bottom: 1rem;
  font-size: 1.35rem;
}
.search-row {
  display: flex;
  gap: 12px;
  align-items: center;
  flex-wrap: wrap;
}
.selected-student-wrapper { flex: 1; display: flex; }
.selected-student-wrapper input {
  flex: 1;
  padding: 14px 16px;
  border: 1px solid #E5E7EB;
  border-radius: 10px;
  background: #FFFFFF;
  color: #1A1A1A;
}
.btn-buscar {
  background: #1B396A;
  color: white;
  padding: 14px 28px;
  border-radius: 10px;
  font-weight: 600;
  cursor: pointer;
}
.btn-buscar:hover { background: #1D4ED8; }

.period-select {
  padding: 14px 16px;
  border: 1px solid #E5E7EB;
  border-radius: 10px;
  background: #FFFFFF;
  color: #1A1A1A;
}

.selected-student {
  margin-top: 1rem;
  padding: 14px 20px;
  background: #F5F5F5;
  border-radius: 10px;
  font-weight: 500;
  color: #1A1A1A;
  border: 1px solid #E5E7EB;
}

.seleccionar-grupo-section h3 {
  color: #1A1A1A;
  margin: 2.5rem 0 1rem;
  font-size: 1.35rem;
}
.group-search-bar {
  display: flex;
  gap: 12px;
  margin-bottom: 1.5rem;
}
.group-input {
  flex: 1;
  padding: 14px 16px;
  border: 1px solid #E5E7EB;
  border-radius: 10px;
  background: #FFFFFF;
  color: #1A1A1A;
}
.btn-filtrar {
  background: #1B396A;
  color: white;
  padding: 14px 28px;
  border-radius: 10px;
  font-weight: 600;
}
.btn-filtrar:hover { background: #1D4ED8; }

.table-container { overflow-x: auto; }
.inscripcion-table {
  width: 100%;
  border-collapse: collapse;
}
.inscripcion-table th {
  background: #F5F5F5;
  padding: 18px 16px;
  font-weight: 600;
  color: #1A1A1A;
  border-bottom: 1px solid #E5E7EB;
}
.inscripcion-table td {
  padding: 18px 16px;
  border-bottom: 1px solid #E5E7EB;
  color: #1A1A1A;
}
.inscritos-badge { font-weight: 600; color: #1A1A1A; }

.btn-inscribir {
  background: #1B396A;
  color: white;
  padding: 10px 24px;
  border-radius: 8px;
  font-weight: 600;
}
.btn-inscribir:hover { background: #1D4ED8; }
.btn-lleno {
  background: #F5F5F5;
  color: #1A1A1A;
  border: 1px solid #E5E7EB;
  padding: 10px 24px;
  border-radius: 8px;
  font-weight: 600;
  cursor: not-allowed;
}

.pagination {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 2rem;
  font-size: 0.95rem;
  color: #6B7280;
}
.pagination-buttons button {
  margin: 0 4px;
  padding: 6px 12px;
  border: 1px solid #E5E7EB;
  background: #FFFFFF;
  border-radius: 6px;
  color: #1A1A1A;
}
.pagination-buttons .active {
  background: #1B396A;
  color: white;
}

.toast {
  position: fixed;
  top: 90px;
  right: 30px;
  padding: 14px 24px;
  border-radius: 8px;
  color: white;
  font-weight: 500;
  box-shadow: 0 6px 20px rgba(0,0,0,0.25);
  z-index: 9999;
  animation: slideIn 0.3s ease;
}
.toast.success { background: #16A34A; }
.toast.error { background: #DC2626; }
@keyframes slideIn { from { transform: translateX(120%); } to { transform: translateX(0); } }
</style>