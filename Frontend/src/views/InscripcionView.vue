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
        <div class="notification-bell">🛎️</div>
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
        <a href="/gestion-grupos" class="menu-item"><span class="icon">🎓</span> Gestión Académica</a>
        <a href="#" class="menu-item"><span class="icon">📅</span> Eventos</a>
        <a href="#" class="menu-item"><span class="icon">👥</span> Comité Académico</a>
      </nav>
    </aside>

    <!-- CONTENIDO PRINCIPAL -->
    <main class="main-content">
      <h1 class="page-title">Inscripción</h1>

      <!-- FILTROS -->
      <div class="filters-bar">
        <input 
          type="text" 
          placeholder="Buscar alumno por nombre o No. Control..." 
          v-model="busquedaAlumno" 
          class="search-input"
          @keyup.enter="filtrar"
        >
        <select v-model="filtroPeriodo" class="filter-select" @change="filtrar">
          <option value="">Todos los periodos</option>
          <option value="Agosto 2024">Agosto 2024</option>
          <option value="Enero 2025">Enero 2025</option>
        </select>
        <button class="btn-limpiar" @click="limpiarFiltros">Limpiar filtros</button>
      </div>

      <!-- TABLA CON DATOS SIMULADOS -->
      <div class="table-container">
        <table class="alumnos-table">
          <thead>
            <tr>
              <th>Materia</th>
              <th>Docente</th>
              <th>Aula</th>
              <th>Capacidad</th>
              <th>Inscritos</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="grupo in gruposFiltrados" :key="grupo.id">
              <td>{{ grupo.materia }}</td>
              <td>{{ grupo.docente }}</td>
              <td>{{ grupo.aula }}</td>
              <td class="text-center">{{ grupo.capacidad }}</td>
              <td class="text-center">
                <span class="inscritos-badge" :class="grupo.inscritos >= grupo.capacidad ? 'lleno' : 'disponible'">
                  {{ grupo.inscritos }} / {{ grupo.capacidad }}
                </span>
              </td>
              <td class="actions">
                <button 
                  class="btn-inscribir" 
                  @click="inscribirAlumno(grupo)"
                  :disabled="grupo.inscritos >= grupo.capacidad"
                >
                  Inscribir
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- PAGINACIÓN -->
      <div class="pagination">
        <div class="pagination-left">Filas por página: <select><option>10</option><option>20</option></select></div>
        <div class="pagination-center">Página 1 de 8</div>
        <div class="pagination-right">
          <button>‹</button>
          <button class="active">1</button>
          <button>2</button>
          <button>3</button>
          <span class="ellipsis">…</span>
          <button>8</button>
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

const busquedaGlobal = ref('')
const busquedaAlumno = ref('')
const filtroPeriodo = ref('')

// ==================== DATOS SIMULADOS (como si vinieran del backend) ====================
const grupos = ref([
  { id: 1, materia: 'Programación y Programación', docente: 'Mtro. Luis Ortega', aula: 'A-101', capacidad: 30, inscritos: 23, periodo: 'Agosto 2024' },
  { id: 2, materia: 'Base de Datos', docente: 'Dra. Laura Castillo', aula: 'B-204', capacidad: 25, inscritos: 18, periodo: 'Agosto 2024' },
  { id: 3, materia: 'Administración de Redes', docente: 'Mtro. Pedro Jiménez', aula: 'C-103', capacidad: 28, inscritos: 28, periodo: 'Agosto 2024' },
  { id: 4, materia: 'Inteligencia Artificial', docente: 'Dra. Ana López', aula: 'D-305', capacidad: 20, inscritos: 12, periodo: 'Enero 2025' },
  { id: 5, materia: 'Desarrollo Web', docente: 'Mtro. Carlos Ramírez', aula: 'A-202', capacidad: 35, inscritos: 31, periodo: 'Agosto 2024' },
  { id: 6, materia: 'Cálculo Diferencial', docente: 'Dra. María González', aula: 'B-105', capacidad: 40, inscritos: 35, periodo: 'Enero 2025' },
  { id: 7, materia: 'Física I', docente: 'Mtro. Juan Pérez', aula: 'C-301', capacidad: 30, inscritos: 22, periodo: 'Agosto 2024' },
  { id: 8, materia: 'Inglés Técnico', docente: 'Mtra. Laura Mendoza', aula: 'D-401', capacidad: 25, inscritos: 25, periodo: 'Enero 2025' }
])

const gruposFiltrados = computed(() => {
  let resultado = grupos.value

  // Filtro de búsqueda
  if (busquedaAlumno.value) {
    const termino = busquedaAlumno.value.toLowerCase()
    resultado = resultado.filter(g => 
      g.materia.toLowerCase().includes(termino) || 
      g.docente.toLowerCase().includes(termino)
    )
  }

  // Filtro por periodo
  if (filtroPeriodo.value) {
    resultado = resultado.filter(g => g.periodo === filtroPeriodo.value)
  }

  return resultado
})

const filtrar = () => {} // ya se actualiza en tiempo real

const limpiarFiltros = () => {
  busquedaAlumno.value = ''
  filtroPeriodo.value = ''
}

const inscribirAlumno = (grupo) => {
  if (grupo.inscritos >= grupo.capacidad) return
  
  grupo.inscritos++
  alert(`✅ Alumno inscrito correctamente en:\n${grupo.materia}\nAula: ${grupo.aula}`)
}
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

/* Estilo para el botón Limpiar filtros - Azul institucional #005187 */
.btn-limpiar { 
  background: #005187; 
  color: white; 
  border: none; 
  padding: 11px 22px; 
  border-radius: 8px; 
  font-weight: 600; 
  cursor: pointer; 
  transition: background-color 0.2s ease; /* Transición suave para el hover */
}

.btn-limpiar:hover {
  background: #1B396A; /* Color más oscuro para el hover */
}

.table-container { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 8px 25px rgba(0,0,0,0.07); }
.alumnos-table { width: 100%; border-collapse: collapse; }
.alumnos-table th { background: #F8FAFC; padding: 16px; text-align: left; font-weight: 600; }
.alumnos-table td { padding: 16px; border-bottom: 1px solid #E5E9F0; }

/* Estilo para los badges de inscritos */
.inscritos-badge { 
  padding: 6px 16px; 
  border-radius: 9999px; 
  font-size: 0.95rem; 
  font-weight: 400; /* Peso normal (no negritas) */
}
.inscritos-badge.disponible { 
  color: #202020; /* Color negro normal */
}
.inscritos-badge.lleno { 
  color: #202020; /* Mismo color negro que los disponibles */
  font-weight: 400;
}

.actions { display: flex; gap: 8px; }

/* Estilo para los botones de inscribir - Color #B38E5D */
.btn-inscribir { 
  padding: 8px 20px; 
  border-radius: 6px; 
  font-size: 0.92rem; 
  font-weight: 600; 
  border: none; 
  cursor: pointer; 
  background: #B38E5D; 
  color: white; 
}

.btn-inscribir:disabled { 
  background: #9AA3AF; 
  cursor: not-allowed; 
}

.pagination { margin-top: 2rem; display: flex; justify-content: space-between; align-items: center; font-size: 0.95rem; }
.pagination-left, .pagination-center, .pagination-right { display: flex; align-items: center; gap: 10px; }
.pagination-right button { padding: 6px 12px; border: 1px solid #D1D9E6; background: white; border-radius: 6px; cursor: pointer; }
.pagination-right .active { background: #005187; color: white; border-color: #005187; }
.ellipsis { padding: 0 8px; color: #9AA3AF; }

.footer { margin-top: 3rem; text-align: center; color: #9AA3AF; font-size: 0.9rem; }

</style>