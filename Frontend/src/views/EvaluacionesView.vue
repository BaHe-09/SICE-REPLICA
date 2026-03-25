<!-- ============================================= -->
<!-- src/views/EvaluacionesView.vue -->
<!-- Iconos actualizados: Lupa + Guardar (bookmark) -->
<!-- ============================================= -->

<template>
  <MainLayout v-slot="{ busquedaGlobal }">
    <div class="evaluaciones-page">

      <div class="breadcrumb">Servicios Escolares › Grupos › Evaluaciones</div>
      <h1 class="page-title">Evaluaciones</h1>

      <!-- Tarjeta Materia -->
      <div class="subject-card">
        <div class="subject-info">
          <h2>Algoritmos y Programación</h2>
          <p>Aula: A-201 Periodo: Ago/Dic 2024<br>Docente: Mtro. Juan Morales</p>
        </div>
        <button @click="abrirModalNueva" class="btn-nueva-eval">
          <span class="plus">+</span> Nueva Evaluación
        </button>
      </div>

      <!-- Filtros -->
      <div class="filters-card">
        <select v-model="filtroPeriodo" class="filter-select">
          <option>Ago/Dic 2024</option>
          <option>Ene/Jun 2025</option>
        </select>
        <select v-model="filtroMateria" class="filter-select">
          <option>Algoritmos y Programación</option>
        </select>
        
        <!-- Botón Buscar con lupa -->
        <button @click="buscar" class="btn-buscar">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="icon-btn">
            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
          </svg>
          Buscar
        </button>
      </div>

      <!-- Tabla -->
      <div class="table-container">
        <table class="eval-table">
          <thead>
            <tr>
              <th>Evaluación</th>
              <th class="text-center">Porcentaje</th>
              <th class="text-center">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, index) in criterios" :key="index">
              <td>{{ item.nombre }}</td>
              <td class="text-center">
                <input v-model="item.porcentaje" type="number" min="0" max="100" class="porcentaje-input"> %
              </td>
              <td class="text-center actions">
                <!-- Botón Guardar por fila con icono bookmark -->
                <button @click="guardarFila(item)" class="btn-guardar-fila">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="icon-btn">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0Z" />
                  </svg>
                </button>
                
                <button @click="editar(item)" class="btn-edit">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                  </svg>
                </button>

                <button @click="eliminar(index)" class="btn-delete">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                  </svg>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Gráfico circular + Guardar global -->
      <div class="bottom-bar">
        <div class="circular-wrapper">
          <div class="circular-progress">
            <svg viewBox="0 0 36 36">
              <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#E5E7EB" stroke-width="4"/>
              <path :d="circlePath" fill="none" stroke="#005187" stroke-width="4" stroke-dasharray="100, 100"/>
            </svg>
            <div class="percent-text">{{ totalPorcentaje }}%</div>
          </div>
        </div>
        
        <!-- Botón Guardar Todos con icono bookmark -->
        <button @click="guardarCambios" :disabled="totalPorcentaje !== 100" class="btn-guardar">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="icon-btn">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0Z" />
          </svg>
          Guardar Todos los Cambios
        </button>
      </div>

      <footer class="footer">© 2026 Tecnológico Nacional de México | Todos los derechos reservados.</footer>
    </div>
  </MainLayout>

  <!-- Modal Nueva Evaluación -->
  <div v-if="showModal" class="modal-overlay" @click.self="cerrarModal">
    <div class="modal-content">
      <div class="modal-header">
        <h3>Nueva Evaluación</h3>
        <button @click="cerrarModal" class="close-btn">✕</button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label>Nombre de la evaluación</label>
          <input v-model="nuevoNombre" type="text" placeholder="Ej: Examen Final" class="modal-input" @keyup.enter="guardarNuevaEvaluacion">
        </div>
        <div class="form-group">
          <label>Porcentaje inicial (%)</label>
          <input v-model="nuevoPorcentaje" type="number" min="0" max="100" placeholder="0" class="modal-input" @keyup.enter="guardarNuevaEvaluacion">
        </div>
      </div>
      <div class="modal-footer">
        <button @click="cerrarModal" class="btn-cancelar">Cancelar</button>
        <button @click="guardarNuevaEvaluacion" class="btn-guardar-modal" :disabled="!nuevoNombre.trim()">Guardar Evaluación</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import MainLayout from '@/layouts/MainLayout.vue'

const busquedaGlobal = ref('')

const criterios = ref([
  { nombre: 'Parcial 1', porcentaje: 30 },
  { nombre: 'Parcial 2', porcentaje: 30 },
  { nombre: 'Proyecto', porcentaje: 40 }
])

const totalPorcentaje = computed(() => criterios.value.reduce((sum, c) => sum + Number(c.porcentaje), 0))
const circlePath = computed(() => `M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831`)

// Filtros
const filtroPeriodo = ref('Ago/Dic 2024')
const filtroMateria = ref('Algoritmos y Programación')

// Modal
const showModal = ref(false)
const nuevoNombre = ref('')
const nuevoPorcentaje = ref(0)

const abrirModalNueva = () => {
  nuevoNombre.value = ''
  nuevoPorcentaje.value = 0
  showModal.value = true
}
const cerrarModal = () => { showModal.value = false }

const guardarNuevaEvaluacion = () => {
  if (!nuevoNombre.value.trim()) return alert('Debes escribir un nombre')
  criterios.value.push({ nombre: nuevoNombre.value.trim(), porcentaje: Number(nuevoPorcentaje.value) || 0 })
  cerrarModal()
}

// Acciones por fila
const guardarFila = (item) => {
  console.log('💾 Guardando fila:', item)
  alert(`✅ Porcentaje de ${item.nombre} guardado`)
}

const editar = (item) => alert(`Editando: ${item.nombre}`)
const eliminar = (index) => {
  if (confirm('¿Eliminar esta evaluación?')) criterios.value.splice(index, 1)
}

const guardarCambios = () => {
  console.log('💾 Guardando todos los cambios...')
  alert('✅ Evaluaciones guardadas correctamente')
}

const buscar = () => console.log('🔎 Buscando...')
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap');

.evaluaciones-page { width: 100%; background: #F5F7FA; }
.page-title { color: #005187; font-size: 2.1rem; font-weight: 700; margin-bottom: 0.4rem; }
.breadcrumb { color: #5A5A5A; margin-bottom: 1rem; font-size: 0.95rem; }

.subject-card { background: #FFFFFF; padding: 1.8rem; border-radius: 12px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; box-shadow: 0 8px 25px rgba(0,0,0,0.07); border: 1px solid #E5E7EB; }
.btn-nueva-eval { background: #005187; color: white; padding: 12px 24px; border-radius: 8px; font-weight: 600; display: flex; align-items: center; gap: 8px; }

.filters-card { background: #FFFFFF; padding: 1.4rem; border-radius: 12px; display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 2rem; box-shadow: 0 8px 25px rgba(0,0,0,0.07); border: 1px solid #E5E7EB; }
.filter-select { padding: 12px 16px; border: 1px solid #E5E7EB; border-radius: 8px; min-width: 180px; }
.btn-buscar { background: #005187; color: white; padding: 12px 28px; border-radius: 8px; font-weight: 600; display: flex; align-items: center; gap: 8px; }

.table-container { background: #FFFFFF; border-radius: 12px; overflow: hidden; box-shadow: 0 8px 25px rgba(0,0,0,0.07); border: 1px solid #E5E7EB; }
.eval-table { width: 100%; border-collapse: collapse; }
.eval-table th { background: #F8FAFC; padding: 18px 16px; font-weight: 600; }
.eval-table td { padding: 18px 16px; border-bottom: 1px solid #E5E9F0; }
.porcentaje-input { width: 100px; text-align: center; padding: 10px; border: 1px solid #84B6E4; border-radius: 8px; }

.actions { display: flex; gap: 8px; }
.btn-guardar-fila, .btn-edit, .btn-delete { 
  width: 38px; 
  height: 38px; 
  border: none; 
  border-radius: 8px; 
  display: flex; 
  align-items: center; 
  justify-content: center; 
  cursor: pointer; 
}
.btn-guardar-fila { background: #005187; color: white; }
.btn-edit { background: #4D82BE; color: white; }
.btn-delete { background: #D32F2F; color: white; }

.bottom-bar { display: flex; justify-content: space-between; align-items: center; margin-top: 2rem; }
.circular-wrapper { width: 180px; height: 180px; position: relative; }
.circular-progress svg { transform: rotate(-90deg); }
.percent-text { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 2.8rem; font-weight: 700; color: #005187; }

.btn-guardar { background: #005187; color: white; padding: 14px 40px; border-radius: 8px; font-weight: 600; font-size: 1.05rem; display: flex; align-items: center; gap: 8px; }

.footer { margin-top: 3rem; text-align: center; color: #9AA3AF; font-size: 0.9rem; }

/* Modal */
.modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.7); display: flex; align-items: center; justify-content: center; z-index: 2000; }
.modal-content { background: white; width: 480px; border-radius: 16px; overflow: hidden; box-shadow: 0 20px 50px rgba(0,0,0,0.3); }
.modal-header { background: #005187; color: white; padding: 1.25rem 1.8rem; display: flex; justify-content: space-between; align-items: center; }
.modal-header h3 { margin: 0; font-size: 1.45rem; font-weight: 700; }
.close-btn { background: none; border: none; color: white; font-size: 1.8rem; cursor: pointer; }
.modal-body { padding: 2rem 1.8rem; }
.form-group { margin-bottom: 1.6rem; }
.form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #1A1A1A; }
.modal-input { width: 100%; padding: 14px 16px; border: 2px solid #E5E9F0; border-radius: 10px; font-size: 1.05rem; }
.modal-footer { padding: 1.2rem 1.8rem; background: #F8FAFC; display: flex; gap: 12px; justify-content: flex-end; }
.btn-cancelar { background: #9AA3AF; color: white; padding: 12px 28px; border-radius: 10px; font-weight: 600; }
.btn-guardar-modal { background: #005187; color: white; padding: 12px 36px; border-radius: 10px; font-weight: 600; }

/* Tamaño de los iconos SVG */
.icon-btn {
  width: 20px;
  height: 20px;
}
</style>