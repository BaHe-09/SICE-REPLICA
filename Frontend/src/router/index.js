import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      redirect: '/servicios-escolares'
    },
    
    { path: '/login', name: 'Login', component: () => import('@/views/LoginView.vue') },
    
    {
      path: '/servicios-escolares',
      name: 'ServiciosEscolares',
      component: () => import('@/views/ServiciosEscolaresView.vue')
    },


    { path: '/alumnos', name: 'Alumnos', component: () => import('@/views/AlumnosView.vue') },
    { path: '/formulario-alumno', name: 'FormularioAlumno', component: () => import('@/views/FormularioAlumnoView.vue') },
    { path: '/evaluaciones', name: 'Evaluaciones', component: () => import('@/views/EvaluacionesView.vue') },
    { path: '/calificaciones', name: 'Calificaciones', component: () => import('@/views/CalificacionesView.vue') },
    { path: '/inscripcion', name: 'Inscripcion', component: () => import('@/views/InscripcionView.vue') },
    { path: '/gestion-grupos', name: 'GestionGrupos', component: () => import('@/views/GestionGruposView.vue') }
  ]
})

export default router