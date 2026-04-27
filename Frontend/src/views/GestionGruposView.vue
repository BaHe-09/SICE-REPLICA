<template>
  <div class="sistema-layout">
    <!-- HEADER SUPERIOR -->
    <header class="top-header">
      <div class="header-left">
        <!-- LOGO CORREGIDO + ESTILO FORZADO -->
        <img 
          src="/logo-tecnm.png" 
          alt="Logo TecNM" 
          class="header-logo"
        >
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
        <a href="/inscripcion" class="menu-item"><span class="icon">📚</span> Servicios Escolares</a>
        <a href="#" class="menu-item active"><span class="icon">🎓</span> Gestión Académica</a>
        <a href="#" class="menu-item"><span class="icon">📅</span> Eventos</a>
        <a href="#" class="menu-item"><span class="icon">👥</span> Comité Académico</a>
      </nav>
    </aside>

    <!-- CONTENIDO PRINCIPAL -->
    <main class="main-content">
      <h1 class="page-title">Grupos</h1>

      <!-- FILTROS (exactos a la Plantilla 2.4) -->
      <div class="filters-bar">
        <input 
          type="text" 
          placeholder="Buscar grupo..." 
          v-model="busquedaGrupo" 
          class="search-input"
          @keyup.enter="filtrar"
        >
        <button class="btn-filtrar" @click="filtrar">Filtrar</button>
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
                <button class="btn-accion evaluaciones" @click="irAEvaluaciones(grupo)">Evaluaciones</button>
                <button class="btn-accion calificaciones" @click="irACalificaciones(grupo)">Calificaciones</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- PAGINACIÓN (exacta a la imagen) -->
      <div class="pagination">
        <div class="pagination-left">Mostrando 3 de 10 grupos disponibles</div>
        <div class="pagination-center">
          Página 1 de 2
          <button>‹</button>
          <button class="active">1</button>
          <button>2</button>
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
const busquedaGrupo = ref('')

// ==================== DATOS SIMULADOS (como si vinieran del backend) ====================
const grupos = ref([
  { id: 1, materia: 'Algoritmos y Programación', docente: 'Mtro. Juan Morales', aula: 'A-201', capacidad: 30, inscritos: 27 },
  { id: 2, materia: 'Algoritmos y Programación', docente: 'Dra. Laura Ortega', aula: 'B-203', capacidad: 25, inscritos: 19 },
  { id: 3, materia: 'Base de Datos', docente: 'Dra. Ana Ruiz', aula: 'A-103', capacidad: 30, inscritos: 28 },
  { id: 4, materia: 'Base de Datos', docente: 'Mtro. Pedro Sánchez', aula: 'B-101', capacidad: 30, inscritos: 24 },
  { id: 5, materia: 'Administración de Redes', docente: 'Mtro. Carlos Jiménez', aula: 'A-204', capacidad: 25, inscritos: 23 },
  { id: 6, materia: 'Administración de Redes', docente: 'Dra. Patricia Villa', aula: 'B-102', capacidad: 30, inscritos: 19 },
  { id: 7, materia: 'Inteligencia Artificial', docente: 'Mtro. Roberto Campos', aula: 'C-301', capacidad: 20, inscritos: 15 },
  { id: 8, materia: 'Desarrollo Web', docente: 'Dra. Sofía Herrera', aula: 'D-405', capacidad: 35, inscritos: 32 }
])

const gruposFiltrados = computed(() => {
  if (!busquedaGrupo.value) return grupos.value
  const termino = busquedaGrupo.value.toLowerCase()
  return grupos.value.filter(g => 
    g.materia.toLowerCase().includes(termino) || 
    g.docente.toLowerCase().includes(termino)
  )
})

const filtrar = () => {} // ya se actualiza en tiempo real

const irAEvaluaciones = (grupo) => {
  alert(`📊 Abriendo Evaluaciones para:\n${grupo.materia}\nDocente: ${grupo.docente}`)
  // router.push(`/evaluaciones/${grupo.id}`) ← aquí iría la ruta real
}

const irACalificaciones = (grupo) => {
  alert(`📝 Abriendo Calificaciones para:\n${grupo.materia}\nDocente: ${grupo.docente}`)
  // router.push(`/calificaciones/${grupo.id}`) ← aquí iría la ruta real
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap');

/* === ESTILOS EXACTOS QUE USAS EN INSCRIPCIÓN (copiados y adaptados) === */
.sistema-layout { font-family: 'Montserrat', sans-serif; display: flex; min-height: 100vh; background: #F5F7FA; }

.top-header { background: #1B396A; padding: 0.9rem 2.5rem; position: fixed; top: 0; left: 0; right: 0; height: 74px; display: flex; align-items: center; justify-content: space-between; z-index: 1000; }
.header-left { display: flex; align-items: center; gap: 1rem; }
.header-logo { height: 58px; }
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

.filters-bar { display: flex; gap: 1rem; margin-bottom: 1.5rem; }
.search-input { padding: 11px 14px; border: 1px solid #D1D9E6; border-radius: 8px; font-size: 1rem; width: 320px; }
.btn-filtrar { background: #005187; color: white; border: none; padding: 11px 22px; border-radius: 8px; font-weight: 600; cursor: pointer; }

.table-container { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 8px 25px rgba(0,0,0,0.07); }
.alumnos-table { width: 100%; border-collapse: collapse; }
.alumnos-table th { background: #F8FAFC; padding: 16px; text-align: left; font-weight: 600; }
.alumnos-table td { padding: 16px; border-bottom: 1px solid #E5E9F0; }

.inscritos-badge { 
  padding: 6px 16px; 
  border-radius: 9999px; 
  font-size: 0.95rem; 
  font-weight: 400; /* Cambiado de 600 a 400 */
}
.inscritos-badge.disponible { 
  color: #202020; 
}
.inscritos-badge.lleno { 
  background: #FFF3E0; 
  color: #ED6C02; 
  font-weight: 400; /* Añadido para mantener consistencia */
}

.actions { display: flex; gap: 8px; }
.btn-accion { 
  padding: 9px 18px; 
  border-radius: 8px; 
  font-size: 0.9rem; 
  font-weight: 600; 
  border: none; 
  cursor: pointer; 
}
.btn-accion.evaluaciones { background: #4D82BE; color: white; }
.btn-accion.calificaciones { background: #B38E5D; color: white; }

.pagination { margin-top: 2rem; display: flex; justify-content: space-between; align-items: center; font-size: 0.95rem; }
.pagination-center button { padding: 6px 12px; border: 1px solid #D1D9E6; background: white; border-radius: 6px; cursor: pointer; }
.pagination-center .active { background: #005187; color: white; }

.header-logo {
  height: 58px;
  filter: drop-shadow(0 0 10px rgba(255,255,255,0.95));
}

.footer { margin-top: 3rem; text-align: center; color: #9AA3AF; font-size: 0.9rem; }
</style>