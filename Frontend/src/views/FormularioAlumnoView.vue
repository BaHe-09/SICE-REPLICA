<template>
  <div class="sistema-layout">
    <!-- HEADER -->
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
        <a href="#" class="menu-item"><span class="icon">🎓</span> Gestión Académica</a>
        <a href="#" class="menu-item"><span class="icon">📅</span> Eventos</a>
        <a href="#" class="menu-item"><span class="icon">👥</span> Comité Académico</a>
      </nav>
    </aside>

    <!-- CONTENIDO PRINCIPAL -->
    <main class="main-content">
      <!-- BREADCRUMB -->
      <div class="breadcrumb">
        Servicios Escolares <span class="arrow">›</span> Formulario Alumno
      </div>

      <h1 class="page-title">Formulario Alumno</h1>
      <p class="subtitle">Complete la información del alumno para registrarlo en el sistema.</p>

      <div class="form-card">
        <!-- SECCIÓN 1: Información Personal -->
        <div class="section">
          <h2 class="section-title">Información Personal</h2>
          <div class="row">
            <div class="field">
              <label>Nombre</label>
              <input type="text" v-model="form.nombre" placeholder="Nombre(s)" required>
            </div>
            <div class="field">
              <label>Apellido Paterno</label>
              <input type="text" v-model="form.apellidoPaterno" required>
            </div>
            <div class="field">
              <label>Apellido Materno</label>
              <input type="text" v-model="form.apellidoMaterno">
            </div>
          </div>
          <div class="field">
            <label>Género</label>
            <select v-model="form.genero" required>
              <option value="">Seleccionar</option>
              <option value="Masculino">Masculino</option>
              <option value="Femenino">Femenino</option>
              <option value="Otro">Otro</option>
            </select>
          </div>
        </div>

        <!-- SECCIÓN 2: Datos Académicos -->
        <div class="section">
          <h2 class="section-title">Datos Académicos</h2>
          <div class="row">
            <div class="field">
              <label>Número de Control</label>
              <input type="text" v-model="form.noControl" maxlength="8" required>
            </div>
            <div class="field">
              <label>Carrera</label>
              <select v-model="form.carrera" required>
                <option value="">Seleccionar carrera</option>
                <option value="Ingeniería en Sistemas Computacionales">Ingeniería en Sistemas Computacionales</option>
                <option value="Ingeniería Industrial">Ingeniería Industrial</option>
                <option value="Ingeniería Civil">Ingeniería Civil</option>
                <option value="Ingeniería Mecatrónica">Ingeniería Mecatrónica</option>
                <option value="Ingeniería Electrónica">Ingeniería Electrónica</option>
              </select>
            </div>
            <div class="field">
              <label>Semestre</label>
              <select v-model="form.semestre" required>
                <option value="">Seleccionar</option>
                <option v-for="n in 8" :key="n" :value="n">{{ n }}</option>
              </select>
            </div>
          </div>
        </div>

        <!-- SECCIÓN 3: Estado y Fecha de Ingreso -->
        <div class="section">
          <h2 class="section-title">Estado y Fecha de Ingreso</h2>
          <div class="row">
            <div class="field">
              <label>Estatus</label>
              <select v-model="form.estatus" required>
                <option value="Activo">Activo</option>
                <option value="Baja Temporal">Baja Temporal</option>
                <option value="Baja Definitiva">Baja Definitiva</option>
              </select>
            </div>
            <div class="field">
              <label>Fecha de Ingreso</label>
              <input type="date" v-model="form.fechaIngreso" required>
            </div>
          </div>
        </div>

        <!-- BOTONES -->
        <div class="form-actions">
          <button class="btn-cancel" @click="cancelar">Cancelar</button>
          <button class="btn-guardar" @click="guardarAlumno">Guardar</button>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const form = ref({
  nombre: '',
  apellidoPaterno: '',
  apellidoMaterno: '',
  genero: '',
  noControl: '',
  carrera: '',
  semestre: '',
  estatus: 'Activo',
  fechaIngreso: ''
})

const busquedaGlobal = ref('')

const guardarAlumno = () => {
  if (!form.value.nombre || !form.value.apellidoPaterno || !form.value.noControl) {
    alert('❌ Los campos obligatorios están incompletos')
    return
  }
  alert('✅ Alumno guardado correctamente (simulado)')
  console.log('Datos guardados:', form.value)
}

const cancelar = () => {
  if (confirm('¿Desea cancelar el registro?')) {
    window.location.href = '/alumnos'
  }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap');

.sistema-layout { font-family: 'Montserrat', sans-serif; display: flex; min-height: 100vh; background: #F5F7FA; }

/* HEADER Y SIDEBAR */
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

/* CONTENIDO */
.main-content { margin-left: 260px; margin-top: 74px; padding: 2rem; flex: 1; }
.breadcrumb { color: #4D82BE; margin-bottom: 0.5rem; font-size: 0.95rem; }
.page-title { color: #005187; font-size: 1.9rem; font-weight: 700; margin-bottom: 0.3rem; }
.subtitle { color: #5A5A5A; margin-bottom: 2rem; }

.form-card {
  background: white;
  border-radius: 16px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.08);
  padding: 2.5rem;
  max-width: 900px;
}

.section { margin-bottom: 2.5rem; }
.section-title { color: #005187; font-size: 1.35rem; font-weight: 600; margin-bottom: 1rem; border-bottom: 2px solid #E0E7FF; padding-bottom: 0.5rem; }

.row { display: flex; gap: 1.5rem; margin-bottom: 1.2rem; }
.field { flex: 1; }
.field label { display: block; margin-bottom: 6px; font-weight: 500; color: #1A1A1A; }
.field input, .field select {
  width: 100%;
  padding: 12px 14px;
  border: 1.5px solid #D1D9E6;
  border-radius: 10px;
  font-size: 1rem;
}
.field input:focus, .field select:focus {
  border-color: #005187;
  outline: none;
  box-shadow: 0 0 0 4px rgba(0,81,135,0.15);
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 2rem;
}

.btn-cancel {
  padding: 12px 28px;
  background: #F5F7FA;
  color: #1A1A1A;
  border: 1px solid #D1D9E6;
  border-radius: 10px;
  font-weight: 600;
  cursor: pointer;
}

.btn-guardar {
  padding: 12px 32px;
  background: #005187;
  color: white;
  border: none;
  border-radius: 10px;
  font-weight: 600;
  cursor: pointer;
}
</style>