<template>
  <div class="sistema-layout">
  
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
        <div class="search-bar">
          <input 
            type="text" 
            placeholder="Buscar..." 
            v-model="busquedaGlobal"
          >
          <span class="search-icon">🔍</span>
        </div>
        <div class="notification-bell">
          <svg xmlns="http://www.w3.org/2000/svg" class="bell-svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 00-9-5.197V8.5m.002 3.5L12 17m-5-5v-.5a6 6 0 0112 0v.5m-12 0v.5" />
          </svg>
        </div>
        <div class="user-menu">
          <span class="user-name">Administrador</span>
          <span class="dropdown-arrow">▼</span>
        </div>
      </div>
    </header>

    
    <aside class="sidebar">
      <nav class="menu">
        <router-link to="/dashboard" class="menu-item" active-class="active">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon-svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1v-5m10-10l2 2m-2-2v10a1 1 0 01-1 1v-5m-6 0a1 1 0 001-1v5" /></svg>
          Dashboard
        </router-link>

        <router-link to="/seguridad" class="menu-item" active-class="active">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon-svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V9a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m-4 4h12" /></svg>
          Seguridad
        </router-link>

        <router-link to="/personas" class="menu-item" active-class="active">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon-svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 01-3-3V8a3 3 0 01-3-3V3a3 3 0 01-3-3H8a3 3 0 01-3 3v2a3 3 0 01-3 3v7a3 3 0 01-3 3v2h5m5-10v10" /></svg>
          Personas
        </router-link>

        <router-link to="/usuarios" class="menu-item" active-class="active">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon-svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7" /></svg>
          Usuarios
        </router-link>

        <router-link to="/recursos-humanos" class="menu-item" active-class="active">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon-svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 01-2-2H7a2 2 0 01-2 2v16m14 0h-4m-6 0H5" /></svg>
          Recursos Humanos
        </router-link>

        <router-link to="/alumnos" class="menu-item active" active-class="active">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon-svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m-3-9h6m-6 6h6" /></svg>
          Servicios Escolares
        </router-link>

        <router-link to="/gestion-academica" class="menu-item" active-class="active">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon-svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2" /></svg>
          Gestión Académica
        </router-link>

        <router-link to="/eventos" class="menu-item" active-class="active">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon-svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2" /></svg>
          Eventos
        </router-link>

        <router-link to="/comite-academico" class="menu-item" active-class="active">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon-svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14v6m-3-3h6M6 10h2a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2z" /></svg>
          Comité Académico
        </router-link>
      </nav>
    </aside>

    
    <main class="main-content">
      <slot :busquedaGlobal="busquedaGlobal" />
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

.header-right { display: flex; align-items: center; gap: 2.8rem; }
.search-bar { position: relative; width: 310px; }
.search-bar input { width: 100%; padding: 11px 15px 11px 45px; border: none; border-radius: 30px; background: rgba(255,255,255,0.18); color: white; }
.search-icon { position: absolute; left: 18px; top: 50%; transform: translateY(-50%); color: white; }
.notification-bell { font-size: 1.6rem; color: white; cursor: pointer; }
.user-menu { display: flex; align-items: center; gap: 8px; color: white; font-weight: 500; cursor: pointer; }

.sidebar { width: 260px; background: #1B396A; position: fixed; top: 74px; bottom: 0; left: 0; padding-top: 1rem; }
.menu-item { 
  display: flex; 
  align-items: center; 
  gap: 12px; 
  padding: 14px 24px; 
  color: white; 
  text-decoration: none; 
  font-size: 0.98rem;
}
.menu-item.active { background: rgba(255,255,255,0.12); font-weight: 600; }

.icon-svg { width: 20px; height: 20px; stroke: currentColor; }

.main-content { margin-left: 260px; margin-top: 74px; padding: 2rem; flex: 1; }
</style>