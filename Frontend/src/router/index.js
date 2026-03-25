import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      redirect: '/dashboard'
    },

    { path: '/login', name: 'Login', component: () => import('@/views/LoginView.vue') },

    {
      path: '/dashboard',
      name: 'Dashboard',
      component: () => import('@/views/DashboardView.vue')
    },

    {
      path: '/servicios-escolares',
      name: 'ServiciosEscolares',
      component: () => import('@/views/ServiciosEscolaresView.vue')
    },

    { path: '/alumnos', name: 'Alumnos', component: () => import('@/views/AlumnosView.vue') },
    { path: '/formulario-alumno', name: 'FormularioAlumno', component: () => import('@/views/FormularioAlumnoView.vue') },


    { 
      path: '/evaluaciones', 
      name: 'EvaluacionesGeneral', 
      component: () => import('@/views/EvaluacionesView.vue') 
    },
    { 
      path: '/evaluaciones/:id', 
      name: 'Evaluaciones', 
      component: () => import('@/views/EvaluacionesView.vue') 
    },

    { 
      path: '/calificaciones', 
      name: 'CalificacionesGeneral', 
      component: () => import('@/views/CalificacionesView.vue') 
    },
    { 
      path: '/calificaciones/:id', 
      name: 'Calificaciones', 
      component: () => import('@/views/CalificacionesView.vue') 
    },

    { path: '/inscripcion', name: 'Inscripcion', component: () => import('@/views/InscripcionView.vue') },
    { path: '/gestion-grupos', name: 'GestionGrupos', component: () => import('@/views/GestionGruposView.vue') },
    { path: '/gestion-academica', name: 'GestionAcademica', component: () => import('@/views/GestionGruposView.vue') }
  ]
})

export default router