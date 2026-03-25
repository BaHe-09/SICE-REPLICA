<template>
  <MainLayout v-slot="{ busquedaGlobal }">
    <div class="grupos-page">

      <h1 class="page-title">Gestión de Grupos</h1>

      <div class="filters-bar">
        <div class="search-group">
          <svg xmlns="http://www.w3.org/2000/svg" class="search-icon-svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 01-14 0 7 7 0 0114 0z" />
          </svg>
          <input
            type="text"
            placeholder="Buscar grupo..."
            v-model="busquedaGrupo"
            class="search-input"
            @keyup.enter="aplicarFiltros"
          >
        </div>

        <select v-model="filtroCarrera" class="filter-select" @change="aplicarFiltros">
          <option value="">Carrera</option>
          <option value="Ingeniería en Sistemas Computacionales">Ingenieria en Sistemas Computacionales</option>
          <option value="Ingeniería Industrial">Ingeniería Industrial</option>
          <option value="Ingeniería Civil">Ingeniería Civil</option>
          <option value="Ingeniería en Gestión Empresarial">Ingeniería en Gestión Empresarial</option>
          <option value="Contador Público">Contador Público</option>
        </select>

        <select v-model="filtroSemestre" class="filter-select" @change="aplicarFiltros">
          <option value="">Semestre</option>
          <option v-for="n in 8" :key="n" :value="n">{{ n }}° Semestre</option>
        </select>

        <button class="btn-filtrar" @click="aplicarFiltros">Filtrar</button>
        <button class="btn-limpiar" @click="limpiarFiltros">Limpiar</button>
        <button class="btn-nuevo" @click="nuevoGrupo">+ Nuevo Grupo</button>
      </div>

      <div class="table-container">
        <table class="grupos-table">
          <thead>
            <tr>
              <th>Materia</th>
              <th>Docente</th>
              <th>Aula</th>
              <th class="text-center">Capacidad</th>
              <th class="text-center">Inscritos</th>
              <th class="text-center">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="grupo in paginatedGrupos" :key="grupo.id">
              <td>{{ grupo.materia }}</td>
              <td>{{ grupo.docente }}</td>
              <td>{{ grupo.aula }}</td>
              <td class="text-center">{{ grupo.capacidad }}</td>
              <td class="text-center">
                <span class="inscritos-badge" :class="{ lleno: grupo.inscritos >= grupo.capacidad }">
                  {{ grupo.inscritos }} / {{ grupo.capacidad }}
                </span>
              </td>
              <td class="actions">
                <button class="btn-accion ver" @click="verDetalle(grupo)">Ver</button>
                <button class="btn-accion editar" @click="editarGrupo(grupo)">Editar</button>
                <button class="btn-accion evaluaciones" @click="irAEvaluaciones(grupo)">Evaluaciones</button>
                <button class="btn-accion calificaciones" @click="irACalificaciones(grupo)">Calificaciones</button>
                <button class="btn-accion eliminar" @click="eliminarGrupo(grupo)">Eliminar</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="pagination">
        <div class="pagination-left">
          Mostrando {{ gruposFiltrados.length }} de {{ grupos.length }} grupos disponibles
        </div>
        <div class="pagination-center">
          <button @click="prevPage" :disabled="currentPage === 1">‹</button>
          <button v-for="page in visiblePages" :key="page" @click="goToPage(page)" :class="{ active: page === currentPage }">{{ page }}</button>
          <button @click="nextPage" :disabled="currentPage === totalPages">›</button>
        </div>
      </div>

      <!-- Modal Nuevo / Editar -->
      <div v-if="showModal" class="modal-overlay" @click.self="cerrarModal">
        <div class="modal-content">
          <div class="modal-header">
            <h3>{{ grupoEditar.id ? 'Editar Grupo' : 'Nuevo Grupo' }}</h3>
            <button @click="cerrarModal" class="close-btn">×</button>
          </div>
          <div class="modal-body">
            <div class="form-row">
              <div class="form-group">
                <label>Materia <span class="obligatorio">*</span></label>
                <input v-model="grupoEditar.materia" type="text" class="modal-input">
              </div>
              <div class="form-group">
                <label>Docente <span class="obligatorio">*</span></label>
                <input v-model="grupoEditar.docente" type="text" class="modal-input">
              </div>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label>Aula <span class="obligatorio">*</span></label>
                <input v-model="grupoEditar.aula" type="text" class="modal-input">
              </div>
              <div class="form-group">
                <label>Carrera <span class="obligatorio">*</span></label>
                <select v-model="grupoEditar.carrera" class="modal-select">
                  <option value="">Seleccionar</option>
                  <option value="Ingeniería en Sistemas Computacionales">Ingeniería en Sistemas Computacionales</option>
                  <option value="Ingeniería Industrial">Ingeniería Industrial</option>
                  <option value="Ingeniería Civil">Ingeniería Civil</option>
                  <option value="Ingeniería en Gestión Empresarial">Ingeniería en Gestión Empresarial</option>
                  <option value="Contador Público">Contador Público</option>
                </select>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label>Semestre <span class="obligatorio">*</span></label>
                <select v-model="grupoEditar.semestre" class="modal-select">
                  <option v-for="n in 8" :key="n" :value="n">{{ n }}° Semestre</option>
                </select>
              </div>
              <div class="form-group">
                <label>Capacidad <span class="obligatorio">*</span></label>
                <input v-model="grupoEditar.capacidad" type="number" min="1" class="modal-input">
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn-cancelar" @click="cerrarModal">Cancelar</button>
            <button v-if="grupoEditar.id" class="btn-eliminar" @click="eliminarGrupoDesdeModal">Eliminar</button>
            <button class="btn-guardar" @click="guardarGrupo">Guardar</button>
          </div>
        </div>
      </div>

    </div>
  </MainLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import MainLayout from '@/layouts/MainLayout.vue'

const router = useRouter()

const busquedaGrupo = ref('')
const filtroCarrera = ref('')
const filtroSemestre = ref('')
const filasPorPagina = ref(10)
const currentPage = ref(1)

const grupos = ref([
  { id: 1, materia: 'Base de Datos I', docente: 'Mtro. Pedro Sánchez Gómez', aula: 'B-101', capacidad: 30, inscritos: 24, carrera: 'Ingeniería en Sistemas Computacionales', semestre: 6 },
  { id: 2, materia: 'Contabilidad Financiera', docente: 'Mtro. Luis Ramírez', aula: 'G-301', capacidad: 35, inscritos: 22, carrera: 'Contador Público', semestre: 4 },
  { id: 3, materia: 'Algoritmos y Programación', docente: 'Mtro. Juan Morales López', aula: 'A-201', capacidad: 30, inscritos: 27, carrera: 'Ingeniería en Sistemas Computacionales', semestre: 5 },
  { id: 4, materia: 'Inteligencia Artificial', docente: 'Mtro. Roberto Campos Rivera', aula: 'C-301', capacidad: 20, inscritos: 15, carrera: 'Ingeniería en Sistemas Computacionales', semestre: 8 },
  { id: 5, materia: 'Redes y Comunicaciones', docente: 'Dra. Laura Ortega Ruiz', aula: 'D-405', capacidad: 25, inscritos: 23, carrera: 'Ingeniería Industrial', semestre: 7 },
  { id: 6, materia: 'Desarrollo Web Avanzado', docente: 'Dra. Sofía Herrera López', aula: 'E-112', capacidad: 35, inscritos: 32, carrera: 'Ingeniería en Sistemas Computacionales', semestre: 7 }
])

const gruposFiltrados = computed(() => {
  return grupos.value.filter(g => {
    const coincideBusqueda = !busquedaGrupo.value || 
      g.materia.toLowerCase().includes(busquedaGrupo.value.toLowerCase()) ||
      g.docente.toLowerCase().includes(busquedaGrupo.value.toLowerCase())
    const coincideCarrera = !filtroCarrera.value || g.carrera === filtroCarrera.value
    const coincideSemestre = !filtroSemestre.value || g.semestre === parseInt(filtroSemestre.value)
    return coincideBusqueda && coincideCarrera && coincideSemestre
  })
})

const totalPages = computed(() => Math.ceil(gruposFiltrados.value.length / filasPorPagina.value) || 1)
const startIndex = computed(() => (currentPage.value - 1) * filasPorPagina.value)
const paginatedGrupos = computed(() => gruposFiltrados.value.slice(startIndex.value, startIndex.value + filasPorPagina.value))
const visiblePages = computed(() => {
  const pages = []
  for (let i = 1; i <= totalPages.value; i++) pages.push(i)
  return pages
})

const aplicarFiltros = () => { currentPage.value = 1 }
const limpiarFiltros = () => {
  busquedaGrupo.value = ''
  filtroCarrera.value = ''
  filtroSemestre.value = ''
  currentPage.value = 1
}

const prevPage = () => { if (currentPage.value > 1) currentPage.value-- }
const nextPage = () => { if (currentPage.value < totalPages.value) currentPage.value++ }
const goToPage = (page) => { currentPage.value = page }

const showModal = ref(false)
const grupoEditar = ref({})

const nuevoGrupo = () => {
  grupoEditar.value = { id: null, materia: '', docente: '', aula: '', capacidad: 30, inscritos: 0, carrera: '', semestre: 5 }
  showModal.value = true
}

const editarGrupo = (grupo) => {
  grupoEditar.value = { ...grupo }
  showModal.value = true
}

const cerrarModal = () => {
  showModal.value = false
  grupoEditar.value = {}
}

const guardarGrupo = () => {
  if (grupoEditar.value.id) {
    const index = grupos.value.findIndex(g => g.id === grupoEditar.value.id)
    if (index !== -1) grupos.value[index] = { ...grupoEditar.value }
  } else {
    grupoEditar.value.id = Date.now()
    grupos.value.push({ ...grupoEditar.value })
  }
  cerrarModal()
}

const eliminarGrupoDesdeModal = () => {
  if (grupoEditar.value.id) {
    const index = grupos.value.findIndex(g => g.id === grupoEditar.value.id)
    if (index !== -1) grupos.value.splice(index, 1)
  }
  cerrarModal()
}

const eliminarGrupo = (grupo) => {
  if (confirm(`¿Eliminar el grupo de ${grupo.materia}?`)) {
    const index = grupos.value.findIndex(g => g.id === grupo.id)
    if (index !== -1) grupos.value.splice(index, 1)
  }
}

const verDetalle = (grupo) => {}
const irAEvaluaciones = (grupo) => router.push(`/evaluaciones/${grupo.id}`)
const irACalificaciones = (grupo) => router.push(`/calificaciones/${grupo.id}`)
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap');

.grupos-page { width: 100%; background: #F5F5F5; }

.page-title { color: #1A1A1A; font-size: 2.4rem; font-weight: 700; margin-bottom: 1.8rem; }

.filters-bar { display: flex; align-items: center; gap: 1rem; margin-bottom: 1.8rem; flex-wrap: nowrap; }

.search-group { position: relative; flex: 0 0 260px; min-width: 260px; }
.search-input { width: 100%; padding: 12px 14px 12px 48px; border: 1px solid #E5E7EB; border-radius: 8px; font-size: 1rem; background: #FFFFFF; }
.search-icon-svg { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); width: 20px; height: 20px; stroke: #6B7280; }

.filter-select { padding: 12px 14px; border: 1px solid #E5E7EB; border-radius: 8px; font-size: 1rem; flex: 1 1 180px; min-width: 180px; background: #FFFFFF; }

.btn-filtrar { background: #1B396A; color: white; border: none; padding: 12px 28px; border-radius: 8px; font-weight: 600; cursor: pointer; }
.btn-limpiar { background: #F5F5F5; color: #1A1A1A; border: 1px solid #E5E7EB; padding: 12px 24px; border-radius: 8px; font-weight: 600; cursor: pointer; }
.btn-nuevo { background: #1B396A; color: white; border: none; padding: 12px 24px; border-radius: 8px; font-weight: 600; cursor: pointer; }

.table-container { background: #FFFFFF; border-radius: 12px; overflow: hidden; box-shadow: 0 8px 25px rgba(0,0,0,0.07); border: 1px solid #E5E7EB; }
.grupos-table { width: 100%; border-collapse: collapse; }
.grupos-table th { background: #F5F5F5; padding: 16px; text-align: left; font-weight: 600; color: #1A1A1A; border-bottom: 1px solid #E5E7EB; }
.grupos-table td { padding: 16px; border-bottom: 1px solid #E5E7EB; color: #1A1A1A; }

.inscritos-badge { padding: 5px 14px; border-radius: 20px; font-size: 0.88rem; font-weight: 500; }
.inscritos-badge.lleno { background: #FEF3C7; color: #F59E0B; }

.actions { display: flex; gap: 8px; }
.btn-accion { padding: 7px 16px; border-radius: 6px; font-size: 0.92rem; cursor: pointer; font-weight: 600; }
.btn-accion.ver { background: #F5F5F5; color: #1A1A1A; border: 1px solid #E5E7EB; }
.btn-accion.editar { background: #1B396A; color: white; }
.btn-accion.evaluaciones { background: #2563EB; color: white; }
.btn-accion.calificaciones { background: #6B7280; color: white; }
.btn-accion.eliminar { background: #DC2626; color: white; }

.pagination { margin-top: 2rem; display: flex; justify-content: space-between; align-items: center; font-size: 0.95rem; color: #6B7280; }
.pagination-center button { padding: 6px 12px; border: 1px solid #E5E7EB; background: #FFFFFF; border-radius: 6px; cursor: pointer; }
.pagination-center .active { background: #1B396A; color: white; border-color: #1B396A; }

.modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.6); display: flex; align-items: center; justify-content: center; z-index: 2000; }
.modal-content { background: #FFFFFF; width: 520px; border-radius: 16px; box-shadow: 0 20px 50px rgba(0,0,0,0.15); overflow: hidden; }
.modal-header { background: #1B396A; color: white; padding: 1.2rem 1.8rem; display: flex; justify-content: space-between; align-items: center; }
.modal-body { padding: 2rem 1.8rem; }
.form-row { display: flex; gap: 1.5rem; margin-bottom: 1.6rem; }
.form-group { flex: 1; }
.form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #1A1A1A; }
.modal-input, .modal-select { width: 100%; padding: 12px 16px; border: 1.5px solid #E5E7EB; border-radius: 10px; font-size: 1rem; background: #FFFFFF; }
.modal-footer { padding: 1.2rem 1.8rem; background: #F5F5F5; display: flex; gap: 12px; justify-content: flex-end; }
.btn-cancelar, .btn-eliminar, .btn-guardar { padding: 12px 28px; border-radius: 10px; font-weight: 600; cursor: pointer; }
.btn-cancelar { background: #FFFFFF; color: #1A1A1A; border: 1px solid #E5E7EB; }
.btn-eliminar { background: #DC2626; color: white; border: none; }
.btn-guardar { background: #1B396A; color: white; border: none; }
</style>