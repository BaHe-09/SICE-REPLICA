<!-- ============================================= -->
<!-- src/views/EvaluacionesView.vue -->
<!-- VERSIÓN FINAL - CON MODAL MEJORADO -->
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
      <!-- Breadcrumb -->
      <div class="breadcrumb">Servicios Escolares &gt; Grupos &gt; Evaluaciones</div>

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

      <!-- TABLA DISTRIBUIDA EN TODO EL PANEL -->
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
              <td class="text-center">
                <button @click="editar(item)" class="btn-edit">✏️</button>
                <button @click="eliminar(index)" class="btn-delete">🗑️</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Gráfico circular y botón guardar -->
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
        <button @click="guardarCambios" :disabled="totalPorcentaje !== 100" class="btn-guardar">
          Guardar Cambios
        </button>
      </div>

      <footer class="footer">© 2026 Tecnológico Nacional de México | Todos los derechos reservados.</footer>
    </main>

    <!-- MODAL MEJORADO - Nueva Evaluación -->
    <div v-if="showModal" class="modal-overlay" @click.self="cerrarModal">
      <div class="modal-content">
        <div class="modal-header">
          <h3>➕ Nueva Evaluación</h3>
          <button @click="cerrarModal" class="close-btn">✕</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>
              <span class="label-icon">📝</span>
              Nombre de la evaluación
            </label>
            <input 
              v-model="nuevoNombre" 
              type="text" 
              placeholder="Ej: Examen Final, Práctica, Proyecto..." 
              class="modal-input" 
              @keyup.enter="guardarNuevaEvaluacion"
              autofocus
            >
          </div>
          <div class="form-group">
            <label>
              <span class="label-icon">📊</span>
              Porcentaje inicial (%)
            </label>
            <div class="percentage-input-wrapper">
              <input 
                v-model="nuevoPorcentaje" 
                type="number" 
                min="0" 
                max="100" 
                step="1"
                placeholder="0" 
                class="modal-input percentage-input" 
                @keyup.enter="guardarNuevaEvaluacion"
              >
              <span class="percentage-symbol">%</span>
            </div>
            <div class="percentage-hint">
              Valor recomendado: 20-40% (Total actual: {{ totalPorcentaje }}%)
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button @click="cerrarModal" class="btn-cancelar">
            <span>✕</span> Cancelar
          </button>
          <button 
            @click="guardarNuevaEvaluacion" 
            class="btn-guardar-modal" 
            :disabled="!nuevoNombre.trim() || nuevoPorcentaje < 0 || nuevoPorcentaje > 100"
          >
            <span>💾</span> Guardar Evaluación
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const busquedaGlobal = ref('')

const criterios = ref([
  { nombre: 'Parcial 1', porcentaje: 30 },
  { nombre: 'Parcial 2', porcentaje: 30 },
  { nombre: 'Proyecto', porcentaje: 40 }
])

const totalPorcentaje = computed(() => criterios.value.reduce((sum, c) => sum + Number(c.porcentaje), 0))
const circlePath = computed(() => `M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831`)

// MODAL
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
  if (!nuevoNombre.value.trim()) {
    alert('❌ Debes escribir un nombre para la evaluación')
    return
  }
  if (nuevoPorcentaje.value < 0 || nuevoPorcentaje.value > 100) {
    alert('❌ El porcentaje debe estar entre 0 y 100')
    return
  }
  if (totalPorcentaje.value + Number(nuevoPorcentaje.value) > 100) {
    alert(`⚠️ El porcentaje total excedería 100% (Actual: ${totalPorcentaje.value}%)`)
    return
  }
  criterios.value.push({ 
    nombre: nuevoNombre.value.trim(), 
    porcentaje: Number(nuevoPorcentaje.value) || 0 
  })
  cerrarModal()
}

const editar = (item) => alert(`Editando: ${item.nombre}`)
const eliminar = (index) => {
  if (confirm('¿Eliminar esta evaluación?')) criterios.value.splice(index, 1)
}
const guardarCambios = () => alert('✅ Evaluaciones guardadas correctamente')
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
.breadcrumb { color: #5A5A5A; margin-bottom: 1rem; font-size: 0.95rem; }

/* TABLA DISTRIBUIDA EN TODO EL PANEL */
.table-container {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 8px 25px rgba(0,0,0,0.07);
  width: 100%;
  margin-bottom: 2rem;
}
.eval-table {
  width: 100%;
  border-collapse: collapse;
  table-layout: fixed;
}
.eval-table th {
  background: #F8FAFC;
  padding: 18px 16px;
  font-weight: 600;
  text-align: left;
}
.eval-table td {
  padding: 18px 16px;
  border-bottom: 1px solid #E5E9F0;
}
.porcentaje-input {
  width: 100px;
  text-align: center;
  padding: 10px;
  border: 1px solid #84B6E4;
  border-radius: 8px;
  font-size: 1rem;
}
.btn-edit { background: #4D82BE; color: white; width: 38px; height: 38px; border: none; border-radius: 8px; margin: 0 6px; font-size: 1.1rem; cursor: pointer; }
.btn-edit:hover { background: #3A6A9E; }
.btn-delete { background: #D32F2F; color: white; width: 38px; height: 38px; border: none; border-radius: 8px; font-size: 1.1rem; cursor: pointer; }
.btn-delete:hover { background: #B71C1C; }

/* Resto de estilos */
.subject-card { background: white; padding: 1.8rem; border-radius: 12px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; box-shadow: 0 8px 25px rgba(0,0,0,0.07); }
.btn-nueva-eval { background: #005187; color: white; padding: 12px 24px; border-radius: 8px; font-weight: 600; display: flex; align-items: center; gap: 8px; cursor: pointer; border: none; }
.btn-nueva-eval:hover { background: #003F6F; }
.bottom-bar { display: flex; justify-content: space-between; align-items: center; margin-top: 2rem; }
.circular-wrapper { width: 180px; height: 180px; position: relative; }
.circular-progress svg { transform: rotate(-90deg); }
.percent-text { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 2.8rem; font-weight: 700; color: #005187; }
.btn-guardar { background: #005187; color: white; padding: 14px 40px; border-radius: 8px; font-weight: 600; font-size: 1.05rem; cursor: pointer; border: none; }
.btn-guardar:hover:not(:disabled) { background: #003F6F; }
.btn-guardar:disabled { background: #9AA3AF; cursor: not-allowed; }
.footer { margin-top: 3rem; text-align: center; color: #9AA3AF; font-size: 0.9rem; }

/* ===== MODAL MEJORADO ===== */
.modal-overlay { 
  position: fixed; 
  top: 0; 
  left: 0; 
  right: 0; 
  bottom: 0; 
  background: rgba(0,0,0,0.7); 
  display: flex; 
  align-items: center; 
  justify-content: center; 
  z-index: 2000;
  animation: fadeIn 0.2s ease-out;
}

.modal-content { 
  background: white; 
  width: 520px; 
  max-width: 90%;
  border-radius: 20px; 
  overflow: hidden; 
  box-shadow: 0 25px 60px rgba(0,0,0,0.3);
  animation: slideIn 0.3s ease-out;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes slideIn {
  from { transform: translateY(-30px); opacity: 0; }
  to { transform: translateY(0); opacity: 1; }
}

.modal-header { 
  background: #005187; 
  color: white; 
  padding: 1.25rem 1.8rem; 
  display: flex; 
  justify-content: space-between; 
  align-items: center; 
}
.modal-header h3 { 
  margin: 0; 
  font-size: 1.45rem; 
  font-weight: 700; 
  display: flex;
  align-items: center;
  gap: 8px;
}
.close-btn { 
  background: none; 
  border: none; 
  color: white; 
  font-size: 1.8rem; 
  cursor: pointer;
  line-height: 1;
  padding: 0;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
}
.close-btn:hover { 
  background: rgba(255,255,255,0.2); 
}

.modal-body { 
  padding: 2rem 1.8rem; 
}
.form-group { 
  margin-bottom: 1.8rem; 
}
.form-group label { 
  display: block; 
  margin-bottom: 10px; 
  font-weight: 600; 
  color: #1A1A1A;
  font-size: 0.95rem;
}
.label-icon {
  margin-right: 8px;
  font-size: 1.1rem;
}
.modal-input { 
  width: 100%; 
  padding: 14px 16px; 
  border: 2px solid #E5E9F0; 
  border-radius: 12px; 
  font-size: 1rem;
  font-family: 'Montserrat', sans-serif;
  transition: border-color 0.2s, box-shadow 0.2s;
}
.modal-input:focus {
  outline: none;
  border-color: #005187;
  box-shadow: 0 0 0 4px rgba(0,81,135,0.1);
}
.modal-input::placeholder {
  color: #9AA3AF;
  font-size: 0.95rem;
}

.percentage-input-wrapper {
  position: relative;
}
.percentage-input {
  padding-right: 50px;
}
.percentage-symbol {
  position: absolute;
  right: 16px;
  top: 50%;
  transform: translateY(-50%);
  color: #005187;
  font-weight: 700;
  font-size: 1.2rem;
  pointer-events: none;
}

.percentage-hint {
  margin-top: 8px;
  font-size: 0.85rem;
  color: #6B7280;
  padding-left: 4px;
}

.modal-footer { 
  padding: 1.2rem 1.8rem; 
  background: #F8FAFC; 
  display: flex; 
  gap: 12px; 
  justify-content: flex-end; 
  border-top: 1px solid #E5E9F0;
}
.btn-cancelar, .btn-guardar-modal {
  padding: 12px 28px;
  border-radius: 10px;
  font-weight: 600;
  font-size: 0.95rem;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 8px;
  transition: all 0.2s;
  border: none;
}
.btn-cancelar { 
  background: #9AA3AF; 
  color: white; 
}
.btn-cancelar:hover { 
  background: #7F8A98; 
}
.btn-guardar-modal { 
  background: #005187; 
  color: white; 
}
.btn-guardar-modal:hover:not(:disabled) { 
  background: #003F6F; 
}
.btn-guardar-modal:disabled {
  background: #9AA3AF;
  cursor: not-allowed;
  opacity: 0.6;
}

/* Responsive */
@media (max-width: 768px) {
  .modal-content {
    width: 90%;
    margin: 1rem;
  }
  .modal-body {
    padding: 1.5rem;
  }
  .modal-footer {
    flex-direction: column-reverse;
  }
  .btn-cancelar, .btn-guardar-modal {
    justify-content: center;
  }
}
</style>