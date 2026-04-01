import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [

    // ── Raíz ──────────────────────────────────────────────────────────────
    {
      path: '/',
      redirect: '/inicio'
    },

    // ── Autenticación ─────────────────────────────────────────────────────
    {
      path: '/login',
      name: 'Login',
      component: () => import('@/views/LoginView.vue')
    },

    // ── Dashboard principal ───────────────────────────────────────────────
    {
      path: '/inicio',
      name: 'Inicio',
      component: () => import('@/views/DashboardView.vue')
    },

    // ── Servicios Escolares ───────────────────────────────────────────────
    {
      path: '/servicios-escolares',
      name: 'ServiciosEscolares',
      component: () => import('@/views/ServiciosEscolaresView.vue')
    },

    // ── Alumnos ───────────────────────────────────────────────────────────
    {
      path: '/alumnos',
      name: 'Alumnos',
      component: () => import('@/views/AlumnosView.vue')
    },
    {
      path: '/formulario-alumno',
      name: 'FormularioAlumno',
      component: () => import('@/views/FormularioAlumnoView.vue')
    },
    {
      path: '/formulario-alumno/:id',
      name: 'EditarAlumno',
      component: () => import('@/views/FormularioAlumnoView.vue')
    },

    // ── Evaluaciones ──────────────────────────────────────────────────────
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

    // ── Calificaciones ────────────────────────────────────────────────────
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

    // ── Inscripción ───────────────────────────────────────────────────────
    {
      path: '/inscripcion',
      name: 'Inscripcion',
      component: () => import('@/views/InscripcionView.vue')
    },

    // ── Gestión de Grupos ─────────────────────────────────────────────────
    {
      path: '/gestion-grupos',
      name: 'GestionGrupos',
      component: () => import('@/views/GestionGruposView.vue')
    },

    // ── Gestión Académica (usa GestionGruposView, archivo existente) ───────
    {
      path: '/gestion-academica',
      name: 'GestionAcademica',
      component: () => import('@/views/GestionGruposView.vue')
    },

    // ══════════════════════════════════════════════════════════════════════
    // MÓDULO: EVENTOS
    // Carpeta: src/views/Modulo_Eventos/
    // Tablas:  tipo_evento, evento, participacion_evento
    // ══════════════════════════════════════════════════════════════════════
    {
      path: '/eventos',
      name: 'Eventos',
      component: () => import('@/views/Modulo_Eventos/EventosView.vue')
    },
    {
      path: '/eventos/nuevo',
      name: 'NuevoEvento',
      component: () => import('@/views/Modulo_Eventos/FormularioEventoView.vue')
    },
    {
      path: '/eventos/:id/editar',
      name: 'EditarEvento',
      component: () => import('@/views/Modulo_Eventos/FormularioEventoView.vue')
    },
    {
      path: '/eventos/:id/participantes',
      name: 'ParticipantesEvento',
      component: () => import('@/views/Modulo_Eventos/ParticipantesEventoView.vue')
    },

    // ══════════════════════════════════════════════════════════════════════
    // MÓDULO: COMITÉ ACADÉMICO
    // Carpeta: src/views/Modulo_Comite_Academico/
    // Tablas:  tipo_solicitud, solicitud_comite, sesion_comite, resolucion_comite
    // ══════════════════════════════════════════════════════════════════════

    // Dashboard del módulo
    {
      path: '/comite',
      name: 'Comite',
      component: () => import('@/views/Modulo_Comite_Academico/ComiteView.vue')
    },

    // Solicitudes
    {
      path: '/comite/solicitudes',
      name: 'SolicitudesComite',
      component: () => import('@/views/Modulo_Comite_Academico/SolicitudesComiteView.vue')
    },
    {
      path: '/comite/solicitudes/nueva',
      name: 'NuevaSolicitud',
      component: () => import('@/views/Modulo_Comite_Academico/NuevaSolicitudView.vue')
    },
    {
      path: '/comite/solicitudes/:id',
      name: 'DetalleSolicitud',
      component: () => import('@/views/Modulo_Comite_Academico/NuevaSolicitudView.vue')
    },

    // Sesiones
    {
      path: '/comite/sesiones',
      name: 'SesionesComite',
      component: () => import('@/views/Modulo_Comite_Academico/SesionesComiteView.vue')
    },

    // Resoluciones
    {
      path: '/comite/resoluciones',
      name: 'ResolucionesComite',
      component: () => import('@/views/Modulo_Comite_Academico/ResolucionesComiteView.vue')
    },
    {
      path: '/comite/resoluciones/nueva',
      name: 'NuevaResolucion',
      component: () => import('@/views/Modulo_Comite_Academico/ResolucionesComiteView.vue')
    },

    // ── Ruta 404: redirige al inicio ───────────────────────────────────────
    {
      path: '/:pathMatch(.*)*',
      name: 'NotFound',
      redirect: '/inicio'
    }
  ]
})

export default router