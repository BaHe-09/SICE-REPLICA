<template>
  <MainLayout v-slot="{ busquedaGlobal }">
    <div class="inscripciones-page">

      <nav class="breadcrumb">
        <router-link to="/inicio" class="breadcrumb-link">Inicio</router-link>
        <span class="sep">›</span>
        <router-link to="/inscripcion" class="breadcrumb-link">Inscripciones</router-link>
        <span class="sep">›</span>
        <span class="breadcrumb-actual">Panel Administrativo</span>
      </nav>

      <div class="page-header">
        <h1 class="page-title">Inscripciones</h1>
        <button class="btn-primario" @click="irAGestionInscripcion">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
               stroke="currentColor" stroke-width="2" class="btn-icono">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
          </svg>
          Nueva Inscripción
        </button>
      </div>

      <!-- Toast -->
      <transition name="toast-slide">
        <div v-if="notificacion.visible" class="toast" :class="notificacion.tipo">
          <svg v-if="notificacion.tipo === 'exito'" xmlns="http://www.w3.org/2000/svg"
               class="toast-icono" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <svg v-else xmlns="http://www.w3.org/2000/svg"
               class="toast-icono" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          {{ notificacion.mensaje }}
        </div>
      </transition>

      <!-- KPIs -->
      <div class="stats-grid">

        <div class="stat-card stat-azul">
          <div class="stat-icono-wrapper">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor" stroke-width="2" class="stat-icono">
              <path stroke-linecap="round" stroke-linejoin="round"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
          </div>
          <div class="stat-info">
            <p class="stat-etiqueta">Total Inscritos</p>
            <div v-if="cargando" class="stat-skeleton"></div>
            <p v-else class="stat-numero">{{ totalInscritos }}</p>
            <span class="stat-link">Periodo activo</span>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icono-wrapper stat-icono-amarillo">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor" stroke-width="2" class="stat-icono-amarillo-svg">
              <path stroke-linecap="round" stroke-linejoin="round"
                    d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
            </svg>
          </div>
          <div class="stat-info">
            <p class="stat-etiqueta">Cambios Solicitados</p>
            <div v-if="cargando" class="stat-skeleton"></div>
            <p v-else class="stat-numero">{{ cambiosSolicitados }}</p>
            <span class="stat-link">Ver cambios →</span>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icono-wrapper stat-icono-rojo">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor" stroke-width="2" class="stat-icono-rojo-svg">
              <path stroke-linecap="round" stroke-linejoin="round"
                    d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
            </svg>
          </div>
          <div class="stat-info">
            <p class="stat-etiqueta">Bajas Registradas</p>
            <div v-if="cargando" class="stat-skeleton"></div>
            <p v-else class="stat-numero">{{ bajasRegistradas }}</p>
            <span class="stat-link">Ver bajas →</span>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icono-wrapper stat-icono-verde">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor" stroke-width="2" class="stat-icono-verde-svg">
              <path stroke-linecap="round" stroke-linejoin="round"
                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
          </div>
          <div class="stat-info">
            <p class="stat-etiqueta">Reinscripciones</p>
            <div v-if="cargando" class="stat-skeleton"></div>
            <p v-else class="stat-numero">{{ reinscripciones }}</p>
            <span class="stat-link">Ver reinscripciones →</span>
          </div>
        </div>

      </div>

      <!-- Error -->
      <div v-if="errorCarga" class="alerta-error">
        <svg xmlns="http://www.w3.org/2000/svg" class="alerta-icono" fill="none"
             viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span>No se pudieron cargar los datos. Verifica la conexión con el servidor.</span>
        <button class="btn-reintentar" @click="cargarDatos">Reintentar</button>
      </div>

      <!-- Filtros -->
      <div class="filtros-barra">
        <div class="search-wrapper">
          <svg xmlns="http://www.w3.org/2000/svg" class="search-icono" fill="none"
               viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
          <input
            type="text"
            class="search-input"
            placeholder="Buscar por número de control o nombre..."
            v-model="busqueda"
          >
        </div>
        <select v-model="filtroPeriodo" class="filter-select">
          <option value="">Todos los periodos</option>
          <option value="2024-A">2024-A</option>
          <option value="2024-B">2024-B</option>
          <option value="2025-A">2025-A</option>
        </select>
        <select v-model="filtroCarrera" class="filter-select">
          <option value="">Todas las carreras</option>
          <option value="ISC">Ing. en Sistemas</option>
          <option value="ICI">Ing. Civil</option>
          <option value="IQ">Ing. Química</option>
        </select>
        <select v-model="filtroEstado" class="filter-select">
          <option value="">Todos los estados</option>
          <option value="activa">Activa</option>
          <option value="baja">Baja</option>
          <option value="cambio">Cambio pendiente</option>
        </select>
        <button class="btn-limpiar" @click="limpiarFiltros">Limpiar filtros</button>
      </div>

      <!-- Tabla -->
      <div class="tabla-card">
        <div class="tabla-header">
          <span class="tabla-conteo">
            {{ inscripcionesFiltradas.length }} inscripciones encontradas
          </span>
        </div>

        <div v-if="cargando" class="tabla-skeleton">
          <div v-for="i in 6" :key="i" class="skeleton-fila"></div>
        </div>

        <template v-else>
          <!-- Empty state -->
          <div v-if="inscripcionesFiltradas.length === 0" class="empty-state">
            <svg xmlns="http://www.w3.org/2000/svg" class="empty-icono" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            <p class="empty-titulo">
              {{ busqueda || filtroPeriodo || filtroCarrera || filtroEstado
                ? 'No se encontraron resultados con los filtros aplicados'
                : 'No hay inscripciones registradas para el periodo seleccionado' }}
            </p>
            <button
              v-if="busqueda || filtroPeriodo || filtroCarrera || filtroEstado"
              class="btn-limpiar-empty"
              @click="limpiarFiltros"
            >
              Limpiar filtros
            </button>
          </div>

          <!-- Tabla con datos -->
          <div v-else class="tabla-scroll">
            <table class="tabla">
              <thead>
                <tr>
                  <th>No. Control</th>
                  <th>Alumno</th>
                  <th>Carrera</th>
                  <th>Periodo</th>
                  <th>Materias</th>
                  <th>Estado</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="ins in inscripcionesPaginadas"
                  :key="ins.id"
                  class="tabla-fila"
                  :class="{ 'fila-seleccionada': filaSeleccionada === ins.id }"
                  @click="filaSeleccionada = ins.id"
                >
                  <td class="td-control">{{ ins.noControl }}</td>
                  <td class="td-nombre">{{ ins.nombre }}</td>
                  <td>
                    <span class="badge badge-carrera">{{ ins.carrera }}</span>
                  </td>
                  <td>{{ ins.periodo }}</td>
                  <td class="td-center">{{ ins.totalMaterias }}</td>
                  <td>
                    <span class="badge" :class="badgeEstado(ins.estado)">
                      {{ etiquetaEstado(ins.estado) }}
                    </span>
                  </td>
                  <td class="td-acciones">
                    <button class="btn-accion btn-ver" @click.stop="verInscripcion(ins)">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                           stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                      </svg>
                      Ver
                    </button>
                    <button class="btn-accion btn-gestionar" @click.stop="gestionarInscripcion(ins)">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                           stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                      </svg>
                      Gestionar
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Paginación -->
          <div v-if="totalPaginas > 1" class="paginacion">
            <button class="pag-btn" :disabled="paginaActual === 1" @click="paginaActual--">
              ‹ Anterior
            </button>
            <button
              v-for="p in totalPaginas" :key="p"
              class="pag-btn pag-numero"
              :class="{ activo: p === paginaActual }"
              @click="paginaActual = p"
            >
              {{ p }}
            </button>
            <button class="pag-btn" :disabled="paginaActual === totalPaginas" @click="paginaActual++">
              Siguiente ›
            </button>
          </div>
        </template>
      </div>

      <!-- Modal Ver -->
      <div v-if="modalVer" class="modal-overlay" @click.self="modalVer = false">
        <div class="modal">
          <div class="modal-header">
            <h3>Detalle de Inscripción</h3>
            <button class="modal-cerrar" @click="modalVer = false">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                   stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
          <div class="modal-body" v-if="inscripcionSeleccionada">
            <div class="modal-campo">
              <span class="modal-label">Número de Control</span>
              <span class="modal-valor">{{ inscripcionSeleccionada.noControl }}</span>
            </div>
            <div class="modal-campo">
              <span class="modal-label">Alumno</span>
              <span class="modal-valor">{{ inscripcionSeleccionada.nombre }}</span>
            </div>
            <div class="modal-campo">
              <span class="modal-label">Carrera</span>
              <span class="modal-valor">{{ inscripcionSeleccionada.carrera }}</span>
            </div>
            <div class="modal-campo">
              <span class="modal-label">Periodo</span>
              <span class="modal-valor">{{ inscripcionSeleccionada.periodo }}</span>
            </div>
            <div class="modal-campo">
              <span class="modal-label">Total de Materias</span>
              <span class="modal-valor">{{ inscripcionSeleccionada.totalMaterias }}</span>
            </div>
            <div class="modal-campo">
              <span class="modal-label">Estado</span>
              <span class="badge" :class="badgeEstado(inscripcionSeleccionada.estado)">
                {{ etiquetaEstado(inscripcionSeleccionada.estado) }}
              </span>
            </div>
            <div class="modal-separador"></div>
            <p class="modal-subtitulo">Materias inscritas</p>
            <div v-for="m in inscripcionSeleccionada.materias" :key="m" class="modal-materia">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                   stroke="currentColor" stroke-width="2" class="materia-icono">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              {{ m }}
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn-cancelar" @click="modalVer = false">Cerrar</button>
            <button class="btn-primario" @click="gestionarInscripcion(inscripcionSeleccionada); modalVer = false">
              Gestionar
            </button>
          </div>
        </div>
      </div>

      <footer class="pie-pagina">
        © 2026 Tecnológico Nacional de México · Todos los derechos reservados
      </footer>

    </div>
  </MainLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import MainLayout from '@/layouts/MainLayout.vue'

const router = useRouter()

const cargando          = ref(false)
const errorCarga        = ref(false)
const totalInscritos    = ref(0)
const cambiosSolicitados = ref(0)
const bajasRegistradas  = ref(0)
const reinscripciones   = ref(0)

const busqueda       = ref('')
const filtroPeriodo  = ref('')
const filtroCarrera  = ref('')
const filtroEstado   = ref('')
const paginaActual   = ref(1)
const porPagina      = 15
const filaSeleccionada = ref(null)

const modalVer               = ref(false)
const inscripcionSeleccionada = ref(null)

const notificacion = ref({ visible: false, mensaje: '', tipo: 'exito' })
let timerNotif = null

const mostrarNotificacion = (mensaje, tipo = 'exito') => {
  if (timerNotif) clearTimeout(timerNotif)
  notificacion.value = { visible: true, mensaje, tipo }
  timerNotif = setTimeout(() => { notificacion.value.visible = false }, 3500)
}

// Datos de ejemplo
const inscripciones = ref([
  { id: 1, noControl: '21456001', nombre: 'García López, Ana',    carrera: 'ISC', periodo: '2025-A', totalMaterias: 5, estado: 'activa',  materias: ['Cálculo I', 'Programación', 'Álgebra', 'Inglés I', 'Taller'] },
  { id: 2, noControl: '21456002', nombre: 'Martínez Ruiz, Carlos', carrera: 'ICI', periodo: '2025-A', totalMaterias: 4, estado: 'baja',    materias: ['Mecánica', 'Topografía', 'Inglés II', 'Cálculo II'] },
  { id: 3, noControl: '21456003', nombre: 'López Torres, Sara',   carrera: 'IQ',  periodo: '2025-A', totalMaterias: 6, estado: 'activa',  materias: ['Química I', 'Física', 'Matemáticas', 'Inglés I', 'Lab. Química', 'Taller'] },
  { id: 4, noControl: '21456004', nombre: 'Hernández Díaz, Luis', carrera: 'ISC', periodo: '2025-A', totalMaterias: 5, estado: 'cambio',  materias: ['POO', 'BD', 'Redes', 'SO', 'Inglés III'] },
  { id: 5, noControl: '21456005', nombre: 'Ramírez Vega, María',  carrera: 'ICI', periodo: '2024-B', totalMaterias: 5, estado: 'activa',  materias: ['Estructuras', 'Hidráulica', 'Cálculo III', 'Inglés III', 'Dibujo'] },
  { id: 6, noControl: '21456006', nombre: 'Pérez Soto, Jorge',    carrera: 'ISC', periodo: '2025-A', totalMaterias: 4, estado: 'activa',  materias: ['IA', 'Compiladores', 'Inglés IV', 'Residencias'] },
])

const cargarDatos = async () => {
  cargando.value   = true
  errorCarga.value = false
  try {
    await new Promise(r => setTimeout(r, 800))
    totalInscritos.value     = 342
    cambiosSolicitados.value = 8
    bajasRegistradas.value   = 3
    reinscripciones.value    = 189
  } catch {
    errorCarga.value = true
  } finally {
    cargando.value = false
  }
}

onMounted(cargarDatos)

const inscripcionesFiltradas = computed(() => {
  return inscripciones.value.filter(i => {
    const q = busqueda.value.toLowerCase()
    const matchQ = !q || i.noControl.includes(q) || i.nombre.toLowerCase().includes(q)
    const matchP = !filtroPeriodo.value  || i.periodo === filtroPeriodo.value
    const matchC = !filtroCarrera.value  || i.carrera === filtroCarrera.value
    const matchE = !filtroEstado.value   || i.estado  === filtroEstado.value
    return matchQ && matchP && matchC && matchE
  })
})

const totalPaginas = computed(() => Math.ceil(inscripcionesFiltradas.value.length / porPagina))

const inscripcionesPaginadas = computed(() => {
  const inicio = (paginaActual.value - 1) * porPagina
  return inscripcionesFiltradas.value.slice(inicio, inicio + porPagina)
})

const limpiarFiltros = () => {
  busqueda.value      = ''
  filtroPeriodo.value = ''
  filtroCarrera.value = ''
  filtroEstado.value  = ''
  paginaActual.value  = 1
}

const badgeEstado = (estado) => ({
  'badge-activa':  estado === 'activa',
  'badge-baja':    estado === 'baja',
  'badge-cambio':  estado === 'cambio',
})

const etiquetaEstado = (estado) => ({
  activa:  'Activa',
  baja:    'Baja',
  cambio:  'Cambio pendiente',
}[estado] || estado)

const verInscripcion = (ins) => {
  inscripcionSeleccionada.value = ins
  modalVer.value = true
}

const gestionarInscripcion = (ins) => {
  router.push(`/inscripciones/gestionar/${ins.id}`)
}

const irAGestionInscripcion = () => {
  router.push('/inscripciones/gestionar/nuevo')
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap');

.inscripciones-page {
  --azul:       #1B396A;
  --azul-hover: #1D4ED8;
  --azul-suave: #DBEAFE;
  --borde:      #E5E7EB;
  --fondo:      #F5F5F5;
  --texto:      #1A1A1A;
  --gris:       #6B7280;
  --verde:      #16A34A;
  --rojo:       #DC2626;
  --amarillo:   #F59E0B;

  width: 100%;
  background: var(--fondo);
  font-family: 'Montserrat', sans-serif;
  padding-bottom: 2rem;
}

/* ── Breadcrumb ── */
.breadcrumb { display: flex; align-items: center; gap: 0.4rem; color: var(--gris); font-size: 0.88rem; margin-bottom: 0.75rem; }
.breadcrumb .sep { color: #E5E7EB; }
.breadcrumb-link { color: var(--gris); text-decoration: none; transition: color 0.15s; }
.breadcrumb-link:hover { color: var(--azul); }
.breadcrumb-actual { color: var(--azul); font-weight: 600; }

/* ── Header ── */
.page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.4rem; }
.page-title { color: var(--texto); font-size: 1.75rem; font-weight: 700; letter-spacing: -0.02em; margin: 0; }

/* ── Botones ── */
.btn-primario {
  display: flex; align-items: center; gap: 8px;
  background: var(--azul); color: white; border: none;
  padding: 10px 20px; border-radius: 8px; font-weight: 600;
  font-size: 0.9rem; cursor: pointer; font-family: 'Montserrat', sans-serif;
  transition: background 0.2s;
}
.btn-primario:hover { background: var(--azul-hover); }
.btn-icono { width: 16px; height: 16px; }

.btn-limpiar {
  background: white; color: var(--azul); border: 1px solid var(--azul);
  padding: 9px 16px; border-radius: 8px; font-weight: 600; font-size: 0.88rem;
  cursor: pointer; font-family: 'Montserrat', sans-serif; transition: background 0.15s; white-space: nowrap;
}
.btn-limpiar:hover { background: var(--azul-suave); }

.btn-cancelar {
  background: white; color: var(--azul); border: 1px solid var(--azul);
  padding: 9px 20px; border-radius: 8px; font-weight: 600; font-size: 0.9rem;
  cursor: pointer; font-family: 'Montserrat', sans-serif; transition: background 0.15s;
}
.btn-cancelar:hover { background: var(--azul-suave); }

.btn-reintentar {
  margin-left: auto; background: var(--azul); color: white; border: none;
  padding: 6px 16px; border-radius: 6px; font-weight: 600; font-size: 0.85rem;
  cursor: pointer; font-family: 'Montserrat', sans-serif; transition: background 0.15s; white-space: nowrap;
}
.btn-reintentar:hover { background: var(--azul-hover); }

/* ── Toast ── */
.toast {
  position: fixed; bottom: 2rem; right: 2rem;
  display: flex; align-items: center; gap: 10px;
  padding: 0.9rem 1.4rem; border-radius: 10px;
  color: white; font-weight: 700; font-size: 0.9rem;
  box-shadow: 0 8px 24px rgba(0,0,0,0.15); z-index: 3000;
  font-family: 'Montserrat', sans-serif; max-width: 380px;
}
.toast.exito { background: var(--azul); }
.toast.error { background: var(--rojo); }
.toast-icono { width: 20px; height: 20px; flex-shrink: 0; }
.toast-slide-enter-active, .toast-slide-leave-active { transition: all 0.3s ease; }
.toast-slide-enter-from, .toast-slide-leave-to { opacity: 0; transform: translateX(100%); }

/* ── KPI Grid ── */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.stat-card {
  background: white; border-radius: 12px; padding: 1.3rem 1.4rem;
  display: flex; align-items: center; gap: 1rem;
  box-shadow: 0 4px 12px rgba(0,0,0,0.05); border: 1px solid var(--borde);
  transition: transform 0.2s, box-shadow 0.2s; min-width: 0;
}
.stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0,0,0,0.09); }

.stat-azul { background: var(--azul); border-color: var(--azul); }
.stat-azul .stat-etiqueta { color: rgba(255,255,255,0.8); }
.stat-azul .stat-numero   { color: white; }
.stat-azul .stat-link     { color: rgba(255,255,255,0.85); }
.stat-azul .stat-icono-wrapper { background: rgba(255,255,255,0.2); }
.stat-azul .stat-icono-wrapper .stat-icono { stroke: white; }

.stat-icono-wrapper { width: 46px; height: 46px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; background: var(--azul-suave); }
.stat-icono-wrapper .stat-icono { width: 22px; height: 22px; stroke: var(--azul); }

.stat-icono-amarillo     { background: #FEF3C7; }
.stat-icono-amarillo-svg { width: 22px; height: 22px; stroke: var(--amarillo); }
.stat-icono-rojo         { background: #FEF2F2; }
.stat-icono-rojo-svg     { width: 22px; height: 22px; stroke: var(--rojo); }
.stat-icono-verde        { background: #DCFCE7; }
.stat-icono-verde-svg    { width: 22px; height: 22px; stroke: var(--verde); }

.stat-info { display: flex; flex-direction: column; gap: 2px; min-width: 0; }
.stat-etiqueta { font-size: 0.83rem; color: var(--gris); font-weight: 500; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.stat-numero   { font-size: 2rem; font-weight: 700; color: var(--texto); margin: 2px 0; line-height: 1; }
.stat-link     { font-size: 0.82rem; color: var(--azul); font-weight: 600; cursor: pointer; transition: color 0.15s; white-space: nowrap; }
.stat-link:hover { color: var(--azul-hover); }

.stat-skeleton {
  width: 80px; height: 32px;
  background: linear-gradient(90deg, #E5E7EB 25%, #F3F4F6 50%, #E5E7EB 75%);
  background-size: 200% 100%; animation: shimmer 1.4s infinite; border-radius: 6px; margin: 2px 0;
}
.stat-azul .stat-skeleton {
  background: linear-gradient(90deg, rgba(255,255,255,0.2) 25%, rgba(255,255,255,0.35) 50%, rgba(255,255,255,0.2) 75%);
  background-size: 200% 100%;
}
@keyframes shimmer { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }

/* ── Error ── */
.alerta-error {
  display: flex; align-items: center; gap: 10px;
  background: #FEF2F2; border: 1px solid #FECACA; border-radius: 10px;
  padding: 12px 16px; margin-bottom: 1.4rem; font-size: 0.9rem;
  color: var(--rojo); font-weight: 500; font-family: 'Montserrat', sans-serif;
}
.alerta-icono { width: 20px; height: 20px; flex-shrink: 0; stroke: var(--rojo); }

/* ── Filtros ── */
.filtros-barra {
  display: flex; align-items: center; gap: 0.75rem;
  flex-wrap: wrap; margin-bottom: 1rem;
}
.search-wrapper { position: relative; flex: 1; min-width: 220px; }
.search-icono {
  position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
  width: 16px; height: 16px; stroke: var(--gris); pointer-events: none;
}
.search-input {
  width: 100%; padding: 9px 14px 9px 38px; border: 1px solid var(--borde);
  border-radius: 8px; font-size: 0.88rem; font-family: 'Montserrat', sans-serif;
  color: var(--texto); background: white; outline: none; transition: border-color 0.2s, box-shadow 0.2s;
  box-sizing: border-box;
}
.search-input::placeholder { color: #9CA3AF; }
.search-input:focus { border-color: var(--azul); box-shadow: 0 0 0 3px var(--azul-suave); }

.filter-select {
  padding: 9px 12px; border: 1px solid var(--borde); border-radius: 8px;
  font-size: 0.88rem; font-family: 'Montserrat', sans-serif; color: var(--texto);
  background: white; outline: none; cursor: pointer; transition: border-color 0.2s;
}
.filter-select:focus { border-color: var(--azul); }

/* ── Tabla Card ── */
.tabla-card {
  background: white; border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.05); border: 1px solid var(--borde);
  overflow: hidden; margin-bottom: 1.5rem;
}
.tabla-header {
  padding: 12px 20px; border-bottom: 1px solid var(--borde);
  display: flex; align-items: center; justify-content: space-between;
  background: #F8FAFC;
}
.tabla-conteo { font-size: 0.85rem; color: var(--gris); font-weight: 500; }

.tabla-skeleton { padding: 16px; display: flex; flex-direction: column; gap: 10px; }
.skeleton-fila { height: 44px; border-radius: 8px; background: linear-gradient(90deg, #F3F4F6 25%, #E5E7EB 50%, #F3F4F6 75%); background-size: 200% 100%; animation: shimmer 1.4s infinite; }

.tabla-scroll { overflow-x: auto; }
.tabla { width: 100%; border-collapse: collapse; font-size: 0.88rem; }
.tabla thead tr { background: #F5F5F5; }
.tabla th {
  padding: 12px 16px; text-align: left; font-weight: 600; font-size: 0.82rem;
  color: var(--texto); white-space: nowrap; border-bottom: 1px solid var(--borde);
}
.tabla-fila { border-bottom: 1px solid var(--borde); transition: background 0.15s; cursor: pointer; }
.tabla-fila:hover { background: #F8FAFC; }
.tabla-fila:last-child { border-bottom: none; }
.fila-seleccionada { background: var(--azul-suave) !important; }
.tabla td { padding: 12px 16px; color: var(--texto); vertical-align: middle; }
.td-control { font-weight: 600; color: var(--azul); font-family: monospace; font-size: 0.9rem; }
.td-nombre  { font-weight: 500; }
.td-center  { text-align: center; font-weight: 600; }

/* ── Badges ── */
.badge { display: inline-flex; align-items: center; padding: 3px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; white-space: nowrap; }
.badge-activa  { background: #DCFCE7; color: #16A34A; }
.badge-baja    { background: #FEF2F2; color: #DC2626; }
.badge-cambio  { background: #FEF3C7; color: #F59E0B; }
.badge-carrera { background: var(--azul-suave); color: var(--azul); }

/* ── Acciones ── */
.td-acciones { display: flex; gap: 6px; align-items: center; }
.btn-accion {
  display: inline-flex; align-items: center; gap: 5px;
  padding: 5px 12px; border-radius: 6px; font-size: 0.8rem;
  font-weight: 600; border: none; cursor: pointer;
  font-family: 'Montserrat', sans-serif; transition: background 0.15s; white-space: nowrap;
}
.btn-accion svg { width: 13px; height: 13px; }
.btn-ver       { background: var(--azul-suave); color: var(--azul); }
.btn-ver:hover { background: #BFDBFE; }
.btn-gestionar       { background: var(--azul); color: white; }
.btn-gestionar:hover { background: var(--azul-hover); }

/* ── Empty state ── */
.empty-state { padding: 60px 20px; text-align: center; }
.empty-icono { width: 56px; height: 56px; stroke: #9CA3AF; margin-bottom: 16px; }
.empty-titulo { font-size: 0.95rem; color: var(--gris); font-weight: 500; margin: 0 0 16px; max-width: 360px; margin-left: auto; margin-right: auto; }
.btn-limpiar-empty {
  background: white; color: var(--azul); border: 1px solid var(--azul);
  padding: 8px 20px; border-radius: 8px; font-weight: 600; font-size: 0.88rem;
  cursor: pointer; font-family: 'Montserrat', sans-serif; transition: background 0.15s;
}
.btn-limpiar-empty:hover { background: var(--azul-suave); }

/* ── Paginación ── */
.paginacion { display: flex; align-items: center; justify-content: center; gap: 6px; padding: 16px; border-top: 1px solid var(--borde); }
.pag-btn {
  padding: 6px 12px; border: 1px solid var(--borde); border-radius: 6px;
  background: white; color: var(--texto); font-size: 0.85rem; font-weight: 500;
  cursor: pointer; font-family: 'Montserrat', sans-serif; transition: all 0.15s;
}
.pag-btn:hover:not(:disabled) { border-color: var(--azul); color: var(--azul); background: var(--azul-suave); }
.pag-btn:disabled { opacity: 0.4; cursor: not-allowed; }
.pag-numero.activo { background: var(--azul); color: white; border-color: var(--azul); }

/* ── Modal ── */
.modal-overlay {
  position: fixed; inset: 0; background: rgba(0,0,0,0.45);
  display: flex; align-items: center; justify-content: center; z-index: 2000; padding: 1rem;
}
.modal { background: white; border-radius: 14px; width: 100%; max-width: 520px; box-shadow: 0 20px 60px rgba(0,0,0,0.2); overflow: hidden; }
.modal-header { background: var(--azul); padding: 18px 22px; display: flex; align-items: center; justify-content: space-between; }
.modal-header h3 { color: white; margin: 0; font-size: 1.05rem; font-weight: 700; }
.modal-cerrar { background: none; border: none; cursor: pointer; padding: 4px; border-radius: 4px; transition: background 0.15s; }
.modal-cerrar:hover { background: rgba(255,255,255,0.15); }
.modal-cerrar svg { width: 20px; height: 20px; stroke: white; display: block; }
.modal-body { padding: 20px 22px; display: flex; flex-direction: column; gap: 12px; max-height: 65vh; overflow-y: auto; }
.modal-campo { display: flex; justify-content: space-between; align-items: center; }
.modal-label { font-size: 0.85rem; color: var(--gris); font-weight: 500; }
.modal-valor { font-size: 0.9rem; color: var(--texto); font-weight: 600; }
.modal-separador { height: 1px; background: var(--borde); margin: 4px 0; }
.modal-subtitulo { font-size: 0.85rem; font-weight: 700; color: var(--azul); margin: 0; }
.modal-materia { display: flex; align-items: center; gap: 8px; font-size: 0.88rem; color: var(--texto); }
.materia-icono { width: 16px; height: 16px; stroke: var(--verde); flex-shrink: 0; }
.modal-footer { padding: 14px 22px; background: #F5F5F5; border-top: 1px solid var(--borde); display: flex; justify-content: flex-end; gap: 10px; }

/* ── Footer ── */
.pie-pagina { text-align: center; color: #9CA3AF; font-size: 0.82rem; padding-top: 2rem; border-top: 1px solid var(--borde); margin-top: 1rem; font-family: 'Montserrat', sans-serif; }

@media (max-width: 1024px) { .stats-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
@media (max-width: 640px)  { .stats-grid { grid-template-columns: 1fr; } .filtros-barra { flex-direction: column; } .filter-select { width: 100%; } }
</style>