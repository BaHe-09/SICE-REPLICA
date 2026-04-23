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

    <!-- SIDEBAR INSTITUCIONAL -->
    <aside class="sidebar">
      <nav class="menu">
        <router-link to="/" class="menu-item"><span class="icon">📊</span> Dashboard</router-link>
        <router-link to="#" class="menu-item"><span class="icon">🔒</span> Seguridad</router-link>
        <router-link to="#" class="menu-item"><span class="icon">👥</span> Personas</router-link>
        <router-link to="#" class="menu-item"><span class="icon">👤</span> Usuarios</router-link>
        <router-link to="#" class="menu-item"><span class="icon">🏢</span> Recursos Humanos</router-link>
        <router-link to="/alumnos" class="menu-item"><span class="icon">📚</span> Servicios Escolares</router-link>
        <router-link to="/gestion-grupos" class="menu-item"><span class="icon">🎓</span> Gestión Académica</router-link>
        <router-link to="/inscripcion" class="menu-item"><span class="icon">📝</span> Inscripción</router-link>
        <router-link to="#" class="menu-item"><span class="icon">📅</span> Eventos</router-link>
        <router-link to="#" class="menu-item"><span class="icon">👥</span> Comité Académico</router-link>
      </nav>
    </aside>

    <!-- CONTENIDO PRINCIPAL (aquí va cada vista) -->
    <main class="main-content">
      <slot /> <!-- ← Todo el contenido de AlumnosView, InscripcionView, etc. irá aquí -->
      <footer class="footer">
        © 2026 Tecnológico Nacional de México | Todos los derechos reservados.
      </footer>
    </main>
  </div>
</template>

<script setup>
import { ref } from 'vue'
const busquedaGlobal = ref('')
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
.menu-item.router-link-active { background: rgba(255,255,255,0.12); } /* ← Active automático */

.main-content { margin-left: 260px; margin-top: 74px; padding: 2rem; flex: 1; }
.footer { margin-top: 3rem; text-align: center; color: #9AA3AF; font-size: 0.9rem; }
</style>