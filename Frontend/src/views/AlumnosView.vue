<template>
  <MainLayout v-slot="{ busquedaGlobal }">
    <div class="alumnos-page">

      <h1 class="page-title">Lista de Alumnos</h1>

      <div class="filters-bar">

        <div class="search-group">
          <input
            type="text"
            placeholder="Buscar alumno..."
            v-model="busquedaAlumno"
            class="search-input"
          >
          <svg xmlns="http://www.w3.org/2000/svg" class="search-icon-svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 01-14 0 7 7 0 0114 0z" />
          </svg>
        </div>

        <select v-model="filtroCarrera" class="filter-select">
          <option value="">Carrera</option>
          <option value="Contador Publico">Contador Publico</option>
          <option value="Ingenieria Civil">Ingenieria Civil</option>
          <option value="Ingenieria en Gestion empresarial">Ingenieria en Gestion empresarial</option>
          <option value="Ingenieria en Sistemas Computacionales">Ingenieria en Sistemas Computacionales</option>
          <option value="Ingenieria Industrial">Ingenieria Industrial</option>
        </select>
        <select v-model="filtroSemestre" class="filter-select">
          <option value="">Semestre</option>
          <option value="1">1</option>
          <option value="2">2</option>
          <option value="3">3</option>
          <option value="4">4</option>
          <option value="5">5</option>
          <option value="6">6</option>
          <option value="7">7</option>
          <option value="8">8</option>
        </select>
        <select v-model="filtroEstatus" class="filter-select">
          <option value="">Estatus</option>
          <option value="Activo">Activo</option>
          <option value="Baja Temporal">Baja Temporal</option>
          <option value="Baja Definitiva">Baja Definitiva</option>
        </select>


        <button class="btn-limpiar" @click="resetFilters">
          <svg xmlns="http://www.w3.org/2000/svg" class="reset-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6h12v12" />
          </svg>
          Limpiar filtros
        </button>

        <button class="btn-nuevo" @click="nuevoAlumno">
          + Nuevo Alumno
        </button>
      </div>

      <div class="table-container">
        <table class="alumnos-table" v-if="paginatedAlumnos.length > 0">
          <thead>
            <tr>
              <th>No. Control</th>
              <th>Nombre</th>
              <th>Carrera</th>
              <th>Semestre</th>
              <th>Estatus</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="alumno in paginatedAlumnos" :key="alumno.id">
              <td>{{ alumno.noControl }}</td>
              <td>{{ alumno.nombre }}</td>
              <td>{{ alumno.carrera }}</td>
              <td>{{ alumno.semestre }}</td>
              <td>
                <span class="status-badge" :class="alumno.estatus.toLowerCase().replace(/\s/g, '-')">
                  {{ alumno.estatus }}
                </span>
              </td>
              <td class="actions">
                <button class="btn-action ver" @click="verAlumno(alumno)">Ver</button>
                <button class="btn-action editar" @click="editarAlumno(alumno)">Editar</button>
              </td>
            </tr>
          </tbody>
        </table>

        <div v-else class="empty-state">
          <h3>No hay resultados</h3>
          <p>No se encontraron alumnos con los filtros aplicados.<br>
             Intenta cambiar los criterios de búsqueda.</p>
          <button class="btn-reset" @click="resetFilters">
            Limpiar filtros
          </button>
        </div>
      </div>

      <div class="pagination">
        <div class="pagination-left">
          Filas por página: 
          <select v-model="filasPorPagina" @change="currentPage = 1">
            <option>10</option>
            <option>20</option>
            <option>50</option>
          </select>
        </div>
        <div class="pagination-center">
          Página {{ currentPage }} de {{ totalPages }}
        </div>
        <div class="pagination-right">
          <button @click="prevPage" :disabled="currentPage === 1">‹</button>
          <button v-for="page in visiblePages" :key="page" @click="goToPage(page)" :class="{ active: page === currentPage }">{{ page }}</button>
          <button @click="nextPage" :disabled="currentPage === totalPages">›</button>
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

const busquedaAlumno = ref('')
const filtroCarrera = ref('')
const filtroSemestre = ref('')
const filtroEstatus = ref('')
const filasPorPagina = ref(10)
const currentPage = ref(1)

const props = defineProps({
  busquedaGlobal: { type: String, default: '' }
})

const normalize = (text) => {
  return text
    .toLowerCase()
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
}

const alumnos = ref([
  { id: 1, noControl: '21456987', nombre: 'Sara Pérez', carrera: 'Ingenieria en Sistemas Computacionales', semestre: 6, estatus: 'Activo' },
  { id: 2, noControl: '21463254', nombre: 'Juan García', carrera: 'Ingenieria Industrial', semestre: 4, estatus: 'Activo' },
  { id: 3, noControl: '21454128', nombre: 'Mariela Gómez', carrera: 'Ingenieria Civil', semestre: 8, estatus: 'Activo' },
  { id: 4, noControl: '21454321', nombre: 'Ana Rodríguez', carrera: 'Contador Publico', semestre: 2, estatus: 'Activo' },
  { id: 5, noControl: '21451986', nombre: 'Carlos Torres', carrera: 'Ingenieria en Gestion empresarial', semestre: 5, estatus: 'Baja Temporal' },
  { id: 6, noControl: '21451976', nombre: 'Luis Herrera', carrera: 'Ingenieria en Sistemas Computacionales', semestre: 7, estatus: 'Activo' },
  { id: 7, noControl: '21454833', nombre: 'Pedro Jiménez', carrera: 'Ingenieria Industrial', semestre: 8, estatus: 'Baja Definitiva' },
])

const alumnosFiltrados = computed(() => {
  return alumnos.value.filter(alumno => {
    const coincideGlobal = !props.busquedaGlobal ||
      normalize(alumno.nombre).includes(normalize(props.busquedaGlobal)) ||
      alumno.noControl.includes(props.busquedaGlobal)

    const coincideLocal = !busquedaAlumno.value ||
      normalize(alumno.nombre).includes(normalize(busquedaAlumno.value)) ||
      alumno.noControl.includes(busquedaAlumno.value)

    const coincideCarrera = !filtroCarrera.value || alumno.carrera === filtroCarrera.value
    const coincideSemestre = !filtroSemestre.value || alumno.semestre === parseInt(filtroSemestre.value)
    const coincideEstatus = !filtroEstatus.value || alumno.estatus === filtroEstatus.value

    return coincideGlobal && coincideLocal && coincideCarrera && coincideSemestre && coincideEstatus
  })
})

const totalPages = computed(() => Math.ceil(alumnosFiltrados.value.length / filasPorPagina.value) || 1)
const paginatedAlumnos = computed(() => {
  const start = (currentPage.value - 1) * filasPorPagina.value
  return alumnosFiltrados.value.slice(start, start + filasPorPagina.value)
})
const visiblePages = computed(() => {
  const pages = []
  for (let i = 1; i <= totalPages.value; i++) pages.push(i)
  return pages
})

const goToPage = (page) => { currentPage.value = page }
const prevPage = () => { if (currentPage.value > 1) currentPage.value-- }
const nextPage = () => { if (currentPage.value < totalPages.value) currentPage.value++ }

const resetFilters = () => {
  busquedaAlumno.value = ''
  filtroCarrera.value = ''
  filtroSemestre.value = ''
  filtroEstatus.value = ''
  currentPage.value = 1
}

const nuevoAlumno = () => router.push('/formulario-alumno')
const verAlumno = (a) => alert(`Ver: ${a.nombre}`)
const editarAlumno = (a) => alert(`Editar: ${a.nombre}`)
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap');

.alumnos-page { max-width: 100%; }
.page-title {
  color: #005187;
  font-size: 2rem;
  font-weight: 700;
  letter-spacing: -0.02em;
}

.btn-action.editar {
  background: #4D82BE;
  color: white;
  border: 1px solid #4D82BE;
}

.btn-action.editar:hover {
  background: #3B6EA5;
  border-color: #3B6EA5;
}

.filters-bar {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 1.8rem;
  flex-wrap: nowrap;
  width: 100%;
}

.search-group {
  position: relative;
  flex: 0 0 260px;
  overflow: hidden;
}
.search-input {
  width: 100%;
  padding: 12px 14px 12px 48px;
  border: 1px solid #D1D9E6;
  border-radius: 8px;
  font-size: 1rem;
}
.search-icon-svg {
  position: absolute;
  left: 14px;
  top: 50%;
  transform: translateY(-50%);
  width: 20px;
  height: 20px;
  stroke: #6B7280;
}

.btn-limpiar {
  display: flex;
  align-items: center;
  gap: 8px;
  background: #F5F7FA;
  color: #1A1A1A;
  border: 1px solid #D1D9E6;
  padding: 12px 20px;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  font-size: 1rem;
  white-space: nowrap;
}
.btn-limpiar:hover { background: #EEF2F7; }
.reset-icon { width: 18px; height: 18px; stroke: #6B7280; }


.filter-select {
  padding: 12px 14px;
  border: 1px solid #D1D9E6;
  border-radius: 8px;
  font-size: 1rem;
  flex: 0 0 180px;
  min-width: 180px;
  background: white;
  cursor: pointer;
}


.btn-nuevo { background: #005187; color: white; border: none; padding: 12px 24px; border-radius: 8px; font-weight: 600; cursor: pointer; white-space: nowrap; }

.table-container { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 8px 25px rgba(0,0,0,0.07); }
.alumnos-table { width: 100%; border-collapse: collapse; }
.alumnos-table th { background: #F8FAFC; padding: 16px; text-align: left; font-weight: 600; }
.alumnos-table td { padding: 16px; border-bottom: 1px solid #E5E9F0; }

.status-badge { padding: 5px 14px; border-radius: 20px; font-size: 0.88rem; font-weight: 500; }
.status-badge.activo { background: #E8F5E9; color: #2E7D32; }
.status-badge.baja-temporal { background: #FFF3E0; color: #ED6C02; }
.status-badge.baja-definitiva { background: #FDECEA; color: #D32F2F; }

.actions { display: flex; gap: 8px; }
.btn-action { padding: 7px 16px; border-radius: 6px; font-size: 0.92rem; cursor: pointer; }

.empty-state { text-align: center; padding: 4rem 2rem; color: #9AA3AF; }
.empty-state h3 { font-size: 1.4rem; color: #1A1A1A; margin-bottom: 0.5rem; }
.empty-state p { margin-bottom: 1.5rem; line-height: 1.5; }
.btn-reset { background: #F5F7FA; color: #1A1A1A; border: 1px solid #D1D9E6; padding: 10px 20px; border-radius: 8px; font-weight: 500; cursor: pointer; }

.pagination { margin-top: 2rem; display: flex; justify-content: space-between; align-items: center; font-size: 0.95rem; }
.pagination-left, .pagination-center, .pagination-right { display: flex; align-items: center; gap: 10px; }
.pagination-right button { padding: 6px 12px; border: 1px solid #D1D9E6; background: white; border-radius: 6px; cursor: pointer; }
.pagination-right .active { background: #005187; color: white; border-color: #005187; }
</style>