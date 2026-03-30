<template>
  <MainLayout v-slot="{ busquedaGlobal }">
    <div class="grupos-page" @keydown="manejarTeclado" tabindex="-1" ref="paginaRef">

      <h1 class="page-title">Gestión de Grupos</h1>
      <div class="atajos-barra">
        <span><kbd>Ctrl+M</kbd> Nuevo grupo</span>
        <span><kbd>Ctrl+F</kbd> Buscar por número de control</span>
        <span><kbd>Ctrl+B</kbd> Buscar por materia</span>
        <span><kbd>Ctrl+L</kbd> Limpiar filtros</span>
        <span><kbd>↑↓</kbd> Navegar filas</span>
        <span><kbd>Enter</kbd> Editar fila seleccionada</span>
        <span><kbd>Esc</kbd> Cerrar modal</span>
      </div>

      <div class="filters-bar">

        <!-- Búsqueda prioritaria por número de control -->
        <div class="search-group search-control">
          <svg xmlns="http://www.w3.org/2000/svg" class="search-icon-svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 01-14 0 7 7 0 0114 0z" />
          </svg>
          <input
            type="text"
            placeholder="Número de control del alumno"
            v-model="busquedaControl"
            class="search-input"
            ref="inputControlRef"
            @keyup.enter="aplicarFiltros"
          >
        </div>

        <!-- Búsqueda secundaria por materia o docente -->
        <div class="search-group">
          <svg xmlns="http://www.w3.org/2000/svg" class="search-icon-svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 01-14 0 7 7 0 0114 0z" />
          </svg>
          <input
            type="text"
            placeholder="Materia o docente..."
            v-model="busquedaGrupo"
            class="search-input"
            ref="inputBusquedaRef"
            @keyup.enter="aplicarFiltros"
          >
        </div>

        <select v-model="filtroCarrera" class="filter-select" @change="aplicarFiltros">
          <option value="">Carrera</option>
          <option value="Ingeniería en Sistemas Computacionales">Ingeniería en Sistemas Computacionales</option>
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

      <!-- Indicador de búsqueda por número de control activa -->
      <div v-if="busquedaControl.trim()" class="control-aviso">
        Mostrando grupos donde está inscrito el alumno con número de control: <strong>{{ busquedaControl }}</strong>
      </div>

      <div class="table-container" :class="{ 'loading-state': cargando }">
        <div v-if="cargando" class="loading-overlay">
          <div class="loading-spinner"></div>
          <span>{{ mensajeCarga }}</span>
        </div>
        <table class="grupos-table">
          <thead>
            <tr>
              <th>Materia</th>
              <th>Docente</th>
              <th>Aula</th>
              <th>Horario</th>
              <th class="text-center">Capacidad</th>
              <th class="text-center">Inscritos</th>
              <th class="text-center">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(grupo, idx) in paginatedGrupos" :key="grupo.id" :class="{ 'fila-activa': filaActiva === idx }" @click="filaActiva = idx" :tabindex="0" @keydown.enter="editarGrupo(grupo)">
              <td>{{ grupo.materia }}</td>
              <td>{{ grupo.docente }}</td>
              <td>{{ grupo.aula }}</td>
              <td>
                <span v-if="grupo.horario && grupo.horario.dia" class="horario-badge">
                  {{ grupo.horario.dia }} {{ grupo.horario.horaInicio }} - {{ grupo.horario.horaFin }}
                </span>
                <span v-else class="sin-horario">Sin horario</span>
              </td>
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

            <!-- Fila 1: Materia y Docente -->
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

            <!-- Fila 2: Aula y Carrera -->
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

            <!-- Fila 3: Semestre y Capacidad -->
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

            <!-- Fila 4: Horario -->
            <div class="form-section-title">Horario</div>
            <div class="form-row horario-row">
              <div class="form-group">
                <label>Día <span class="obligatorio">*</span></label>
                <select v-model="grupoEditar.horario.dia" class="modal-select">
                  <option value="">Seleccionar</option>
                  <option value="Lunes">Lunes</option>
                  <option value="Martes">Martes</option>
                  <option value="Miércoles">Miércoles</option>
                  <option value="Jueves">Jueves</option>
                  <option value="Viernes">Viernes</option>
                  <option value="Sábado">Sábado</option>
                  <option value="Lunes y Miércoles">Lunes y Miércoles</option>
                  <option value="Martes y Jueves">Martes y Jueves</option>
                  <option value="Lunes, Miércoles y Viernes">Lunes, Miércoles y Viernes</option>
                </select>
              </div>
              <div class="form-group">
                <label>Hora inicio <span class="obligatorio">*</span></label>
                <input v-model="grupoEditar.horario.horaInicio" type="time" class="modal-input">
              </div>
              <div class="form-group">
                <label>Hora fin <span class="obligatorio">*</span></label>
                <input v-model="grupoEditar.horario.horaFin" type="time" class="modal-input">
              </div>
            </div>

          </div>
          <div class="modal-footer">
            <button class="btn-cancelar" @click="cerrarModal" :disabled="cargando">Cancelar</button>
            <button v-if="grupoEditar.id" class="btn-eliminar" @click="eliminarGrupoDesdeModal" :disabled="cargando">Eliminar</button>
            <button class="btn-guardar" @click="guardarGrupo" :disabled="cargando">
              <span v-if="cargando" class="spinner-btn"></span>
              {{ cargando ? 'Guardando...' : 'Guardar' }}
            </button>
          </div>
        </div>
      </div>

    </div>
  </MainLayout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue'
import { useRouter } from 'vue-router'
import MainLayout from '@/layouts/MainLayout.vue'

const router = useRouter()

const busquedaControl = ref('')
const busquedaGrupo = ref('')
const filtroCarrera = ref('')
const filtroSemestre = ref('')
const filasPorPagina = ref(10)
const currentPage = ref(1)
const cargando = ref(false)
const mensajeCarga = ref('')

const filaActiva = ref(-1)
const inputControlRef = ref(null)
const inputBusquedaRef = ref(null)
const paginaRef = ref(null)

const simularCarga = (mensaje, fn, ms = 600) => {
  cargando.value = true
  mensajeCarga.value = mensaje
  setTimeout(() => {
    fn()
    cargando.value = false
    mensajeCarga.value = ''
  }, ms)
}

// ── Navegación por teclado ──────────────────────────────────────
const manejarTeclado = (e) => {
  // Ignorar si el foco está en un input, select o textarea
  const tag = document.activeElement?.tagName
  const enCampo = ['INPUT', 'SELECT', 'TEXTAREA'].includes(tag)

  // Atajos globales (funcionan siempre)
  if (e.key === 'Escape') {
    if (showModal.value) cerrarModal()
    return
  }

  if (e.ctrlKey) {
    switch (e.key.toLowerCase()) {
      case 'm':
        e.preventDefault()
        nuevoGrupo()
        break
      case 'f':
        e.preventDefault()
        nextTick(() => inputControlRef.value?.focus())
        break
      case 'b':
        e.preventDefault()
        nextTick(() => inputBusquedaRef.value?.focus())
        break
      case 'l':
        e.preventDefault()
        limpiarFiltros()
        break
    }
    return
  }

  // Navegación con flechas en la tabla (solo si no está en un campo)
  if (!enCampo && !showModal.value) {
    const total = paginatedGrupos.value.length
    if (e.key === 'ArrowDown') {
      e.preventDefault()
      filaActiva.value = Math.min(filaActiva.value + 1, total - 1)
    } else if (e.key === 'ArrowUp') {
      e.preventDefault()
      filaActiva.value = Math.max(filaActiva.value - 1, 0)
    } else if (e.key === 'ArrowRight') {
      e.preventDefault()
      nextPage()
      filaActiva.value = 0
    } else if (e.key === 'ArrowLeft') {
      e.preventDefault()
      prevPage()
      filaActiva.value = 0
    }
  }
}

onMounted(() => {
  window.addEventListener('keydown', manejarTeclado)
  nextTick(() => paginaRef.value?.focus())
})
onUnmounted(() => {
  window.removeEventListener('keydown', manejarTeclado)
})

// ── Datos ────────────────────────────────────────────────────────
const grupos = ref([
  {
    id: 1,
    materia: 'Base de Datos I',
    docente: 'Mtro. Pedro Sánchez Gómez',
    aula: 'B-101',
    capacidad: 30,
    inscritos: 24,
    carrera: 'Ingeniería en Sistemas Computacionales',
    semestre: 6,
    horario: { dia: 'Lunes y Miércoles', horaInicio: '07:00', horaFin: '09:00' },
    alumnos: [
      { noControl: '21456987', nombre: 'Sara Pérez' },
      { noControl: '21456900', nombre: 'Luis Gómez' },
      { noControl: '20301122', nombre: 'Ana Torres' }
    ]
  },
  {
    id: 2,
    materia: 'Contabilidad Financiera',
    docente: 'Mtro. Luis Ramírez',
    aula: 'G-301',
    capacidad: 35,
    inscritos: 22,
    carrera: 'Contador Público',
    semestre: 4,
    horario: { dia: 'Martes y Jueves', horaInicio: '09:00', horaFin: '11:00' },
    alumnos: [
      { noControl: '21456988', nombre: 'Pedro Ruiz' },
      { noControl: '20301123', nombre: 'María López' }
    ]
  },
  {
    id: 3,
    materia: 'Algoritmos y Programación',
    docente: 'Mtro. Juan Morales López',
    aula: 'A-201',
    capacidad: 30,
    inscritos: 27,
    carrera: 'Ingeniería en Sistemas Computacionales',
    semestre: 5,
    horario: { dia: 'Lunes, Miércoles y Viernes', horaInicio: '11:00', horaFin: '13:00' },
    alumnos: [
      { noControl: '21456987', nombre: 'Sara Pérez' },
      { noControl: '21000001', nombre: 'Carlos Hernández' }
    ]
  },
  {
    id: 4,
    materia: 'Inteligencia Artificial',
    docente: 'Mtro. Roberto Campos Rivera',
    aula: 'C-301',
    capacidad: 20,
    inscritos: 15,
    carrera: 'Ingeniería en Sistemas Computacionales',
    semestre: 8,
    horario: { dia: 'Martes y Jueves', horaInicio: '13:00', horaFin: '15:00' },
    alumnos: [
      { noControl: '21000002', nombre: 'Diana Flores' }
    ]
  },
  {
    id: 5,
    materia: 'Redes y Comunicaciones',
    docente: 'Dra. Laura Ortega Ruiz',
    aula: 'D-405',
    capacidad: 25,
    inscritos: 23,
    carrera: 'Ingeniería Industrial',
    semestre: 7,
    horario: { dia: 'Viernes', horaInicio: '07:00', horaFin: '10:00' },
    alumnos: [
      { noControl: '21000003', nombre: 'Roberto Jiménez' },
      { noControl: '20301122', nombre: 'Ana Torres' }
    ]
  },
  {
    id: 6,
    materia: 'Desarrollo Web Avanzado',
    docente: 'Dra. Sofía Herrera López',
    aula: 'E-112',
    capacidad: 35,
    inscritos: 32,
    carrera: 'Ingeniería en Sistemas Computacionales',
    semestre: 7,
    horario: { dia: 'Lunes y Miércoles', horaInicio: '15:00', horaFin: '17:00' },
    alumnos: [
      { noControl: '21456987', nombre: 'Sara Pérez' },
      { noControl: '21000004', nombre: 'Lucía Vargas' }
    ]
  }
])

const gruposFiltrados = computed(() => {
  return grupos.value.filter(g => {
    // Prioridad: número de control del alumno inscrito
    const coincideControl = !busquedaControl.value ||
      g.alumnos.some(a => a.noControl.includes(busquedaControl.value.trim()))
    // Secundario: materia o docente
    const coincideBusqueda = !busquedaGrupo.value ||
      g.materia.toLowerCase().includes(busquedaGrupo.value.toLowerCase()) ||
      g.docente.toLowerCase().includes(busquedaGrupo.value.toLowerCase())
    const coincideCarrera = !filtroCarrera.value || g.carrera === filtroCarrera.value
    const coincideSemestre = !filtroSemestre.value || g.semestre === parseInt(filtroSemestre.value)
    return coincideControl && coincideBusqueda && coincideCarrera && coincideSemestre
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

const aplicarFiltros = () => {
  simularCarga('Aplicando filtros...', () => {
    currentPage.value = 1
    filaActiva.value = -1
  })
}
const limpiarFiltros = () => {
  simularCarga('Limpiando filtros...', () => {
    busquedaControl.value = ''
    busquedaGrupo.value = ''
    filtroCarrera.value = ''
    filtroSemestre.value = ''
    currentPage.value = 1
    filaActiva.value = -1
  })
}

const prevPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--
    filaActiva.value = -1
  }
}
const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++
    filaActiva.value = -1
  }
}
const goToPage = (page) => { currentPage.value = page }

const showModal = ref(false)
const grupoEditar = ref({})

const nuevoGrupo = () => {
  grupoEditar.value = {
    id: null,
    materia: '',
    docente: '',
    aula: '',
    capacidad: 30,
    inscritos: 0,
    carrera: '',
    semestre: 5,
    horario: { dia: '', horaInicio: '', horaFin: '' }
  }
  showModal.value = true
}

const editarGrupo = (grupo) => {
  grupoEditar.value = {
    ...grupo,
    horario: { ...grupo.horario }
  }
  showModal.value = true
}

const cerrarModal = () => {
  showModal.value = false
  grupoEditar.value = {}
}

const guardarGrupo = () => {
  const esEdicion = !!grupoEditar.value.id
  simularCarga(esEdicion ? 'Guardando cambios...' : 'Creando grupo...', () => {
    if (esEdicion) {
      const index = grupos.value.findIndex(g => g.id === grupoEditar.value.id)
      if (index !== -1) grupos.value[index] = { ...grupoEditar.value, horario: { ...grupoEditar.value.horario } }
    } else {
      grupoEditar.value.id = Date.now()
      grupos.value.push({ ...grupoEditar.value, horario: { ...grupoEditar.value.horario } })
    }
    cerrarModal()
  })
}

const eliminarGrupoDesdeModal = () => {
  simularCarga('Eliminando grupo...', () => {
    if (grupoEditar.value.id) {
      const index = grupos.value.findIndex(g => g.id === grupoEditar.value.id)
      if (index !== -1) grupos.value.splice(index, 1)
    }
    cerrarModal()
  })
}

const eliminarGrupo = (grupo) => {
  if (confirm(`¿Eliminar el grupo de ${grupo.materia}?`)) {
    simularCarga('Eliminando grupo...', () => {
      const index = grupos.value.findIndex(g => g.id === grupo.id)
      if (index !== -1) grupos.value.splice(index, 1)
    })
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

.horario-badge {
  display: inline-block;
  background: #EFF6FF;
  color: #1B396A;
  border: 1px solid #BFDBFE;
  border-radius: 6px;
  padding: 4px 10px;
  font-size: 0.85rem;
  font-weight: 500;
  white-space: nowrap;
}
.sin-horario {
  color: #9CA3AF;
  font-size: 0.85rem;
  font-style: italic;
}

.inscritos-badge { padding: 5px 14px; border-radius: 20px; font-size: 0.88rem; font-weight: 500; }
.inscritos-badge.lleno { background: #FEF3C7; color: #F59E0B; }

.actions { display: flex; gap: 8px; }
.btn-accion { padding: 7px 16px; border-radius: 6px; font-size: 0.92rem; cursor: pointer; font-weight: 600; border: none; }
.btn-accion.ver { background: #F5F5F5; color: #1A1A1A; border: 1px solid #E5E7EB; }
.btn-accion.editar { background: #1B396A; color: white; }
.btn-accion.evaluaciones { background: #2563EB; color: white; }
.btn-accion.calificaciones { background: #6B7280; color: white; }
.btn-accion.eliminar { background: #DC2626; color: white; }

.pagination { margin-top: 2rem; display: flex; justify-content: space-between; align-items: center; font-size: 0.95rem; color: #6B7280; }
.pagination-center button { padding: 6px 12px; border: 1px solid #E5E7EB; background: #FFFFFF; border-radius: 6px; cursor: pointer; }
.pagination-center .active { background: #1B396A; color: white; border-color: #1B396A; }

.modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.6); display: flex; align-items: center; justify-content: center; z-index: 2000; }
.modal-content { background: #FFFFFF; width: 580px; border-radius: 16px; box-shadow: 0 20px 50px rgba(0,0,0,0.15); overflow: hidden; }
.modal-header { background: #1B396A; color: white; padding: 1.2rem 1.8rem; display: flex; justify-content: space-between; align-items: center; }
.modal-header h3 { margin: 0; font-size: 1.1rem; }
.close-btn { background: transparent; border: none; color: white; font-size: 1.6rem; cursor: pointer; line-height: 1; }
.modal-body { padding: 2rem 1.8rem; }

.form-section-title {
  font-size: 0.85rem;
  font-weight: 700;
  color: #6B7280;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  margin-bottom: 1rem;
  padding-bottom: 0.5rem;
  border-bottom: 1px solid #E5E7EB;
}

.form-row { display: flex; gap: 1.5rem; margin-bottom: 1.6rem; }
.horario-row { margin-bottom: 0; }
.form-group { flex: 1; }
.form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #1A1A1A; font-size: 0.95rem; }
.obligatorio { color: #DC2626; }
.modal-input, .modal-select { width: 100%; padding: 12px 16px; border: 1.5px solid #E5E7EB; border-radius: 10px; font-size: 1rem; background: #FFFFFF; box-sizing: border-box; }

.modal-footer { padding: 1.2rem 1.8rem; background: #F5F5F5; display: flex; gap: 12px; justify-content: flex-end; }
.btn-cancelar, .btn-eliminar, .btn-guardar { padding: 12px 28px; border-radius: 10px; font-weight: 600; cursor: pointer; }
.btn-cancelar { background: #FFFFFF; color: #1A1A1A; border: 1px solid #E5E7EB; }
.btn-eliminar { background: #DC2626; color: white; border: none; }
.btn-guardar { background: #1B396A; color: white; border: none; }

.search-control .search-input {
  border-color: #1B396A;
  border-width: 2px;
}

.control-aviso {
  background: #EFF6FF;
  border: 1px solid #BFDBFE;
  border-radius: 8px;
  padding: 10px 16px;
  margin-bottom: 1rem;
  font-size: 0.92rem;
  color: #1B396A;
}

.table-container { position: relative; }
.loading-state { pointer-events: none; opacity: 0.7; }

.loading-overlay {
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(255,255,255,0.82);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 12px;
  z-index: 10;
  border-radius: 12px;
  font-weight: 600;
  color: #1B396A;
  font-size: 0.95rem;
}

.loading-spinner {
  width: 32px;
  height: 32px;
  border: 3px solid #E5E7EB;
  border-top-color: #1B396A;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
}

.spinner-btn {
  display: inline-block;
  width: 14px;
  height: 14px;
  border: 2px solid rgba(255,255,255,0.4);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
  margin-right: 6px;
  vertical-align: middle;
}

@keyframes spin { to { transform: rotate(360deg); } }

.filters-bar { flex-wrap: wrap; }

/* ── Navegación por teclado ── */
.atajos-barra {
  display: flex;
  flex-wrap: wrap;
  gap: 10px 20px;
  margin-bottom: 1.4rem;
  font-size: 0.8rem;
  color: #6B7280;
  align-items: center;
}
.atajos-barra kbd {
  display: inline-block;
  background: #F5F5F5;
  border: 1px solid #D1D5DB;
  border-bottom-width: 2px;
  border-radius: 4px;
  padding: 1px 6px;
  font-size: 0.78rem;
  font-family: monospace;
  color: #1A1A1A;
  margin-right: 3px;
}

.fila-activa {
  background: #EFF6FF !important;
  outline: 2px solid #1B396A;
  outline-offset: -2px;
}
.grupos-table tbody tr:focus {
  background: #EFF6FF;
  outline: 2px solid #1B396A;
  outline-offset: -2px;
}
.grupos-page:focus { outline: none; }
</style>