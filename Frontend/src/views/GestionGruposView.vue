<template>
  <MainLayout v-slot="{ busquedaGlobal }">
    <div class="grupos-page">

      <h1 class="page-title">Grupos</h1>

      <div class="search-wrapper">
        <div class="search-group">
          <svg xmlns="http://www.w3.org/2000/svg" class="search-icon-svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 01-14 0 7 7 0 0114 0z" />
          </svg>
          <input
            type="text"
            placeholder="Buscar grupo..."
            v-model="busquedaGrupo"
            class="search-input"
            @keyup.enter="filtrar"
          >
        </div>
        <button class="btn-filtrar" @click="filtrar">Filtrar</button>
      </div>

      <div class="table-container">
        <table class="grupos-table">
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
                <span class="inscritos-text">{{ grupo.inscritos }} / {{ grupo.capacidad }}</span>
              </td>
              <td class="actions">
                <button class="btn-accion evaluaciones" @click="irAEvaluaciones(grupo)">Evaluaciones</button>
                <button class="btn-accion calificaciones" @click="irACalificaciones(grupo)">Calificaciones</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="pagination">
        <div class="pagination-left">Mostrando {{ gruposFiltrados.length }} de {{ grupos.length }} grupos disponibles</div>
        <div class="pagination-center">
          Página 1 de 2
          <button>‹</button>
          <button class="active">1</button>
          <button>2</button>
          <button>›</button>
        </div>
      </div>

    </div>
  </MainLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import MainLayout from '@/layouts/MainLayout.vue'

const router = useRouter()
const busquedaGrupo = ref('')

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

const filtrar = () => {
}

const irAEvaluaciones = (grupo) => {
  router.push({ 
    name: 'Evaluaciones', 
    params: { id: grupo.id }
  })
}

const irACalificaciones = (grupo) => {
  router.push({ 
    name: 'Calificaciones', 
    params: { id: grupo.id }
  })
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap');

.grupos-page { width: 100%; }

.page-title {
  color: #005187;
  font-size: 2.4rem;
  font-weight: 700;
  margin-bottom: 1.8rem;
}

.search-wrapper {
  display: flex;
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.06);
  overflow: hidden;
  max-width: 620px;
  margin-bottom: 2rem;
}

.search-group {
  position: relative;
  flex: 1;
}
.search-input {
  width: 100%;
  padding: 14px 20px 14px 50px;
  border: none;
  font-size: 1rem;
  background: transparent;
}
.search-icon-svg {
  position: absolute;
  left: 18px;
  top: 50%;
  transform: translateY(-50%);
  width: 20px;
  height: 20px;
  stroke: #6B7280;
}

.btn-filtrar {
  background: #005187;
  color: white;
  border: none;
  padding: 0 34px;
  font-weight: 600;
  cursor: pointer;
  font-size: 1rem;
  white-space: nowrap;
}

.table-container {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 8px 25px rgba(0,0,0,0.07);
}
.grupos-table { width: 100%; border-collapse: collapse; }
.grupos-table th { background: #F8FAFC; padding: 16px; text-align: left; font-weight: 600; }
.grupos-table td { padding: 16px; border-bottom: 1px solid #E5E9F0; }

.inscritos-text { font-weight: 600; color: #1A1A1A; font-size: 1rem; }

.actions { display: flex; gap: 10px; }
.btn-accion {
  padding: 9px 20px;
  border-radius: 8px;
  font-size: 0.92rem;
  font-weight: 600;
  border: none;
  cursor: pointer;
}
.btn-accion.evaluaciones { background: #005187; color: white; }
.btn-accion.calificaciones { background: #9AA3AF; color: white; }

.pagination {
  margin-top: 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 0.95rem;
}
.pagination-center button {
  padding: 6px 12px;
  border: 1px solid #D1D9E6;
  background: white;
  border-radius: 6px;
  cursor: pointer;
}
.pagination-center .active { background: #005187; color: white; }
</style>