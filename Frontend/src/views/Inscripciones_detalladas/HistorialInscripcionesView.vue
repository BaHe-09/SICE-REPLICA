<template>
  <MainLayout v-slot="{ busquedaGlobal }">
    <div class="historial-page">

      <nav class="breadcrumb">
        <router-link to="/inicio" class="breadcrumb-link">Inicio</router-link>
        <span class="sep">›</span>
        <router-link to="/inscripciones" class="breadcrumb-link">Inscripciones</router-link>
        <span class="sep">›</span>
        <span class="breadcrumb-actual">Historial</span>
      </nav>

      <div class="page-header">
        <h1 class="page-title">Historial de Inscripciones</h1>
        <button class="btn-secundario" v-if="alumnoSeleccionado" @click="exportarHistorial">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
               stroke="currentColor" stroke-width="2" class="btn-icono">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
          </svg>
          Exportar historial
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
          {{ notificacion.mensaje }}
        </div>
      </transition>

      <!-- Buscador de alumno -->
      <div class="busqueda-card">
        <div class="seccion-titulo">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
               stroke="currentColor" stroke-width="2" class="seccion-icono">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
          <span>Buscar Alumno</span>
        </div>
        <div class="seccion-linea"></div>

        <div class="search-inline">
          <div class="search-wrapper">
            <svg xmlns="http://www.w3.org/2000/svg" class="search-icono" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input
              type="text"
              class="campo-input"
              :class="{ 'input-encontrado': alumnoSeleccionado }"
              placeholder="Número de control o nombre..."
              v-model="busquedaAlumno"
              @input="alumnoSeleccionado = null; periodos.value = []"
            >
          </div>
          <button class="btn-buscar" @click="buscarAlumno" :disabled="buscando">
            <span v-if="buscando" class="spinner-mini"></span>
            <span v-else>Consultar historial</span>
          </button>
        </div>

        <!-- Bloque alumno encontrado -->
        <transition name="slide-down">
          <div v-if="alumnoSeleccionado" class="bloque-alumno">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor" stroke-width="2" class="bloque-icono">
              <path stroke-linecap="round" stroke-linejoin="round"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div class="bloque-info">
              <p class="bloque-nombre">{{ alumnoSeleccionado.nombre }}</p>
              <p class="bloque-detalle">
                {{ alumnoSeleccionado.carrera }} · {{ alumnoSeleccionado.noControl }}
              </p>
            </div>
          </div>
        </transition>
      </div>

      <!-- Historial por periodos -->
      <transition name="slide-down">
        <div v-if="alumnoSeleccionado && !buscando" class="historial-contenedor">

          <div v-if="periodos.length === 0" class="empty-state">
            <svg xmlns="http://www.w3.org/2000/svg" class="empty-icono" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            <p class="empty-titulo">No hay historial de inscripciones para este alumno</p>
          </div>

          <div v-else class="periodos-lista">
            <div
              v-for="periodo in periodos"
              :key="periodo.id"
              class="periodo-card"
            >
              <!-- Header acordeón -->
              <div class="periodo-header" @click="togglePeriodo(periodo.id)">
                <div class="periodo-info">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                       stroke="currentColor" stroke-width="2" class="periodo-icono">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                  <div>
                    <p class="periodo-nombre">{{ periodo.nombre }}</p>
                    <p class="periodo-detalle">
                      {{ periodo.materias.length }} materias ·
                      {{ periodo.materias.filter(m => m.estado === 'acreditada').length }} acreditadas
                    </p>
                  </div>
                </div>
                <div class="periodo-derecha">
                  <span class="badge badge-activo" v-if="periodo.activo">Periodo activo</span>
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                       stroke="currentColor" stroke-width="2"
                       class="flecha-acordeon"
                       :class="{ 'flecha-abierta': periodosAbiertos.includes(periodo.id) }">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                  </svg>
                </div>
              </div>

              <!-- Contenido del acordeón -->
              <transition name="acordeon">
                <div v-if="periodosAbiertos.includes(periodo.id)" class="periodo-contenido">
                  <div class="tabla-scroll">
                    <table class="tabla-historial">
                      <thead>
                        <tr>
                          <th>Clave</th>
                          <th>Materia</th>
                          <th>Créditos</th>
                          <th>Calificación</th>
                          <th>Grupo</th>
                          <th>Estado</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr
                          v-for="mat in periodo.materias"
                          :key="mat.clave"
                          class="historial-fila"
                        >
                          <td class="td-clave">{{ mat.clave }}</td>
                          <td class="td-materia">{{ mat.nombre }}</td>
                          <td class="td-center">{{ mat.creditos }}</td>
                          <td class="td-center">
                            <span v-if="mat.calificacion !== null" class="calificacion"
                                  :class="claseCalificacion(mat.calificacion)">
                              {{ mat.calificacion }}
                            </span>
                            <span v-else class="calificacion-pendiente">—</span>
                          </td>
                          <td class="td-grupo">{{ mat.grupo }}</td>
                          <td>
                            <span class="badge" :class="badgeEstadoMateria(mat.estado)">
                              {{ etiquetaEstadoMateria(mat.estado) }}
                            </span>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </transition>
            </div>
          </div>

        </div>
      </transition>

      <!-- Estado inicial sin búsqueda -->
      <div v-if="!alumnoSeleccionado && !buscando" class="inicio-estado">
        <svg xmlns="http://www.w3.org/2000/svg" class="inicio-icono" fill="none"
             viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
        </svg>
        <p class="inicio-titulo">Consulta el historial de un alumno</p>
        <p class="inicio-subtitulo">Ingresa el número de control o nombre para ver sus inscripciones por periodo</p>
      </div>

      <footer class="pie-pagina">
        © 2026 Tecnológico Nacional de México · Todos los derechos reservados
      </footer>

    </div>
  </MainLayout>
</template>

<script setup>
import { ref } from 'vue'
import MainLayout from '@/layouts/MainLayout.vue'

const busquedaAlumno   = ref('')
const buscando         = ref(false)
const alumnoSeleccionado = ref(null)
const periodosAbiertos   = ref([])

const periodos = ref([])

const notificacion = ref({ visible: false, mensaje: '', tipo: 'exito' })
let timerNotif = null
const mostrarNotificacion = (mensaje, tipo = 'exito') => {
  if (timerNotif) clearTimeout(timerNotif)
  notificacion.value = { visible: true, mensaje, tipo }
  timerNotif = setTimeout(() => { notificacion.value.visible = false }, 3500)
}

const buscarAlumno = async () => {
  if (!busquedaAlumno.value.trim()) return
  buscando.value = true
  await new Promise(r => setTimeout(r, 1000))
  alumnoSeleccionado.value = {
    nombre:   'García López, Ana',
    carrera:  'Ing. en Sistemas Computacionales',
    noControl: busquedaAlumno.value
  }
  periodos.value = [
    {
      id: '2025-A', nombre: 'Periodo 2025-A', activo: true,
      materias: [
        { clave: 'ACA-0408', nombre: 'Cálculo Integral',     creditos: 5, calificacion: null, grupo: 'A', estado: 'inscrita' },
        { clave: 'SCC-1016', nombre: 'POO',                  creditos: 4, calificacion: null, grupo: 'B', estado: 'inscrita' },
        { clave: 'SCC-1017', nombre: 'Base de Datos',        creditos: 4, calificacion: null, grupo: 'A', estado: 'inscrita' },
        { clave: 'ACA-0901', nombre: 'Inglés IV',            creditos: 3, calificacion: null, grupo: 'C', estado: 'inscrita' },
      ]
    },
    {
      id: '2024-B', nombre: 'Periodo 2024-B', activo: false,
      materias: [
        { clave: 'ACA-0307', nombre: 'Cálculo Diferencial',  creditos: 5, calificacion: 9.5, grupo: 'A', estado: 'acreditada' },
        { clave: 'SCC-1010', nombre: 'Fundamentos de BD',    creditos: 4, calificacion: 8.0, grupo: 'B', estado: 'acreditada' },
        { clave: 'FIS-0202', nombre: 'Física',               creditos: 5, calificacion: 6.5, grupo: 'A', estado: 'acreditada' },
        { clave: 'ACA-0801', nombre: 'Inglés III',           creditos: 3, calificacion: 5.0, grupo: 'D', estado: 'reprobada' },
      ]
    },
    {
      id: '2024-A', nombre: 'Periodo 2024-A', activo: false,
      materias: [
        { clave: 'MAT-0101', nombre: 'Álgebra Lineal',       creditos: 5, calificacion: 10,  grupo: 'B', estado: 'acreditada' },
        { clave: 'PRG-0201', nombre: 'Programación I',       creditos: 5, calificacion: 9.0, grupo: 'A', estado: 'acreditada' },
        { clave: 'ACA-0701', nombre: 'Inglés II',            creditos: 3, calificacion: null, grupo: 'A', estado: 'baja' },
      ]
    },
  ]
  periodosAbiertos.value = ['2025-A']
  buscando.value = false
}

const togglePeriodo = (id) => {
  const idx = periodosAbiertos.value.indexOf(id)
  if (idx === -1) periodosAbiertos.value.push(id)
  else periodosAbiertos.value.splice(idx, 1)
}

const claseCalificacion = (cal) => {
  if (cal >= 9)   return 'cal-excelente'
  if (cal >= 7)   return 'cal-bueno'
  if (cal >= 6)   return 'cal-regular'
  return 'cal-reprobado'
}

const badgeEstadoMateria = (estado) => ({
  'badge-acreditada': estado === 'acreditada',
  'badge-reprobada':  estado === 'reprobada',
  'badge-inscrita':   estado === 'inscrita',
  'badge-baja':       estado === 'baja',
})

const etiquetaEstadoMateria = (estado) => ({
  acreditada: 'Acreditada',
  reprobada:  'Reprobada',
  inscrita:   'En curso',
  baja:       'Baja',
}[estado] || estado)

const exportarHistorial = () => {
  mostrarNotificacion('Historial exportado correctamente', 'exito')
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap');

.historial-page {
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

.breadcrumb { display: flex; align-items: center; gap: 0.4rem; color: var(--gris); font-size: 0.88rem; margin-bottom: 0.75rem; }
.breadcrumb .sep { color: #E5E7EB; }
.breadcrumb-link { color: var(--gris); text-decoration: none; transition: color 0.15s; }
.breadcrumb-link:hover { color: var(--azul); }
.breadcrumb-actual { color: var(--azul); font-weight: 600; }

.page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.4rem; flex-wrap: wrap; gap: 0.75rem; }
.page-title  { color: var(--texto); font-size: 1.75rem; font-weight: 700; letter-spacing: -0.02em; margin: 0; }

.btn-icono { width: 16px; height: 16px; }
.btn-secundario {
  display: flex; align-items: center; gap: 8px;
  background: var(--azul-suave); color: var(--azul); border: none;
  padding: 10px 20px; border-radius: 8px; font-weight: 600;
  font-size: 0.9rem; cursor: pointer; font-family: 'Montserrat', sans-serif;
  transition: background 0.2s;
}
.btn-secundario:hover { background: #BFDBFE; }

.toast {
  position: fixed; bottom: 2rem; right: 2rem;
  display: flex; align-items: center; gap: 10px;
  padding: 0.9rem 1.4rem; border-radius: 10px; color: white;
  font-weight: 700; font-size: 0.9rem; box-shadow: 0 8px 24px rgba(0,0,0,0.15);
  z-index: 3000; font-family: 'Montserrat', sans-serif; max-width: 380px;
}
.toast.exito { background: var(--azul); }
.toast.error { background: var(--rojo); }
.toast-icono { width: 20px; height: 20px; flex-shrink: 0; }
.toast-slide-enter-active, .toast-slide-leave-active { transition: all 0.3s ease; }
.toast-slide-enter-from, .toast-slide-leave-to { opacity: 0; transform: translateX(100%); }

/* ── Búsqueda card ── */
.busqueda-card {
  background: white; border-radius: 14px; padding: 1.6rem 2rem;
  box-shadow: 0 4px 12px rgba(0,0,0,0.05); border: 1px solid var(--borde);
  margin-bottom: 1.25rem;
}
.seccion-titulo { display: flex; align-items: center; gap: 8px; font-size: 1rem; font-weight: 700; color: var(--azul); margin-bottom: 6px; }
.seccion-icono  { width: 20px; height: 20px; stroke: var(--azul); flex-shrink: 0; }
.seccion-linea  { height: 1px; background: var(--borde); margin-bottom: 1.2rem; }

.search-inline { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
.search-wrapper { position: relative; flex: 1; min-width: 200px; }
.search-icono   { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; stroke: var(--gris); pointer-events: none; }

.campo-input {
  width: 100%; padding: 10px 14px 10px 38px;
  border: 1px solid var(--borde); border-radius: 8px;
  font-size: 0.9rem; font-family: 'Montserrat', sans-serif; color: var(--texto);
  background: white; outline: none; transition: border-color 0.2s, box-shadow 0.2s; box-sizing: border-box;
}
.campo-input::placeholder { color: #9CA3AF; }
.campo-input:focus { border-color: var(--azul); box-shadow: 0 0 0 3px var(--azul-suave); }
.campo-input.input-encontrado { border-color: var(--verde); background: #F0FDF4; }

.btn-buscar {
  background: var(--azul); color: white; border: none;
  padding: 10px 20px; border-radius: 8px; font-weight: 600;
  font-size: 0.88rem; cursor: pointer; font-family: 'Montserrat', sans-serif;
  transition: background 0.2s; white-space: nowrap; display: flex; align-items: center; gap: 8px;
}
.btn-buscar:hover:not(:disabled) { background: var(--azul-hover); }
.btn-buscar:disabled { opacity: 0.6; cursor: not-allowed; }

.bloque-alumno {
  display: flex; align-items: center; gap: 12px;
  background: #F0FDF4; border: 1px solid #86EFAC; border-radius: 10px;
  padding: 12px 16px; margin-top: 12px;
}
.bloque-icono   { width: 24px; height: 24px; stroke: var(--verde); flex-shrink: 0; }
.bloque-info    { display: flex; flex-direction: column; gap: 2px; }
.bloque-nombre  { font-size: 0.95rem; font-weight: 700; color: var(--texto); margin: 0; }
.bloque-detalle { font-size: 0.82rem; color: var(--gris); margin: 0; }

/* ── Estado inicial ── */
.inicio-estado { text-align: center; padding: 60px 20px; }
.inicio-icono  { width: 60px; height: 60px; stroke: #9CA3AF; margin-bottom: 16px; }
.inicio-titulo { font-size: 1rem; font-weight: 600; color: var(--gris); margin: 0 0 8px; }
.inicio-subtitulo { font-size: 0.88rem; color: #9CA3AF; margin: 0; max-width: 360px; margin-left: auto; margin-right: auto; }

/* ── Historial ── */
.historial-contenedor { display: flex; flex-direction: column; gap: 0; }
.periodos-lista { display: flex; flex-direction: column; gap: 0.75rem; }

.periodo-card { background: white; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border: 1px solid var(--borde); overflow: hidden; }

.periodo-header {
  display: flex; align-items: center; justify-content: space-between;
  padding: 14px 18px; cursor: pointer; transition: background 0.15s;
  user-select: none;
}
.periodo-header:hover { background: #F8FAFC; }

.periodo-info { display: flex; align-items: center; gap: 12px; }
.periodo-icono { width: 20px; height: 20px; stroke: var(--azul); flex-shrink: 0; }
.periodo-nombre  { font-size: 0.95rem; font-weight: 700; color: var(--texto); margin: 0 0 2px; }
.periodo-detalle { font-size: 0.82rem; color: var(--gris); margin: 0; }

.periodo-derecha { display: flex; align-items: center; gap: 10px; }
.flecha-acordeon { width: 18px; height: 18px; stroke: var(--gris); transition: transform 0.25s; flex-shrink: 0; }
.flecha-abierta  { transform: rotate(180deg); }

.periodo-contenido { border-top: 1px solid var(--borde); }

/* ── Acordeón ── */
.acordeon-enter-active, .acordeon-leave-active { transition: all 0.25s ease; overflow: hidden; }
.acordeon-enter-from, .acordeon-leave-to { opacity: 0; max-height: 0; }
.acordeon-enter-to, .acordeon-leave-from { opacity: 1; max-height: 800px; }

/* ── Tabla historial ── */
.tabla-scroll { overflow-x: auto; }
.tabla-historial { width: 100%; border-collapse: collapse; font-size: 0.88rem; }
.tabla-historial thead tr { background: #F5F5F5; }
.tabla-historial th { padding: 10px 16px; text-align: left; font-weight: 600; font-size: 0.82rem; color: var(--texto); border-bottom: 1px solid var(--borde); white-space: nowrap; }
.historial-fila { border-bottom: 1px solid var(--borde); transition: background 0.15s; }
.historial-fila:last-child { border-bottom: none; }
.historial-fila:hover { background: #F8FAFC; }
.historial-fila td { padding: 11px 16px; color: var(--texto); vertical-align: middle; }
.td-clave  { font-family: monospace; font-size: 0.85rem; color: var(--gris); }
.td-materia { font-weight: 500; }
.td-center { text-align: center; }
.td-grupo  { font-size: 0.85rem; color: var(--gris); }

.calificacion { display: inline-flex; align-items: center; justify-content: center; width: 42px; height: 26px; border-radius: 6px; font-weight: 700; font-size: 0.85rem; }
.cal-excelente { background: #DCFCE7; color: #16A34A; }
.cal-bueno     { background: var(--azul-suave); color: var(--azul); }
.cal-regular   { background: #FEF3C7; color: #F59E0B; }
.cal-reprobado { background: #FEF2F2; color: #DC2626; }
.calificacion-pendiente { color: #9CA3AF; font-weight: 600; }

/* ── Badges ── */
.badge { display: inline-flex; align-items: center; padding: 3px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; white-space: nowrap; }
.badge-acreditada { background: #DCFCE7; color: #16A34A; }
.badge-reprobada  { background: #FEF2F2; color: #DC2626; }
.badge-inscrita   { background: #FEF3C7; color: #F59E0B; }
.badge-baja       { background: #F3F4F6; color: var(--gris); }
.badge-activo     { background: var(--azul-suave); color: var(--azul); font-size: 0.72rem; }

/* ── Empty state ── */
.empty-state { padding: 60px 20px; text-align: center; }
.empty-icono  { width: 56px; height: 56px; stroke: #9CA3AF; margin-bottom: 16px; }
.empty-titulo { font-size: 0.95rem; color: var(--gris); font-weight: 500; margin: 0; }

/* ── Animaciones ── */
.slide-down-enter-active { transition: all 0.3s ease; }
.slide-down-enter-from   { opacity: 0; transform: translateY(-10px); }

.spinner-mini { width: 16px; height: 16px; border: 2px solid rgba(255,255,255,0.4); border-top-color: white; border-radius: 50%; animation: spin 0.7s linear infinite; display: inline-block; }
@keyframes spin { to { transform: rotate(360deg); } }

.pie-pagina { text-align: center; color: #9CA3AF; font-size: 0.82rem; padding-top: 2rem; border-top: 1px solid var(--borde); margin-top: 1.5rem; font-family: 'Montserrat', sans-serif; }
</style>