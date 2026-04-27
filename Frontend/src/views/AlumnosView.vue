<template>
  <div class="sistema-layout">
    <!-- HEADER SUPERIOR -->
    <header class="top-header">
      <div class="header-left">
        <img 
          src="/logo-tecnm.png" 
          alt="Logo TecNM" 
          class="header-logo"
        >
        <span class="system-title">SISTEMA INTEGRAL DE CONTROL ESCOLAR</span>
      </div>
      
      <div class="header-right">
        <!-- Buscador -->
        <div class="search-bar">
          <input type="text" placeholder="Buscar..." v-model="busquedaGlobal">
          <span class="search-icon">🔍</span>
        </div>

        <!-- Icono de Campana -->
        <div class="notification-bell">
          <span class="bell-icon">🛎️</span>
        </div>

        <!-- Usuario -->
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
      <h1 class="page-title">Lista de Alumnos</h1>

      <!-- BARRA DE FILTROS -->
      <div class="filters-bar">
        <input type="text" placeholder="Buscar alumno..." v-model="busquedaAlumno" class="search-input">
        <select v-model="filtroCarrera" class="filter-select">
          <option value="">Carrera</option>
          <option value="Ingeniería en Sistemas Computacionales">Ingeniería en Sistemas Computacionales</option>
          <option value="Ingeniería Industrial">Ingeniería Industrial</option>
          <option value="Ingeniería Civil">Ingeniería Civil</option>
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
        </select>

        <!-- BOTÓN ACTUALIZADO -->
        <button class="btn-nuevo" @click="nuevoAlumno">
          + Nuevo Alumno
        </button>
      </div>

      <!-- TABLA -->
      <div class="table-container">
        <table class="alumnos-table">
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
            <tr v-for="alumno in alumnosFiltrados" :key="alumno.id">
              <td>{{ alumno.noControl }}</td>
              <td>{{ alumno.nombre }}</td>
              <td>{{ alumno.carrera }}</td>
              <td>{{ alumno.semestre }}</td>
              <td><span class="status-badge" :class="alumno.estatus.toLowerCase().replace(/\s/g, '-')">{{ alumno.estatus }}</span></td>
              <td class="actions">
                <button class="btn-action ver" @click="verAlumno(alumno)">Ver</button>
                <button class="btn-action editar" @click="editarAlumno(alumno)">Editar</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- PAGINACIÓN -->
      <div class="pagination">
        <div class="pagination-left">
          Filas por página: 
          <select v-model="filasPorPagina">
            <option>10</option>
            <option>20</option>
            <option>50</option>
          </select>
        </div>
        
        <div class="pagination-center">
          Página 1 de 14
        </div>
        
        <div class="pagination-right">
          <button>‹</button>
          <button class="active">1</button>
          <button>2</button>
          <button>3</button>
          <button>4</button>
          <span class="ellipsis">…</span>
          <button>14</button>
          <button>›</button>
        </div>
      </div>

      <footer class="footer">
        © 2026 Tecnológico Nacional de México | Todos los derechos reservados.
      </footer>
    </main>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

const busquedaGlobal = ref('')
const busquedaAlumno = ref('')
const filtroCarrera = ref('')
const filtroSemestre = ref('')
const filtroEstatus = ref('')
const filasPorPagina = ref(10)

const alumnos = ref([
  { id: 1, noControl: '21456987', nombre: 'Sara Pérez', carrera: 'Ingeniería en Sistemas Computacionales', semestre: 6, estatus: 'Activo' },
  { id: 2, noControl: '21463254', nombre: 'Juan García', carrera: 'Ingeniería Industrial', semestre: 4, estatus: 'Activo' },
  { id: 3, noControl: '21454128', nombre: 'Mariela Gómez', carrera: 'Ingeniería Civil', semestre: 8, estatus: 'Activo' },
  { id: 4, noControl: '21454321', nombre: 'Ana Rodríguez', carrera: 'Lic. en Administración', semestre: 2, estatus: 'Activo' },
  { id: 5, noControl: '21451986', nombre: 'Carlos Torres', carrera: 'Ingeniería Mecatrónica', semestre: 5, estatus: 'Baja Temporal' },
  { id: 6, noControl: '21451976', nombre: 'Luis Herrera', carrera: 'Ingeniería Electrónica', semestre: 7, estatus: 'Activo' },
  { id: 7, noControl: '21454833', nombre: 'Pedro Jiménez', carrera: 'Ingeniería Química', semestre: 8, estatus: 'Activo' },
])

const alumnosFiltrados = computed(() => alumnos.value)

// Navega al formulario ===
const nuevoAlumno = () => {
  router.push('/formulario-alumno')
}

const verAlumno = (a) => alert(`Ver: ${a.nombre}`)
const editarAlumno = (a) => alert(`Editar: ${a.nombre}`)
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
.page-title { color: #005187; font-size: 1.9rem; font-weight: 700; margin-bottom: 1.2rem; }

.filters-bar { display: flex; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap; }
.search-input, .filter-select { padding: 11px 14px; border: 1px solid #D1D9E6; border-radius: 8px; font-size: 1rem; }
.btn-nuevo { background: #005187; color: white; border: none; padding: 11px 22px; border-radius: 8px; font-weight: 600; cursor: pointer; }

.table-container { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 8px 25px rgba(0,0,0,0.07); }
.alumnos-table { width: 100%; border-collapse: collapse; }
.alumnos-table th { background: #F8FAFC; padding: 16px; text-align: left; font-weight: 600; }
.alumnos-table td { padding: 16px; border-bottom: 1px solid #E5E9F0; }

.status-badge { padding: 5px 14px; border-radius: 20px; font-size: 0.88rem; font-weight: 500; }
.status-badge.activo { background: #E8F5E9; color: #2E7D32; }
.status-badge.baja-temporal { background: #FFF3E0; color: #ED6C02; }

.actions { display: flex; gap: 8px; }
.btn-action { padding: 7px 16px; border-radius: 6px; font-size: 0.92rem; cursor: pointer; }

.pagination { margin-top: 2rem; display: flex; justify-content: space-between; align-items: center; font-size: 0.95rem; }
.pagination-left, .pagination-center, .pagination-right { display: flex; align-items: center; gap: 10px; }
.pagination-right button { padding: 6px 12px; border: 1px solid #D1D9E6; background: white; border-radius: 6px; cursor: pointer; }
.pagination-right .active { background: #005187; color: white; border-color: #005187; }
.ellipsis { padding: 0 8px; color: #9AA3AF; }

.footer { margin-top: 3rem; text-align: center; color: #9AA3AF; font-size: 0.9rem; }
</style>