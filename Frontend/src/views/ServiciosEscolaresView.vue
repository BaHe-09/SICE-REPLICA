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
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 01-3-3V8a3 3 0 01-3-3V3a3 3 0 01-3-3H8a3 3 0 01-3 3v2a3 3 0 01-3 3v7a3 3 0 01-3 3v2h5m5-10v10" />
            </svg>
            <div class="info">
              <h3>Alumnos Activos</h3>
              <p class="number">{{ alumnosActivos }}</p>
              <a href="#" class="ver-link" @click.prevent="irAAlumnos">Ver Alumnos →</a>
            </div>
          </div>


          <div class="stat-card">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18 9.246 18 10.832 18.477 12 19.253zm0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18 14.754 18 13.168 18.477 12 19.253z" />
            </svg>
            <div class="info">
              <h3>Inscripciones del Período</h3>
              <p class="number">{{ inscripcionesPeriodo }}</p>
              <a href="#" class="ver-link" @click.prevent="irAInscripciones">Ver Inscripciones →</a>
            </div>
          </div>


          <div class="stat-card">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 01-3-3V8a3 3 0 01-3-3V3a3 3 0 01-3-3H8a3 3 0 01-3 3v2a3 3 0 01-3 3v7a3 3 0 01-3 3v2h5m5-10v10" />
            </svg>
            <div class="info">
              <h3>Grupos Abiertos</h3>
              <p class="number">{{ gruposAbiertos }}</p>
              <a href="#" class="ver-link" @click.prevent="irAGrupos">Ver Grupos →</a>
            </div>
          </div>

          <div class="stat-card">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 8.944 11.922.42.095.858.143 1.295.143a3 3 0 01.935-.072" />
            </svg>
            <div class="info">
              <h3>Evaluaciones Pendientes</h3>
              <p class="number">{{ evaluacionesPendientes }}</p>
              <a href="#" class="ver-link" @click.prevent="irAEvaluaciones">Ver Evaluaciones →</a>
            </div>
          </div>

        </div>


        <div class="accesos-rapidos">
          <h2 class="section-title">Accesos Rápidos</h2>
          <div class="quick-grid">

            <div class="quick-card" @click="nuevaInscripcion">
              <div class="quick-icon green">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2 2 2 0 01-2 2v-2m14-2V9a2 2 0 00-2-2M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2" />
                </svg>
              </div>
              <div class="quick-content">
                <h4>Gestionar Inscripciones</h4>
                <p>Dale de alta a los alumnos</p>
              </div>
              <span class="arrow">→</span>
            </div>

            <div class="quick-card" @click="irAGrupos">
              <div class="quick-icon blue">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 01-3-3V8a3 3 0 01-3-3V3a3 3 0 01-3-3H8a3 3 0 01-3 3v2a3 3 0 01-3 3v7a3 3 0 01-3 3v2h5m5-10v10" />
                </svg>
              </div>
              <div class="quick-content">
                <h4>Administrar Grupos</h4>
                <p>Visualiza y organiza grupos</p>
              </div>
              <span class="arrow">→</span>
            </div>

            <div class="quick-card" @click="irAEvaluaciones">
              <div class="quick-icon amber">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 8.944 11.922.42.095.858.143 1.295.143a3 3 0 01.935-.072" />
                </svg>
              </div>
              <div class="quick-content">
                <h4>Supervisar Evaluaciones</h4>
                <p>Revisa evaluaciones pendientes</p>
              </div>
              <span class="arrow">→</span>
            </div>

            <div class="quick-card" @click="consultarKardex">
              <div class="quick-icon purple">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2" />
                </svg>
              </div>
              <div class="quick-content">
                <h4>Consultar Kardex</h4>
                <p>Revisa el expediente académico</p>
              </div>
              <span class="arrow">→</span>
            </div>

          </div>
        </div>

      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import { ref, computed, reactive } from 'vue'
import { useRouter } from 'vue-router'
import MainLayout from '@/layouts/MainLayout.vue'

const router = useRouter()

const busquedaTabla = ref('')
const filtroCarrera = ref('')
const filtroSemestre = ref('')
const filtroEstatus = ref('')

const notification = reactive({ message: '', type: '' })

const inscripcionesFiltradas = computed(() => inscripciones.value)

const irAAlumnos = () => router.push('/alumnos')
const irAInscripciones = () => router.push('/inscripcion')
const irAGrupos = () => router.push('/gestion-grupos')
const irAEvaluaciones = () => router.push('/evaluaciones')

const nuevaInscripcion = () => router.push('/inscripcion')
const gestionarTodo = () => {}
const consultarKardex = () => router.push('/kardex')

const showNotification = (message, type) => {
  notification.message = message
  notification.type = type
  setTimeout(() => { notification.message = '' }, 3000)
}

import { onMounted } from 'vue'

const alumnosActivos = ref(0)
const inscripcionesPeriodo = ref(0)
const gruposAbiertos = ref(0)
const evaluacionesPendientes = ref(0)

onMounted(async () => {
  try {
    const res = await fetch('http://localhost:8000/api/dashboard')
    const data = await res.json()

    alumnosActivos.value = data.kpis.alumnos
    inscripcionesPeriodo.value = data.kpis.inscripciones
    gruposAbiertos.value = data.kpis.grupos
    evaluacionesPendientes.value = data.kpis.evaluaciones || 0

  } catch (error) {
    console.error(error)
  }
})
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap');

.servicios-escolares-page {
  width: 100%;
  padding: 2rem 2.5rem;
  background: #F5F5F5;
}

.breadcrumb {
  color: #6B7280;
  font-size: 0.95rem;
  margin-bottom: 1rem;
}

.page-title {
  color: #1A1A1A;
  font-size: 2.4rem;
  font-weight: 700;
  margin-bottom: 2.5rem;
}

.content-card {
  background: #FFFFFF;
  border-radius: 20px;
  box-shadow: 0 12px 35px rgba(0,0,0,0.09);
  padding: 3rem;
  max-width: 1200px;
  margin: 0 auto;
}


.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 1.5rem;
  margin-bottom: 3rem;
}

.stat-card {
  background: #FFFFFF;
  border-radius: 16px;
  padding: 1.8rem 1.5rem;
  display: flex;
  align-items: center;
  gap: 1.2rem;
  box-shadow: 0 6px 20px rgba(0,0,0,0.06);
  transition: transform 0.2s ease;
}
.stat-card:hover {
  transform: translateY(-4px);
}

.stat-card.blue {
  background: linear-gradient(135deg, #1B396A, #2563EB);
  color: white;
}

.icon {
  width: 52px;
  height: 52px;
  flex-shrink: 0;
}

.info h3 {
  font-size: 1.1rem;
  margin: 0 0 0.4rem 0;
  font-weight: 600;
}
.number {
  font-size: 2.6rem;
  font-weight: 700;
  margin: 0;
  line-height: 1;
}
.ver-link {
  color: #1B396A;
  font-weight: 600;
  text-decoration: none;
  font-size: 0.95rem;
}
.stat-card.blue .ver-link { color: white; }


.accesos-rapidos {
  margin-top: 1rem;
}
.section-title {
  color: #1A1A1A;
  font-size: 1.45rem;
  font-weight: 700;
  margin-bottom: 1.4rem;
}

.quick-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 1.2rem;
}

.quick-card {
  background: #FFFFFF;
  border-radius: 16px;
  padding: 1.6rem;
  display: flex;
  align-items: center;
  gap: 1.2rem;
  box-shadow: 0 6px 20px rgba(0,0,0,0.06);
  cursor: pointer;
  transition: all 0.2s ease;
}
.quick-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 10px 30px rgba(0,0,0,0.12);
}

.quick-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.quick-icon.green { background: #DCFCE7; color: #16A34A; }
.quick-icon.blue { background: #DBEAFE; color: #1B396A; }
.quick-icon.amber { background: #FEF3C7; color: #F59E0B; }
.quick-icon.purple { background: #E0E7FF; color: #4D82BE; }

.quick-content h4 {
  margin: 0 0 0.3rem 0;
  font-size: 1.1rem;
  font-weight: 600;
  color: #1A1A1A;
}
.quick-content p {
  margin: 0;
  color: #6B7280;
  font-size: 0.95rem;
}
.arrow {
  margin-left: auto;
  font-size: 1.4rem;
  color: #6B7280;
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
