<template>
  <MainLayout v-slot="{ busquedaGlobal }">
    <div class="gestion-page">

      <nav class="breadcrumb">
        <router-link to="/inicio" class="breadcrumb-link">Inicio</router-link>
        <span class="sep">›</span>
        <router-link to="/inscripciones" class="breadcrumb-link">Inscripciones</router-link>
        <span class="sep">›</span>
        <span class="breadcrumb-actual">Gestionar Inscripción</span>
      </nav>

      <div class="page-header">
        <h1 class="page-title">Gestionar Inscripción</h1>
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

      <div class="formulario-card">

        <!-- SECCIÓN 1: Alumno -->
        <div class="seccion">
          <div class="seccion-titulo">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor" stroke-width="2" class="seccion-icono">
              <path stroke-linecap="round" stroke-linejoin="round"
                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            <span>Alumno</span>
          </div>
          <div class="seccion-linea"></div>

          <div class="campo-grupo">
            <label class="campo-label">Número de Control</label>
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
                  :class="{ 'input-encontrado': alumnoEncontrado }"
                  placeholder="Ej: 21456887"
                  v-model="noControl"
                  @input="alumnoEncontrado = false"
                  :disabled="guardando"
                >
              </div>
              <button
                class="btn-buscar"
                :class="{ 'btn-encontrado': alumnoEncontrado }"
                @click="buscarAlumno"
                :disabled="buscandoAlumno || alumnoEncontrado"
              >
                <span v-if="buscandoAlumno" class="spinner-mini"></span>
                <span v-else-if="alumnoEncontrado">Alumno encontrado — Continuar</span>
                <span v-else>Buscar alumno</span>
              </button>
            </div>
          </div>

          <!-- Bloque alumno encontrado -->
          <transition name="slide-down">
            <div v-if="alumnoEncontrado && alumno" class="bloque-alumno">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                   stroke="currentColor" stroke-width="2" class="bloque-icono">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <div class="bloque-info">
                <p class="bloque-nombre">{{ alumno.nombre }}</p>
                <p class="bloque-detalle">{{ alumno.carrera }} · Semestre {{ alumno.semestre }} · {{ alumno.noControl }}</p>
              </div>
            </div>
          </transition>
        </div>

        <!-- SECCIÓN 2: Materias -->
        <div class="seccion" v-if="alumnoEncontrado">
          <div class="seccion-titulo">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor" stroke-width="2" class="seccion-icono">
              <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
            <span>Materias Inscritas</span>
          </div>
          <div class="seccion-linea"></div>

          <!-- Alerta si todas las materias tienen baja -->
          <transition name="slide-down">
            <div v-if="todasConBaja" class="alerta-amarilla">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                   stroke="currentColor" stroke-width="2" class="alerta-icono">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
              <span>El alumno quedará sin materias inscritas en este periodo. Agrega al menos un motivo de baja por cada materia.</span>
            </div>
          </transition>

          <div class="tabla-materias-card">
            <table class="tabla-materias">
              <thead>
                <tr>
                  <th>Materia</th>
                  <th>Grupo Actual</th>
                  <th>Acción</th>
                  <th>Detalle</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(mat, idx) in materias" :key="idx" class="materia-fila">
                  <td>
                    <p class="materia-nombre">{{ mat.nombre }}</p>
                    <p class="materia-clave">{{ mat.clave }}</p>
                  </td>
                  <td class="td-grupo">{{ mat.grupo }}</td>
                  <td>
                    <select class="select-accion" v-model="mat.accion" @change="mat.nuevoGrupo = ''; mat.motivo = ''">
                      <option value="sin-cambio">Sin cambio</option>
                      <option value="cambio-grupo">Solicitar cambio de grupo</option>
                      <option value="baja">Solicitar baja</option>
                    </select>
                  </td>
                  <td>
                    <transition name="slide-down">
                      <div v-if="mat.accion === 'cambio-grupo'" class="detalle-accion">
                        <select class="select-grupo" v-model="mat.nuevoGrupo">
                          <option value="">Seleccionar nuevo grupo...</option>
                          <option v-for="g in gruposDisponibles" :key="g.id" :value="g.id">
                            {{ g.nombre }} — {{ g.horario }} ({{ g.cupo }} lugares)
                          </option>
                        </select>
                      </div>
                      <div v-else-if="mat.accion === 'baja'" class="detalle-accion">
                        <textarea
                          class="textarea-motivo"
                          v-model="mat.motivo"
                          placeholder="Motivo de la baja (requerido)..."
                          rows="2"
                        ></textarea>
                        <p v-if="mat.motivo.length === 0 && intentoGuardar" class="error-campo">El motivo es requerido para solicitar baja.</p>
                      </div>
                    </transition>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Footer del formulario -->
        <div class="form-footer">
          <button class="btn-cancelar" @click="$router.push('/inscripciones')" :disabled="guardando">
            Cancelar
          </button>
          <button
            class="btn-primario"
            @click="guardarCambios"
            :disabled="!alumnoEncontrado || guardando"
          >
            <span v-if="guardando" class="spinner-btn"></span>
            <span v-else>Guardar cambios</span>
          </button>
        </div>

      </div>

      <footer class="pie-pagina">
        © 2026 Tecnológico Nacional de México · Todos los derechos reservados
      </footer>

    </div>
  </MainLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import MainLayout from '@/layouts/MainLayout.vue'

const router = useRouter()

const noControl       = ref('')
const buscandoAlumno  = ref(false)
const alumnoEncontrado = ref(false)
const guardando        = ref(false)
const intentoGuardar   = ref(false)

const alumno = ref(null)

const materias = ref([])

const gruposDisponibles = ref([
  { id: 'A', nombre: 'Grupo A', horario: 'Lun-Mié 7:00-8:30', cupo: 5 },
  { id: 'B', nombre: 'Grupo B', horario: 'Mar-Jue 9:00-10:30', cupo: 2 },
  { id: 'C', nombre: 'Grupo C', horario: 'Vie 7:00-10:00', cupo: 8 },
])

const notificacion = ref({ visible: false, mensaje: '', tipo: 'exito' })
let timerNotif = null
const mostrarNotificacion = (mensaje, tipo = 'exito') => {
  if (timerNotif) clearTimeout(timerNotif)
  notificacion.value = { visible: true, mensaje, tipo }
  timerNotif = setTimeout(() => { notificacion.value.visible = false }, 3500)
}

const todasConBaja = computed(() =>
  materias.value.length > 0 && materias.value.every(m => m.accion === 'baja')
)

const buscarAlumno = async () => {
  if (!noControl.value.trim()) return
  buscandoAlumno.value = true
  await new Promise(r => setTimeout(r, 900))
  alumno.value = {
    nombre:   'García López, Ana',
    carrera:  'Ing. en Sistemas Computacionales',
    semestre: 4,
    noControl: noControl.value
  }
  materias.value = [
    { nombre: 'Cálculo Integral', clave: 'ACA-0408', grupo: 'A — Lun-Mié 7:00', accion: 'sin-cambio', nuevoGrupo: '', motivo: '' },
    { nombre: 'Programación Orientada a Objetos', clave: 'SCC-1016', grupo: 'B — Mar-Jue 9:00', accion: 'sin-cambio', nuevoGrupo: '', motivo: '' },
    { nombre: 'Base de Datos', clave: 'SCC-1017', grupo: 'A — Lun-Mié 11:00', accion: 'sin-cambio', nuevoGrupo: '', motivo: '' },
    { nombre: 'Inglés IV', clave: 'ACA-0901', grupo: 'C — Vie 7:00', accion: 'sin-cambio', nuevoGrupo: '', motivo: '' },
  ]
  alumnoEncontrado.value = true
  buscandoAlumno.value   = false
  mostrarNotificacion('Alumno encontrado', 'exito')
}

const guardarCambios = async () => {
  intentoGuardar.value = true
  const bajasInvalidas = materias.value.some(m => m.accion === 'baja' && !m.motivo.trim())
  if (bajasInvalidas) {
    mostrarNotificacion('Completa el motivo de cada baja solicitada.', 'error')
    return
  }
  guardando.value = true
  await new Promise(r => setTimeout(r, 1200))
  guardando.value = false
  mostrarNotificacion('Cambios guardados correctamente', 'exito')
  setTimeout(() => router.push('/inscripciones'), 1500)
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap');

.gestion-page {
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

.page-header { margin-bottom: 1.4rem; }
.page-title  { color: var(--texto); font-size: 1.75rem; font-weight: 700; letter-spacing: -0.02em; margin: 0; }

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

/* ── Card del formulario ── */
.formulario-card {
  background: white; border-radius: 14px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.06); border: 1px solid var(--borde);
  overflow: hidden; margin-bottom: 1.5rem;
}

/* ── Secciones ── */
.seccion { padding: 1.6rem 2rem; }
.seccion + .seccion { border-top: 1px solid var(--borde); }

.seccion-titulo {
  display: flex; align-items: center; gap: 8px;
  font-size: 1rem; font-weight: 700; color: var(--azul); margin-bottom: 6px;
}
.seccion-icono { width: 20px; height: 20px; stroke: var(--azul); flex-shrink: 0; }
.seccion-linea { height: 1px; background: var(--borde); margin-bottom: 1.2rem; }

/* ── Campos ── */
.campo-grupo { display: flex; flex-direction: column; gap: 6px; margin-bottom: 1rem; }
.campo-label { font-size: 0.85rem; font-weight: 600; color: var(--texto); }

.search-inline { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
.search-wrapper { position: relative; flex: 1; min-width: 200px; }
.search-icono { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; stroke: var(--gris); pointer-events: none; }

.campo-input {
  width: 100%; padding: 10px 14px 10px 38px;
  border: 1px solid var(--borde); border-radius: 8px;
  font-size: 0.9rem; font-family: 'Montserrat', sans-serif; color: var(--texto);
  background: white; outline: none; transition: border-color 0.2s, box-shadow 0.2s;
  box-sizing: border-box;
}
.campo-input::placeholder { color: #9CA3AF; }
.campo-input:focus { border-color: var(--azul); box-shadow: 0 0 0 3px var(--azul-suave); }
.campo-input.input-encontrado { border-color: var(--verde); background: #F0FDF4; }
.campo-input:disabled { background: #F9FAFB; color: var(--gris); cursor: not-allowed; }

.btn-buscar {
  background: var(--azul); color: white; border: none;
  padding: 10px 20px; border-radius: 8px; font-weight: 600;
  font-size: 0.88rem; cursor: pointer; font-family: 'Montserrat', sans-serif;
  transition: background 0.2s; white-space: nowrap; display: flex; align-items: center; gap: 8px;
}
.btn-buscar:hover:not(:disabled) { background: var(--azul-hover); }
.btn-buscar:disabled { opacity: 0.6; cursor: not-allowed; }
.btn-buscar.btn-encontrado { background: var(--verde); }

/* ── Bloque alumno ── */
.bloque-alumno {
  display: flex; align-items: center; gap: 12px;
  background: #F0FDF4; border: 1px solid #86EFAC;
  border-radius: 10px; padding: 12px 16px; margin-top: 12px;
}
.bloque-icono { width: 24px; height: 24px; stroke: var(--verde); flex-shrink: 0; }
.bloque-info { display: flex; flex-direction: column; gap: 2px; }
.bloque-nombre  { font-size: 0.95rem; font-weight: 700; color: var(--texto); margin: 0; }
.bloque-detalle { font-size: 0.82rem; color: var(--gris); margin: 0; }

/* ── Alerta amarilla ── */
.alerta-amarilla {
  display: flex; align-items: flex-start; gap: 10px;
  background: #FEF3C7; border: 1px solid #FDE68A; border-radius: 10px;
  padding: 12px 16px; margin-bottom: 1rem; font-size: 0.88rem;
  color: #92400E; font-weight: 500;
}
.alerta-icono { width: 20px; height: 20px; stroke: var(--amarillo); flex-shrink: 0; margin-top: 1px; }

/* ── Tabla de materias ── */
.tabla-materias-card { border-radius: 10px; overflow: hidden; border: 1px solid var(--borde); }
.tabla-materias { width: 100%; border-collapse: collapse; font-size: 0.88rem; }
.tabla-materias thead tr { background: #F5F5F5; }
.tabla-materias th { padding: 10px 14px; text-align: left; font-weight: 600; font-size: 0.82rem; color: var(--texto); border-bottom: 1px solid var(--borde); white-space: nowrap; }
.materia-fila { border-bottom: 1px solid var(--borde); transition: background 0.15s; }
.materia-fila:last-child { border-bottom: none; }
.materia-fila:hover { background: #F8FAFC; }
.materia-fila td { padding: 12px 14px; vertical-align: top; }
.materia-nombre { font-weight: 600; color: var(--texto); margin: 0 0 2px; }
.materia-clave  { font-size: 0.8rem; color: var(--gris); margin: 0; font-family: monospace; }
.td-grupo { font-size: 0.85rem; color: var(--gris); white-space: nowrap; }

.select-accion {
  padding: 7px 10px; border: 1px solid var(--borde); border-radius: 6px;
  font-size: 0.85rem; font-family: 'Montserrat', sans-serif; color: var(--texto);
  background: white; outline: none; cursor: pointer; transition: border-color 0.2s; width: 100%;
}
.select-accion:focus { border-color: var(--azul); }

.detalle-accion { padding-top: 4px; }
.select-grupo {
  padding: 7px 10px; border: 1px solid var(--borde); border-radius: 6px;
  font-size: 0.85rem; font-family: 'Montserrat', sans-serif; color: var(--texto);
  background: white; outline: none; cursor: pointer; width: 100%; transition: border-color 0.2s;
}
.select-grupo:focus { border-color: var(--azul); }

.textarea-motivo {
  width: 100%; padding: 8px 10px; border: 1px solid var(--borde); border-radius: 6px;
  font-size: 0.85rem; font-family: 'Montserrat', sans-serif; color: var(--texto);
  background: white; outline: none; resize: vertical; transition: border-color 0.2s;
  box-sizing: border-box;
}
.textarea-motivo:focus { border-color: var(--azul); box-shadow: 0 0 0 3px var(--azul-suave); }

.error-campo { font-size: 0.78rem; color: var(--rojo); font-weight: 600; margin: 4px 0 0; }

/* ── Animaciones ── */
.slide-down-enter-active { transition: all 0.25s ease; }
.slide-down-enter-from   { opacity: 0; transform: translateY(-8px); }

/* ── Form footer ── */
.form-footer {
  padding: 14px 2rem; background: #F5F5F5; border-top: 1px solid var(--borde);
  display: flex; justify-content: flex-end; gap: 10px;
}

.btn-primario {
  display: flex; align-items: center; gap: 8px;
  background: var(--azul); color: white; border: none;
  padding: 10px 24px; border-radius: 8px; font-weight: 600;
  font-size: 0.9rem; cursor: pointer; font-family: 'Montserrat', sans-serif;
  transition: background 0.2s; min-width: 160px; justify-content: center;
}
.btn-primario:hover:not(:disabled) { background: var(--azul-hover); }
.btn-primario:disabled { opacity: 0.6; cursor: not-allowed; }

.btn-cancelar {
  background: white; color: var(--azul); border: 1px solid var(--azul);
  padding: 10px 20px; border-radius: 8px; font-weight: 600; font-size: 0.9rem;
  cursor: pointer; font-family: 'Montserrat', sans-serif; transition: background 0.15s;
}
.btn-cancelar:hover:not(:disabled) { background: var(--azul-suave); }
.btn-cancelar:disabled { opacity: 0.5; cursor: not-allowed; }

/* ── Spinners ── */
.spinner-mini {
  width: 16px; height: 16px; border: 2px solid rgba(255,255,255,0.4);
  border-top-color: white; border-radius: 50%; animation: spin 0.7s linear infinite; display: inline-block;
}
.spinner-btn {
  width: 16px; height: 16px; border: 2px solid rgba(255,255,255,0.4);
  border-top-color: white; border-radius: 50%; animation: spin 0.7s linear infinite; display: inline-block;
}
@keyframes spin { to { transform: rotate(360deg); } }

.pie-pagina { text-align: center; color: #9CA3AF; font-size: 0.82rem; padding-top: 2rem; border-top: 1px solid var(--borde); margin-top: 1rem; font-family: 'Montserrat', sans-serif; }
</style>