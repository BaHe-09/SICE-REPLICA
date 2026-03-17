<!-- ============================================= -->
<!-- src/views/CalificacionesView.vue -->
<!-- VERSIÓN FINAL COMPLETA + COLUMNA NC INTELIGENTE -->
<!-- NC = No Crédito (solo cuando final < 6.0 o todas las notas = 0) -->
<!-- ============================================= -->

<template>
  <div class="sistema-layout">
    <!-- HEADER SUPERIOR -->
    <header class="top-header">
      <div class="header-left">
        <img src="/logo-tecnm.png" alt="Logo TecNM" class="header-logo">
        <span class="system-title">SISTEMA INTEGRAL DE CONTROL ESCOLAR</span>
      </div>
      <div class="header-right">
        <div class="search-bar">
          <input type="text" placeholder="Buscar..." v-model="busquedaGlobal">
          <span class="search-icon">🔍</span>
        </div>
        <div class="notification-bell"><span class="bell-icon">🛎️</span></div>
        <div class="user-menu">
          <span class="user-name">Administrador</span>
          <span class="dropdown-arrow">▼</span>
        </div>
      </div>
    </header>

    <!-- SIDEBAR -->
    <aside class="sidebar">
      <nav class="menu">
        <a href="#" class="menu-item"><span class="icon">📊</span> Dashboard</a>
        <a href="#" class="menu-item"><span class="icon">🔒</span> Seguridad</a>
        <a href="#" class="menu-item"><span class="icon">👥</span> Personas</a>
        <a href="#" class="menu-item"><span class="icon">👤</span> Usuarios</a>
        <a href="#" class="menu-item"><span class="icon">🏢</span> Recursos Humanos</a>
        <a href="#" class="menu-item active"><span class="icon">📚</span> Servicios Escolares</a>
        <a href="#" class="menu-item"><span class="icon">🎓</span> Gestión Académica</a>
        <a href="#" class="menu-item"><span class="icon">📅</span> Eventos</a>
        <a href="#" class="menu-item"><span class="icon">👥</span> Comité Académico</a>
      </nav>
    </aside>

    <!-- CONTENIDO PRINCIPAL -->
    <main class="main-content">
      <div class="breadcrumb">Servicios Escolares &gt; Grupos &gt; Calificaciones</div>
      <h1 class="page-title">Calificaciones</h1>
      <p class="subtitle">Captura de calificaciones de los alumnos del grupo seleccionado</p>

      <!-- FILTROS -->
      <div class="filters-card">
        <select v-model="filtroPeriodo" class="filter-select"><option>Mayo - Dic 2024</option></select>
        <select v-model="filtroCarrera" class="filter-select"><option>Ingeniería en Sistemas</option></select>
        <select v-model="filtroMateria" class="filter-select"><option>Algoritmos y Programación</option></select>
        <select v-model="filtroGrupo" class="filter-select"><option>IS-601-A</option></select>

        <button @click="buscar" class="btn-buscar">Buscar</button>
        <button class="btn-exportar">Exportar ▼</button>
      </div>

      <!-- PROMEDIO GENERAL -->
      <div class="average-card">
        <div class="chart-icon">📊</div>
        <div>
          <span class="avg-label">Promedio General</span>
          <span class="avg-number">8.76</span>
        </div>
      </div>

      <!-- TABLA CON COLUMNA NC -->
      <div class="table-container">
        <table class="calif-table">
          <thead>
            <tr>
              <th>No. Control</th>
              <th>Alumno</th>
              <th class="text-center">Parcial 1 (100%)</th>
              <th class="text-center">Parcial 2 (100%)</th>
              <th class="text-center">Proyecto (40%)</th>
              <th class="text-center">Final</th>
              <th class="text-center">NC</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="alumno in alumnos" :key="alumno.control">
              <td>{{ alumno.control }}</td>
              <td>{{ alumno.nombre }}</td>
              <td class="text-center"><input v-model="alumno.p1" type="number" step="0.1" class="nota-input"></td>
              <td class="text-center"><input v-model="alumno.p2" type="number" step="0.1" class="nota-input"></td>
              <td class="text-center"><input v-model="alumno.proy" type="number" step="0.1" class="nota-input"></td>
              <td class="text-center final">{{ calcularFinal(alumno) }}</td>
              <td class="text-center">
                <span v-if="esNC(alumno)" class="nc-badge">NC</span>
                <span v-else class="final-normal">{{ calcularFinal(alumno) }}</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- BOTÓN GUARDAR -->
      <div class="bottom-bar">
        <button @click="guardarTodo" class="btn-guardar">Guardar Cambios</button>
      </div>

      <footer class="footer">© 2026 Tecnológico Nacional de México | Todos los derechos reservados.</footer>
    </main>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const busquedaGlobal = ref('')

const filtroPeriodo = ref('Mayo - Dic 2024')
const filtroCarrera = ref('Ingeniería en Sistemas')
const filtroMateria = ref('Algoritmos y Programación')
const filtroGrupo = ref('IS-601-A')

const alumnos = ref([
  { control: '21456987', nombre: 'Sara Pérez', p1: 8, p2: 9.5, proy: 8 },
  { control: '21463254', nombre: 'Juan García', p1: 7, p2: 8.5, proy: 9 },
  { control: '21454128', nombre: 'Mariela Gómez', p1: 6, p2: 6.5, proy: 10 },
  { control: '21454321', nombre: 'Ana Rodríguez', p1: 9, p2: 0, proy: 0 },
  { control: '21451986', nombre: 'Carlos Torres', p1: 0, p2: 0, proy: 0 }   // Ejemplo de NC
])

const calcularFinal = (a) => ((Number(a.p1) * 0.3 + Number(a.p2) * 0.3 + Number(a.proy) * 0.4)).toFixed(1)

// LÓGICA NC (No Crédito)
const esNC = (a) => {
  const final = Number(calcularFinal(a))
  const todasCero = Number(a.p1) === 0 && Number(a.p2) === 0 && Number(a.proy) === 0
  return final < 6.0 || todasCero
}

const buscar = () => alert('✅ Búsqueda realizada')
const guardarTodo = () => alert('✅ Calificaciones guardadas correctamente')
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap');

.sistema-layout { font-family: 'Montserrat', sans-serif; display: flex; min-height: 100vh; background: #F5F7FA; }
.top-header { background: #1B396A; padding: 0.9rem 2.5rem; position: fixed; top: 0; left: 0; right: 0; height: 74px; display: flex; align-items: center; justify-content: space-between; z-index: 1000; }
.header-left { display: flex; align-items: center; gap: 1rem; }
.header-logo { height: 58px; filter: drop-shadow(0 0 10px rgba(255,255,255,0.95)); }
.system-title { font-size: 1.28rem; font-weight: 700; color: white; }
.header-right { display: flex; align-items: center; gap: 4rem; }
.search-bar { position: relative; width: 310px; }
.search-bar input { width: 100%; padding: 11px 15px 11px 45px; border: none; border-radius: 30px; background: rgba(255,255,255,0.18); color: white; }
.search-icon { position: absolute; left: 18px; top: 50%; transform: translateY(-50%); color: white; }
.notification-bell { font-size: 1.6rem; color: white; cursor: pointer; }
.user-menu { display: flex; align-items: center; gap: 8px; color: white; font-weight: 500; cursor: pointer; }
.sidebar { width: 260px; background: #1B396A; position: fixed; top: 74px; bottom: 0; left: 0; padding-top: 1rem; }
.menu-item { display: flex; align-items: center; gap: 12px; padding: 14px 24px; color: white; text-decoration: none; }
.menu-item.active { background: rgba(255,255,255,0.12); }
.main-content { margin-left: 260px; margin-top: 74px; padding: 2rem; flex: 1; }
.page-title { color: #005187; font-size: 1.9rem; font-weight: 700; margin-bottom: 1rem; }
.subtitle { color: #5A5A5A; margin-bottom: 1.5rem; }
.breadcrumb { color: #5A5A5A; margin-bottom: 1rem; font-size: 0.95rem; }

.filters-card { background: white; padding: 1.5rem; border-radius: 12px; display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 2rem; box-shadow: 0 8px 25px rgba(0,0,0,0.07); }
.filter-select { padding: 12px 16px; border: 1px solid #84B6E4; border-radius: 8px; min-width: 180px; }
.btn-buscar { background: #005187; color: white; padding: 12px 28px; border-radius: 8px; font-weight: 600; }
.btn-exportar { background: white; border: 1px solid #005187; color: #005187; padding: 12px 28px; border-radius: 8px; font-weight: 600; }

.average-card { background: white; border-radius: 12px; padding: 1.8rem; display: flex; align-items: center; gap: 1.5rem; margin-bottom: 2rem; box-shadow: 0 8px 25px rgba(0,0,0,0.07); }
.chart-icon { font-size: 2.8rem; }
.avg-label { color: #5A5A5A; font-size: 1.1rem; }
.avg-number { font-size: 2.8rem; font-weight: 700; color: #005187; }

.table-container { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 8px 25px rgba(0,0,0,0.07); }
.calif-table { width: 100%; border-collapse: collapse; }
.calif-table th { background: #F8FAFC; padding: 16px; font-weight: 600; }
.calif-table td { padding: 16px; border-bottom: 1px solid #E5E9F0; }
.nota-input { width: 90px; text-align: center; padding: 8px; border: 1px solid #84B6E4; border-radius: 6px; }
.nota-input:focus { border-color: #005187; }

.final { font-weight: 700; background: #E8F5E9; color: #2E7D32; padding: 8px 16px; border-radius: 6px; }

/* NC (No Crédito) - Estilo exacto de la imagen */
.nc-badge {
  background: #E1F5FE;
  color: #0288D1;
  padding: 6px 18px;
  border-radius: 6px;
  font-weight: 600;
  font-size: 0.95rem;
  display: inline-block;
}

.final-normal {
  font-weight: 700;
  color: #005187;
  padding: 8px 16px;
}

.bottom-bar { display: flex; justify-content: flex-end; margin-top: 2rem; }
.btn-guardar { background: #005187; color: white; padding: 14px 40px; border-radius: 8px; font-weight: 600; font-size: 1.05rem; }
.footer { margin-top: 3rem; text-align: center; color: #9AA3AF; font-size: 0.9rem; }
</style>