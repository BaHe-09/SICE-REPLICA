<template>
  <MainLayout v-slot="{ busquedaGlobal }">
    <div class="calificaciones-page">

      <div class="breadcrumb">Servicios Escolares › Grupos › Calificaciones</div>
      <h1 class="page-title">Calificaciones</h1>
      <p class="subtitle">Captura de calificaciones de los alumnos del grupo seleccionado</p>

      <div class="filters-card">
        <select v-model="filtroPeriodo" class="filter-select">
          <option>Mayo - Dic 2024</option>
        </select>
        <select v-model="filtroCarrera" class="filter-select">
          <option>Ingeniería en Sistemas</option>
        </select>
        <select v-model="filtroMateria" class="filter-select">
          <option>Algoritmos y Programación</option>
        </select>
        <select v-model="filtroGrupo" class="filter-select">
          <option>IS-601-A</option>
        </select>

        <button @click="buscar" class="btn-buscar">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 01-14 0 7 7 0 0114 0z" />
          </svg>
          Buscar
        </button>

        <button class="btn-exportar">Exportar ▼</button>
      </div>

      <div class="average-card">
        <svg xmlns="http://www.w3.org/2000/svg" class="avg-icon" width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="#005187" stroke-width="2.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 13h4v7H3zM9 7h4v13H9zM15 3h4v17h-4z" />
        </svg>
        <div class="avg-text">
          <span class="avg-label">Promedio General</span>
          <span class="avg-number">8.76</span>
        </div>
      </div>

      <div class="table-container">
        <table class="calif-table">
          <thead>
            <tr>
              <th>No. Control</th>
              <th>Alumno</th>
              <th class="text-center">Parcial 1 (30%)</th>
              <th class="text-center">Parcial 2 (30%)</th>
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

      <div class="actions-bar">
        <button @click="guardarTodo" class="btn-guardar">Guardar Cambios</button>
      </div>

    </div>
  </MainLayout>
</template>

<script setup>
import { ref } from 'vue'
import MainLayout from '@/layouts/MainLayout.vue'

const filtroPeriodo = ref('Mayo - Dic 2024')
const filtroCarrera = ref('Ingeniería en Sistemas')
const filtroMateria = ref('Algoritmos y Programación')
const filtroGrupo = ref('IS-601-A')

const alumnos = ref([
  { control: '21456987', nombre: 'Sara Pérez', p1: 8, p2: 9.5, proy: 8 },
  { control: '21463254', nombre: 'Juan García', p1: 7, p2: 8.5, proy: 9 },
  { control: '21454128', nombre: 'Mariela Gómez', p1: 6, p2: 6.5, proy: 10 },
  { control: '21454321', nombre: 'Ana Rodríguez', p1: 9, p2: 0, proy: 0 },
  { control: '21451986', nombre: 'Carlos Torres', p1: 0, p2: 0, proy: 0 }
])

const calcularFinal = (a) => ((Number(a.p1) * 0.3 + Number(a.p2) * 0.3 + Number(a.proy) * 0.4)).toFixed(1)

const esNC = (a) => {
  const final = Number(calcularFinal(a))
  const todasCero = Number(a.p1) === 0 && Number(a.p2) === 0 && Number(a.proy) === 0
  return final < 6.0 || todasCero
}

const buscar = () => alert('✅ Búsqueda realizada')
const guardarTodo = () => alert('✅ Calificaciones guardadas correctamente')
</script>

<style scoped>
.calificaciones-page { max-width: 100%; }

.page-title { 
  color: #005187;
  font-size: 2.1rem;
  font-weight: 700;
  margin-bottom: 0.4rem;
}
.subtitle { color: #5A5A5A; margin-bottom: 1.8rem; }
.breadcrumb { color: #5A5A5A; margin-bottom: 1rem; font-size: 0.95rem; }

.filters-card {
  background: white;
  padding: 1.4rem;
  border-radius: 12px;
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
  margin-bottom: 2rem;
  box-shadow: 0 8px 25px rgba(0,0,0,0.07);
}
.filter-select { padding: 12px 16px; border: 1px solid #84B6E4; border-radius: 8px; min-width: 180px; }

.btn-buscar {
  background: #005187;
  color: white;
  padding: 12px 28px;
  border-radius: 8px;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 8px;
}
.btn-exportar { background: white; border: 1px solid #005187; color: #005187; padding: 12px 28px; border-radius: 8px; font-weight: 600; }

.average-card {
  background: #F8FAFC;
  border-radius: 12px;
  padding: 1rem 1.6rem;
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 2rem;
  max-width: 340px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}
.avg-icon { width: 28px; height: 28px; }
.avg-label { color: #5A5A5A; font-size: 1rem; font-weight: 500; }
.avg-number { font-size: 2.1rem; font-weight: 700; color: #005187; line-height: 1; }

.table-container { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 8px 25px rgba(0,0,0,0.07); }
.calif-table th { background: #F8FAFC; padding: 16px; font-weight: 600; text-align: center; }
.nota-input { width: 90px; text-align: center; padding: 8px; border: 1px solid #D1D9E6; border-radius: 6px; }

.final { font-weight: 700; color: #1A1A1A; }
.nc-badge { background: #E1F5FE; color: #0288D1; padding: 6px 18px; border-radius: 6px; font-weight: 600; }

.actions-bar {
  display: flex;
  justify-content: flex-end;
  margin-top: 2.5rem;
}
.btn-guardar {
  background: #005187;
  color: white;
  padding: 14px 42px;
  border-radius: 8px;
  font-weight: 600;
  font-size: 1.05rem;
}
</style>