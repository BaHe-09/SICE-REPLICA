<template>
  <div class="sistema-layout" :class="{ 'sidebar-collapsed': isCollapsed }">
  
    <header class="top-header">
      <div class="header-left">
        <button class="toggle-sidebar-btn" @click="toggleSidebar" title="Ocultar/Mostrar menú">
          <svg xmlns="http://www.w3.org/2000/svg" class="toggle-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" 
                  :d="isCollapsed ? 'M15 19l-7-7 7-7' : 'M9 5l7 7-7 7'" />
          </svg>
        </button>

        <img src="/logo-tecnm.png" alt="Logo TecNM" class="header-logo">
        
        <span class="system-title">TECNOLÓGICO NACIONAL DE MÉXICO</span>
      </div>

      <div class="header-right">
        <div class="search-group">
          <input 
            type="text" 
            placeholder="Buscar..." 
            v-model="busquedaGlobal"
          >
          <svg xmlns="http://www.w3.org/2000/svg" class="search-icon-svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 01-14 0 7 7 0 0114 0z" />
          </svg>
        </div>

        <div class="notification-bell" @click="toggleNotifications">
          <svg xmlns="http://www.w3.org/2000/svg" class="bell-svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 00-9-5.197V8.5m.002 3.5L12 17m-5-5v-.5a6 6 0 0112 0v.5m-12 0v.5" />
          </svg>
          

          <span v-if="notifications.length > 0" class="notification-badge">
            {{ notifications.length }}
          </span>


          <div v-if="showNotifications" class="notifications-dropdown" @click.stop>
            <div class="notifications-header">
              <h4>Notificaciones</h4>
              <span v-if="notifications.length > 0" class="mark-all" @click="markAllAsRead">Marcar todo como leído</span>
            </div>

            <div class="notifications-list">

              <div v-if="notifications.length > 0" class="notification-item" 
                   v-for="(notif, index) in notifications" :key="index">
                <svg xmlns="http://www.w3.org/2000/svg" class="notification-icon-svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2 2 2 0 01-2 2v-2m14-2V9a2 2 0 00-2-2M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2" />
                </svg>
                <div class="notification-content">
                  <p><strong>{{ notif.title }}</strong></p>
                  <p class="notification-time">{{ notif.subtitle }} • {{ notif.time }}</p>
                </div>
              </div>


              <div v-else class="empty-notifications">
                <svg xmlns="http://www.w3.org/2000/svg" class="empty-icon-svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 00-9-5.197V8.5m.002 3.5L12 17m-5-5v-.5a6 6 0 0112 0v.5m-12 0v.5" />
                </svg>
                <p class="empty-title">No tienes notificaciones</p>
                <p class="empty-subtitle">Cuando ocurra algo importante aparecerá aquí</p>
              </div>
            </div>

            <div v-if="notifications.length > 0" class="notifications-footer">
              Ver todas las notificaciones
            </div>
          </div>
        </div>

        <div class="user-menu" @click="toggleUserDropdown">
          <svg xmlns="http://www.w3.org/2000/svg" class="user-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7" />
          </svg>
          <span class="user-name">{{ currentRoleName }}</span>
          <span class="dropdown-arrow">▼</span>

          <div v-if="showUserDropdown" class="user-dropdown">
            <div class="dropdown-item" @click="setRole('servicios-escolares')">Servicios Escolares</div>
            <div class="dropdown-item" @click="setRole('admin')">Administrador</div>
          </div>
        </div>
      </div>
    </header> 

    <aside class="sidebar">
      <nav class="menu">

        <router-link to="/dashboard" class="menu-item" active-class="active">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon-svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1v-5m10-10l2 2m-2-2v10a1 1 0 01-1 1v-5m-6 0a1 1 0 001-1v5" /></svg>
          Dashboard
        </router-link>

        <div class="menu-item parent" @click="toggleServicios">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon-svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m-3-9h6m-6 6h6" /></svg>
          Servicios Escolares
          <span class="submenu-arrow" :class="{ open: isServiciosOpen }">›</span>
        </div>

        <div v-if="isServiciosOpen" class="submenu">
          <router-link to="/servicios-escolares" class="menu-item submenu-item" active-class="active" :key="'servicios-escolares'">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon-svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1v-5m10-10l2 2m-2-2v10a1 1 0 01-1 1v-5m-6 0a1 1 0 001-1v5" /></svg>
            Principal
          </router-link>

          <router-link to="/alumnos" class="menu-item submenu-item" active-class="active" :key="'alumnos'">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon-svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m-3-9h6m-6 6h6" /></svg>
            Alumnos
          </router-link>
          <router-link to="/evaluaciones" class="menu-item submenu-item" active-class="active" :key="'evaluaciones'">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon-svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2 2 2 0 01-2 2v-2m14-2V9a2 2 0 00-2-2M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2" /></svg>
            Evaluaciones
          </router-link>
          <router-link to="/calificaciones" class="menu-item submenu-item" active-class="active" :key="'calificaciones'">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon-svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2 2 2 0 01-2-2 2 2 0 01-2-2 2 2 0 012-2z" /></svg>
            Calificaciones
          </router-link>
          <router-link to="/inscripcion" class="menu-item submenu-item" active-class="active" :key="'inscripcion'">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon-svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14v6m-3-3h6M6 10h2a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2z" /></svg>
            Inscripción
          </router-link>
          <router-link to="/gestion-grupos" class="menu-item submenu-item" active-class="active" :key="'gestion-grupos'">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon-svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 01-3-3V8a3 3 0 01-3-3V3a3 3 0 01-3-3H8a3 3 0 01-3 3v2a3 3 0 01-3 3v7a3 3 0 01-3 3v2h5m5-10v10" /></svg>
            Gestión de Grupos
          </router-link>
        </div>

        <router-link to="/gestion-academica" class="menu-item" active-class="active">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon-svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2" /></svg>
          Gestión Académica
        </router-link>

        <router-link to="/eventos" class="menu-item" active-class="active">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon-svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2" /></svg>
          Eventos
        </router-link>

        <template v-if="currentRole === 'admin'">
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
        </template>

      </nav>
    </aside>

    <main class="main-content">
      <slot :busquedaGlobal="busquedaGlobal" />
    </main>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const busquedaGlobal = ref('')
const isCollapsed = ref(false)
const isServiciosOpen = ref(true)
const showUserDropdown = ref(false)
const showNotifications = ref(false)


const notifications = ref([])

const currentRole = ref('servicios-escolares')

const currentRoleName = computed(() => 
  currentRole.value === 'admin' ? 'Administrador' : 'Servicios Escolares'
)

const toggleSidebar = () => { isCollapsed.value = !isCollapsed.value }
const toggleServicios = () => { isServiciosOpen.value = !isServiciosOpen.value }

const toggleUserDropdown = () => {
  showUserDropdown.value = !showUserDropdown.value
  showNotifications.value = false
}

const toggleNotifications = () => {
  showNotifications.value = !showNotifications.value
  showUserDropdown.value = false
}

const markAllAsRead = () => {

  notifications.value = []
  showNotifications.value = false
}

const setRole = (role) => {
  currentRole.value = role
  showUserDropdown.value = false
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap');

.sistema-layout { 
  font-family: 'Montserrat', sans-serif; 
  display: flex; 
  min-height: 100vh; 
  background: #F5F5F5; 
  transition: all 0.3s ease; 
}

.top-header { 
  background: #1B396A; 
  padding: 0.9rem 2.5rem; 
  position: fixed; 
  top: 0; 
  left: 0; 
  right: 0; 
  height: 74px; 
  display: flex; 
  align-items: center; 
  justify-content: space-between; 
  z-index: 1000; 
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
  backdrop-filter: blur(8px);
}

.header-left { 
  display: flex; 
  align-items: center; 
  gap: 1rem; 
}

.header-logo { 
  height: 58px; 
  filter: drop-shadow(0 0 10px rgba(255,255,255,0.95)); 
}

.system-title { 
  font-size: 1.28rem; 
  font-weight: 700; 
  color: white; 
  letter-spacing: -0.02em;
}

.toggle-sidebar-btn { 
  background: none; 
  border: none; 
  color: white; 
  width: 40px; 
  height: 40px; 
  display: flex; 
  align-items: center; 
  justify-content: center; 
  cursor: pointer; 
  border-radius: 6px; 
}
.toggle-sidebar-btn:hover { background: rgba(255,255,255,0.15); }
.toggle-icon { width: 24px; height: 24px; stroke: white; }

.header-right { 
  display: flex; 
  align-items: center; 
  gap: 4rem; 
  height: 100%; 
}

.search-group { 
  position: relative; 
  width: 290px; 
}
.search-group input { 
  width: 100%; 
  padding: 11px 15px 11px 48px; 
  border: none; 
  border-radius: 30px; 
  background: rgba(255,255,255,0.25);
  color: white; 
  font-size: 1rem; 
  height: 32px; 
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.search-icon-svg { 
  position: absolute; 
  left: 18px; 
  top: 50%; 
  transform: translateY(-50%); 
  width: 20px; 
  height: 20px; 
  stroke: white; 
}

.notification-bell { 
  position: relative; 
  cursor: pointer; 
  display: flex; 
  align-items: center; 
  justify-content: center;
  width: 32px;
  height: 32px;
}
.bell-svg { 
  width: 26px; 
  height: 26px; 
  stroke: white; 
  stroke-width: 2; 
}

.notification-badge {
  position: absolute;
  top: -4px;
  right: -4px;
  background: #EF4444;
  color: white;
  font-size: 0.75rem;
  font-weight: 700;
  min-width: 18px;
  height: 18px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 2px 6px rgba(0,0,0,0.2);
  padding: 0 5px;
}


.notifications-dropdown {
  position: absolute;
  top: 52px;
  right: -20px;
  width: 360px;
  background: #FFFFFF;
  border-radius: 12px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.18);
  overflow: hidden;
  z-index: 1100;
  border: 1px solid #E5E7EB;
}
.notifications-header {
  padding: 16px 20px;
  background: #F8FAFC;
  border-bottom: 1px solid #E5E7EB;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.notifications-header h4 {
  margin: 0;
  font-size: 1.1rem;
  font-weight: 600;
  color: #1A1A1A;
}
.mark-all {
  font-size: 0.85rem;
  color: #1B396A;
  cursor: pointer;
  font-weight: 500;
}
.notifications-list {
  max-height: 340px;
  overflow-y: auto;
}
.notification-item {
  display: flex;
  gap: 14px;
  padding: 16px 20px;
  border-bottom: 1px solid #F1F5F9;
  transition: background 0.2s;
}
.notification-item:hover {
  background: #F8FAFC;
}
.notification-icon-svg {
  width: 24px;
  height: 24px;
  stroke: #1B396A;
  flex-shrink: 0;
}
.notification-content p {
  margin: 0;
  line-height: 1.4;
}
.notification-content strong {
  color: #1A1A1A;
}
.notification-time {
  font-size: 0.82rem;
  color: #6B7280;
  margin-top: 4px;
}


.empty-notifications {
  padding: 60px 20px;
  text-align: center;
  color: #6B7280;
}
.empty-icon-svg {
  width: 64px;
  height: 64px;
  stroke: #6B7280;
  margin-bottom: 16px;
}
.empty-title {
  font-size: 1.1rem;
  font-weight: 600;
  margin-bottom: 4px;
}
.empty-subtitle {
  font-size: 0.9rem;
}

.notifications-footer {
  padding: 14px 20px;
  text-align: center;
  color: #1B396A;
  font-weight: 600;
  font-size: 0.95rem;
  border-top: 1px solid #E5E7EB;
  cursor: pointer;
}

.user-menu { 
  display: flex; 
  align-items: center; 
  gap: 8px; 
  color: white; 
  font-weight: 500; 
  cursor: pointer; 
  position: relative;
}
.user-icon {
  width: 26px;
  height: 26px;
  stroke: white;
}
.user-name { 
  font-size: 1.02rem; 
}
.dropdown-arrow { 
  font-size: 1.1rem; 
  opacity: 0.85; 
}

.user-dropdown {
  position: absolute;
  top: 100%;
  right: 0;
  background: #FFFFFF;
  color: #1A1A1A;
  border-radius: 8px;
  box-shadow: 0 8px 25px rgba(0,0,0,0.15);
  padding: 8px 0;
  min-width: 180px;
  z-index: 1100;
  margin-top: 8px;
}
.dropdown-item {
  padding: 10px 20px;
  cursor: pointer;
  color: #1A1A1A;
}
.dropdown-item:hover {
  background: #F5F5F5;
}

.sidebar { 
  width: 260px; 
  background: #D6D6D6; 
  position: fixed; 
  top: 74px; 
  bottom: 0; 
  left: 0; 
  padding-top: 1rem; 
  transition: width 0.4s ease; 
}
.sistema-layout.sidebar-collapsed .sidebar { width: 0; overflow: hidden; }

.menu-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 14px 24px;
  color: #1A1A1A;
  text-decoration: none;
  font-size: 0.98rem;
}
.menu-item:hover,
.menu-item.active { 
  background: #E5E7EB; 
  font-weight: 600; 
}
.icon-svg { 
  width: 20px; 
  height: 20px; 
  stroke: #6B7280; 
}

.parent { cursor: pointer; }
.submenu-arrow { margin-left: auto; transition: transform 0.3s ease; }
.submenu-arrow.open { transform: rotate(90deg); }

.submenu { 
  background: #E5E7EB; 
  padding-left: 24px; 
}
.submenu-item {
  padding: 12px 24px 12px 48px;
  font-size: 0.95rem;
  color: #1A1A1A;
}

.main-content { 
  margin-left: 260px; 
  margin-top: 74px; 
  padding: 2rem; 
  flex: 1; 
  transition: margin-left 0.4s ease; 
  background: #F5F5F5; 
}
.sistema-layout.sidebar-collapsed .main-content { margin-left: 0; }
</style>