<!-- ============================================= -->
<!-- src/views/CalificacionesView.vue -->
<!-- Iconos actualizados: Lupa + Guardar (bookmark) -->
<!-- ============================================= -->

<template>
  <MainLayout v-slot="{ busquedaGlobal }">
    <div class="calificaciones-page">

      <div class="breadcrumb">Servicios Escolares › Grupos › Calificaciones</div>
      <h1 class="page-title">Calificaciones</h1>
      <p class="subtitle">Captura de calificaciones de los alumnos del grupo seleccionado</p>

      <!-- Filtros -->
      <div class="filters-card">
        <select v-model="filtroPeriodo" class="filter-select">
          <option>Mayo - Dic 2024</option>
          <option>Ene - Jun 2025</option>
        </select>
        <select v-model="filtroCarrera" class="filter-select">
          <option>Ingeniería en Sistemas Computacionales</option>
        </select>
        <select v-model="filtroMateria" class="filter-select">
          <option>Algoritmos y Programación</option>
        </select>
        <select v-model="filtroGrupo" class="filter-select">
          <option>IS-601-A</option>
        </select>

        <!-- Botón Buscar con lupa -->
        <button @click="buscar" class="btn-buscar">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="icon-btn">
            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
          </svg>
          Buscar
        </button>
        
        <button class="btn-exportar">Exportar ▼</button>
      </div>

      <!-- PROMEDIO GENERAL -->
      <div class="average-card">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#1B396A" class="avg-icon">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
        </svg>
        <div class="avg-text">
          <span class="avg-label">Promedio General</span>
          <span class="avg-number">8.76</span>
        </div>
      </div>

      <!-- Tabla -->
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
              <th class="text-center">Acciones</th>
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
              <td class="text-center">
                <!-- Botón Guardar por fila con icono bookmark -->
                <button @click="guardarFila(alumno)" class="btn-guardar-fila">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="icon-btn">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0Z" />
                  </svg>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Botón Guardar Todas con icono bookmark -->
      <div class="actions-bar">
        <button @click="guardarTodo" class="btn-guardar">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="icon-btn">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0Z" />
          </svg>
          Guardar Todas las Calificaciones
        </button>
      </div>

    </div>
  </MainLayout>
</template>

<script setup>
import { ref } from 'vue'
import MainLayout from '@/layouts/MainLayout.vue'

const filtroPeriodo = ref('Mayo - Dic 2024')
const filtroCarrera = ref('Ingeniería en Sistemas Computacionales')
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

const buscar = () => console.log('🔎 Buscando...')
const guardarFila = (alumno) => alert(`✅ Fila de ${alumno.nombre} guardada`)
const guardarTodo = () => alert('✅ Todas las calificaciones guardadas correctamente')
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap');

.calificaciones-page { width: 100%; background: #F5F7FA; }
.page-title { color: #005187; font-size: 2.1rem; font-weight: 700; margin-bottom: 0.4rem; }
.subtitle { color: #5A5A5A; margin-bottom: 1.8rem; }
.breadcrumb { color: #5A5A5A; margin-bottom: 1rem; font-size: 0.95rem; }

.filters-card { background: #FFFFFF; padding: 1.4rem; border-radius: 12px; display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 2rem; box-shadow: 0 8px 25px rgba(0,0,0,0.07); border: 1px solid #E5E7EB; }
.filter-select { padding: 12px 16px; border: 1px solid #E5E7EB; border-radius: 8px; min-width: 180px; }
.btn-buscar { background: #005187; color: white; padding: 12px 28px; border-radius: 8px; font-weight: 600; display: flex; align-items: center; gap: 8px; }
.btn-exportar { background: #FFFFFF; border: 1px solid #005187; color: #005187; padding: 12px 28px; border-radius: 8px; font-weight: 600; }

/* Promedio General */
.average-card {
  background: #FFFFFF;
  border-radius: 12px;
  padding: 1.5rem 2rem;
  display: flex;
  align-items: center;
  gap: 1.5rem;
  margin-bottom: 2rem;
  box-shadow: 0 8px 25px rgba(0,0,0,0.07);
  max-width: 340px;
}
.avg-icon { width: 48px; height: 48px; }
.avg-label { color: #5A5A5A; font-size: 1rem; font-weight: 500; }
.avg-number { font-size: 2.8rem; font-weight: 700; color: #005187; line-height: 1; }

.table-container { background: #FFFFFF; border-radius: 12px; overflow: hidden; box-shadow: 0 8px 25px rgba(0,0,0,0.07); border: 1px solid #E5E7EB; }
.calif-table { width: 100%; border-collapse: collapse; }
.calif-table th { background: #F8FAFC; padding: 18px 16px; font-weight: 600; }
.nota-input { width: 90px; text-align: center; padding: 10px; border: 1px solid #84B6E4; border-radius: 8px; }

.final { font-weight: 700; background: #E8F5E9; color: #2E7D32; padding: 8px 16px; border-radius: 6px; }
.nc-badge { background: #E1F5FE; color: #0288D1; padding: 6px 18px; border-radius: 6px; font-weight: 600; }

.btn-guardar-fila { 
  background: #005187; 
  color: white; 
  padding: 8px 20px; 
  border-radius: 8px; 
  font-weight: 600; 
  font-size: 0.9rem; 
  cursor: pointer; 
  display: flex; 
  align-items: center; 
  justify-content: center; 
  gap: 6px;
  width: 110px;
}

.actions-bar { display: flex; justify-content: flex-end; margin-top: 2.5rem; }
.btn-guardar { 
  background: #005187; 
  color: white; 
  padding: 14px 42px; 
  border-radius: 8px; 
  font-weight: 600; 
  font-size: 1.05rem; 
  display: flex; 
  align-items: center; 
  gap: 8px; 
}

/* Tamaño de iconos SVG */
.icon-btn {
  width: 20px;
  height: 20px;
}
</style>