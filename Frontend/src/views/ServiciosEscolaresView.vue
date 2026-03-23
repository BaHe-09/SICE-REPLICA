<template>
  <MainLayout v-slot="{ busquedaGlobal }">
    <div class="servicios-escolares-page">

      <div class="breadcrumb">
        Servicios Escolares
      </div>

      <h1 class="page-title">Servicios Escolares</h1>

      <div v-if="notification.message" class="toast" :class="notification.type">
        {{ notification.message }}
      </div>

      <div class="content-card">

        <div class="stats-grid">

          <div class="stat-card blue">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14v6m-3-3h6M6 10h2a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2zm10 0h2a2 2 0 002-2v-2a2 2 0 00-2-2h-2a2 2 0 00-2 2v2a2 2 0 002 2zM6 20h2a2 2 0 002-2v-2a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2" />
            </svg>
            <div class="info">
              <h3>Alumnos Activos</h3>
              <p class="number">{{ totalAlumnos }}</p>
              <a href="#" class="ver-link" @click.prevent="irAAlumnos">Ver Alumnos →</a>
            </div>
          </div>

          <div class="stat-card">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18 9.246 18 10.832 18.477 12 19.253zm0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18 14.754 18 13.168 18.477 12 19.253z" />
            </svg>
            <div class="info">
              <h3>Inscripciones del Período</h3>
              <p class="number">{{ totalInscripciones }}</p>
              <a href="#" class="ver-link" @click.prevent="irAInscripciones">Ver Inscripciones →</a>
            </div>
          </div>

          <div class="stat-card">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14v6m-3-3h6M6 10h2a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2zm10 0h2a2 2 0 002-2v-2a2 2 0 00-2-2h-2a2 2 0 00-2 2v2a2 2 0 002 2zM6 20h2a2 2 0 002-2v-2a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2" />
            </svg>
            <div class="info">
              <h3>Grupos Abiertos</h3>
              <p class="number">24</p>
              <a href="#" class="ver-link" @click.prevent="irAGrupos">Ver Grupos →</a>
            </div>
          </div>

          <div class="stat-card">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 8.944 11.922.42.095.858.143 1.295.143a3 3 0 01.935-.072" />
            </svg>
            <div class="info">
              <h3>Evaluaciones pendientes</h3>
              <p class="number">{{ totalEvaluaciones }}</p>
              <a href="#" class="ver-link" @click.prevent="irAEvaluaciones">Ver Evaluaciones →</a>
            </div>
          </div>
        </div>

        <div class="mensaje-importante">
          <svg xmlns="http://www.w3.org/2000/svg" class="mensaje-icon" fill="none" viewBox="0 0 24 24" stroke="#D2B48C" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
          </svg>
          <div class="mensaje-texto">
            <strong>Mensaje Importante</strong><br>
            Recuerda que hoy es la fecha límite para inscripciones del periodo vigente. ¡No olvides registrar a los alumnos pendientes!
          </div>
          <button class="btn-nueva-inscripcion" @click="nuevaInscripcion">
            + Nueva Inscripción
          </button>
        </div>

        <div class="ultimas-inscripciones">
          <div class="table-header">
            <h3>Últimas Inscripciones</h3>
            <div class="filtros">
              <input type="text" v-model="busquedaTabla" placeholder="Buscar alumno..." class="search-input">
              <select v-model="filtroCarrera" class="filter-select">
                <option value="">Carrera</option>
                <option value="Ingeniería en Sistemas Computacionales">Ingeniería en Sistemas Computacionales</option>
                <option value="Ingeniería Industrial">Ingeniería Industrial</option>
              </select>
              <select v-model="filtroSemestre" class="filter-select">
                <option value="">Semestre</option>
                <option v-for="n in 8" :key="n" :value="n">{{ n }}</option>
              </select>
              <select v-model="filtroEstatus" class="filter-select">
                <option value="">Estatus</option>
                <option value="Activo">Activo</option>
              </select>
              <button class="btn-gestionar" @click="gestionarTodo">Gestionar</button>
            </div>
          </div>

          <div class="table-container">
            <table class="inscripciones-table">
              <thead>
                <tr>
                  <th>No. Control</th>
                  <th>Nombre</th>
                  <th>Carrera</th>
                  <th>Semestre</th>
                  <th>Fecha Inscripción</th>
                  <th>Estatus</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in inscripcionesFiltradas" :key="item.id">
                  <td>{{ item.noControl }}</td>
                  <td>{{ item.nombre }}</td>
                  <td>{{ item.carrera }}</td>
                  <td class="text-center">{{ item.semestre }}</td>
                  <td>{{ item.fecha }}</td>
                  <td><span class="estatus-badge">Activo</span></td>
                  <td class="text-right">⋯</td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="pagination">
            <span>Página 1 de 9</span>
            <div class="pagination-buttons">
              <button class="page-btn" disabled>‹</button>
              <button class="page-btn active">1</button>
              <button class="page-btn">2</button>
              <button class="page-btn">3</button>
              <button class="page-btn">4</button>
              <button class="page-btn">9</button>
              <button class="page-btn">›</button>
            </div>
          </div>
        </div>

      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import { ref, computed, reactive, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import MainLayout from '@/layouts/MainLayout.vue'

const router = useRouter()

const busquedaTabla = ref('')
const filtroCarrera = ref('')
const filtroSemestre = ref('')
const filtroEstatus = ref('')

const notification = reactive({ message: '', type: '' })

const inscripciones = ref([
  { id: 1, noControl: '21456987', nombre: 'Sara Pérez', carrera: 'Ingeniería en Sistemas Computacionales', semestre: 6, fecha: '23 abr 2024' },
  { id: 2, noControl: '21463254', nombre: 'Juan García', carrera: 'Ingeniería Industrial', semestre: 4, fecha: '22 abr 2024' },
  { id: 3, noControl: '21454128', nombre: 'Mariela Gómez', carrera: 'Ingeniería Civil', semestre: 8, fecha: '22 abr 2024' },
  { id: 4, noControl: '21454321', nombre: 'Ana Rodríguez', carrera: 'Lic. en Administración', semestre: 2, fecha: '22 abr 2024' }
])

const inscripcionesFiltradas = computed(() => inscripciones.value)

const totalAlumnos = ref(0)
const totalInscripciones = ref(0)
const totalEvaluaciones = ref(0)

const cargarResumen = async () => {
  try {
    const response = await fetch('http://localhost:8000/api/resumen-escolar')
    if (!response.ok) throw new Error('Error')
    const data = await response.json()
    totalAlumnos.value = data.total_alumnos
    totalInscripciones.value = data.total_inscripciones
  } catch (error) {
    console.error('Error cargando resumen:', error)
  }
}

onMounted(() => {
  cargarResumen()
})

const irAAlumnos = () => router.push('/alumnos')
const irAInscripciones = () => router.push('/inscripcion')
const irAGrupos = () => router.push('/gestion-grupos')
const irAEvaluaciones = () => router.push('/evaluaciones')
const nuevaInscripcion = () => router.push('/inscripcion')
const gestionarTodo = () => {}

const showNotification = (message, type) => {
  notification.message = message
  notification.type = type
  setTimeout(() => { notification.message = '' }, 3000)
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap');

.servicios-escolares-page {
  width: 100%;
  padding: 2rem 2.5rem;
}

.breadcrumb {
  color: #5A5A5A;
  font-size: 0.95rem;
  margin-bottom: 1rem;
}

.page-title {
  text-align: left;
  font-size: 2.4rem;
  font-weight: 700;
  color: #1A1A1A;
  margin-bottom: 2rem;
}

.content-card {
  background: white;
  border-radius: 16px;
  box-shadow: 0 12px 35px rgba(0,0,0,0.09);
  padding: 3rem;
  max-width: 1100px;
  margin: 0 auto;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
  gap: 1.5rem;
  margin-bottom: 3rem;
}

.stat-card {
  background: #F5F7FA;
  border-radius: 14px;
  padding: 1.8rem;
  display: flex;
  align-items: center;
  gap: 1.2rem;
}

.stat-card.blue {
  background: #005187;
  color: white;
}

.icon {
  width: 48px;
  height: 48px;
}

.info h3 { font-size: 1.1rem; margin: 0 0 0.3rem 0; font-weight: 600; }
.number { font-size: 2.4rem; font-weight: 700; margin: 0; }
.ver-link { color: #005187; font-weight: 600; text-decoration: none; }
.stat-card.blue .ver-link { color: white; }

.mensaje-importante {
  background: #FFF9E6;
  border-left: 6px solid #D2B48C;
  border-radius: 12px;
  padding: 1.4rem 1.8rem;
  display: flex;
  align-items: center;
  gap: 1.2rem;
  margin-bottom: 3rem;
}

.mensaje-icon {
  width: 36px;
  height: 36px;
}

.mensaje-texto {
  flex: 1;
  color: #1A1A1A;
  line-height: 1.5;
}

.btn-nueva-inscripcion {
  background: #005187;
  color: white;
  padding: 12px 28px;
  border-radius: 10px;
  font-weight: 600;
  border: none;
  cursor: pointer;
}

.table-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 1rem;
  margin-bottom: 1rem;
}

.filtros {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}

.search-input, .filter-select {
  padding: 12px 16px;
  border: 1px solid #D1D9E6;
  border-radius: 10px;
}

.btn-gestionar {
  background: #005187;
  color: white;
  padding: 12px 26px;
  border-radius: 10px;
  font-weight: 600;
}

.inscripciones-table {
  width: 100%;
  border-collapse: collapse;
}

.inscripciones-table th {
  background: #F5F7FA;
  padding: 18px 16px;
  font-weight: 600;
  color: #1A1A1A;
}

.inscripciones-table td {
  padding: 18px 16px;
  border-bottom: 1px solid #E0E7FF;
}

.estatus-badge {
  background: #2E7D32;
  color: white;
  padding: 4px 14px;
  border-radius: 20px;
  font-size: 0.85rem;
  font-weight: 600;
}

.pagination {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 2rem;
  color: #5A5A5A;
  font-size: 0.95rem;
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
.toast.success { background: #2E7D32; }
.toast.error { background: #D32F2F; }
@keyframes slideIn { from { transform: translateX(120%); } to { transform: translateX(0); } }
</style>