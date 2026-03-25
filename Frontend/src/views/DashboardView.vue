<template>
  <MainLayout v-slot="{ busquedaGlobal }">
    <div class="dashboard-page">

      <div class="dashboard-header">
        <h1 class="page-title">Dashboard</h1>
        <p class="welcome-text">Bienvenido al Sistema Integral de Control Escolar</p>
      </div>


      <div class="kpi-grid">
        <div class="kpi-card" v-for="(kpi, index) in kpis" :key="index">
          <div class="kpi-icon-wrapper">
            <svg xmlns="http://www.w3.org/2000/svg" class="kpi-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path :d="kpi.iconPath" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
            </svg>
          </div>
          <div class="kpi-content">
            <p class="kpi-title">{{ kpi.title }}</p>
            <p class="kpi-value">{{ kpi.value }}</p>
            <p v-if="kpi.trend" class="kpi-trend" :class="{ positive: kpi.trend.includes('+'), negative: kpi.trend.includes('-') }">
              {{ kpi.trend }}
            </p>
          </div>
        </div>
      </div>


      <div class="charts-row">

        <div class="chart-card">
          <h3 class="chart-title">Alumnos por Carrera</h3>
          <div class="bar-chart">
            <div v-for="(item, i) in carreraData" :key="i" class="bar-item">
              <div class="bar-label">{{ item.carrera }}</div>
              <div class="bar-container">
                <div class="bar-fill" :style="{ width: item.porcentaje + '%' }"></div>
              </div>
              <div class="bar-value">{{ item.porcentaje }}%</div>
            </div>
          </div>
        </div>


        <div class="chart-card">
          <h3 class="chart-title">Distribución por Semestre</h3>
          <div class="pie-container">
            <div class="pie-chart">
              <div class="pie-segment" style="background: conic-gradient(#1B396A 0deg 120deg, #D6D6D6 120deg 240deg, #6B7280 240deg 360deg);"></div>
            </div>
            <div class="pie-legend">
              <div v-for="(item, i) in semestreData" :key="i" class="legend-item">
                <span class="legend-color" :style="{ backgroundColor: item.color }"></span>
                <span class="legend-text">{{ item.semestre }}° Semestre — {{ item.cantidad }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>


      <div class="bottom-row">

        <div class="recent-activity">
          <h3 class="section-title">Actividad Reciente</h3>
          <div class="activity-list">
            <div v-for="(act, i) in recentActivity" :key="i" class="activity-item">
              <div class="activity-dot"></div>
              <div class="activity-info">
                <p class="activity-desc">{{ act.descripcion }}</p>
                <p class="activity-time">{{ act.tiempo }}</p>
              </div>
            </div>
          </div>
        </div>


        <div class="quick-actions">
          <h3 class="section-title">Acciones Rápidas</h3>
          <div class="actions-grid">
            <button @click="nuevaInscripcion" class="quick-btn">
              <span class="btn-icon">+</span>
              Nueva Inscripción
            </button>
            <button @click="irAAlumnos" class="quick-btn">
              Ver Lista de Alumnos
            </button>
            <button @click="irAGrupos" class="quick-btn">
              Gestión de Grupos
            </button>
            <button @click="irACalificaciones" class="quick-btn">
              Cargar Calificaciones
            </button>
          </div>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import MainLayout from '@/layouts/MainLayout.vue'

const router = useRouter()

// KPIs (estructura original conservada)
const kpis = ref([
  { title: 'Alumnos Activos', value: '0', iconPath: 'M17 20h5v-2a3 3 0 01-3-3V8a3 3 0 01-3-3V3a3 3 0 01-3-3H8a3 3 0 01-3 3v2a3 3 0 01-3 3v7a3 3 0 01-3 3v2h5m5-10v10', trend: '' },
  { title: 'Inscripciones', value: '0', iconPath: 'M12 6v12m-3-9h6m-6 6h6', trend: '' },
  { title: 'Baja Temporal', value: '0', iconPath: 'M13 10V3L4 14h7v7l9-11h-7z', trend: '' },
  { title: 'Baja Definitiva', value: '0', iconPath: 'M6 18L18 6M6 6h12v12', trend: '' },
  { title: 'Grupos Activos', value: '0', iconPath: 'M17 20h5v-2a3 3 0 01-3-3V8a3 3 0 01-3-3V3a3 3 0 01-3-3H8a3 3 0 01-3 3v2a3 3 0 01-3 3v7a3 3 0 01-3 3v2h5m5-10v10', trend: '' },
  { title: 'Promedio General', value: '0', iconPath: 'M14 10h4.764a2 2 0 011.789 2.894L18 19H6l-2.236-6.106A2 2 0 015.236 10H10', trend: '' }
])

const carreraData = ref([])
const semestreData = ref([])
const recentActivity = ref([])

const loading = ref(true)
const error = ref(null)

// Cargar datos reales
onMounted(async () => {
  try {
    const res = await fetch('http://localhost:8000/api/dashboard')

    if (!res.ok) throw new Error('Error en API')

    const data = await res.json()

    // KPIs dinámicos (sin romper estructura)
    kpis.value[0].value = data.kpis?.alumnos ?? 0
    kpis.value[1].value = data.kpis?.inscripciones ?? 0
    kpis.value[2].value = data.kpis?.bajas_temporales ?? 0
    kpis.value[3].value = data.kpis?.bajas_definitivas ?? 0
    kpis.value[4].value = data.kpis?.grupos ?? 0
    kpis.value[5].value = data.kpis?.promedio ?? 0

    // Carreras
    const total = data.carreras?.reduce((acc, c) => acc + c.total, 0) || 1

    carreraData.value = (data.carreras || []).map(c => ({
      carrera: c.nombre,
      porcentaje: Math.round((c.total / total) * 100)
    }))

    // Semestres
    semestreData.value = (data.semestres || []).map(s => ({
      semestre: s.semestre_actual,
      cantidad: s.total,
      color: '#1B396A'
    }))

    // Actividad reciente (puedes conectar a bitacora luego)
    recentActivity.value = [
      { descripcion: 'Datos cargados desde el sistema', tiempo: 'Ahora' }
    ]

  } catch (err) {
    console.error(err)
    error.value = 'Error cargando datos'
  } finally {
    loading.value = false
  }
})

// Navegación
const nuevaInscripcion = () => router.push('/inscripcion')
const irAAlumnos = () => router.push('/alumnos')
const irAGrupos = () => router.push('/gestion-grupos')
const irACalificaciones = () => router.push('/calificaciones')
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap');

.dashboard-page {
  max-width: 100%;
  background: #F5F5F5;
  font-family: 'Montserrat', sans-serif;
  padding-bottom: 2rem;
}

.dashboard-header {
  margin-bottom: 2rem;
}
.page-title {
  color: #1A1A1A;
  font-size: 2rem;
  font-weight: 700;
  letter-spacing: -0.02em;
}
.welcome-text {
  color: #6B7280;
  font-size: 1.1rem;
  margin-top: 4px;
}


.kpi-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2.5rem;
}
.kpi-card {
  background: #FFFFFF;
  border-radius: 12px;
  padding: 1.5rem;
  display: flex;
  align-items: center;
  gap: 1.2rem;
  box-shadow: 0 8px 25px rgba(0,0,0,0.07);
  border: 1px solid #E5E7EB;
  transition: transform 0.2s;
}
.kpi-card:hover {
  transform: translateY(-4px);
}
.kpi-icon-wrapper {
  width: 52px;
  height: 52px;
  background: #F5F5F5;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
}
.kpi-icon {
  width: 28px;
  height: 28px;
  stroke: #1B396A;
}
.kpi-content .kpi-title {
  font-size: 0.95rem;
  color: #6B7280;
  margin: 0;
}
.kpi-content .kpi-value {
  font-size: 1.8rem;
  font-weight: 700;
  color: #1A1A1A;
  margin: 4px 0 2px;
}
.kpi-trend {
  font-size: 0.85rem;
  font-weight: 600;
}
.kpi-trend.positive { color: #16A34A; }
.kpi-trend.negative { color: #DC2626; }


.charts-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.5rem;
  margin-bottom: 2.5rem;
}
.chart-card {
  background: #FFFFFF;
  border-radius: 12px;
  padding: 1.8rem;
  box-shadow: 0 8px 25px rgba(0,0,0,0.07);
  border: 1px solid #E5E7EB;
}
.chart-title {
  margin: 0 0 1.2rem;
  font-size: 1.1rem;
  font-weight: 600;
  color: #1A1A1A;
}


.bar-chart {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}
.bar-item {
  display: flex;
  align-items: center;
  gap: 12px;
}
.bar-label {
  width: 110px;
  font-size: 0.95rem;
  color: #1A1A1A;
}
.bar-container {
  flex: 1;
  height: 12px;
  background: #E5E7EB;
  border-radius: 9999px;
  overflow: hidden;
}
.bar-fill {
  height: 100%;
  background: #1B396A;
  border-radius: 9999px;
  transition: width 1s ease;
}
.bar-value {
  width: 42px;
  text-align: right;
  font-weight: 600;
  color: #1B396A;
}


.pie-container {
  display: flex;
  align-items: center;
  gap: 2rem;
}
.pie-chart {
  width: 180px;
  height: 180px;
  border-radius: 50%;
  position: relative;
}
.pie-segment {
  width: 100%;
  height: 100%;
  border-radius: 50%;
}
.pie-legend {
  flex: 1;
}
.legend-item {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 12px;
}
.legend-color {
  width: 14px;
  height: 14px;
  border-radius: 3px;
}


.bottom-row {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 1.5rem;
}


.recent-activity {
  background: #FFFFFF;
  border-radius: 12px;
  padding: 1.8rem;
  box-shadow: 0 8px 25px rgba(0,0,0,0.07);
  border: 1px solid #E5E7EB;
}
.section-title {
  margin: 0 0 1.2rem;
  font-size: 1.1rem;
  font-weight: 600;
  color: #1A1A1A;
}
.activity-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}
.activity-item {
  display: flex;
  gap: 12px;
  align-items: flex-start;
}
.activity-dot {
  width: 8px;
  height: 8px;
  background: #1B396A;
  border-radius: 50%;
  margin-top: 6px;
}
.activity-info {
  flex: 1;
}
.activity-desc {
  margin: 0;
  color: #1A1A1A;
  font-size: 0.98rem;
}
.activity-time {
  margin: 2px 0 0;
  color: #6B7280;
  font-size: 0.85rem;
}


.quick-actions {
  background: #FFFFFF;
  border-radius: 12px;
  padding: 1.8rem;
  box-shadow: 0 8px 25px rgba(0,0,0,0.07);
  border: 1px solid #E5E7EB;
}
.actions-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}
.quick-btn {
  background: #1B396A;
  color: white;
  border: none;
  padding: 14px 20px;
  border-radius: 10px;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  font-size: 1rem;
  transition: background 0.2s;
}
.quick-btn:hover {
  background: #1D4ED8;
}
.btn-icon {
  font-size: 1.4rem;
  line-height: 1;
}
</style>
