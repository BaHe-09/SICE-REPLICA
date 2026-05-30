<template>
  <MainLayout v-slot="{ busquedaGlobal }">
    <div class="alumnos-page">

      <!-- ══════════════════════════════════════════════
           BARRA DE CARGA GLOBAL
      ══════════════════════════════════════════════ -->
      <div class="barra-carga-global" :class="{ visible: cargando }">
        <div class="barra-progreso"></div>
      </div>

      <!-- ══════════════════════════════════════════════
           NOTIFICACIÓN TOAST
      ══════════════════════════════════════════════ -->
      <transition name="notif-fade">
        <div v-if="notificacion.visible" class="notificacion-ui" :class="notificacion.tipo">
          <!-- Icono éxito -->
          <svg v-if="notificacion.tipo === 'exito'" class="notif-icono" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <!-- Icono error -->
          <svg v-else class="notif-icono" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          {{ notificacion.mensaje }}
        </div>
      </transition>

      <!-- ══════════════════════════════════════════════
           BREADCRUMB
      ══════════════════════════════════════════════ -->
      <nav class="breadcrumb" aria-label="Ruta de navegación">
        <span class="breadcrumb-link" @click="$router.push('/gestion-academica')">GESTIÓN ACADÉMICA</span>
        <svg class="breadcrumb-chevron" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="14" height="14">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <span class="breadcrumb-actual">ALUMNOS</span>
      </nav>

      <!-- ══════════════════════════════════════════════
           ENCABEZADO DE PÁGINA
      ══════════════════════════════════════════════ -->
      <div class="page-header">
        <div class="page-header-left">
          <h1 class="page-title">GESTIÓN DE ALUMNOS</h1>
          <p class="page-subtitle">
            {{ alumnosFiltrados.length }} REGISTRO{{ alumnosFiltrados.length !== 1 ? 'S' : '' }} ENCONTRADO{{ alumnosFiltrados.length !== 1 ? 'S' : '' }}
          </p>
        </div>
        <button class="btn-nuevo" @click="nuevoAlumno">
          <!-- Lucide: UserPlus -->
          <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="17" height="17" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
            <circle cx="9" cy="7" r="4" />
            <line x1="19" y1="8" x2="19" y2="14" />
            <line x1="22" y1="11" x2="16" y2="11" />
          </svg>
          NUEVA INSCRIPCIÓN
        </button>
      </div>

      <!-- ══════════════════════════════════════════════
           KPIs VISUALES — 5 TARJETAS
      ══════════════════════════════════════════════ -->
      <div class="kpis-grid">

        <!-- KPI: Total -->
        <div class="kpi-card kpi-total">
          <div class="kpi-icon-wrap">
            <!-- Lucide: Users -->
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="22" height="22" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
              <circle cx="9" cy="7" r="4" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M23 21v-2a4 4 0 0 0-3-3.87" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M16 3.13a4 4 0 0 1 0 7.75" />
            </svg>
          </div>
          <div class="kpi-data">
            <span class="kpi-numero">{{ kpiTotal }}</span>
            <span class="kpi-label">TOTAL ALUMNOS</span>
          </div>
        </div>

        <!-- KPI: Activos -->
        <div class="kpi-card kpi-activo">
          <div class="kpi-icon-wrap">
            <!-- Lucide: CheckCircle2 -->
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="22" height="22" stroke-width="2">
              <circle cx="12" cy="12" r="10" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4" />
            </svg>
          </div>
          <div class="kpi-data">
            <span class="kpi-numero">{{ kpiActivos }}</span>
            <span class="kpi-label">ACTIVOS</span>
          </div>
        </div>

        <!-- KPI: Bajas Temporales -->
        <div class="kpi-card kpi-temporal">
          <div class="kpi-icon-wrap">
            <!-- Lucide: Clock -->
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="22" height="22" stroke-width="2">
              <circle cx="12" cy="12" r="10" />
              <polyline stroke-linecap="round" stroke-linejoin="round" points="12 6 12 12 16 14" />
            </svg>
          </div>
          <div class="kpi-data">
            <span class="kpi-numero">{{ kpiTemporales }}</span>
            <span class="kpi-label">BAJAS TEMPORALES</span>
          </div>
        </div>

        <!-- KPI: Bajas Definitivas -->
        <div class="kpi-card kpi-definitiva">
          <div class="kpi-icon-wrap">
            <!-- Lucide: XCircle -->
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="22" height="22" stroke-width="2">
              <circle cx="12" cy="12" r="10" />
              <line x1="15" y1="9" x2="9" y2="15" />
              <line x1="9" y1="9" x2="15" y2="15" />
            </svg>
          </div>
          <div class="kpi-data">
            <span class="kpi-numero">{{ kpiDefinitivas }}</span>
            <span class="kpi-label">BAJAS DEFINITIVAS</span>
          </div>
        </div>

        <!-- KPI: Egresados -->
        <div class="kpi-card kpi-egresado">
          <div class="kpi-icon-wrap">
            <!-- Lucide: GraduationCap -->
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="22" height="22" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M22 10v6M2 10l10-5 10 5-10 5z" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 12v5c3 3 9 3 12 0v-5" />
            </svg>
          </div>
          <div class="kpi-data">
            <span class="kpi-numero">{{ kpiEgresados }}</span>
            <span class="kpi-label">EGRESADOS</span>
          </div>
        </div>

      </div>

      <!-- ══════════════════════════════════════════════
           BARRA DE BÚSQUEDA Y FILTROS
      ══════════════════════════════════════════════ -->
      <div class="barra-acciones">

        <!-- Buscador mejorado -->
        <div class="busqueda-grupo">
          <div class="input-con-icono">
            <!-- Lucide: Search -->
            <svg class="icono-input" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="18" height="18" stroke-width="2">
              <circle cx="11" cy="11" r="8" />
              <line x1="21" y1="21" x2="16.65" y2="16.65" />
            </svg>
            <input
              type="text"
              ref="inputBusqueda"
              v-model="busquedaAlumno"
              class="input-busqueda"
              placeholder="BUSCAR POR NO. CONTROL O NOMBRE..."
              @input="currentPage = 1"
              @keydown.escape="limpiarBusqueda"
              autocomplete="off"
              spellcheck="false"
            />
            <button v-if="busquedaAlumno" class="btn-limpiar-input" @click="limpiarBusqueda" title="LIMPIAR BÚSQUEDA">
              <!-- Lucide: X -->
              <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="14" height="14" stroke-width="2.5">
                <line x1="18" y1="6" x2="6" y2="18" />
                <line x1="6" y1="6" x2="18" y2="18" />
              </svg>
            </button>
          </div>
        </div>

        <!-- Botón filtros con badge -->
        <button
          class="btn-filtro"
          @click="mostrarFiltros = !mostrarFiltros"
          :class="{ activo: filtrosActivos, abierto: mostrarFiltros }"
          :aria-expanded="mostrarFiltros"
        >
          <!-- Lucide: SlidersHorizontal -->
          <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" height="16" stroke-width="2">
            <line x1="21" y1="4" x2="14" y2="4" />
            <line x1="10" y1="4" x2="3" y2="4" />
            <line x1="21" y1="12" x2="12" y2="12" />
            <line x1="8" y1="12" x2="3" y2="12" />
            <line x1="21" y1="20" x2="16" y2="20" />
            <line x1="12" y1="20" x2="3" y2="20" />
            <line x1="14" y1="2" x2="14" y2="6" />
            <line x1="8" y1="10" x2="8" y2="14" />
            <line x1="16" y1="18" x2="16" y2="22" />
          </svg>
          FILTROS
          <span v-if="filtrosActivos" class="filtros-badge">{{ contadorFiltros }}</span>
        </button>

      </div>

      <!-- ══════════════════════════════════════════════
           PANEL DE FILTROS (COLAPSABLE)
      ══════════════════════════════════════════════ -->
      <transition name="filtros-slide">
        <div v-if="mostrarFiltros" class="filtros-panel">
          <div class="filtros-grid">

            <!-- Filtro Carrera -->
            <div class="filtro-item">
              <label class="filtro-label">
                <!-- Lucide: BookOpen -->
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="13" height="13" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z" />
                  <path stroke-linecap="round" stroke-linejoin="round" d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z" />
                </svg>
                CARRERA
              </label>
              <select v-model="filtroCarreraId" class="filter-select" @change="currentPage = 1">
                <option value="">TODAS LAS CARRERAS</option>
                <option v-for="c in catalogos.carreras" :key="c.id_carrera" :value="c.id_carrera">
                  {{ c.nombre?.toUpperCase() }}
                </option>
              </select>
            </div>

            <!-- Filtro Semestre -->
            <div class="filtro-item">
              <label class="filtro-label">
                <!-- Lucide: Layers -->
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="13" height="13" stroke-width="2">
                  <polygon stroke-linecap="round" stroke-linejoin="round" points="12 2 2 7 12 12 22 7 12 2" />
                  <polyline stroke-linecap="round" stroke-linejoin="round" points="2 17 12 22 22 17" />
                  <polyline stroke-linecap="round" stroke-linejoin="round" points="2 12 12 17 22 12" />
                </svg>
                SEMESTRE
              </label>
              <select v-model="filtroSemestre" class="filter-select" @change="currentPage = 1">
                <option value="">TODOS</option>
                <option v-for="n in 8" :key="n" :value="String(n)">{{ n }}° SEMESTRE</option>
              </select>
            </div>

            <!-- Filtro Estatus -->
            <div class="filtro-item">
              <label class="filtro-label">
                <!-- Lucide: Tag -->
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="13" height="13" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z" />
                  <line x1="7" y1="7" x2="7.01" y2="7" />
                </svg>
                ESTATUS
              </label>
              <select v-model="filtroEstatusId" class="filter-select" @change="currentPage = 1">
                <option value="">TODOS</option>
                <option v-for="e in catalogos.estatus_alumno" :key="e.id_estatus_alumno" :value="e.id_estatus_alumno">
                  {{ e.nombre?.toUpperCase() }}
                </option>
              </select>
            </div>

          </div>

          <div class="filtros-footer">
            <button class="btn-limpiar-filtros" @click="resetFilters">
              <!-- Lucide: RotateCcw -->
              <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="14" height="14" stroke-width="2">
                <polyline stroke-linecap="round" stroke-linejoin="round" points="1 4 1 10 7 10" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.51 15a9 9 0 1 0 .49-3.5" />
              </svg>
              LIMPIAR FILTROS
            </button>
          </div>
        </div>
      </transition>

      <!-- ══════════════════════════════════════════════
           ESTADO: CARGANDO
      ══════════════════════════════════════════════ -->
      <div v-if="cargando && alumnos.length === 0" class="estado-cargando">
        <div class="spinner-cards"></div>
        <p>CARGANDO REGISTROS...</p>
      </div>

      <!-- ══════════════════════════════════════════════
           ESTADO: VACÍO
      ══════════════════════════════════════════════ -->
      <div v-else-if="!cargando && alumnosFiltrados.length === 0" class="estado-vacio">
        <!-- Lucide: UserX -->
        <svg class="icono-vacio" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.3">
          <path stroke-linecap="round" stroke-linejoin="round" d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
          <circle cx="9" cy="7" r="4" />
          <line x1="17" y1="11" x2="23" y2="17" />
          <line x1="23" y1="11" x2="17" y2="17" />
        </svg>
        <h3>SIN RESULTADOS</h3>
        <p>NO SE ENCONTRARON ALUMNOS CON LOS CRITERIOS APLICADOS.</p>
        <button class="btn-limpiar-vacio" @click="resetFilters">LIMPIAR FILTROS</button>
      </div>

      <!-- ══════════════════════════════════════════════
           GRID DE CARDS DE ALUMNOS
      ══════════════════════════════════════════════ -->
      <div
        v-else
        class="alumnos-grid"
        ref="gridRef"
        @keydown="navegarTeclado"
        tabindex="0"
        role="list"
        aria-label="Lista de alumnos"
      >
        <div
          v-for="(alumno, index) in paginatedAlumnos"
          :key="alumno.id_alumno || alumno.id"
          class="alumno-card"
          :class="{ 'card-activa': cardActiva === index }"
          role="listitem"
          tabindex="-1"
          @click="abrirModalVer(alumno)"
          @keydown.enter.prevent="abrirModalVer(alumno)"
        >
          <!-- Acento de color según estatus -->
          <div class="card-acento" :class="`acento-${slugEstatus(alumno.estatus)}`"></div>

          <!-- Parte superior: Avatar + Datos principales -->
          <div class="card-top">
            <div class="card-avatar-wrap">
              <img
                v-if="alumno.foto"
                :src="alumno.foto"
                class="card-avatar-img"
                :alt="resolverNombre(alumno)"
              />
              <div v-else class="card-avatar-placeholder" :class="`avatar-${colorIndex(resolverNombre(alumno))}`">
                <span>{{ iniciales(resolverNombre(alumno)) }}</span>
              </div>
              <!-- Indicador de estatus en el avatar -->
              <span class="avatar-dot" :class="`dot-${slugEstatus(alumno.estatus)}`"></span>
            </div>

            <div class="card-info">
              <span class="card-control">{{ alumno.numero_control || alumno.noControl }}</span>
              <h3 class="card-nombre">{{ resolverNombre(alumno).toUpperCase() }}</h3>
              <p class="card-carrera">{{ resolverCarrera(alumno).toUpperCase() }}</p>
            </div>
          </div>

          <!-- Separador -->
          <div class="card-divider"></div>

          <!-- Parte inferior: Semestre + Estatus + Acciones -->
          <div class="card-bottom">
            <div class="card-meta">
              <div class="card-meta-item">
                <!-- Lucide: Layers mini -->
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="13" height="13" stroke-width="2">
                  <polygon stroke-linecap="round" stroke-linejoin="round" points="12 2 2 7 12 12 22 7 12 2" />
                  <polyline stroke-linecap="round" stroke-linejoin="round" points="2 12 12 17 22 12" />
                </svg>
                <span>{{ alumno.semestre_actual || alumno.semestre }}° SEM.</span>
              </div>
              <span class="estatus-badge" :class="`badge-${slugEstatus(alumno.estatus)}`">
                {{ (alumno.estatus || 'N/D').toUpperCase() }}
              </span>
            </div>

            <div class="card-acciones">
              <!-- Ver expediente -->
              <button
                class="btn-card-accion btn-ver"
                @click.stop="abrirModalVer(alumno)"
                title="VER EXPEDIENTE"
              >
                <!-- Lucide: Eye -->
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="15" height="15" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                  <circle cx="12" cy="12" r="3" />
                </svg>
              </button>
              <!-- Editar -->
              <button
                class="btn-card-accion btn-editar"
                @click.stop="abrirModalEditar(alumno)"
                title="EDITAR"
              >
                <!-- Lucide: Pencil -->
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="15" height="15" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                  <path stroke-linecap="round" stroke-linejoin="round" d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                </svg>
              </button>
              <!-- Kardex directo -->
              <button
                class="btn-card-accion btn-kardex"
                @click.stop="verKardex(alumno)"
                title="VER KARDEX"
              >
                <!-- Lucide: FileText -->
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="15" height="15" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                  <polyline stroke-linecap="round" stroke-linejoin="round" points="14 2 14 8 20 8" />
                  <line x1="16" y1="13" x2="8" y2="13" />
                  <line x1="16" y1="17" x2="8" y2="17" />
                  <polyline stroke-linecap="round" stroke-linejoin="round" points="10 9 9 9 8 9" />
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- ══════════════════════════════════════════════
           PAGINACIÓN
      ══════════════════════════════════════════════ -->
      <div class="paginacion" v-if="alumnosFiltrados.length > 0">
        <div class="paginacion-izquierda">
          <span class="pag-label">REGISTROS POR PÁGINA:</span>
          <select v-model="filasPorPagina" @change="currentPage = 1" class="select-filas">
            <option :value="12">12</option>
            <option :value="24">24</option>
            <option :value="48">48</option>
          </select>
        </div>

        <div class="paginacion-centro">
          PÁGINA <strong>{{ currentPage }}</strong> DE <strong>{{ totalPages }}</strong>
        </div>

        <div class="paginacion-derecha">
          <button class="btn-pag" @click="prevPage" :disabled="currentPage === 1" aria-label="Página anterior">
            <!-- Lucide: ChevronLeft -->
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" height="16" stroke-width="2">
              <polyline stroke-linecap="round" stroke-linejoin="round" points="15 18 9 12 15 6" />
            </svg>
          </button>
          <button
            v-for="page in visiblePages"
            :key="page"
            class="btn-pag"
            :class="{ activo: page === currentPage }"
            @click="goToPage(page)"
          >{{ page }}</button>
          <button class="btn-pag" @click="nextPage" :disabled="currentPage === totalPages" aria-label="Página siguiente">
            <!-- Lucide: ChevronRight -->
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" height="16" stroke-width="2">
              <polyline stroke-linecap="round" stroke-linejoin="round" points="9 18 15 12 9 6" />
            </svg>
          </button>
        </div>
      </div>

      <footer class="pie-pagina">© 2026 TECNOLÓGICO NACIONAL DE MÉXICO | TODOS LOS DERECHOS RESERVADOS</footer>
    </div>

    <!-- ══════════════════════════════════════════════════════════════
         MODAL VER ALUMNO (Expediente con pestañas)
    ══════════════════════════════════════════════════════════════ -->
    <Teleport to="body">
      <transition name="modal-fade">
        <div v-if="showViewModal && alumnoSeleccionado" class="modal-overlay" @click.self="cerrarModalVer" role="dialog" aria-modal="true">
          <div class="modal-content modal-ver-alumno">

            <!-- Header del modal -->
            <div class="modal-header">
              <div class="modal-header-avatar-group">
                <div class="detalle-avatar">
                  <img v-if="alumnoSeleccionado.foto" :src="alumnoSeleccionado.foto" class="avatar-img" :alt="resolverNombre(alumnoSeleccionado)" />
                  <div v-else class="avatar-placeholder" :class="`avatar-${colorIndex(resolverNombre(alumnoSeleccionado))}`">
                    <span>{{ iniciales(resolverNombre(alumnoSeleccionado)) }}</span>
                  </div>
                </div>
                <div class="modal-header-info">
                  <span class="modal-header-tag">FICHA DE ALUMNO</span>
                  <h3>{{ resolverNombre(alumnoSeleccionado).toUpperCase() }}</h3>
                  <span class="sub-header-control">{{ alumnoSeleccionado.numero_control || alumnoSeleccionado.noControl }}</span>
                </div>
              </div>
              <button @click="cerrarModalVer" class="btn-cerrar-modal" title="CERRAR" aria-label="Cerrar modal">
                <!-- Lucide: X -->
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="22" height="22" stroke-width="2">
                  <line x1="18" y1="6" x2="6" y2="18" />
                  <line x1="6" y1="6" x2="18" y2="18" />
                </svg>
              </button>
            </div>

            <!-- Pestañas -->
            <div class="modal-body-tabs">
              <div class="detalle-tabs" role="tablist">
                <button
                  v-for="tab in tabs"
                  :key="tab.id"
                  class="tab-btn"
                  :class="{ activo: tabActivo === tab.id }"
                  @click="tabActivo = tab.id"
                  :role="'tab'"
                  :aria-selected="tabActivo === tab.id"
                >
                  <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" height="16" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" :d="tab.icon" />
                  </svg>
                  {{ tab.label }}
                </button>
              </div>

              <div class="tab-contenido-scroll">

                <!-- Tab: Datos Generales -->
                <div v-if="tabActivo === 'general'" class="tab-panel">
                  <div class="detalle-grid">
                    <div class="detalle-campo">
                      <span class="detalle-label">NO. DE CONTROL</span>
                      <span class="detalle-valor mono-bold">{{ alumnoSeleccionado.numero_control || alumnoSeleccionado.noControl }}</span>
                    </div>
                    <div class="detalle-campo">
                      <span class="detalle-label">SEMESTRE ACTUAL</span>
                      <span class="detalle-valor detalle-numero">{{ alumnoSeleccionado.semestre_actual || alumnoSeleccionado.semestre }}°</span>
                    </div>
                    <div class="detalle-campo full-width">
                      <span class="detalle-label">CARRERA</span>
                      <span class="detalle-valor">{{ resolverCarrera(alumnoSeleccionado).toUpperCase() }}</span>
                    </div>
                    <div class="detalle-campo">
                      <span class="detalle-label">ESTATUS ACADÉMICO</span>
                      <span class="estatus-badge-modal" :class="`badge-${slugEstatus(alumnoSeleccionado.estatus)}`">
                        {{ (alumnoSeleccionado.estatus || '').toUpperCase() }}
                      </span>
                    </div>
                    <div class="detalle-campo" v-if="alumnoSeleccionado.fecha_ingreso">
                      <span class="detalle-label">FECHA DE INGRESO</span>
                      <span class="detalle-valor">{{ formatearFecha(alumnoSeleccionado.fecha_ingreso) }}</span>
                    </div>
                    <div class="detalle-campo full-width" v-if="alumnoSeleccionado.email || alumnoSeleccionado.persona?.email">
                      <span class="detalle-label">CORREO ELECTRÓNICO</span>
                      <span class="detalle-valor">{{ alumnoSeleccionado.email || alumnoSeleccionado.persona?.email }}</span>
                    </div>
                  </div>
                </div>

                <!-- Tab: Kardex -->
                <div v-if="tabActivo === 'kardex'" class="tab-panel">
                  <div v-if="cargandoKardex" class="kardex-cargando">
                    <div class="skeleton-linea largo"></div>
                    <div class="skeleton-linea medio"></div>
                    <div v-for="i in 4" :key="i" class="skeleton-fila"></div>
                  </div>
                  <div v-else-if="kardexError" class="kardex-error">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="20" height="20" stroke-width="2">
                      <circle cx="12" cy="12" r="10" />
                      <line x1="12" y1="8" x2="12" y2="12" />
                      <line x1="12" y1="16" x2="12.01" y2="16" />
                    </svg>
                    <span>NO SE PUDO CARGAR EL KARDEX.</span>
                    <button @click="cargarKardexAlumno(alumnoSeleccionado)">REINTENTAR</button>
                  </div>
                  <div v-else-if="kardexData">
                    <div class="creditos-bloque" v-if="kardexData.alumno?.creditos_totales">
                      <div class="creditos-row">
                        <span class="creditos-label">AVANCE DE CRÉDITOS</span>
                        <span class="creditos-pct" :class="{ verde: porcentajeCreditos >= 80 }">
                          {{ kardexData.alumno.creditos_acumulados }}/{{ kardexData.alumno.creditos_totales }} ({{ porcentajeCreditos }}%)
                        </span>
                      </div>
                      <div class="creditos-barra-track">
                        <div class="creditos-barra-fill" :style="{ width: porcentajeCreditos + '%', background: porcentajeCreditos >= 80 ? '#16A34A' : '#1B396A' }"></div>
                      </div>
                    </div>
                    <div v-if="kardexData.kardex?.semestres?.length" class="kardex-semestres">
                      <div v-for="semestre in kardexData.kardex.semestres" :key="semestre.numero" class="semestre-bloque">
                        <button class="semestre-btn" @click="toggleSemestre(semestre.numero)" :class="{ abierto: semestresAbiertos.includes(semestre.numero) }">
                          <span class="semestre-titulo">SEMESTRE {{ semestre.numero }}</span>
                          <div class="semestre-badges">
                            <span class="badge-mini acreditadas">{{ semestre.acreditadas }} ACRED.</span>
                            <span v-if="semestre.reprobadas > 0" class="badge-mini reprobadas">{{ semestre.reprobadas }} REPR.</span>
                            <svg class="flecha-semestre" :class="{ girado: semestresAbiertos.includes(semestre.numero) }" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" height="16" stroke-width="2">
                              <polyline stroke-linecap="round" stroke-linejoin="round" points="6 9 12 15 18 9" />
                            </svg>
                          </div>
                        </button>
                        <transition name="expand">
                          <div v-if="semestresAbiertos.includes(semestre.numero)" class="semestre-materias">
                            <table class="tabla-mini">
                              <thead>
                                <tr><th>CLAVE</th><th>MATERIA</th><th>CAL.</th><th>ESTADO</th></tr>
                              </thead>
                              <tbody>
                                <tr v-for="m in semestre.materias" :key="m.clave" :class="{ 'fila-repr': m.estado === 'reprobada' }">
                                  <td class="clave-mono">{{ m.clave }}</td>
                                  <td>{{ m.nombre }}</td>
                                  <td class="centrado">
                                    <strong v-if="m.calificacion !== null" :class="{ 'text-verde': m.estado === 'acreditada', 'text-rojo': m.estado === 'reprobada' }">{{ m.calificacion }}</strong>
                                    <span v-else class="text-gris">—</span>
                                  </td>
                                  <td><span class="badge-estado-mini" :style="estiloEstado(m.estado)">{{ etiquetaEstado(m.estado) }}</span></td>
                                </tr>
                              </tbody>
                            </table>
                          </div>
                        </transition>
                      </div>
                    </div>
                    <div v-else class="kardex-vacio"><p>NO HAY MATERIAS REGISTRADAS EN EL KARDEX.</p></div>
                  </div>
                  <div v-else class="kardex-vacio"><p>KARDEX NO DISPONIBLE.</p></div>
                </div>

                <!-- Tab: Horario -->
                <div v-if="tabActivo === 'horario'" class="tab-panel">
                  <div v-if="cargandoHorario" class="kardex-cargando">
                    <div v-for="i in 5" :key="i" class="skeleton-fila"></div>
                  </div>
                  <div v-else-if="horarioData?.length" class="horario-lista">
                    <div v-for="materia in horarioData" :key="materia.id" class="horario-item">
                      <div class="horario-color" :style="{ background: colorMateria(materia.nombre) }"></div>
                      <div class="horario-info">
                        <span class="horario-nombre">{{ materia.nombre }}</span>
                        <span class="horario-meta">{{ materia.dias }} · {{ materia.hora_inicio }}–{{ materia.hora_fin }}</span>
                        <span class="horario-aula">AULA: {{ materia.aula || 'N/D' }}</span>
                      </div>
                    </div>
                  </div>
                  <div v-else class="kardex-vacio">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="32" height="32" style="stroke:#9CA3AF; margin-bottom:8px" stroke-width="1.5">
                      <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                      <line x1="16" y1="2" x2="16" y2="6" />
                      <line x1="8" y1="2" x2="8" y2="6" />
                      <line x1="3" y1="10" x2="21" y2="10" />
                    </svg>
                    <p>NO HAY HORARIO REGISTRADO PARA ESTE ALUMNO.</p>
                  </div>
                </div>

              </div>
            </div>

            <!-- Footer del modal -->
            <div class="modal-footer">
              <button class="btn-secundario" @click="cerrarModalVer">CERRAR</button>
              <button class="btn-accion-outline" @click="verKardex(alumnoSeleccionado); cerrarModalVer()">
                <!-- Lucide: FileText mini -->
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="15" height="15" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                  <polyline stroke-linecap="round" stroke-linejoin="round" points="14 2 14 8 20 8" />
                  <line x1="16" y1="13" x2="8" y2="13" />
                  <line x1="16" y1="17" x2="8" y2="17" />
                </svg>
                VER KARDEX COMPLETO
              </button>
              <button class="btn-guardar" @click="abrirModalEditar(alumnoSeleccionado); cerrarModalVer()">
                <!-- Lucide: Pencil mini -->
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="15" height="15" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                  <path stroke-linecap="round" stroke-linejoin="round" d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                </svg>
                EDITAR
              </button>
            </div>
          </div>
        </div>
      </transition>
    </Teleport>

    <!-- ══════════════════════════════════════════════════════════════
         MODAL EDITAR / CREAR ALUMNO
    ══════════════════════════════════════════════════════════════ -->
    <Teleport to="body">
      <transition name="modal-fade">
        <div v-if="showModal" class="modal-overlay" @click.self="cerrarModal" role="dialog" aria-modal="true">
          <div class="modal-content">
            <div class="modal-header">
              <div class="modal-header-info">
                <span class="modal-header-tag">{{ alumnoEditar.id_alumno ? 'EDICIÓN' : 'NUEVO' }}</span>
                <h3>{{ alumnoEditar.id_alumno ? 'EDITAR ALUMNO' : 'NUEVO ALUMNO' }}</h3>
              </div>
              <button @click="cerrarModal" class="btn-cerrar-modal" title="CERRAR">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="22" height="22" stroke-width="2">
                  <line x1="18" y1="6" x2="6" y2="18" /><line x1="6" y1="6" x2="18" y2="18" />
                </svg>
              </button>
            </div>
            <div class="modal-body form-body">
              <div class="form-grupo" v-if="alumnoEditar.id_alumno">
                <label>NÚMERO DE CONTROL</label>
                <input v-model="alumnoEditar.noControl" type="text" readonly class="modal-input deshabilitado" />
              </div>
              <div class="form-grupo">
                <label>NOMBRE COMPLETO <span class="obligatorio">*</span></label>
                <input v-model="alumnoEditar.nombre" type="text" class="modal-input" placeholder="NOMBRE COMPLETO" style="text-transform:uppercase" />
              </div>
              <div class="form-grupo">
                <label>CARRERA <span class="obligatorio">*</span></label>
                <select v-model="alumnoEditar.id_carrera" class="modal-select">
                  <option value="">SELECCIONAR CARRERA</option>
                  <option v-for="c in catalogos.carreras" :key="c.id_carrera" :value="c.id_carrera">{{ c.nombre?.toUpperCase() }}</option>
                </select>
              </div>
              <div class="form-grupo-doble">
                <div class="form-grupo">
                  <label>SEMESTRE</label>
                  <select v-model="alumnoEditar.semestre" class="modal-select">
                    <option v-for="n in 8" :key="n" :value="n">{{ n }}° SEMESTRE</option>
                  </select>
                </div>
                <div class="form-grupo">
                  <label>ESTATUS</label>
                  <select v-model="alumnoEditar.id_estatus_alumno" class="modal-select">
                    <option value="">SELECCIONAR ESTATUS</option>
                    <option v-for="e in catalogos.estatus_alumno" :key="e.id_estatus_alumno" :value="e.id_estatus_alumno">{{ e.nombre?.toUpperCase() }}</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button class="btn-secundario" @click="cerrarModal" :disabled="guardando">CANCELAR</button>
              <button v-if="alumnoEditar.id_alumno" class="btn-eliminar" @click="solicitarEliminar" :disabled="guardando">ELIMINAR</button>
              <button class="btn-guardar" @click="guardarCambios" :disabled="guardando">
                <span v-if="guardando" class="spinner-btn"></span>
                {{ guardando ? 'GUARDANDO...' : 'GUARDAR CAMBIOS' }}
              </button>
            </div>
          </div>
        </div>
      </transition>
    </Teleport>

    <!-- ══════════════════════════════════════════════════════════════
         MODAL CONFIRMACIÓN DE ELIMINACIÓN
    ══════════════════════════════════════════════════════════════ -->
    <Teleport to="body">
      <transition name="modal-fade">
        <div v-if="showModalEliminar" class="modal-overlay" @click.self="showModalEliminar = false" role="alertdialog" aria-modal="true">
          <div class="modal-content modal-confirmar">
            <div class="modal-header">
              <div class="modal-header-info">
                <span class="modal-header-tag">CONFIRMAR</span>
                <h3>ELIMINAR ALUMNO</h3>
              </div>
              <button @click="showModalEliminar = false" class="btn-cerrar-modal">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="22" height="22" stroke-width="2">
                  <line x1="18" y1="6" x2="6" y2="18" /><line x1="6" y1="6" x2="18" y2="18" />
                </svg>
              </button>
            </div>
            <div class="modal-body confirmar-body">
              <!-- Lucide: AlertTriangle -->
              <svg class="confirmar-icono" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
                <line x1="12" y1="9" x2="12" y2="13" />
                <line x1="12" y1="17" x2="12.01" y2="17" />
              </svg>
              <p>¿ESTÁS SEGURO DE ELIMINAR A <strong>{{ alumnoEditar.nombre?.toUpperCase() }}</strong>? ESTA ACCIÓN NO SE PUEDE DESHACER.</p>
            </div>
            <div class="modal-footer">
              <button class="btn-secundario" @click="showModalEliminar = false">CANCELAR</button>
              <button class="btn-eliminar" @click="confirmarEliminar" :disabled="guardando">
                <span v-if="guardando" class="spinner-btn"></span>
                {{ guardando ? 'ELIMINANDO...' : 'ELIMINAR' }}
              </button>
            </div>
          </div>
        </div>
      </transition>
    </Teleport>

  </MainLayout>
</template>

<script setup>
import { ref, computed, onMounted, watch, nextTick } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import MainLayout from '@/layouts/MainLayout.vue'

// ── Router y entorno ─────────────────────────────────────────────────
const router  = useRouter()
const route   = useRoute()
const API_URL = import.meta.env.VITE_API_URL

// ── Props desde MainLayout ───────────────────────────────────────────
const props = defineProps({
  busquedaGlobal: { type: String, default: '' }
})

// ── Estado principal ─────────────────────────────────────────────────
const alumnos        = ref([])
const cargando       = ref(false)
const guardando      = ref(false)
const cardActiva     = ref(-1)  // Índice de card seleccionada (teclado)
const gridRef        = ref(null)
const inputBusqueda  = ref(null)

// ── Modales ──────────────────────────────────────────────────────────
const showViewModal      = ref(false)
const showModal          = ref(false)
const showModalEliminar  = ref(false)
const alumnoSeleccionado = ref(null)
const alumnoEditar       = ref({})

// ── Pestañas (modal ver: Datos / Kardex / Horario) ───────────────────
const tabActivo         = ref('general')
const kardexData        = ref(null)
const cargandoKardex    = ref(false)
const kardexError       = ref(false)
const horarioData       = ref(null)
const cargandoHorario   = ref(false)
const semestresAbiertos = ref([])

const tabs = [
  {
    id: 'general',
    label: 'DATOS',
    icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'
  },
  {
    id: 'kardex',
    label: 'KARDEX',
    icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 9.414V19a2 2 0 01-2 2z'
  },
  {
    id: 'horario',
    label: 'HORARIO',
    icon: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'
  },
]

// ── Catálogos ────────────────────────────────────────────────────────
const catalogos = ref({ carreras: [], estatus_alumno: [] })

// ── Filtros y paginación ─────────────────────────────────────────────
const busquedaAlumno  = ref('')
const mostrarFiltros  = ref(false)
const filtroCarreraId = ref('')
const filtroSemestre  = ref('')
const filtroEstatusId = ref('')
const filasPorPagina  = ref(12)   // Cards: múltiplos de 4 (12, 24, 48)
const currentPage     = ref(1)

// ── Notificación (Toast) ─────────────────────────────────────────────
const notificacion = ref({ visible: false, mensaje: '', tipo: 'exito' })
let timerNotif = null

const mostrarNotificacion = (mensaje, tipo = 'exito') => {
  if (timerNotif) clearTimeout(timerNotif)
  notificacion.value = { visible: true, mensaje: mensaje.toUpperCase(), tipo }
  timerNotif = setTimeout(() => { notificacion.value.visible = false }, 3500)
}

// ── Helpers ──────────────────────────────────────────────────────────

/** Normaliza texto para búsqueda (sin tildes, minúsculas) */
const normalize = (t) =>
  !t ? '' : t.toString().toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '')

/** Genera slug de estatus para clases CSS */
const slugEstatus = (estatus) => {
  if (!estatus) return 'desconocido'
  const e = estatus.toLowerCase()
  if (e === 'activo')           return 'activo'
  if (e.includes('temporal'))   return 'temporal'
  if (e.includes('definitiva')) return 'definitiva'
  if (e === 'titulado')         return 'titulado'
  if (e === 'egresado')         return 'egresado'
  return 'desconocido'
}

/** Índice de color para avatares (0-4) */
const colorIndex = (nombre) => {
  if (!nombre) return 0
  let h = 0
  for (let i = 0; i < nombre.length; i++) h += nombre.charCodeAt(i)
  return h % 5
}

const resolverNombre   = (a) => a?.nombre || a?.persona?.nombre_completo || a?.persona?.nombre || ''
const resolverCarrera  = (a) => a?.carrera?.nombre_carrera || a?.carrera || ''
const resolverIdCarrera = (a) => a?.id_carrera || a?.carrera?.id_carrera || null
const iniciales        = (nombre) => !nombre ? '?' : nombre.split(' ').filter(Boolean).slice(0, 2).map(p => p[0]).join('').toUpperCase()
const formatearFecha   = (f) => !f ? '' : new Date(f).toLocaleDateString('es-MX', { day: '2-digit', month: 'long', year: 'numeric' })

/** Color determinista para materias en horario */
const colorMateria = (nombre) => {
  const colors = ['#1B396A', '#7C3AED', '#0891B2', '#059669', '#DC2626', '#D97706']
  let h = 0
  for (let i = 0; i < (nombre || '').length; i++) h += nombre.charCodeAt(i)
  return colors[h % colors.length]
}

/** Corrección conocida de nombre */
const corregirNombreAlumno = (nombre) => {
  if (!nombre) return nombre
  return nombre.replace(/\bWilfido\b/gi, 'Wilfredo')
}

// ── KPIs calculados ──────────────────────────────────────────────────
const kpiTotal      = computed(() => alumnos.value.length)
const kpiActivos    = computed(() => alumnos.value.filter(a => slugEstatus(a.estatus) === 'activo').length)
const kpiTemporales = computed(() => alumnos.value.filter(a => slugEstatus(a.estatus) === 'temporal').length)
const kpiDefinitivas= computed(() => alumnos.value.filter(a => slugEstatus(a.estatus) === 'definitiva').length)
const kpiEgresados  = computed(() => alumnos.value.filter(a => ['egresado', 'titulado'].includes(slugEstatus(a.estatus))).length)

// ── Computadas de filtros ────────────────────────────────────────────
const filtrosActivos  = computed(() => !!(filtroCarreraId.value || filtroSemestre.value || filtroEstatusId.value))
const contadorFiltros = computed(() =>
  [filtroCarreraId.value, filtroSemestre.value, filtroEstatusId.value].filter(Boolean).length
)

// ── Filtrado combinado (global + local + dropdowns) ──────────────────
const alumnosFiltrados = computed(() => {
  return alumnos.value.filter(alumno => {
    const nombre    = resolverNombre(alumno)
    const noControl = (alumno.numero_control || alumno.noControl || '').toString()

    // Búsqueda global desde MainLayout
    const busqGlobal = !props.busquedaGlobal ||
      normalize(nombre).includes(normalize(props.busquedaGlobal)) ||
      noControl.includes(props.busquedaGlobal)

    // Búsqueda local del componente (por No. Control y Nombre completo)
    const termLocal = busquedaAlumno.value.trim()
    const busqLocal = !termLocal ||
      normalize(nombre).includes(normalize(termLocal)) ||
      noControl.toLowerCase().includes(termLocal.toLowerCase())

    // Filtros de dropdown
    const filtCarrera  = !filtroCarreraId.value || Number(resolverIdCarrera(alumno)) === Number(filtroCarreraId.value)
    const filtSemestre = !filtroSemestre.value  || String(alumno.semestre_actual || alumno.semestre) === String(filtroSemestre.value)
    const filtEstatus  = !filtroEstatusId.value || Number(alumno.id_estatus_alumno) === Number(filtroEstatusId.value)

    return busqGlobal && busqLocal && filtCarrera && filtSemestre && filtEstatus
  })
})

// ── Paginación ───────────────────────────────────────────────────────
const totalPages = computed(() =>
  Math.ceil(alumnosFiltrados.value.length / filasPorPagina.value) || 1
)

const paginatedAlumnos = computed(() => {
  const start = (currentPage.value - 1) * filasPorPagina.value
  return alumnosFiltrados.value.slice(start, start + filasPorPagina.value)
})

const visiblePages = computed(() => {
  const total = totalPages.value
  const cur   = currentPage.value
  if (total <= 7) return Array.from({ length: total }, (_, i) => i + 1)
  const pages = new Set([1, total, cur, cur - 1, cur + 1].filter(p => p >= 1 && p <= total))
  return [...pages].sort((a, b) => a - b)
})

const goToPage = (p) => { currentPage.value = p; cardActiva.value = -1 }
const prevPage = () => { if (currentPage.value > 1) currentPage.value-- }
const nextPage = () => { if (currentPage.value < totalPages.value) currentPage.value++ }

const resetFilters = () => {
  busquedaAlumno.value  = ''
  filtroCarreraId.value = ''
  filtroSemestre.value  = ''
  filtroEstatusId.value = ''
  currentPage.value = 1
  cardActiva.value  = -1
}

const limpiarBusqueda = () => {
  busquedaAlumno.value = ''
  currentPage.value    = 1
  nextTick(() => inputBusqueda.value?.focus())
}

// ── Navegación por teclado (grid de cards) ───────────────────────────
const navegarTeclado = (e) => {
  const total = paginatedAlumnos.value.length
  if (total === 0) return
  if (e.key === 'ArrowRight' || e.key === 'ArrowDown') {
    e.preventDefault()
    cardActiva.value = Math.min(cardActiva.value + 1, total - 1)
  } else if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') {
    e.preventDefault()
    cardActiva.value = Math.max(cardActiva.value - 1, 0)
  } else if (e.key === 'Enter' && cardActiva.value >= 0) {
    e.preventDefault()
    abrirModalVer(paginatedAlumnos.value[cardActiva.value])
  } else if (e.key === 'PageDown') {
    e.preventDefault(); nextPage()
  } else if (e.key === 'PageUp') {
    e.preventDefault(); prevPage()
  }
}

// ── Carga de datos ───────────────────────────────────────────────────
const cargarAlumnosDesdeBD = async () => {
  cargando.value = true
  try {
    const res  = await fetch(`${API_URL}/api/alumnos-crud`)
    if (!res.ok) throw new Error()
    const data = await res.json()

    alumnos.value = data.map(a => {
      const nombreOriginal = a.nombre || a.persona?.nombre_completo || a.persona?.nombre || ''
      const nombreCorregido = corregirNombreAlumno(nombreOriginal)
      const alumnoCorregido = { ...a, nombre: nombreCorregido }
      if (a.persona) {
        alumnoCorregido.persona = {
          ...a.persona,
          nombre_completo: corregirNombreAlumno(a.persona.nombre_completo || ''),
          nombre:          corregirNombreAlumno(a.persona.nombre || '')
        }
      }
      return alumnoCorregido
    })

    // Apertura directa por query param ?ver=NOCTRL
    const verControl = route.query.ver
    if (verControl) {
      const encontrado = alumnos.value.find(
        a => String(a.numero_control) === String(verControl)
      )
      if (encontrado) abrirModalVer(encontrado)
    }
  } catch {
    mostrarNotificacion('No se pudo cargar la lista de alumnos.', 'error')
  } finally {
    cargando.value = false
  }
}

const cargarCatalogos = async () => {
  try {
    const res  = await fetch(`${API_URL}/api/alumnos/catalogos`)
    if (!res.ok) throw new Error()
    const data = await res.json()
    catalogos.value.carreras       = data.carreras       || []
    catalogos.value.estatus_alumno = data.estatus_alumno || []
  } catch {
    mostrarNotificacion('No se pudieron cargar los catálogos.', 'error')
  }
}

onMounted(() => {
  cargarAlumnosDesdeBD()
  cargarCatalogos()
})

// ── Watcher para query param ?ver= ──────────────────────────────────
watch(() => route.query.ver, (verControl) => {
  if (!verControl) return
  const encontrado = alumnos.value.find(
    a => String(a.numero_control) === String(verControl)
  )
  if (encontrado) abrirModalVer(encontrado)
})

// ── Modal VER alumno ─────────────────────────────────────────────────
const abrirModalVer = (alumno) => {
  alumnoSeleccionado.value = alumno
  tabActivo.value   = 'general'
  kardexData.value  = null
  horarioData.value = null
  showViewModal.value = true
}

const cerrarModalVer = () => {
  showViewModal.value = false
  setTimeout(() => { alumnoSeleccionado.value = null }, 300)
}

// Carga lazy de Kardex y Horario al cambiar pestaña
watch(tabActivo, async (tab) => {
  if (!alumnoSeleccionado.value) return
  if (tab === 'kardex'  && !kardexData.value)  await cargarKardexAlumno(alumnoSeleccionado.value)
  if (tab === 'horario' && !horarioData.value) await cargarHorarioAlumno(alumnoSeleccionado.value)
})

const cargarKardexAlumno = async (alumno) => {
  const nc = alumno.numero_control || alumno.noControl
  if (!nc) return
  cargandoKardex.value = true
  kardexError.value    = false
  try {
    const res = await fetch(`${API_URL}/api/kardex/${nc}`)
    if (!res.ok) throw new Error()
    kardexData.value = await res.json()
    if (kardexData.value?.kardex?.semestres?.length) {
      semestresAbiertos.value = [kardexData.value.kardex.semestres[0].numero]
    }
  } catch {
    kardexError.value = true
  } finally {
    cargandoKardex.value = false
  }
}

const cargarHorarioAlumno = async (alumno) => {
  const nc = alumno.numero_control || alumno.noControl
  if (!nc) return
  cargandoHorario.value = true
  try {
    const res = await fetch(`${API_URL}/api/horario/${nc}`)
    if (!res.ok) throw new Error()
    horarioData.value = await res.json()
  } catch {
    horarioData.value = []
  } finally {
    cargandoHorario.value = false
  }
}

const toggleSemestre = (num) => {
  const idx = semestresAbiertos.value.indexOf(num)
  if (idx === -1) semestresAbiertos.value.push(num)
  else            semestresAbiertos.value.splice(idx, 1)
}

const porcentajeCreditos = computed(() => {
  const a = kardexData.value?.alumno
  if (!a?.creditos_totales) return 0
  return Math.round((a.creditos_acumulados / a.creditos_totales) * 100)
})

const estiloEstado = (estado) => {
  const map = {
    acreditada: { background: '#DCFCE7', color: '#16A34A' },
    reprobada:  { background: '#FEF2F2', color: '#DC2626' },
    pendiente:  { background: '#FEF3C7', color: '#F59E0B' },
    no_cursada: { background: '#F3F4F6', color: '#6B7280' },
  }
  return map[estado] ?? map.no_cursada
}

const etiquetaEstado = (estado) => {
  const map = {
    acreditada: 'ACRED.',
    reprobada:  'REPR.',
    pendiente:  'EN CURSO',
    no_cursada: 'PENDIENTE'
  }
  return map[estado] ?? (estado || '').toUpperCase()
}

const verKardex = (alumno) => {
  const nc = alumno.numero_control || alumno.noControl
  if (nc) router.push(`/kardex/${nc}`)
}

// ── Modal EDITAR ─────────────────────────────────────────────────────
const abrirModalEditar = (alumno) => {
  alumnoEditar.value = {
    id_alumno:         alumno.id_alumno || alumno.id,
    noControl:         alumno.numero_control || alumno.noControl || '',
    nombre:            resolverNombre(alumno),
    id_carrera:        resolverIdCarrera(alumno),
    semestre:          alumno.semestre_actual || alumno.semestre || 1,
    id_estatus_alumno: alumno.id_estatus_alumno ||
      catalogos.value.estatus_alumno.find(e => e.nombre === alumno.estatus)?.id_estatus_alumno || null
  }
  showModal.value = true
}

const cerrarModal = () => { showModal.value = false }
const nuevoAlumno = () => router.push('/formulario-alumno')

const guardarCambios = async () => {
  const id = alumnoEditar.value.id_alumno
  if (!id)                              { mostrarNotificacion('No se encontró el identificador.', 'error'); return }
  if (!alumnoEditar.value.id_carrera)   { mostrarNotificacion('Selecciona una carrera.', 'error'); return }
  if (!alumnoEditar.value.id_estatus_alumno) { mostrarNotificacion('Selecciona un estatus.', 'error'); return }

  const nombreEstatus = catalogos.value.estatus_alumno
    .find(e => e.id_estatus_alumno === alumnoEditar.value.id_estatus_alumno)?.nombre || 'Activo'

  guardando.value = true
  try {
    const res = await fetch(`${API_URL}/api/alumnos/${id}`, {
      method:  'PUT',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body: JSON.stringify({
        nombre:            alumnoEditar.value.nombre,
        id_carrera:        alumnoEditar.value.id_carrera,
        semestre_actual:   parseInt(alumnoEditar.value.semestre),
        estatus:           nombreEstatus,
        id_estatus_alumno: alumnoEditar.value.id_estatus_alumno
      })
    })
    const data = await res.json()
    if (res.ok) {
      await cargarAlumnosDesdeBD()
      cerrarModal()
      mostrarNotificacion('Alumno actualizado correctamente.', 'exito')
    } else {
      mostrarNotificacion(data.message || data.error || 'Error al actualizar.', 'error')
    }
  } catch {
    mostrarNotificacion('Error de conexión.', 'error')
  } finally {
    guardando.value = false
  }
}

const solicitarEliminar = () => {
  showModal.value        = false
  showModalEliminar.value = true
}

const confirmarEliminar = async () => {
  const id = alumnoEditar.value.id_alumno
  if (!id) return
  guardando.value = true
  try {
    const res = await fetch(`${API_URL}/api/alumnos/${id}`, {
      method:  'DELETE',
      headers: { 'Accept': 'application/json' }
    })
    let data = {}
    if (res.status !== 204) data = await res.json().catch(() => ({}))
    if (res.ok) {
      await cargarAlumnosDesdeBD()
      showModalEliminar.value = false
      mostrarNotificacion('Alumno eliminado correctamente.', 'exito')
    } else {
      mostrarNotificacion(data.message || 'No se pudo eliminar el alumno.', 'error')
    }
  } catch {
    mostrarNotificacion('Error de conexión al eliminar.', 'error')
  } finally {
    guardando.value = false
  }
}
</script>

<style scoped>
/* ══════════════════════════════════════════════════════
   VARIABLES Y BASE
══════════════════════════════════════════════════════ */
.alumnos-page {
  padding: 20px 24px 40px;
  max-width: 1400px;
  margin: 0 auto;
  background: #F4F6FA;
  font-family: 'Montserrat', sans-serif;
  min-height: 100vh;
}

/* ══════════════════════════════════════════════════════
   BARRA DE CARGA GLOBAL
══════════════════════════════════════════════════════ */
.barra-carga-global {
  position: fixed;
  top: 0; left: 0; right: 0;
  height: 3px;
  z-index: 9999;
  pointer-events: none;
  opacity: 0;
  transition: opacity 0.2s;
}
.barra-carga-global.visible { opacity: 1; }
.barra-progreso {
  height: 100%;
  background: linear-gradient(90deg, #1B396A, #3B82F6, #1B396A);
  background-size: 200% 100%;
  animation: barraAnim 1.2s linear infinite;
}
@keyframes barraAnim { 0% { background-position: 100% 0; } 100% { background-position: -100% 0; } }

/* ══════════════════════════════════════════════════════
   NOTIFICACIÓN TOAST
══════════════════════════════════════════════════════ */
.notificacion-ui {
  position: fixed;
  bottom: 24px; right: 24px;
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px 20px;
  border-radius: 10px;
  font-weight: 600;
  font-size: 0.88rem;
  z-index: 10000;
  box-shadow: 0 8px 24px rgba(0,0,0,0.14);
  max-width: 380px;
  letter-spacing: 0.02em;
}
.notificacion-ui.exito  { background: #1B396A; color: #fff; }
.notificacion-ui.error  { background: #DC2626; color: #fff; }
.notif-icono            { width: 18px; height: 18px; flex-shrink: 0; }
.notif-fade-enter-active, .notif-fade-leave-active { transition: all 0.3s cubic-bezier(.4,0,.2,1); }
.notif-fade-enter-from  { opacity: 0; transform: translateY(20px); }
.notif-fade-leave-to    { opacity: 0; transform: translateY(20px); }

/* ══════════════════════════════════════════════════════
   BREADCRUMB
══════════════════════════════════════════════════════ */
.breadcrumb {
  display: flex;
  align-items: center;
  gap: 6px;
  margin-bottom: 16px;
  font-size: 0.78rem;
  font-weight: 600;
  letter-spacing: 0.05em;
  color: #9CA3AF;
}
.breadcrumb-link {
  cursor: pointer;
  color: #1B396A;
  transition: opacity 0.15s;
}
.breadcrumb-link:hover { opacity: 0.75; text-decoration: underline; }
.breadcrumb-chevron    { stroke: #D1D5DB; flex-shrink: 0; }
.breadcrumb-actual     { color: #374151; }

/* ══════════════════════════════════════════════════════
   ENCABEZADO DE PÁGINA
══════════════════════════════════════════════════════ */
.page-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 16px;
  margin-bottom: 24px;
  flex-wrap: wrap;
}
.page-header-left { display: flex; flex-direction: column; gap: 4px; }
.page-title {
  font-size: 1.7rem;
  font-weight: 800;
  color: #0F172A;
  letter-spacing: -0.01em;
  margin: 0;
}
.page-subtitle {
  font-size: 0.82rem;
  font-weight: 600;
  color: #6B7280;
  letter-spacing: 0.04em;
  margin: 0;
}

/* Botón Nueva Inscripción */
.btn-nuevo {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 20px;
  background: #1B396A;
  color: #fff;
  border: none;
  border-radius: 9px;
  font-family: 'Montserrat', sans-serif;
  font-weight: 700;
  font-size: 0.82rem;
  letter-spacing: 0.06em;
  cursor: pointer;
  transition: background 0.18s, transform 0.1s, box-shadow 0.18s;
  box-shadow: 0 2px 8px rgba(27,57,106,0.25);
  white-space: nowrap;
}
.btn-nuevo:hover  { background: #152D57; box-shadow: 0 4px 16px rgba(27,57,106,0.35); }
.btn-nuevo:active { transform: scale(0.97); }

/* ══════════════════════════════════════════════════════
   KPIs VISUALES
══════════════════════════════════════════════════════ */
.kpis-grid {
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: 14px;
  margin-bottom: 26px;
}

.kpi-card {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 16px 18px;
  border-radius: 12px;
  background: #fff;
  border: 1px solid #E5E7EB;
  box-shadow: 0 1px 4px rgba(0,0,0,0.06);
  transition: box-shadow 0.2s, transform 0.15s;
  position: relative;
  overflow: hidden;
}
.kpi-card::before {
  content: '';
  position: absolute;
  top: 0; left: 0;
  width: 4px;
  height: 100%;
  border-radius: 12px 0 0 12px;
}
.kpi-card:hover {
  box-shadow: 0 4px 18px rgba(0,0,0,0.1);
  transform: translateY(-1px);
}

/* Colores de acento KPI */
.kpi-total     { }
.kpi-total::before     { background: #1B396A; }
.kpi-activo    { }
.kpi-activo::before    { background: #16A34A; }
.kpi-temporal  { }
.kpi-temporal::before  { background: #D97706; }
.kpi-definitiva{ }
.kpi-definitiva::before{ background: #DC2626; }
.kpi-egresado  { }
.kpi-egresado::before  { background: #7C3AED; }

.kpi-icon-wrap {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 42px;
  height: 42px;
  border-radius: 10px;
  flex-shrink: 0;
}
.kpi-total     .kpi-icon-wrap { background: #EFF6FF; color: #1B396A; }
.kpi-activo    .kpi-icon-wrap { background: #F0FDF4; color: #16A34A; }
.kpi-temporal  .kpi-icon-wrap { background: #FFFBEB; color: #D97706; }
.kpi-definitiva.kpi-icon-wrap { background: #FEF2F2; color: #DC2626; }
.kpi-definitiva .kpi-icon-wrap{ background: #FEF2F2; color: #DC2626; }
.kpi-egresado  .kpi-icon-wrap { background: #F5F3FF; color: #7C3AED; }

.kpi-data { display: flex; flex-direction: column; gap: 2px; min-width: 0; }
.kpi-numero {
  font-size: 1.8rem;
  font-weight: 800;
  color: #0F172A;
  line-height: 1;
  letter-spacing: -0.02em;
}
.kpi-label {
  font-size: 0.68rem;
  font-weight: 700;
  color: #6B7280;
  letter-spacing: 0.06em;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

/* ══════════════════════════════════════════════════════
   BARRA DE ACCIONES (BUSCADOR + FILTROS)
══════════════════════════════════════════════════════ */
.barra-acciones {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 16px;
  flex-wrap: wrap;
}
.busqueda-grupo { flex: 1; min-width: 260px; }

.input-con-icono { position: relative; width: 100%; }
.icono-input {
  position: absolute;
  left: 13px;
  top: 50%;
  transform: translateY(-50%);
  color: #9CA3AF;
  pointer-events: none;
}
.input-busqueda {
  width: 100%;
  padding: 11px 40px 11px 42px;
  border: 1.5px solid #D1D5DB;
  border-radius: 9px;
  font-family: 'Montserrat', sans-serif;
  font-size: 0.88rem;
  font-weight: 600;
  letter-spacing: 0.04em;
  text-transform: uppercase;
  transition: border-color 0.2s, box-shadow 0.2s;
  box-sizing: border-box;
  background: #fff;
  color: #111827;
}
.input-busqueda::placeholder { color: #9CA3AF; font-weight: 600; }
.input-busqueda:focus {
  outline: none;
  border-color: #1B396A;
  box-shadow: 0 0 0 3px rgba(27,57,106,0.12);
}
.btn-limpiar-input {
  position: absolute;
  right: 11px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  color: #9CA3AF;
  cursor: pointer;
  display: flex;
  align-items: center;
  padding: 4px;
  border-radius: 4px;
  transition: color 0.15s;
}
.btn-limpiar-input:hover { color: #374151; }

/* Botón Filtros */
.btn-filtro {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 11px 18px;
  border: 1.5px solid #D1D5DB;
  border-radius: 9px;
  background: #fff;
  font-family: 'Montserrat', sans-serif;
  font-weight: 700;
  font-size: 0.82rem;
  letter-spacing: 0.06em;
  color: #374151;
  cursor: pointer;
  transition: all 0.18s;
  position: relative;
  white-space: nowrap;
}
.btn-filtro:hover  { border-color: #1B396A; color: #1B396A; }
.btn-filtro.activo { border-color: #1B396A; color: #1B396A; background: #EFF6FF; }
.btn-filtro.abierto{ background: #1B396A; color: #fff; border-color: #1B396A; }
.filtros-badge {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 18px;
  height: 18px;
  border-radius: 50%;
  background: #DC2626;
  color: #fff;
  font-size: 0.68rem;
  font-weight: 800;
  line-height: 1;
}

/* ══════════════════════════════════════════════════════
   PANEL DE FILTROS
══════════════════════════════════════════════════════ */
.filtros-panel {
  background: #fff;
  border: 1.5px solid #E5E7EB;
  border-radius: 12px;
  padding: 18px 20px 14px;
  margin-bottom: 18px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}
.filtros-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 16px;
}
.filtro-item { display: flex; flex-direction: column; gap: 6px; }
.filtro-label {
  display: flex;
  align-items: center;
  gap: 5px;
  font-size: 0.73rem;
  font-weight: 700;
  color: #6B7280;
  letter-spacing: 0.06em;
}
.filter-select {
  padding: 9px 12px;
  border: 1.5px solid #D1D5DB;
  border-radius: 8px;
  font-family: 'Montserrat', sans-serif;
  font-size: 0.83rem;
  font-weight: 600;
  color: #111827;
  background: #F9FAFB;
  outline: none;
  cursor: pointer;
  transition: border-color 0.18s, box-shadow 0.18s;
}
.filter-select:focus {
  border-color: #1B396A;
  box-shadow: 0 0 0 3px rgba(27,57,106,0.1);
  background: #fff;
}
.filtros-footer {
  display: flex;
  justify-content: flex-end;
  margin-top: 14px;
  padding-top: 12px;
  border-top: 1px solid #F3F4F6;
}
.btn-limpiar-filtros {
  display: flex;
  align-items: center;
  gap: 7px;
  padding: 7px 14px;
  background: none;
  border: 1.5px solid #D1D5DB;
  border-radius: 7px;
  font-family: 'Montserrat', sans-serif;
  font-size: 0.78rem;
  font-weight: 700;
  letter-spacing: 0.04em;
  color: #6B7280;
  cursor: pointer;
  transition: all 0.15s;
}
.btn-limpiar-filtros:hover { border-color: #DC2626; color: #DC2626; }

/* Transición filtros */
.filtros-slide-enter-active, .filtros-slide-leave-active { transition: all 0.25s cubic-bezier(.4,0,.2,1); }
.filtros-slide-enter-from, .filtros-slide-leave-to { opacity: 0; transform: translateY(-10px); }

/* ══════════════════════════════════════════════════════
   ESTADOS: CARGANDO / VACÍO
══════════════════════════════════════════════════════ */
.estado-cargando {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 80px 20px;
  gap: 16px;
  color: #6B7280;
  font-weight: 600;
  font-size: 0.85rem;
  letter-spacing: 0.05em;
}
.spinner-cards {
  width: 36px;
  height: 36px;
  border: 3px solid #E5E7EB;
  border-top-color: #1B396A;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

.estado-vacio {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 80px 20px;
  gap: 12px;
  text-align: center;
}
.icono-vacio { width: 52px; height: 52px; stroke: #D1D5DB; }
.estado-vacio h3 {
  font-size: 1.05rem;
  font-weight: 800;
  color: #374151;
  letter-spacing: 0.05em;
  margin: 0;
}
.estado-vacio p {
  font-size: 0.85rem;
  color: #9CA3AF;
  font-weight: 600;
  margin: 0;
  letter-spacing: 0.03em;
}
.btn-limpiar-vacio {
  margin-top: 8px;
  padding: 9px 22px;
  border-radius: 8px;
  border: 1.5px solid #1B396A;
  background: none;
  color: #1B396A;
  font-family: 'Montserrat', sans-serif;
  font-weight: 700;
  font-size: 0.82rem;
  letter-spacing: 0.05em;
  cursor: pointer;
  transition: background 0.15s;
}
.btn-limpiar-vacio:hover { background: #EFF6FF; }

/* ══════════════════════════════════════════════════════
   GRID DE CARDS DE ALUMNOS
══════════════════════════════════════════════════════ */
.alumnos-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 16px;
  margin-bottom: 26px;
  outline: none;
}

.alumno-card {
  position: relative;
  background: #fff;
  border-radius: 14px;
  border: 1.5px solid #E5E7EB;
  overflow: hidden;
  cursor: pointer;
  transition: box-shadow 0.2s, transform 0.18s, border-color 0.18s;
  display: flex;
  flex-direction: column;
}
.alumno-card:hover {
  box-shadow: 0 8px 28px rgba(0,0,0,0.12);
  transform: translateY(-3px);
  border-color: #C7D2E6;
}
.alumno-card.card-activa {
  outline: 2px solid #1B396A;
  outline-offset: 2px;
  box-shadow: 0 8px 28px rgba(27,57,106,0.18);
}

/* Acento de color superior por estatus */
.card-acento {
  height: 4px;
  width: 100%;
  flex-shrink: 0;
}
.acento-activo     { background: #16A34A; }
.acento-temporal   { background: #D97706; }
.acento-definitiva { background: #DC2626; }
.acento-titulado   { background: #7C3AED; }
.acento-egresado   { background: #0891B2; }
.acento-desconocido{ background: #9CA3AF; }

/* Parte superior de la card */
.card-top {
  display: flex;
  align-items: flex-start;
  gap: 13px;
  padding: 16px 16px 12px;
}

/* Avatar */
.card-avatar-wrap {
  position: relative;
  flex-shrink: 0;
}
.card-avatar-img {
  width: 52px;
  height: 52px;
  border-radius: 12px;
  object-fit: cover;
  border: 2px solid #E5E7EB;
}
.card-avatar-placeholder {
  width: 52px;
  height: 52px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 2px solid transparent;
}
.card-avatar-placeholder span {
  font-size: 1.1rem;
  font-weight: 800;
  color: #fff;
  letter-spacing: -0.01em;
}

/* Paleta de 5 colores para avatares */
.avatar-0 { background: linear-gradient(135deg, #1B396A, #2563EB); border-color: #BFDBFE; }
.avatar-1 { background: linear-gradient(135deg, #7C3AED, #A78BFA); border-color: #EDE9FE; }
.avatar-2 { background: linear-gradient(135deg, #0891B2, #38BDF8); border-color: #E0F2FE; }
.avatar-3 { background: linear-gradient(135deg, #059669, #34D399); border-color: #D1FAE5; }
.avatar-4 { background: linear-gradient(135deg, #DC2626, #F87171); border-color: #FEE2E2; }

/* Punto indicador de estatus */
.avatar-dot {
  position: absolute;
  bottom: 2px;
  right: 2px;
  width: 11px;
  height: 11px;
  border-radius: 50%;
  border: 2px solid #fff;
}
.dot-activo     { background: #16A34A; }
.dot-temporal   { background: #D97706; }
.dot-definitiva { background: #DC2626; }
.dot-titulado   { background: #7C3AED; }
.dot-egresado   { background: #0891B2; }
.dot-desconocido{ background: #9CA3AF; }

/* Info de la card */
.card-info { flex: 1; min-width: 0; display: flex; flex-direction: column; gap: 2px; }
.card-control {
  font-size: 0.73rem;
  font-weight: 700;
  font-family: 'Courier New', monospace;
  color: #1B396A;
  letter-spacing: 0.04em;
}
.card-nombre {
  font-size: 0.92rem;
  font-weight: 800;
  color: #0F172A;
  margin: 0;
  line-height: 1.2;
  letter-spacing: -0.01em;
  overflow: hidden;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
}
.card-carrera {
  font-size: 0.73rem;
  font-weight: 600;
  color: #6B7280;
  margin: 0;
  letter-spacing: 0.02em;
  overflow: hidden;
  white-space: nowrap;
  text-overflow: ellipsis;
}

/* Divisor */
.card-divider {
  height: 1px;
  background: #F3F4F6;
  margin: 0 16px;
}

/* Parte inferior de la card */
.card-bottom {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 10px 16px 14px;
  gap: 8px;
}
.card-meta {
  display: flex;
  flex-direction: column;
  gap: 5px;
}
.card-meta-item {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 0.72rem;
  font-weight: 700;
  color: #6B7280;
  letter-spacing: 0.04em;
}
.card-meta-item svg { stroke: #9CA3AF; flex-shrink: 0; }

/* Badges de estatus */
.estatus-badge {
  display: inline-flex;
  align-items: center;
  padding: 3px 9px;
  border-radius: 20px;
  font-size: 0.67rem;
  font-weight: 800;
  letter-spacing: 0.05em;
  width: fit-content;
}
.badge-activo     { background: #DCFCE7; color: #15803D; }
.badge-temporal   { background: #FEF3C7; color: #B45309; }
.badge-definitiva { background: #FEE2E2; color: #B91C1C; }
.badge-titulado   { background: #EDE9FE; color: #6D28D9; }
.badge-egresado   { background: #E0F2FE; color: #0369A1; }
.badge-desconocido{ background: #F3F4F6; color: #6B7280; }

/* Acciones de la card */
.card-acciones {
  display: flex;
  align-items: center;
  gap: 5px;
  flex-shrink: 0;
}
.btn-card-accion {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  border-radius: 8px;
  border: 1.5px solid transparent;
  cursor: pointer;
  transition: all 0.15s;
  background: #F8FAFC;
}
.btn-card-accion svg { flex-shrink: 0; }

.btn-ver    { color: #1B396A; }
.btn-ver:hover    { background: #EFF6FF; border-color: #BFDBFE; }

.btn-editar { color: #D97706; }
.btn-editar:hover { background: #FFFBEB; border-color: #FDE68A; }

.btn-kardex { color: #7C3AED; }
.btn-kardex:hover { background: #F5F3FF; border-color: #DDD6FE; }

/* ══════════════════════════════════════════════════════
   PAGINACIÓN
══════════════════════════════════════════════════════ */
.paginacion {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  flex-wrap: wrap;
  padding: 14px 18px;
  background: #fff;
  border: 1.5px solid #E5E7EB;
  border-radius: 12px;
  box-shadow: 0 1px 4px rgba(0,0,0,0.05);
}
.paginacion-izquierda {
  display: flex;
  align-items: center;
  gap: 8px;
}
.pag-label {
  font-size: 0.75rem;
  font-weight: 700;
  color: #6B7280;
  letter-spacing: 0.04em;
}
.select-filas {
  padding: 5px 10px;
  border: 1.5px solid #E5E7EB;
  border-radius: 7px;
  font-family: 'Montserrat', sans-serif;
  font-size: 0.8rem;
  font-weight: 700;
  color: #374151;
  cursor: pointer;
  background: #F9FAFB;
}
.paginacion-centro {
  font-size: 0.78rem;
  font-weight: 600;
  color: #6B7280;
  letter-spacing: 0.04em;
}
.paginacion-derecha { display: flex; gap: 4px; }

.btn-pag {
  display: flex;
  align-items: center;
  justify-content: center;
  min-width: 34px;
  height: 34px;
  padding: 0 4px;
  border: 1.5px solid #E5E7EB;
  border-radius: 8px;
  background: #fff;
  font-family: 'Montserrat', sans-serif;
  font-size: 0.82rem;
  font-weight: 700;
  color: #374151;
  cursor: pointer;
  transition: all 0.15s;
}
.btn-pag:hover:not(:disabled) { border-color: #1B396A; color: #1B396A; background: #EFF6FF; }
.btn-pag.activo { background: #1B396A; color: #fff; border-color: #1B396A; }
.btn-pag:disabled { opacity: 0.35; cursor: not-allowed; }

/* ══════════════════════════════════════════════════════
   PIE DE PÁGINA
══════════════════════════════════════════════════════ */
.pie-pagina {
  text-align: center;
  margin-top: 28px;
  font-size: 0.72rem;
  font-weight: 600;
  letter-spacing: 0.06em;
  color: #9CA3AF;
}

/* ══════════════════════════════════════════════════════
   MODALES — BASE
══════════════════════════════════════════════════════ */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(15,23,42,0.55);
  backdrop-filter: blur(3px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 16px;
}
.modal-content {
  background: #fff;
  border-radius: 16px;
  width: 100%;
  max-width: 560px;
  max-height: 90vh;
  display: flex;
  flex-direction: column;
  box-shadow: 0 20px 60px rgba(0,0,0,0.2);
  overflow: hidden;
}
.modal-ver-alumno  { max-width: 640px; }
.modal-confirmar   { max-width: 440px; }

/* Header del modal */
.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 20px 22px 18px;
  background: linear-gradient(135deg, #1B396A 0%, #1D4ED8 100%);
  flex-shrink: 0;
}
.modal-header-avatar-group { display: flex; gap: 14px; align-items: center; }
.detalle-avatar { flex-shrink: 0; }
.avatar-img {
  width: 50px;
  height: 50px;
  border-radius: 12px;
  object-fit: cover;
  border: 2px solid rgba(255,255,255,0.35);
}
.avatar-placeholder {
  width: 50px;
  height: 50px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 2px solid rgba(255,255,255,0.3);
}
.avatar-placeholder span { color: white; font-weight: 800; font-size: 1.1rem; }

.modal-header-info { display: flex; flex-direction: column; gap: 3px; }
.modal-header-tag {
  font-size: 0.68rem;
  font-weight: 700;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: rgba(255,255,255,0.7);
}
.modal-header h3  { margin: 0; font-size: 1.1rem; font-weight: 800; color: #fff; letter-spacing: -0.01em; }
.sub-header-control { font-size: 0.82rem; font-weight: 700; font-family: monospace; color: rgba(255,255,255,0.8); }

.btn-cerrar-modal {
  background: rgba(255,255,255,0.1);
  border: 1px solid rgba(255,255,255,0.2);
  color: white;
  cursor: pointer;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
  flex-shrink: 0;
  transition: background 0.15s;
}
.btn-cerrar-modal:hover { background: rgba(255,255,255,0.2); }

/* ── Pestañas ── */
.modal-body-tabs { display: flex; flex-direction: column; flex: 1; overflow: hidden; }
.detalle-tabs    { display: flex; background: #F8FAFC; border-bottom: 1px solid #E5E7EB; flex-shrink: 0; }
.tab-btn {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  padding: 12px 8px;
  background: none;
  border: none;
  border-bottom: 2px solid transparent;
  cursor: pointer;
  font-size: 0.78rem;
  font-weight: 700;
  letter-spacing: 0.05em;
  color: #9CA3AF;
  font-family: 'Montserrat', sans-serif;
  transition: all 0.15s;
}
.tab-btn:hover  { color: #1B396A; }
.tab-btn.activo { color: #1B396A; border-bottom-color: #1B396A; background: #fff; }

.tab-contenido-scroll { overflow-y: auto; padding: 1.4rem 1.6rem; flex: 1; }
.tab-panel { display: flex; flex-direction: column; gap: 1rem; }

/* ── Detalle grid ── */
.detalle-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0.9rem; }
.detalle-campo {
  display: flex;
  flex-direction: column;
  gap: 4px;
  padding: 0.8rem 1rem;
  background: #F8FAFC;
  border-radius: 9px;
  border: 1px solid #E5E7EB;
}
.detalle-campo.full-width { grid-column: 1 / -1; }
.detalle-label {
  font-size: 0.68rem;
  font-weight: 700;
  color: #9CA3AF;
  letter-spacing: 0.08em;
}
.detalle-valor  { font-size: 0.92rem; font-weight: 600; color: #1A1A1A; }
.detalle-numero { font-size: 1.1rem;  font-weight: 800; color: #1B396A; }
.mono-bold { font-family: 'Courier New', monospace; font-weight: 800; font-size: 1rem; color: #1B396A; }

/* Badges en modal */
.estatus-badge-modal {
  display: inline-flex;
  align-items: center;
  padding: 5px 14px;
  border-radius: 20px;
  font-size: 0.78rem;
  font-weight: 800;
  letter-spacing: 0.06em;
  width: fit-content;
}

/* ── Créditos / Kardex ── */
.creditos-bloque {
  background: #F8FAFC;
  border-radius: 10px;
  padding: 12px 14px;
  border: 1px solid #E5E7EB;
}
.creditos-row  { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; font-size: 0.85rem; }
.creditos-label{ font-weight: 700; color: #1A1A1A; letter-spacing: 0.04em; font-size: 0.8rem; }
.creditos-pct  { font-weight: 700; color: #6B7280; font-size: 0.8rem; }
.creditos-pct.verde { color: #16A34A; }
.creditos-barra-track { height: 8px; background: #E5E7EB; border-radius: 4px; overflow: hidden; }
.creditos-barra-fill  { height: 100%; border-radius: 4px; transition: width 0.8s ease; }

.kardex-semestres { display: flex; flex-direction: column; gap: 0.5rem; }
.semestre-bloque  { border: 1px solid #E5E7EB; border-radius: 10px; overflow: hidden; background: white; }
.semestre-btn {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 10px 14px;
  background: none;
  border: none;
  cursor: pointer;
  font-family: 'Montserrat', sans-serif;
  transition: background 0.15s;
}
.semestre-btn:hover  { background: #F8FAFC; }
.semestre-btn.abierto{ background: #EFF6FF; }
.semestre-titulo { font-size: 0.85rem; font-weight: 800; color: #1A1A1A; letter-spacing: 0.04em; }
.semestre-badges { display: flex; align-items: center; gap: 5px; }
.badge-mini { font-size: 0.68rem; font-weight: 800; padding: 2px 8px; border-radius: 20px; letter-spacing: 0.04em; }
.badge-mini.acreditadas { background: #DCFCE7; color: #16A34A; }
.badge-mini.reprobadas  { background: #FEE2E2; color: #DC2626; }
.flecha-semestre { stroke: #6B7280; transition: transform 0.2s; flex-shrink: 0; }
.flecha-semestre.girado { transform: rotate(180deg); }

.semestre-materias { border-top: 1px solid #E5E7EB; }
.tabla-mini { width: 100%; border-collapse: collapse; font-size: 0.8rem; }
.tabla-mini th {
  background: #F8FAFC;
  padding: 7px 12px;
  text-align: left;
  font-size: 0.68rem;
  font-weight: 800;
  color: #9CA3AF;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  border-bottom: 1px solid #E5E7EB;
}
.tabla-mini td      { padding: 7px 12px; border-bottom: 1px solid #F9FAFB; vertical-align: middle; color: #1A1A1A; font-weight: 500; }
.tabla-mini tr.fila-repr { background: #FEF2F2; }
.tabla-mini td.centrado  { text-align: center; }
.clave-mono { font-family: monospace; font-size: 0.78rem; font-weight: 800; color: #1B396A; }
.badge-estado-mini { font-size: 0.68rem; font-weight: 800; padding: 2px 8px; border-radius: 10px; display: inline-block; letter-spacing: 0.03em; }
.text-verde { color: #16A34A; }
.text-rojo  { color: #DC2626; }
.text-gris  { color: #9CA3AF; }

/* ── Horario ── */
.horario-lista { display: flex; flex-direction: column; gap: 0.6rem; }
.horario-item  { display: flex; align-items: stretch; border: 1px solid #E5E7EB; border-radius: 9px; overflow: hidden; background: white; }
.horario-color { width: 5px; flex-shrink: 0; }
.horario-info  { padding: 10px 12px; display: flex; flex-direction: column; gap: 2px; }
.horario-nombre{ font-weight: 800; font-size: 0.86rem; color: #1A1A1A; }
.horario-meta  { font-size: 0.75rem; color: #6B7280; font-weight: 600; }
.horario-aula  { font-size: 0.72rem; color: #9CA3AF; font-weight: 600; letter-spacing: 0.03em; }

/* ── Kardex skeleton ── */
.kardex-cargando { display: flex; flex-direction: column; gap: 8px; }
.skeleton-linea  {
  height: 14px; border-radius: 6px;
  background: linear-gradient(90deg, #E5E7EB 25%, #F3F4F6 50%, #E5E7EB 75%);
  background-size: 200% 100%;
  animation: shimmer 1.4s infinite;
}
.skeleton-linea.largo { width: 70%; }
.skeleton-linea.medio { width: 45%; }
.skeleton-fila {
  height: 40px; border-radius: 8px;
  background: linear-gradient(90deg, #F3F4F6 25%, #FFFFFF 50%, #F3F4F6 75%);
  background-size: 200% 100%;
  animation: shimmer 1.4s infinite;
}
@keyframes shimmer { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }

.kardex-error {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 14px;
  background: #FEF2F2;
  border-radius: 8px;
  font-size: 0.85rem;
  font-weight: 600;
  color: #DC2626;
}
.kardex-error button {
  margin-left: auto;
  padding: 5px 12px;
  border-radius: 6px;
  border: 1.5px solid #DC2626;
  background: none;
  color: #DC2626;
  font-family: 'Montserrat', sans-serif;
  font-weight: 700;
  font-size: 0.78rem;
  letter-spacing: 0.04em;
  cursor: pointer;
}
.kardex-vacio {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 30px;
  text-align: center;
  color: #9CA3AF;
  font-size: 0.85rem;
  font-weight: 600;
  letter-spacing: 0.04em;
  gap: 8px;
}

/* ── Formulario modal ── */
.form-body { padding: 1.4rem 1.6rem; overflow-y: auto; }
.form-grupo { margin-bottom: 1.1rem; }
.form-grupo-doble { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
.form-grupo label {
  display: block;
  margin-bottom: 6px;
  font-weight: 700;
  font-size: 0.78rem;
  letter-spacing: 0.06em;
  color: #374151;
}
.obligatorio { color: #DC2626; }
.modal-input, .modal-select {
  width: 100%;
  padding: 10px 14px;
  border: 1.5px solid #E5E7EB;
  border-radius: 9px;
  font-size: 0.9rem;
  font-weight: 600;
  background: #fff;
  color: #1A1A1A;
  font-family: 'Montserrat', sans-serif;
  outline: none;
  transition: border-color 0.2s, box-shadow 0.2s;
  box-sizing: border-box;
}
.modal-input:focus, .modal-select:focus {
  border-color: #1B396A;
  box-shadow: 0 0 0 3px rgba(27,57,106,0.12);
}
.modal-input.deshabilitado { background: #F5F7FA; cursor: not-allowed; color: #9CA3AF; }

/* ── Footer modal ── */
.modal-footer {
  padding: 14px 20px;
  background: #F8FAFC;
  display: flex;
  gap: 8px;
  justify-content: flex-end;
  border-top: 1px solid #E5E7EB;
  flex-shrink: 0;
}
.btn-secundario {
  padding: 9px 18px;
  border-radius: 9px;
  font-weight: 700;
  cursor: pointer;
  font-family: 'Montserrat', sans-serif;
  background: #fff;
  color: #374151;
  border: 1.5px solid #D1D5DB;
  transition: background 0.15s;
  font-size: 0.82rem;
  letter-spacing: 0.04em;
}
.btn-secundario:hover:not(:disabled) { background: #F3F4F6; }
.btn-secundario:disabled { opacity: 0.5; cursor: not-allowed; }

.btn-eliminar {
  padding: 9px 18px;
  border-radius: 9px;
  font-weight: 700;
  cursor: pointer;
  font-family: 'Montserrat', sans-serif;
  background: #DC2626;
  color: white;
  border: 1.5px solid #DC2626;
  transition: background 0.15s;
  font-size: 0.82rem;
  letter-spacing: 0.04em;
  display: flex;
  align-items: center;
  gap: 6px;
}
.btn-eliminar:hover:not(:disabled) { background: #B91C1C; }
.btn-eliminar:disabled { opacity: 0.5; cursor: not-allowed; }

.btn-guardar {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 9px 20px;
  border-radius: 9px;
  font-weight: 700;
  cursor: pointer;
  font-family: 'Montserrat', sans-serif;
  background: #1B396A;
  color: white;
  border: 1.5px solid #1B396A;
  transition: background 0.15s;
  font-size: 0.82rem;
  letter-spacing: 0.04em;
}
.btn-guardar:hover:not(:disabled) { background: #152D57; }
.btn-guardar:disabled { opacity: 0.55; cursor: not-allowed; }

.btn-accion-outline {
  display: flex;
  align-items: center;
  gap: 7px;
  padding: 9px 16px;
  border-radius: 9px;
  font-weight: 700;
  cursor: pointer;
  font-family: 'Montserrat', sans-serif;
  background: #fff;
  color: #1B396A;
  border: 1.5px solid #1B396A;
  transition: background 0.15s;
  font-size: 0.82rem;
  letter-spacing: 0.04em;
}
.btn-accion-outline:hover { background: #EFF6FF; }

/* ── Spinner botón ── */
.spinner-btn {
  display: inline-block;
  width: 13px;
  height: 13px;
  border: 2px solid rgba(255,255,255,0.4);
  border-top-color: #fff;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
  flex-shrink: 0;
}

/* ── Confirmación ── */
.confirmar-body {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
  text-align: center;
  padding: 2rem 1.6rem;
}
.confirmar-icono { width: 52px; height: 52px; stroke: #F59E0B; }
.confirmar-body p {
  color: #374151;
  font-size: 0.9rem;
  font-weight: 600;
  margin: 0;
  line-height: 1.5;
  letter-spacing: 0.02em;
}

/* ── Transición expand (semestres Kardex) ── */
.expand-enter-active, .expand-leave-active { transition: all 0.25s ease; overflow: hidden; }
.expand-enter-from, .expand-leave-to { max-height: 0; opacity: 0; }
.expand-enter-to, .expand-leave-from { max-height: 500px; opacity: 1; }

/* ── Transición modal ── */
.modal-fade-enter-active, .modal-fade-leave-active { transition: all 0.25s cubic-bezier(.4,0,.2,1); }
.modal-fade-enter-from, .modal-fade-leave-to { opacity: 0; }
.modal-fade-enter-from .modal-content, .modal-fade-leave-to .modal-content { transform: scale(0.95) translateY(10px); }

/* ══════════════════════════════════════════════════════
   RESPONSIVE
══════════════════════════════════════════════════════ */
@media (max-width: 1200px) {
  .kpis-grid { grid-template-columns: repeat(3, 1fr); }
}
@media (max-width: 900px) {
  .kpis-grid { grid-template-columns: repeat(2, 1fr); }
  .alumnos-grid { grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); }
  .filtros-grid { grid-template-columns: 1fr 1fr; }
}
@media (max-width: 600px) {
  .alumnos-page    { padding: 14px 14px 32px; }
  .kpis-grid       { grid-template-columns: 1fr 1fr; }
  .alumnos-grid    { grid-template-columns: 1fr; }
  .filtros-grid    { grid-template-columns: 1fr; }
  .detalle-grid    { grid-template-columns: 1fr; }
  .form-grupo-doble{ grid-template-columns: 1fr; }
  .modal-footer    { flex-direction: column; }
  .modal-footer button { width: 100%; justify-content: center; }
  .paginacion      { flex-direction: column; gap: 10px; }
  .page-header     { flex-direction: column; align-items: flex-start; }
  .btn-nuevo       { width: 100%; justify-content: center; }
}
</style>