<template>
  <MainLayout>
    <div class="inscripcion-page" ref="paginaRef" @keydown="manejarTeclado" tabindex="-1">

      <div class="breadcrumb">
        <router-link to="/servicios-escolares" class="breadcrumb-link">Servicios Escolares</router-link>
        <span class="arrow">›</span> Inscripción
      </div>

      <h1 class="page-title">Inscripción</h1>
      <p class="subtitle">Busque al alumno por número de control o nombre, y asígnelo a un grupo disponible.</p>

      <!-- Toast de notificación -->
      <div v-if="notification.message" class="toast" :class="notification.type">
        {{ notification.message }}
      </div>

      <!-- ── PASOS ─────────────────────────────────────── -->
      <div class="pasos-barra">
        <div class="paso" :class="{ activo: paso >= 1, completado: paso > 1 }">
          <div class="paso-circulo">
            <span v-if="paso > 1">✓</span>
            <span v-else>1</span>
          </div>
          <span class="paso-label">Buscar alumno</span>
        </div>
        <div class="paso-linea" :class="{ completado: paso > 1 }"></div>
        <div class="paso" :class="{ activo: paso >= 2, completado: paso > 2 }">
          <div class="paso-circulo">
            <span v-if="paso > 2">✓</span>
            <span v-else>2</span>
          </div>
          <span class="paso-label">Seleccionar grupo</span>
        </div>
        <div class="paso-linea" :class="{ completado: paso > 2 }"></div>
        <div class="paso" :class="{ activo: paso >= 3 }">
          <div class="paso-circulo">3</div>
          <span class="paso-label">Confirmar</span>
        </div>
      </div>

      <div class="content-card">

        <!-- ── PASO 1: Buscar alumno ──────────────────── -->
        <div v-if="paso === 1">
          <div class="paso-header">
            <div class="paso-icono">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
              </svg>
            </div>
            <div>
              <h3>Buscar Alumno</h3>
              <p class="paso-desc">Ingrese el número de control o el nombre del alumno a inscribir.</p>
            </div>
          </div>

          <div class="busqueda-row">
            <div class="campo-grupo campo-prioritario">
              <label>Número de control <span class="etiqueta-principal">Principal</span></label>
              <div class="input-con-icono">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input
                  type="text"
                  v-model="busquedaControl"
                  placeholder="Ej: 21456987"
                  ref="inputControlRef"
                  @keyup.enter="buscarAlumno"
                  @input="limpiarAlumno"
                />
              </div>
            </div>
            <div class="campo-grupo">
              <label>Nombre del alumno</label>
              <div class="input-con-icono">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <input
                  type="text"
                  v-model="busquedaNombre"
                  placeholder="Ej: Sara Pérez"
                  @keyup.enter="buscarAlumno"
                  @input="limpiarAlumno"
                />
              </div>
            </div>
            <div class="campo-grupo campo-periodo">
              <label>Periodo</label>
              <select v-model="periodo" class="select-periodo">
                <option value="Ago/Dic 2024">Ago/Dic 2024</option>
                <option value="Ene/Jun 2025">Ene/Jun 2025</option>
              </select>
            </div>
          </div>

          <div class="busqueda-acciones">
            <button class="btn-buscar" @click="buscarAlumno" :disabled="cargando">
              <span v-if="cargando" class="spinner-btn"></span>
              {{ cargando ? 'Buscando...' : 'Buscar alumno' }}
            </button>
          </div>

          <!-- Resultados de búsqueda -->
          <div v-if="resultadosBusqueda.length > 0 && !alumnoSeleccionado" class="resultados-lista">
            <p class="resultados-titulo">Seleccione al alumno correspondiente:</p>
            <div
              v-for="alumno in resultadosBusqueda"
              :key="alumno.noControl"
              class="resultado-item"
              @click="elegirAlumno(alumno)"
            >
              <div class="resultado-avatar">{{ alumno.nombre.charAt(0) }}</div>
              <div class="resultado-info">
                <strong>{{ alumno.nombre }}</strong>
                <span>{{ alumno.noControl }} · {{ alumno.carrera }} · {{ alumno.semestre }}° Semestre</span>
              </div>
              <button class="btn-seleccionar">Seleccionar</button>
            </div>
          </div>

          <!-- Alumno ya seleccionado -->
          <div v-if="alumnoSeleccionado" class="alumno-seleccionado">
            <div class="alumno-avatar">{{ alumnoSeleccionado.nombre.charAt(0) }}</div>
            <div class="alumno-datos">
              <strong>{{ alumnoSeleccionado.nombre }}</strong>
              <span>{{ alumnoSeleccionado.noControl }} · {{ alumnoSeleccionado.carrera }} · {{ alumnoSeleccionado.semestre }}° Semestre</span>
            </div>
            <div class="alumno-acciones">
              <button class="btn-cambiar" @click="limpiarAlumno">Cambiar alumno</button>
              <button class="btn-siguiente" @click="paso = 2">
                Continuar
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
              </button>
            </div>
          </div>
        </div>

        <!-- ── PASO 2: Seleccionar grupo ─────────────── -->
        <div v-if="paso === 2">
          <div class="paso-header">
            <div class="paso-icono">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
              </svg>
            </div>
            <div>
              <h3>Seleccionar Grupo</h3>
              <p class="paso-desc">
                Asignando a: <strong>{{ alumnoSeleccionado?.nombre }}</strong>
                ({{ alumnoSeleccionado?.noControl }})
              </p>
            </div>
          </div>

          <!-- Filtro de grupos -->
          <div class="filtros-card-inline">
            <div class="filtros-label">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" height="16">
                <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
              </svg>
              Filtrar:
            </div>
            <div class="busqueda-grupos-wrap">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" height="16" class="icono-lupa-gris">
                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
              </svg>
              <input
                type="text"
                v-model="busquedaGrupo"
                placeholder="Materia o docente..."
                class="input-busqueda-grupos"
              />
              <button v-if="busquedaGrupo" @click="busquedaGrupo = ''" class="btn-limpiar-busqueda-g">✕</button>
            </div>
            <button class="btn-filtrar-inline" @click="filtrarGrupos">Filtrar</button>
          </div>

          <!-- Tabla de grupos -->
          <div class="table-container" :class="{ 'loading-state': cargando }">
            <div v-if="cargando" class="loading-overlay">
              <div class="loading-spinner"></div>
              <span>{{ mensajeCarga }}</span>
            </div>
            <table class="inscripcion-table">
              <thead>
                <tr>
                  <th>Materia</th>
                  <th>Docente</th>
                  <th>Aula</th>
                  <th>Horario</th>
                  <th class="text-center">Lugares</th>
                  <th class="text-center">Acción</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="(grupo, idx) in gruposFiltrados"
                  :key="grupo.id"
                  :class="{ 'fila-activa': filaActiva === idx }"
                  @click="filaActiva = idx"
                >
                  <td>{{ grupo.materia }}</td>
                  <td>{{ grupo.docente }}</td>
                  <td>{{ grupo.aula }}</td>
                  <td>
                    <span v-if="grupo.horario?.dia" class="horario-badge">
                      {{ grupo.horario.dia }} {{ grupo.horario.horaInicio }}–{{ grupo.horario.horaFin }}
                    </span>
                    <span v-else class="sin-dato">—</span>
                  </td>
                  <td class="text-center">
                    <span class="lugares-badge" :class="{ lleno: grupo.inscritos >= grupo.capacidad, casi: grupo.inscritos >= grupo.capacidad * 0.9 }">
                      {{ grupo.capacidad - grupo.inscritos }} disponibles
                    </span>
                  </td>
                  <td class="text-center">
                    <button
                      v-if="grupo.inscritos < grupo.capacidad"
                      class="btn-elegir"
                      @click="inscribirAlumno(grupo)"
                    >
                      Elegir
                    </button>
                    <span v-else class="badge-lleno">Sin lugares</span>
                  </td>
                </tr>
                <tr v-if="gruposFiltrados.length === 0">
                  <td colspan="6" class="sin-resultados">No se encontraron grupos disponibles.</td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Paginación -->
          <div class="pagination">
            <span>Mostrando {{ gruposFiltrados.length }} de {{ grupos.length }} grupos</span>
            <div class="pagination-buttons">
              <button class="page-btn" @click="prevPage" :disabled="currentPage === 1">‹</button>
              <button
                v-for="page in totalPages"
                :key="page"
                class="page-btn"
                :class="{ active: page === currentPage }"
                @click="currentPage = page"
              >{{ page }}</button>
              <button class="page-btn" @click="nextPage" :disabled="currentPage === totalPages">›</button>
            </div>
          </div>

          <div class="paso-navegacion">
            <button class="btn-atras" @click="paso = 1">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
              </svg>
              Regresar
            </button>
          </div>
        </div>

        <!-- ── PASO 3: Confirmación ───────────────────── -->
        <div v-if="paso === 3">
          <div class="paso-header">
            <div class="paso-icono icono-verde">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <div>
              <h3>Confirmar Inscripción</h3>
              <p class="paso-desc">Verifique los datos antes de confirmar.</p>
            </div>
          </div>

          <div class="confirmacion-grid">
            <div class="confirmacion-bloque">
              <p class="bloque-titulo">Alumno</p>
              <p class="bloque-valor">{{ alumnoSeleccionado?.nombre }}</p>
              <p class="bloque-sub">{{ alumnoSeleccionado?.noControl }} · {{ alumnoSeleccionado?.semestre }}° Semestre</p>
              <p class="bloque-sub">{{ alumnoSeleccionado?.carrera }}</p>
            </div>
            <div class="confirmacion-flecha">→</div>
            <div class="confirmacion-bloque">
              <p class="bloque-titulo">Grupo</p>
              <p class="bloque-valor">{{ grupoSeleccionado?.materia }}</p>
              <p class="bloque-sub">{{ grupoSeleccionado?.docente }}</p>
              <p class="bloque-sub">{{ grupoSeleccionado?.aula }} · {{ grupoSeleccionado?.horario?.dia }} {{ grupoSeleccionado?.horario?.horaInicio }}–{{ grupoSeleccionado?.horario?.horaFin }}</p>
            </div>
            <div class="confirmacion-flecha">·</div>
            <div class="confirmacion-bloque">
              <p class="bloque-titulo">Periodo</p>
              <p class="bloque-valor">{{ periodo }}</p>
              <p class="bloque-sub">
                Lugares: {{ grupoSeleccionado?.capacidad - grupoSeleccionado?.inscritos }} disponibles
              </p>
            </div>
          </div>

          <div class="paso-navegacion">
            <button class="btn-atras" @click="paso = 2" :disabled="cargando">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
              </svg>
              Regresar
            </button>
            <button class="btn-confirmar" @click="confirmarInscripcion" :disabled="cargando">
              <span v-if="cargando" class="spinner-btn"></span>
              {{ cargando ? 'Inscribiendo...' : 'Confirmar inscripción' }}
            </button>
          </div>
        </div>

      </div>
      <!-- Footer institucional -->
      <footer class="footer-institucional">
        <span>Sistema Integral de Control Escolar</span>
        <span class="footer-sep">·</span>
        <span>Servicios Escolares</span>
        <span class="footer-sep">·</span>
        <span>{{ new Date().getFullYear() }}</span>
      </footer>
    </div>
  </MainLayout>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted, nextTick } from 'vue'
import MainLayout from '@/layouts/MainLayout.vue'

// ── Estado general ────────────────────────────────────────────────
const paso = ref(1)
const periodo = ref('Ene/Jun 2025')
const cargando = ref(false)
const mensajeCarga = ref('')
const filaActiva = ref(-1)
const paginaRef = ref(null)
const inputControlRef = ref(null)

const notification = ref({ message: '', type: '' })

const simularCarga = (mensaje, fn, ms = 700) => {
  cargando.value = true
  mensajeCarga.value = mensaje
  setTimeout(() => {
    fn()
    cargando.value = false
    mensajeCarga.value = ''
  }, ms)
}

const showNotification = (message, type) => {
  notification.value = { message, type }
  setTimeout(() => { notification.value = { message: '', type: '' } }, 3500)
}

// ── Paso 1: Búsqueda de alumno ────────────────────────────────────
const busquedaControl = ref('')
let debounceTimer = null
// Búsqueda automática con debounce al escribir en número de control
watch(busquedaControl, (val) => {
  clearTimeout(debounceTimer)
  if (val.trim().length >= 3) {
    debounceTimer = setTimeout(() => buscarAlumno(), 500)
  }
})
const busquedaNombre = ref('')
const resultadosBusqueda = ref([])
const alumnoSeleccionado = ref(null)

// Datos mock de alumnos
const alumnosMock = [
  { noControl: '21456987', nombre: 'Sara Pérez González', carrera: 'Ingeniería en Sistemas Computacionales', semestre: 6 },
  { noControl: '21456988', nombre: 'Pedro Ruiz Martínez', carrera: 'Contador Público', semestre: 4 },
  { noControl: '20301122', nombre: 'Ana Torres López', carrera: 'Ingeniería Industrial', semestre: 7 },
  { noControl: '21000001', nombre: 'Carlos Hernández Vega', carrera: 'Ingeniería en Sistemas Computacionales', semestre: 5 },
  { noControl: '21000002', nombre: 'Diana Flores Soto', carrera: 'Ingeniería en Gestión Empresarial', semestre: 3 },
]

const buscarAlumno = () => {
  const ctrl = busquedaControl.value.trim()
  const nombre = busquedaNombre.value.trim().toLowerCase()
  if (!ctrl && !nombre) return

  simularCarga('Buscando alumno...', () => {
    resultadosBusqueda.value = alumnosMock.filter(a => {
      const coincideControl = !ctrl || a.noControl.includes(ctrl)
      const coincideNombre = !nombre || a.nombre.toLowerCase().includes(nombre)
      return coincideControl && coincideNombre
    })
    if (resultadosBusqueda.value.length === 0) {
      showNotification('No se encontró ningún alumno con esos datos.', 'error')
    }
  })
}

const elegirAlumno = (alumno) => {
  alumnoSeleccionado.value = alumno
  resultadosBusqueda.value = []
}

const limpiarAlumno = () => {
  alumnoSeleccionado.value = null
  resultadosBusqueda.value = []
}

// ── Paso 2: Selección de grupo ────────────────────────────────────
const busquedaGrupo = ref('')
const currentPage = ref(1)
const porPagina = 5
const grupoSeleccionado = ref(null)

const grupos = ref([
  { id: 1, materia: 'Algoritmos y Programación', docente: 'Mtro. Juan Morales', aula: 'A-201', capacidad: 30, inscritos: 23, horario: { dia: 'Lunes y Miércoles', horaInicio: '07:00', horaFin: '09:00' } },
  { id: 2, materia: 'Base de Datos I', docente: 'Dra. Ana Ruiz', aula: 'B-103', capacidad: 30, inscritos: 28, horario: { dia: 'Martes y Jueves', horaInicio: '09:00', horaFin: '11:00' } },
  { id: 3, materia: 'Administración de Redes', docente: 'Mtro. Carlos Jiménez', aula: 'A-204', capacidad: 25, inscritos: 19, horario: { dia: 'Viernes', horaInicio: '11:00', horaFin: '14:00' } },
  { id: 4, materia: 'Inteligencia Artificial', docente: 'Mtro. Roberto Campos', aula: 'C-301', capacidad: 20, inscritos: 20, horario: { dia: 'Lunes y Miércoles', horaInicio: '13:00', horaFin: '15:00' } },
  { id: 5, materia: 'Desarrollo Web Avanzado', docente: 'Dra. Sofía Herrera', aula: 'E-112', capacidad: 35, inscritos: 32, horario: { dia: 'Martes y Jueves', horaInicio: '15:00', horaFin: '17:00' } },
  { id: 6, materia: 'Contabilidad Financiera', docente: 'Mtro. Luis Ramírez', aula: 'G-301', capacidad: 35, inscritos: 22, horario: { dia: 'Lunes, Miércoles y Viernes', horaInicio: '07:00', horaFin: '09:00' } },
])

const gruposFiltrados = computed(() => {
  const q = busquedaGrupo.value.toLowerCase()
  const todos = !q
    ? grupos.value
    : grupos.value.filter(g =>
        g.materia.toLowerCase().includes(q) ||
        g.docente.toLowerCase().includes(q)
      )
  const inicio = (currentPage.value - 1) * porPagina
  return todos.slice(inicio, inicio + porPagina)
})

const totalPages = computed(() =>
  Math.ceil(grupos.value.length / porPagina) || 1
)

const filtrarGrupos = () => {
  currentPage.value = 1
  filaActiva.value = -1
}

const prevPage = () => { if (currentPage.value > 1) { currentPage.value--; filaActiva.value = -1 } }
const nextPage = () => { if (currentPage.value < totalPages.value) { currentPage.value++; filaActiva.value = -1 } }

const inscribirAlumno = (grupo) => {
  grupoSeleccionado.value = grupo
  paso.value = 3
}

// ── Paso 3: Confirmación ──────────────────────────────────────────
const confirmarInscripcion = () => {
  simularCarga('Registrando inscripción...', () => {
    const idx = grupos.value.findIndex(g => g.id === grupoSeleccionado.value.id)
    if (idx !== -1) grupos.value[idx].inscritos++
    showNotification(`Inscripción confirmada: ${alumnoSeleccionado.value.nombre} en ${grupoSeleccionado.value.materia}`, 'success')
    // Resetear todo
    paso.value = 1
    alumnoSeleccionado.value = null
    grupoSeleccionado.value = null
    busquedaControl.value = ''
    busquedaNombre.value = ''
    busquedaGrupo.value = ''
    currentPage.value = 1
    filaActiva.value = -1
  }, 1000)
}

// ── Navegación por teclado ────────────────────────────────────────
const manejarTeclado = (e) => {
  const tag = document.activeElement?.tagName
  const enCampo = ['INPUT', 'SELECT', 'TEXTAREA'].includes(tag)

  if (e.key === 'Escape') {
    if (paso.value > 1) paso.value--
    return
  }

  if (e.ctrlKey) {
    switch (e.key.toLowerCase()) {
      case 'f':
        e.preventDefault()
        nextTick(() => inputControlRef.value?.focus())
        break
      case 'b':
        e.preventDefault()
        if (paso.value === 1) buscarAlumno()
        break
      case 'l':
        e.preventDefault()
        if (paso.value === 1) {
          busquedaControl.value = ''
          busquedaNombre.value = ''
          limpiarAlumno()
        }
        break
    }
    return
  }

  if (!enCampo && paso.value === 2) {
    const total = gruposFiltrados.value.length
    if (e.key === 'ArrowDown') {
      e.preventDefault()
      filaActiva.value = Math.min(filaActiva.value + 1, total - 1)
    } else if (e.key === 'ArrowUp') {
      e.preventDefault()
      filaActiva.value = Math.max(filaActiva.value - 1, 0)
    } else if (e.key === 'ArrowRight') {
      e.preventDefault(); nextPage()
    } else if (e.key === 'ArrowLeft') {
      e.preventDefault(); prevPage()
    } else if (e.key === 'Enter' && filaActiva.value >= 0) {
      const grupo = gruposFiltrados.value[filaActiva.value]
      if (grupo && grupo.inscritos < grupo.capacidad) inscribirAlumno(grupo)
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
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap');

.inscripcion-page {
  width: 100%;
  padding: 2rem 2.5rem;
  background: #F5F5F5;
  outline: none;
}

.breadcrumb { color: #6B7280; font-size: 0.92rem; margin-bottom: 1rem; }
.arrow { color: #1A1A1A; font-weight: bold; }

.page-title { font-size: 2.4rem; font-weight: 700; color: #1A1A1A; margin-bottom: 0.4rem; }
.subtitle { color: #6B7280; margin-bottom: 1.8rem; font-size: 0.95rem; }

/* Toast */
.toast {
  position: fixed; top: 90px; right: 30px;
  padding: 14px 24px; border-radius: 8px; color: white;
  font-weight: 500; box-shadow: 0 6px 20px rgba(0,0,0,0.25);
  z-index: 9999; animation: slideIn 0.3s ease;
}
.toast.success { background: #16A34A; }
.toast.error { background: #DC2626; }
@keyframes slideIn { from { transform: translateX(120%); } to { transform: translateX(0); } }

/* Barra de pasos */
.pasos-barra {
  display: flex; align-items: center; margin-bottom: 2rem;
  background: #FFFFFF; border-radius: 12px; padding: 1.2rem 2rem;
  box-shadow: 0 2px 8px rgba(0,0,0,0.06); border: 1px solid #E5E7EB;
}
.paso { display: flex; align-items: center; gap: 10px; }
.paso-circulo {
  width: 34px; height: 34px; border-radius: 50%;
  background: #E5E7EB; color: #6B7280;
  display: flex; align-items: center; justify-content: center;
  font-weight: 700; font-size: 0.9rem; transition: all 0.3s;
}
.paso.activo .paso-circulo { background: #1B396A; color: white; }
.paso.completado .paso-circulo { background: #16A34A; color: white; }
.paso-label { font-size: 0.9rem; font-weight: 600; color: #6B7280; transition: color 0.3s; }
.paso.activo .paso-label { color: #1B396A; }
.paso.completado .paso-label { color: #16A34A; }
.paso-linea {
  flex: 1; height: 2px; background: #E5E7EB; margin: 0 1rem; transition: background 0.3s;
}
.paso-linea.completado { background: #16A34A; }

/* Card principal */
.content-card {
  background: #FFFFFF; border-radius: 16px;
  box-shadow: 0 8px 25px rgba(0,0,0,0.08);
  padding: 2.5rem; border: 1px solid #E5E7EB;
  max-width: 1100px;
}

/* Cabecera de cada paso */
.paso-header {
  display: flex; align-items: flex-start; gap: 1rem; margin-bottom: 2rem;
  padding-bottom: 1.5rem; border-bottom: 1px solid #E5E7EB;
}
.paso-icono {
  width: 48px; height: 48px; border-radius: 12px;
  background: #EFF6FF; display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.paso-icono svg { width: 24px; height: 24px; stroke: #1B396A; }
.icono-verde { background: #F0FDF4; }
.icono-verde svg { stroke: #16A34A; }
.paso-header h3 { font-size: 1.25rem; font-weight: 700; color: #1A1A1A; margin: 0 0 4px; }
.paso-desc { color: #6B7280; font-size: 0.92rem; margin: 0; }

/* Paso 1: búsqueda */
.busqueda-row {
  display: flex; gap: 1.2rem; align-items: flex-end; flex-wrap: wrap; margin-bottom: 1.2rem;
}
.campo-grupo { display: flex; flex-direction: column; gap: 6px; flex: 1; min-width: 200px; }
.campo-prioritario { flex: 1.2; }
.campo-periodo { flex: 0 0 160px; min-width: 160px; }
.campo-grupo label {
  font-size: 0.88rem; font-weight: 600; color: #1A1A1A;
  display: flex; align-items: center; gap: 8px;
}
.etiqueta-principal {
  background: #1B396A; color: white; font-size: 0.7rem;
  padding: 2px 8px; border-radius: 10px; font-weight: 600;
}

.input-con-icono { position: relative; }
.input-con-icono svg {
  position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
  width: 18px; height: 18px; stroke: #6B7280; pointer-events: none;
}
.input-con-icono input {
  width: 100%; padding: 12px 14px 12px 40px;
  border: 1.5px solid #E5E7EB; border-radius: 10px;
  font-size: 0.97rem; background: #FFFFFF; color: #1A1A1A;
  box-sizing: border-box; transition: border-color 0.2s;
}
.campo-prioritario .input-con-icono input { border-color: #1B396A; }
.input-con-icono input:focus { outline: none; border-color: #1B396A; box-shadow: 0 0 0 3px rgba(27,57,106,0.1); }
.flex-1 { flex: 1; }

.select-periodo {
  width: 100%; padding: 12px 14px; border: 1.5px solid #E5E7EB;
  border-radius: 10px; font-size: 0.97rem; background: #FFFFFF; color: #1A1A1A;
}

.busqueda-acciones { display: flex; justify-content: flex-end; margin-bottom: 1.5rem; }

.btn-buscar {
  background: #1B396A; color: white; padding: 12px 32px;
  border-radius: 10px; font-weight: 600; font-size: 0.97rem;
  border: none; cursor: pointer; display: flex; align-items: center; gap: 8px;
  transition: background 0.2s;
}
.btn-buscar:hover:not(:disabled) { background: #2563EB; }
.btn-buscar:disabled { opacity: 0.7; cursor: not-allowed; }

/* Resultados */
.resultados-titulo { font-weight: 600; color: #1A1A1A; margin-bottom: 0.8rem; font-size: 0.92rem; }
.resultados-lista { margin-top: 0.5rem; display: flex; flex-direction: column; gap: 8px; }
.resultado-item {
  display: flex; align-items: center; gap: 14px;
  border: 1px solid #E5E7EB; border-radius: 10px; padding: 12px 16px;
  cursor: pointer; transition: border-color 0.2s, background 0.2s;
}
.resultado-item:hover { border-color: #1B396A; background: #F8FAFF; }
.resultado-avatar {
  width: 40px; height: 40px; border-radius: 50%;
  background: #1B396A; color: white; display: flex;
  align-items: center; justify-content: center; font-weight: 700; font-size: 1.1rem; flex-shrink: 0;
}
.resultado-info { flex: 1; display: flex; flex-direction: column; }
.resultado-info strong { color: #1A1A1A; font-size: 0.97rem; }
.resultado-info span { color: #6B7280; font-size: 0.85rem; margin-top: 2px; }
.btn-seleccionar {
  background: #EFF6FF; color: #1B396A; border: 1px solid #BFDBFE;
  padding: 7px 18px; border-radius: 8px; font-weight: 600; font-size: 0.88rem; cursor: pointer;
}

/* Alumno seleccionado */
.alumno-seleccionado {
  display: flex; align-items: center; gap: 16px;
  background: #F0FDF4; border: 1.5px solid #86EFAC;
  border-radius: 12px; padding: 16px 20px; margin-top: 1rem;
}
.alumno-avatar {
  width: 46px; height: 46px; border-radius: 50%;
  background: #16A34A; color: white; display: flex;
  align-items: center; justify-content: center; font-weight: 700; font-size: 1.2rem; flex-shrink: 0;
}
.alumno-datos { flex: 1; }
.alumno-datos strong { display: block; color: #1A1A1A; font-size: 1rem; }
.alumno-datos span { color: #6B7280; font-size: 0.85rem; }
.alumno-acciones { display: flex; gap: 10px; }
.btn-cambiar {
  background: white; color: #6B7280; border: 1px solid #E5E7EB;
  padding: 9px 18px; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 0.9rem;
}
.btn-siguiente {
  background: #1B396A; color: white; border: none;
  padding: 9px 22px; border-radius: 8px; font-weight: 600; cursor: pointer;
  display: flex; align-items: center; gap: 6px; font-size: 0.9rem;
}
.btn-siguiente svg { width: 16px; height: 16px; stroke: white; }

/* Paso 2: tabla de grupos */


.table-container {
  border-radius: 12px; overflow: hidden;
  border: 1px solid #E5E7EB; position: relative;
}
.loading-state { opacity: 0.7; pointer-events: none; }
.loading-overlay {
  position: absolute; top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(255,255,255,0.85); display: flex; flex-direction: column;
  align-items: center; justify-content: center; gap: 10px; z-index: 10;
  border-radius: 12px; font-weight: 600; color: #1B396A;
}
.loading-spinner {
  width: 30px; height: 30px; border: 3px solid #E5E7EB;
  border-top-color: #1B396A; border-radius: 50%;
  animation: spin 0.7s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

.inscripcion-table { width: 100%; border-collapse: collapse; }
.inscripcion-table th {
  background: #F5F5F5; padding: 14px 16px;
  font-weight: 600; color: #1A1A1A; border-bottom: 1px solid #E5E7EB; text-align: left;
}
.inscripcion-table td {
  padding: 14px 16px; border-bottom: 1px solid #E5E7EB; color: #1A1A1A;
}
.fila-activa { background: #EFF6FF !important; outline: 2px solid #1B396A; outline-offset: -2px; }

.horario-badge {
  display: inline-block; background: #EFF6FF; color: #1B396A;
  border: 1px solid #BFDBFE; border-radius: 6px; padding: 3px 9px;
  font-size: 0.82rem; font-weight: 500; white-space: nowrap;
}
.sin-dato { color: #9CA3AF; font-size: 0.85rem; }

.lugares-badge {
  display: inline-block; background: #F0FDF4; color: #16A34A;
  border: 1px solid #86EFAC; border-radius: 20px;
  padding: 4px 12px; font-size: 0.85rem; font-weight: 600;
}
.lugares-badge.casi { background: #FFFBEB; color: #D97706; border-color: #FCD34D; }
.lugares-badge.lleno { background: #FEF2F2; color: #DC2626; border-color: #FECACA; }

.btn-elegir {
  background: #1B396A; color: white; border: none;
  padding: 8px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 0.9rem;
  transition: background 0.2s;
}
.btn-elegir:hover { background: #1D4ED8; }

.btn-inscribir {
  background: #1B396A; color: white; border: none;
  padding: 8px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 0.9rem;
  transition: background 0.2s;
}
.btn-inscribir:hover { background: #1D4ED8; }
.badge-lleno {
  display: inline-block; background: #F5F5F5; color: #9CA3AF;
  border: 1px solid #E5E7EB; border-radius: 8px;
  padding: 7px 14px; font-size: 0.88rem; font-weight: 600;
}
.sin-resultados { text-align: center; padding: 2rem; color: #6B7280; }

/* Paginación */
.pagination {
  display: flex; justify-content: space-between; align-items: center;
  margin-top: 1.2rem; font-size: 0.92rem; color: #6B7280;
}
.pagination-buttons { display: flex; gap: 4px; }
.page-btn {
  padding: 6px 12px; border: 1px solid #E5E7EB; background: #FFFFFF;
  border-radius: 6px; cursor: pointer; color: #1A1A1A; font-weight: 500;
}
.page-btn.active { background: #1B396A; color: white; border-color: #1B396A; }
.page-btn:disabled { opacity: 0.4; cursor: not-allowed; }

/* Paso 3: confirmación */
.confirmacion-grid {
  display: flex; align-items: center; gap: 1rem;
  background: #F8FAFF; border: 1px solid #E5E7EB; border-radius: 12px;
  padding: 2rem; margin-bottom: 2rem; flex-wrap: wrap;
}
.confirmacion-bloque { flex: 1; min-width: 180px; }
.bloque-titulo {
  font-size: 0.78rem; font-weight: 700; text-transform: uppercase;
  letter-spacing: 0.05em; color: #6B7280; margin: 0 0 6px;
}
.bloque-valor { font-size: 1.05rem; font-weight: 700; color: #1A1A1A; margin: 0 0 4px; }
.bloque-sub { font-size: 0.85rem; color: #6B7280; margin: 0 0 2px; }
.confirmacion-flecha { font-size: 1.5rem; color: #D1D5DB; font-weight: 300; }

/* Navegación entre pasos */
.paso-navegacion {
  display: flex; justify-content: space-between; align-items: center;
  margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #E5E7EB;
}
.btn-atras {
  background: white; color: #6B7280; border: 1px solid #E5E7EB;
  padding: 11px 22px; border-radius: 10px; font-weight: 600; cursor: pointer;
  display: flex; align-items: center; gap: 6px; font-size: 0.95rem;
}
.btn-atras svg { width: 16px; height: 16px; stroke: #6B7280; }
.btn-confirmar {
  background: #16A34A; color: white; border: none;
  padding: 13px 36px; border-radius: 10px; font-weight: 700; cursor: pointer;
  display: flex; align-items: center; gap: 8px; font-size: 1rem;
  transition: background 0.2s;
}
.btn-confirmar:hover:not(:disabled) { background: #15803D; }
.btn-confirmar:disabled { opacity: 0.7; cursor: not-allowed; }

/* Spinner inline */
.spinner-btn {
  display: inline-block; width: 14px; height: 14px;
  border: 2px solid rgba(255,255,255,0.4); border-top-color: white;
  border-radius: 50%; animation: spin 0.7s linear infinite;
}

.text-center { text-align: center; }

/* ── Atajos de teclado ── */
.atajos-barra {
  display: flex; flex-wrap: wrap; gap: 8px 18px;
  margin-bottom: 1.6rem; font-size: 0.8rem; color: #6B7280; align-items: center;
}
.atajos-barra kbd {
  display: inline-block; background: #F5F5F5;
  border: 1px solid #D1D5DB; border-bottom-width: 2px;
  border-radius: 4px; padding: 1px 6px;
  font-size: 0.78rem; font-family: monospace; color: #1A1A1A; margin-right: 3px;
}

/* ── Transición suave entre pasos ── */
.content-card > div {
  animation: fadeSlide 0.25s ease;
}
@keyframes fadeSlide {
  from { opacity: 0; transform: translateY(8px); }
  to   { opacity: 1; transform: translateY(0); }
}

/* ── Breadcrumb navegable ── */
.breadcrumb-link {
  color: #6B7280;
  text-decoration: none;
  transition: color 0.2s;
}
.breadcrumb-link:hover {
  color: #1B396A;
  text-decoration: underline;
}

/* ── Footer institucional ── */
.footer-institucional {
  margin-top: 2.5rem;
  padding-top: 1.2rem;
  border-top: 1px solid #E5E7EB;
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 0.82rem;
  color: #9CA3AF;
}
.footer-sep { color: #D1D5DB; }

/* ── Filtro grupos (paso 2) ── */
.filtros-card-inline {
  background: #FFFFFF;
  border-radius: 12px;
  border: 1px solid #E5E7EB;
  box-shadow: 0 4px 12px rgba(0,0,0,0.05);
  padding: 0.75rem 1.1rem;
  display: flex; align-items: center; gap: 0.6rem;
  flex-wrap: wrap; margin-bottom: 1.2rem;
}
.filtros-label {
  display: flex; align-items: center; gap: 5px;
  font-size: 0.82rem; font-weight: 700; color: #6B7280; white-space: nowrap;
}
.filtros-label svg { stroke: #6B7280; }
.busqueda-grupos-wrap {
  display: flex; align-items: center; gap: 8px;
  background: #FFFFFF; border: 1px solid #E5E7EB;
  border-radius: 8px; padding: 0 12px;
  flex: 1; min-width: 200px;
}
.icono-lupa-gris { stroke: #6B7280; flex-shrink: 0; }
.input-busqueda-grupos {
  border: none; background: transparent;
  padding: 8px 0; font-size: 0.875rem;
  font-family: inherit; outline: none; flex: 1; color: #1A1A1A;
}
.input-busqueda-grupos::placeholder { color: #9CA3AF; }
.btn-limpiar-busqueda-g {
  background: none; border: none; color: #6B7280;
  cursor: pointer; font-size: 0.9rem; padding: 2px; line-height: 1;
}
.btn-filtrar-inline {
  background: #1B396A; color: white; border: none;
  padding: 9px 20px; border-radius: 8px;
  font-weight: 600; cursor: pointer; font-size: 0.875rem;
  white-space: nowrap; transition: background 0.2s;
}
.btn-filtrar-inline:hover { background: #1D4ED8; }
</style>