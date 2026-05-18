<template>
  <MainLayout v-slot="{ busquedaGlobal }">
    <div class="alumnos-page">

      <!-- ── Barra de carga superior ── -->
      <div class="barra-carga-global" :class="{ visible: cargando }">
        <div class="barra-progreso"></div>
      </div>

      <!-- ── Notificación ── -->
      <transition name="notif-fade">
        <div v-if="notificacion.visible" class="notificacion-ui" :class="notificacion.tipo">
          <svg v-if="notificacion.tipo === 'exito'" class="notif-icono" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <svg v-else class="notif-icono" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          {{ notificacion.mensaje }}
        </div>
      </transition>

      <!-- ══════════════════════════════════════════════ -->
      <!-- PANEL IZQUIERDO: Búsqueda + Lista             -->
      <!-- PANEL DERECHO: Detalle del alumno             -->
      <!-- ══════════════════════════════════════════════ -->
      <div class="layout-principal" :class="{ 'con-detalle': alumnoSeleccionado }">

        <!-- ── Panel lista ── -->
        <div class="panel-lista">

          <!-- Header -->
          <div class="page-header">
            <div>
              <h1 class="page-title">Alumnos</h1>
              <span class="page-subtitle">{{ alumnosFiltrados.length }} registro(s)</span>
            </div>
            <button class="btn-nuevo" @click="nuevoAlumno">
              <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" height="16">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              Nueva inscripción
            </button>
          </div>

          <!-- ── Barra de búsqueda principal ── -->
          <div class="busqueda-card">
            <div class="busqueda-wrap" :class="{ activo: busquedaAlumno.length > 0, cargando: cargandoBusqueda }">
              <svg class="busqueda-icono" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
              <input
                ref="inputBusqueda"
                type="text"
                v-model="busquedaAlumno"
                placeholder="Buscar por número de control o nombre..."
                class="busqueda-input"
                @keydown.enter="aplicarBusqueda"
                @keydown.escape="limpiarBusqueda"
              />
              <span v-if="cargandoBusqueda" class="spinner-busqueda"></span>
              <button v-if="busquedaAlumno && !cargandoBusqueda" class="btn-limpiar-input" @click="limpiarBusqueda">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="14" height="14">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
          </div>

          <!-- ── Filtros ── -->
          <div class="filters-bar">
            <select v-model="filtroCarreraId" class="filter-select" @change="currentPage = 1">
              <option value="">Carrera</option>
              <option v-for="c in catalogos.carreras" :key="c.id_carrera" :value="c.id_carrera">{{ c.nombre }}</option>
            </select>
            <select v-model="filtroSemestre" class="filter-select" @change="currentPage = 1">
              <option value="">Semestre</option>
              <option v-for="n in 8" :key="n" :value="String(n)">{{ n }}°</option>
            </select>
            <select v-model="filtroEstatusId" class="filter-select" @change="currentPage = 1">
              <option value="">Estatus</option>
              <option v-for="e in catalogos.estatus_alumno" :key="e.id_estatus_alumno" :value="e.id_estatus_alumno">{{ e.nombre }}</option>
            </select>
            <button class="btn-limpiar" @click="resetFilters" title="Limpiar filtros">
              <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="14" height="14">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
              </svg>
              Limpiar
            </button>
          </div>

          <!-- ── Tabla ── -->
          <div class="table-container">

            <!-- Estado: cargando -->
            <div v-if="cargando && alumnos.length === 0" class="estado-cargando">
              <div class="spinner-tabla"></div>
              <p>Cargando registros...</p>
            </div>

            <!-- Tabla con datos -->
            <table
              v-else-if="paginatedAlumnos.length > 0"
              class="alumnos-table"
              ref="tablaRef"
              @keydown="navegarTeclado"
              tabindex="0"
            >
              <thead>
                <tr>
                  <th>No. Control</th>
                  <th>Nombre</th>
                  <th class="col-carrera">Carrera</th>
                  <th class="col-semestre centrado">Sem.</th>
                  <th class="centrado">Estatus</th>
                  <th class="centrado">Acciones</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="(alumno, index) in paginatedAlumnos"
                  :key="alumno.id_alumno || alumno.id"
                  :class="{
                    'fila-seleccionada': alumnoSeleccionado?.id_alumno === alumno.id_alumno,
                    'fila-activa-teclado': filaActiva === index
                  }"
                  @click="seleccionarAlumno(alumno)"
                >
                  <td class="celda-control">{{ alumno.numero_control || alumno.noControl }}</td>
                  <td class="celda-nombre">{{ resolverNombre(alumno) }}</td>
                  <td class="col-carrera celda-carrera">{{ resolverCarrera(alumno) }}</td>
                  <td class="col-semestre centrado">{{ alumno.semestre_actual || alumno.semestre }}°</td>
                  <td class="centrado">
                    <span class="estatus-badge" :class="claseEstatus(alumno.estatus)">{{ alumno.estatus }}</span>
                  </td>
                  <td class="centrado celda-acciones">
                    <button class="btn-icono ver" @click.stop="abrirModalVer(alumno)" title="Ver detalles">
                      <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>
                    <button class="btn-icono editar" @click.stop="abrirModalEditar(alumno)" title="Editar">
                      <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>

            <!-- Estado: sin resultados -->
            <div v-else class="estado-vacio">
              <svg class="icono-vacio" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <h3>Sin resultados</h3>
              <p>No se encontraron alumnos con los criterios aplicados.</p>
              <button class="btn-limpiar-vacio" @click="resetFilters">Limpiar filtros</button>
            </div>
          </div>

          <!-- ── Paginación ── -->
          <div class="paginacion">
            <div class="paginacion-izquierda">
              Filas:
              <select v-model="filasPorPagina" @change="currentPage = 1" class="select-filas">
                <option :value="10">10</option>
                <option :value="20">20</option>
                <option :value="50">50</option>
              </select>
            </div>
            <div class="paginacion-centro">{{ currentPage }}/{{ totalPages }}</div>
            <div class="paginacion-derecha">
              <button class="btn-pag" @click="prevPage" :disabled="currentPage === 1">‹</button>
              <button v-for="page in visiblePages" :key="page" class="btn-pag" :class="{ activo: page === currentPage }" @click="goToPage(page)">{{ page }}</button>
              <button class="btn-pag" @click="nextPage" :disabled="currentPage === totalPages">›</button>
            </div>
          </div>
        </div>

        <!-- ══════════════════════════════════════════════ -->
        <!-- PANEL DERECHO: Perfil completo del alumno     -->
        <!-- ══════════════════════════════════════════════ -->
        <transition name="panel-slide">
          <div v-if="alumnoSeleccionado" class="panel-detalle">

            <!-- Header del panel -->
            <div class="detalle-header">
              <div class="detalle-avatar">
                <img v-if="alumnoSeleccionado.foto" :src="alumnoSeleccionado.foto" class="avatar-img" :alt="resolverNombre(alumnoSeleccionado)" />
                <div v-else class="avatar-placeholder">
                  <span>{{ iniciales(resolverNombre(alumnoSeleccionado)) }}</span>
                </div>
              </div>
              <div class="detalle-header-info">
                <h2 class="detalle-nombre">{{ resolverNombre(alumnoSeleccionado) }}</h2>
                <span class="detalle-control">{{ alumnoSeleccionado.numero_control || alumnoSeleccionado.noControl }}</span>
                <span class="estatus-badge" :class="claseEstatus(alumnoSeleccionado.estatus)">{{ alumnoSeleccionado.estatus }}</span>
              </div>
              <button class="btn-cerrar-panel" @click="cerrarDetalle" title="Cerrar">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="18" height="18">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            <!-- Tabs de navegación -->
            <div class="detalle-tabs">
              <button
                v-for="tab in tabs"
                :key="tab.id"
                class="tab-btn"
                :class="{ activo: tabActivo === tab.id }"
                @click="tabActivo = tab.id"
              >
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="15" height="15">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="tab.icon" />
                </svg>
                {{ tab.label }}
              </button>
            </div>

            <!-- ── Tab: Datos Generales ── -->
            <div v-if="tabActivo === 'general'" class="tab-contenido">
              <div class="info-grid">
                <div class="info-item">
                  <span class="info-label">No. de Control</span>
                  <span class="info-valor mono">{{ alumnoSeleccionado.numero_control || alumnoSeleccionado.noControl }}</span>
                </div>
                <div class="info-item">
                  <span class="info-label">Nombre completo</span>
                  <span class="info-valor">{{ resolverNombre(alumnoSeleccionado) }}</span>
                </div>
                <div class="info-item">
                  <span class="info-label">Carrera</span>
                  <span class="info-valor">{{ resolverCarrera(alumnoSeleccionado) }}</span>
                </div>
                <div class="info-item">
                  <span class="info-label">Semestre actual</span>
                  <span class="info-valor">{{ alumnoSeleccionado.semestre_actual || alumnoSeleccionado.semestre }}° Semestre</span>
                </div>
                <div class="info-item">
                  <span class="info-label">Estatus académico</span>
                  <span class="estatus-badge" :class="claseEstatus(alumnoSeleccionado.estatus)">{{ alumnoSeleccionado.estatus }}</span>
                </div>
                <div class="info-item" v-if="alumnoSeleccionado.fecha_ingreso">
                  <span class="info-label">Fecha de ingreso</span>
                  <span class="info-valor">{{ formatearFecha(alumnoSeleccionado.fecha_ingreso) }}</span>
                </div>
                <div class="info-item" v-if="alumnoSeleccionado.plan_estudio">
                  <span class="info-label">Plan de estudios</span>
                  <span class="info-valor">{{ alumnoSeleccionado.plan_estudio }}</span>
                </div>
                <div class="info-item" v-if="alumnoSeleccionado.persona?.genero">
                  <span class="info-label">Género</span>
                  <span class="info-valor">{{ alumnoSeleccionado.persona.genero }}</span>
                </div>
              </div>

              <div class="detalle-acciones">
                <button class="btn-accion-detalle primario" @click="verKardex(alumnoSeleccionado)">
                  <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" height="16">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 9.414V19a2 2 0 01-2 2z"/>
                  </svg>
                  Ver Kardex completo
                </button>
                <button class="btn-accion-detalle secundario" @click="abrirModalEditar(alumnoSeleccionado)">
                  <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" height="16">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                  </svg>
                  Editar datos
                </button>
              </div>
            </div>

            <!-- ── Tab: Kardex ── -->
            <div v-if="tabActivo === 'kardex'" class="tab-contenido">
              <div v-if="cargandoKardex" class="kardex-cargando">
                <div class="skeleton-linea largo"></div>
                <div class="skeleton-linea medio"></div>
                <div v-for="i in 4" :key="i" class="skeleton-fila"></div>
              </div>
              <div v-else-if="kardexError" class="kardex-error">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="20" height="20">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>No se pudo cargar el kardex.</span>
                <button @click="cargarKardexAlumno(alumnoSeleccionado)">Reintentar</button>
              </div>
              <div v-else-if="kardexData">
                <!-- Avance en créditos -->
                <div class="creditos-bloque" v-if="kardexData.alumno?.creditos_totales">
                  <div class="creditos-row">
                    <span class="creditos-label">Avance de créditos</span>
                    <span class="creditos-pct" :class="{ verde: porcentajeCreditos >= 80 }">{{ kardexData.alumno.creditos_acumulados }}/{{ kardexData.alumno.creditos_totales }} ({{ porcentajeCreditos }}%)</span>
                  </div>
                  <div class="creditos-barra-track">
                    <div class="creditos-barra-fill" :style="{ width: porcentajeCreditos + '%', background: porcentajeCreditos >= 80 ? '#16A34A' : '#1B396A' }"></div>
                  </div>
                </div>
                <!-- Semestres acordeón -->
                <div v-if="kardexData.kardex?.semestres?.length" class="kardex-semestres">
                  <div v-for="semestre in kardexData.kardex.semestres" :key="semestre.numero" class="semestre-bloque">
                    <button class="semestre-btn" @click="toggleSemestre(semestre.numero)" :class="{ abierto: semestresAbiertos.includes(semestre.numero) }">
                      <span class="semestre-titulo">Semestre {{ semestre.numero }}</span>
                      <div class="semestre-badges">
                        <span class="badge-mini acreditadas">{{ semestre.acreditadas }} acred.</span>
                        <span v-if="semestre.reprobadas > 0" class="badge-mini reprobadas">{{ semestre.reprobadas }} repr.</span>
                        <svg class="flecha-semestre" :class="{ girado: semestresAbiertos.includes(semestre.numero) }" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" height="16">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                      </div>
                    </button>
                    <transition name="expand">
                      <div v-if="semestresAbiertos.includes(semestre.numero)" class="semestre-materias">
                        <table class="tabla-mini">
                          <thead><tr><th>Clave</th><th>Materia</th><th>Cal.</th><th>Estado</th></tr></thead>
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
                <div v-else class="kardex-vacio">
                  <p>No hay materias registradas en el kardex.</p>
                </div>
                <div class="kardex-link">
                  <button class="btn-accion-detalle primario" @click="verKardex(alumnoSeleccionado)">
                    Ver kardex completo
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="14" height="14">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                  </button>
                </div>
              </div>
              <div v-else class="kardex-vacio">
                <p>Kardex no disponible.</p>
              </div>
            </div>

            <!-- ── Tab: Horario ── -->
            <div v-if="tabActivo === 'horario'" class="tab-contenido">
              <div v-if="cargandoHorario" class="kardex-cargando">
                <div v-for="i in 5" :key="i" class="skeleton-fila"></div>
              </div>
              <div v-else-if="horarioData?.length" class="horario-lista">
                <div v-for="materia in horarioData" :key="materia.id" class="horario-item">
                  <div class="horario-color" :style="{ background: colorMateria(materia.nombre) }"></div>
                  <div class="horario-info">
                    <span class="horario-nombre">{{ materia.nombre }}</span>
                    <span class="horario-meta">{{ materia.dias }} · {{ materia.hora_inicio }}–{{ materia.hora_fin }}</span>
                    <span class="horario-aula">Aula: {{ materia.aula || 'N/D' }}</span>
                  </div>
                </div>
              </div>
              <div v-else class="kardex-vacio">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="32" height="32" style="stroke:#9CA3AF; margin-bottom:8px">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p>No hay horario registrado para este alumno.</p>
              </div>
            </div>

          </div>
        </transition>
      </div>

      <!-- ══════════════════════════════════ -->
      <!-- MODAL VER (compacto)              -->
      <!-- ══════════════════════════════════ -->
      <div v-if="showViewModal" class="modal-overlay" @click.self="cerrarModalVer">
        <div class="modal-content modal-ver">
          <div class="modal-header">
            <h3>Datos del Alumno</h3>
            <button @click="cerrarModalVer" class="btn-cerrar-modal">×</button>
          </div>
          <div class="modal-body">
            <div class="detalle-fila"><span class="detalle-label">No. de Control</span><span class="detalle-valor mono">{{ alumnoVer.noControl }}</span></div>
            <div class="detalle-fila"><span class="detalle-label">Nombre</span><span class="detalle-valor">{{ alumnoVer.nombre }}</span></div>
            <div class="detalle-fila"><span class="detalle-label">Carrera</span><span class="detalle-valor">{{ alumnoVer.carrera }}</span></div>
            <div class="detalle-fila"><span class="detalle-label">Semestre</span><span class="detalle-valor">{{ alumnoVer.semestre }}°</span></div>
            <div class="detalle-fila"><span class="detalle-label">Estatus</span><span class="estatus-badge" :class="claseEstatus(alumnoVer.estatus)">{{ alumnoVer.estatus }}</span></div>
          </div>
          <div class="modal-footer">
            <button class="btn-secundario" @click="cerrarModalVer">Cerrar</button>
            <button class="btn-guardar" @click="verKardexDirecto(alumnoVer.noControl)">Ver Kardex</button>
          </div>
        </div>
      </div>

      <!-- ══════════════════════════════════ -->
      <!-- MODAL EDITAR / NUEVO              -->
      <!-- ══════════════════════════════════ -->
      <div v-if="showModal" class="modal-overlay" @click.self="cerrarModal">
        <div class="modal-content">
          <div class="modal-header">
            <h3>{{ alumnoEditar.id_alumno ? 'Editar Alumno' : 'Nuevo Alumno' }}</h3>
            <button @click="cerrarModal" class="btn-cerrar-modal">×</button>
          </div>
          <div class="modal-body">
            <div class="form-grupo">
              <label>Número de Control</label>
              <input v-model="alumnoEditar.noControl" type="text" readonly class="modal-input deshabilitado" />
            </div>
            <div class="form-grupo">
              <label>Nombre completo</label>
              <input v-model="alumnoEditar.nombre" type="text" class="modal-input" />
            </div>
            <div class="form-grupo">
              <label>Carrera</label>
              <select v-model="alumnoEditar.id_carrera" class="modal-select">
                <option value="">Seleccionar carrera</option>
                <option v-for="c in catalogos.carreras" :key="c.id_carrera" :value="c.id_carrera">{{ c.nombre }}</option>
              </select>
            </div>
            <div class="form-grupo-doble">
              <div class="form-grupo">
                <label>Semestre</label>
                <select v-model="alumnoEditar.semestre" class="modal-select">
                  <option v-for="n in 8" :key="n" :value="n">{{ n }}° Semestre</option>
                </select>
              </div>
              <div class="form-grupo">
                <label>Estatus</label>
                <select v-model="alumnoEditar.id_estatus_alumno" class="modal-select">
                  <option value="">Seleccionar estatus</option>
                  <option v-for="e in catalogos.estatus_alumno" :key="e.id_estatus_alumno" :value="e.id_estatus_alumno">{{ e.nombre }}</option>
                </select>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn-secundario" @click="cerrarModal" :disabled="guardando">Cancelar</button>
            <button v-if="alumnoEditar.id_alumno" class="btn-eliminar" @click="eliminarAlumno" :disabled="guardando">Eliminar</button>
            <button class="btn-guardar" @click="guardarCambios" :disabled="guardando">
              <span v-if="guardando" class="spinner-btn"></span>
              {{ guardando ? 'Guardando...' : 'Guardar cambios' }}
            </button>
          </div>
        </div>
      </div>

    </div>
  </MainLayout>
</template>

<script setup>
import { ref, computed, onMounted, watch, nextTick } from 'vue'
import { useRouter } from 'vue-router'
import MainLayout from '@/layouts/MainLayout.vue'

const router = useRouter()
const API_URL = import.meta.env.VITE_API_URL

// ── Estado principal ─────────────────────────────────────────────────
const alumnos          = ref([])
const cargando         = ref(false)
const cargandoBusqueda = ref(false)
const guardando        = ref(false)
const filaActiva       = ref(-1)
const tablaRef         = ref(null)
const inputBusqueda    = ref(null)

// ── Panel de detalle del alumno ──────────────────────────────────────
const alumnoSeleccionado = ref(null)
const tabActivo          = ref('general')
const kardexData         = ref(null)
const cargandoKardex     = ref(false)
const kardexError        = ref(false)
const horarioData        = ref(null)
const cargandoHorario    = ref(false)
const semestresAbiertos  = ref([])

const tabs = [
  { id: 'general', label: 'Datos',    icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z' },
  { id: 'kardex',  label: 'Kardex',   icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 9.414V19a2 2 0 01-2 2z' },
  { id: 'horario', label: 'Horario',  icon: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z' },
]

// ── Catálogos ────────────────────────────────────────────────────────
const catalogos = ref({ carreras: [], estatus_alumno: [] })

const cargarCatalogos = async () => {
  try {
    const res = await fetch(`${API_URL}/api/alumnos/catalogos`)
    if (!res.ok) throw new Error()
    const data = await res.json()
    catalogos.value.carreras       = data.carreras       || []
    catalogos.value.estatus_alumno = data.estatus_alumno || []
  } catch {
    mostrarNotificacion('No se pudieron cargar los catálogos.', 'error')
  }
}

// ── Filtros y paginación ─────────────────────────────────────────────
const busquedaAlumno  = ref('')   // valor del input (lo que el usuario escribe)
const busquedaAplicada = ref('')  // valor que realmente filtra (solo se actualiza al buscar)
const filtroCarreraId = ref('')
const filtroSemestre  = ref('')
const filtroEstatusId = ref('')
const filasPorPagina  = ref(10)
const currentPage     = ref(1)

// ── Notificación ─────────────────────────────────────────────────────
const notificacion = ref({ visible: false, mensaje: '', tipo: 'exito' })
let timerNotif = null

const mostrarNotificacion = (mensaje, tipo = 'exito') => {
  if (timerNotif) clearTimeout(timerNotif)
  notificacion.value = { visible: true, mensaje, tipo }
  timerNotif = setTimeout(() => { notificacion.value.visible = false }, 3500)
}

// ── Props ────────────────────────────────────────────────────────────
const props = defineProps({ busquedaGlobal: { type: String, default: '' } })

// ── Helpers ──────────────────────────────────────────────────────────
const normalize = (t) => !t ? '' : t.toString().toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '')

const claseEstatus = (estatus) => {
  if (!estatus) return ''
  return estatus.toLowerCase().replace(/\s/g, '-')
}

const resolverNombre = (a) =>
  a.nombre || a.persona?.nombre_completo || a.persona?.nombre || ''

const resolverCarrera = (a) =>
  a.carrera?.nombre_carrera || a.carrera || ''

const resolverIdCarrera = (a) =>
  a.id_carrera || a.carrera?.id_carrera || null

const iniciales = (nombre) => {
  if (!nombre) return '?'
  return nombre.split(' ').slice(0, 2).map(p => p[0]).join('').toUpperCase()
}

const formatearFecha = (f) => {
  if (!f) return ''
  return new Date(f).toLocaleDateString('es-MX', { day: '2-digit', month: 'long', year: 'numeric' })
}

const colorMateria = (nombre) => {
  const colors = ['#1B396A', '#7C3AED', '#0891B2', '#059669', '#DC2626', '#D97706']
  let h = 0
  for (let i = 0; i < (nombre || '').length; i++) h += nombre.charCodeAt(i)
  return colors[h % colors.length]
}

// ── Carga de alumnos ─────────────────────────────────────────────────
const cargarAlumnosDesdeBD = async () => {
  cargando.value = true
  try {
    const res = await fetch(`${API_URL}/api/alumnos-crud`)
    if (!res.ok) throw new Error()
    alumnos.value = await res.json()
  } catch {
    mostrarNotificacion('No se pudo cargar la lista de alumnos.', 'error')
  } finally {
    cargando.value = false
  }
}

onMounted(() => { cargarAlumnosDesdeBD(); cargarCatalogos() })

// ── Búsqueda: NO filtra mientras se escribe.
//    Solo aplica el filtro al presionar Enter o el botón Buscar.
//    Para nombre (texto libre) se permite buscar al presionar Enter también.
// No hay watch reactivo sobre busquedaAlumno — el filtrado usa busquedaAplicada.

// ── Selección de alumno (panel lateral) ─────────────────────────────
const seleccionarAlumno = async (alumno) => {
  if (alumnoSeleccionado.value?.id_alumno === alumno.id_alumno) {
    cerrarDetalle()
    return
  }
  alumnoSeleccionado.value = alumno
  tabActivo.value = 'general'
  kardexData.value = null
  horarioData.value = null
}

const cerrarDetalle = () => { alumnoSeleccionado.value = null }

// Cargar kardex cuando el tab se activa
watch(tabActivo, async (tab) => {
  if (!alumnoSeleccionado.value) return
  if (tab === 'kardex' && !kardexData.value) {
    await cargarKardexAlumno(alumnoSeleccionado.value)
  }
  if (tab === 'horario' && !horarioData.value) {
    await cargarHorarioAlumno(alumnoSeleccionado.value)
  }
})

const cargarKardexAlumno = async (alumno) => {
  const nc = alumno.numero_control || alumno.noControl
  if (!nc) return
  cargandoKardex.value = true
  kardexError.value = false
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
    if (!res.ok) { horarioData.value = []; return }
    horarioData.value = await res.json()
  } catch {
    horarioData.value = []
  } finally {
    cargandoHorario.value = false
  }
}

// ── Accordion semestres Kardex ───────────────────────────────────────
const toggleSemestre = (num) => {
  const idx = semestresAbiertos.value.indexOf(num)
  if (idx === -1) semestresAbiertos.value.push(num)
  else semestresAbiertos.value.splice(idx, 1)
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
  const map = { acreditada: 'Acred.', reprobada: 'Repr.', pendiente: 'En curso', no_cursada: 'Pendiente' }
  return map[estado] ?? estado
}

// ── Navegación a kardex ──────────────────────────────────────────────
const verKardex = (alumno) => {
  const nc = alumno.numero_control || alumno.noControl
  if (nc) router.push(`/kardex/${nc}`)
}
const verKardexDirecto = (nc) => { if (nc) router.push(`/kardex/${nc}`) }

// ── Modal Ver ────────────────────────────────────────────────────────
const showViewModal = ref(false)
const alumnoVer     = ref({})

const abrirModalVer = (alumno) => {
  alumnoVer.value = {
    noControl: alumno.numero_control || alumno.noControl || '',
    nombre:    resolverNombre(alumno),
    carrera:   resolverCarrera(alumno),
    semestre:  alumno.semestre_actual || alumno.semestre || '',
    estatus:   alumno.estatus || ''
  }
  showViewModal.value = true
}
const cerrarModalVer = () => { showViewModal.value = false }

// ── Modal Editar ─────────────────────────────────────────────────────
const showModal    = ref(false)
const alumnoEditar = ref({})

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

// ── Guardar cambios ──────────────────────────────────────────────────
const guardarCambios = async () => {
  const id = alumnoEditar.value.id_alumno
  if (!id) { mostrarNotificacion('No se encontró el identificador del alumno.', 'error'); return }
  if (!alumnoEditar.value.id_carrera) { mostrarNotificacion('Selecciona una carrera.', 'error'); return }
  if (!alumnoEditar.value.id_estatus_alumno) { mostrarNotificacion('Selecciona un estatus.', 'error'); return }

  const nombreEstatus = catalogos.value.estatus_alumno
    .find(e => e.id_estatus_alumno === alumnoEditar.value.id_estatus_alumno)?.nombre || 'Activo'

  guardando.value = true
  try {
    const res = await fetch(`${API_URL}/api/alumnos/${id}`, {
      method: 'PUT',
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
      if (alumnoSeleccionado.value?.id_alumno === id) {
        alumnoSeleccionado.value = alumnos.value.find(a => a.id_alumno === id) || alumnoSeleccionado.value
      }
    } else {
      mostrarNotificacion(data.message || data.error || 'Error al actualizar.', 'error')
    }
  } catch {
    mostrarNotificacion('Error de conexión al actualizar.', 'error')
  } finally {
    guardando.value = false
  }
}

// ── Eliminar alumno ──────────────────────────────────────────────────
const eliminarAlumno = async () => {
  const id = alumnoEditar.value.id_alumno
  if (!id) return
  if (!window.confirm(`¿Confirma que desea eliminar al alumno "${alumnoEditar.value.nombre}"?\n\nEsta acción no se puede deshacer.`)) return

  guardando.value = true
  try {
    const res = await fetch(`${API_URL}/api/alumnos/${id}`, {
      method: 'DELETE',
      headers: { 'Accept': 'application/json' }
    })
    let data = {}
    if (res.status !== 204) data = await res.json().catch(() => ({}))
    if (res.ok) {
      if (alumnoSeleccionado.value?.id_alumno === id) cerrarDetalle()
      await cargarAlumnosDesdeBD()
      cerrarModal()
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

// ── Filtrado ─────────────────────────────────────────────────────────
const alumnosFiltrados = computed(() => {
  return alumnos.value.filter(alumno => {
    const nombre    = resolverNombre(alumno)
    const noControl = (alumno.numero_control || alumno.noControl || '').toString()
    const busqGlobal = !props.busquedaGlobal ||
      normalize(nombre).includes(normalize(props.busquedaGlobal)) ||
      noControl.includes(props.busquedaGlobal)
    // ── CORRECCIÓN: usa busquedaAplicada (solo se actualiza al presionar Buscar/Enter) ──
    const busqLocal = !busquedaAplicada.value ||
      normalize(nombre).includes(normalize(busquedaAplicada.value)) ||
      noControl.includes(busquedaAplicada.value)
    const filtCarrera  = !filtroCarreraId.value || Number(resolverIdCarrera(alumno)) === Number(filtroCarreraId.value)
    const filtSemestre = !filtroSemestre.value  || String(alumno.semestre_actual || alumno.semestre) === String(filtroSemestre.value)
    const filtEstatus  = !filtroEstatusId.value || Number(alumno.id_estatus_alumno) === Number(filtroEstatusId.value)
    return busqGlobal && busqLocal && filtCarrera && filtSemestre && filtEstatus
  })
})

// ── Paginación ───────────────────────────────────────────────────────
const totalPages = computed(() => Math.ceil(alumnosFiltrados.value.length / filasPorPagina.value) || 1)
const paginatedAlumnos = computed(() => {
  const start = (currentPage.value - 1) * filasPorPagina.value
  return alumnosFiltrados.value.slice(start, start + filasPorPagina.value)
})
const visiblePages = computed(() => {
  const total = totalPages.value, cur = currentPage.value
  if (total <= 7) return Array.from({ length: total }, (_, i) => i + 1)
  const pages = new Set([1, total, cur, cur - 1, cur + 1].filter(p => p >= 1 && p <= total))
  return [...pages].sort((a, b) => a - b)
})

const goToPage = (p) => { currentPage.value = p; filaActiva.value = -1 }
const prevPage = () => { if (currentPage.value > 1) currentPage.value-- }
const nextPage = () => { if (currentPage.value < totalPages.value) currentPage.value++ }

const resetFilters = () => {
  busquedaAlumno.value = ''
  busquedaAplicada.value = ''
  filtroCarreraId.value = filtroSemestre.value = filtroEstatusId.value = ''
  currentPage.value = 1; filaActiva.value = -1
}

const limpiarBusqueda = () => { busquedaAlumno.value = ''; busquedaAplicada.value = ''; currentPage.value = 1; nextTick(() => inputBusqueda.value?.focus()) }
// ── CORRECCIÓN: aplicarBusqueda copia el término al ref que usa el filtro ──
const aplicarBusqueda = () => { busquedaAplicada.value = busquedaAlumno.value; currentPage.value = 1 }
const nuevoAlumno     = () => router.push('/formulario-alumno')

// ── Navegación por teclado ───────────────────────────────────────────
const navegarTeclado = (e) => {
  const total = paginatedAlumnos.value.length
  if (total === 0) return
  if (e.key === 'ArrowDown')     { e.preventDefault(); filaActiva.value = Math.min(filaActiva.value + 1, total - 1) }
  else if (e.key === 'ArrowUp')  { e.preventDefault(); filaActiva.value = Math.max(filaActiva.value - 1, 0) }
  else if (e.key === 'Enter' && filaActiva.value >= 0) { e.preventDefault(); seleccionarAlumno(paginatedAlumnos.value[filaActiva.value]) }
  else if (e.key === 'PageDown') { e.preventDefault(); nextPage() }
  else if (e.key === 'PageUp')   { e.preventDefault(); prevPage() }
  else if (e.key === 'Escape')   { cerrarDetalle() }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap');

.alumnos-page {
  --azul:       #1B396A;
  --azul-hover: #1D4ED8;
  --borde:      #E5E7EB;
  --fondo:      #F5F5F5;
  --texto:      #1A1A1A;
  --gris:       #6B7280;
  --verde:      #16A34A;
  --rojo:       #DC2626;

  max-width: 100%;
  background: var(--fondo);
  font-family: 'Montserrat', sans-serif;
}

/* ── Layout principal con panel lateral ── */
.layout-principal {
  display: flex;
  gap: 1rem;
  align-items: flex-start;
  transition: all 0.3s ease;
}

.panel-lista {
  flex: 1;
  min-width: 0;
  transition: flex 0.3s ease;
}

.layout-principal.con-detalle .panel-lista {
  flex: 0 0 55%;
}

/* ── Barra carga global ── */
.barra-carga-global {
  height: 3px;
  background: transparent;
  border-radius: 2px;
  margin-bottom: 1rem;
  overflow: hidden;
  opacity: 0;
  transition: opacity 0.3s;
}
.barra-carga-global.visible { opacity: 1; }
.barra-progreso {
  height: 100%;
  width: 40%;
  background: var(--azul);
  border-radius: 2px;
  animation: deslizar 1.2s ease-in-out infinite;
}
@keyframes deslizar { 0% { transform: translateX(-100%); } 100% { transform: translateX(350%); } }

/* ── Notificación ── */
.notificacion-ui {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px 18px;
  border-radius: 10px;
  font-size: 0.93rem;
  font-weight: 500;
  margin-bottom: 1rem;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.notificacion-ui.exito { background: #DCFCE7; color: #16A34A; border: 1px solid #86EFAC; }
.notificacion-ui.error { background: #FEE2E2; color: #DC2626; border: 1px solid #FCA5A5; }
.notif-icono { width: 20px; height: 20px; flex-shrink: 0; }
.notif-fade-enter-active, .notif-fade-leave-active { transition: all 0.35s ease; }
.notif-fade-enter-from, .notif-fade-leave-to { opacity: 0; transform: translateY(-8px); }

/* ── Page header ── */
.page-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  margin-bottom: 1.1rem;
  flex-wrap: wrap;
}
.page-title {
  color: var(--texto);
  font-size: 1.75rem;
  font-weight: 700;
  letter-spacing: -0.02em;
  margin: 0;
}
.page-subtitle { font-size: 0.88rem; color: var(--gris); font-weight: 500; }

.btn-nuevo {
  display: flex;
  align-items: center;
  gap: 6px;
  background: var(--azul);
  color: white;
  border: none;
  padding: 9px 18px;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  white-space: nowrap;
  font-family: 'Montserrat', sans-serif;
  font-size: 0.88rem;
  transition: background 0.2s;
}
.btn-nuevo:hover { background: var(--azul-hover); }

/* ── Búsqueda ── */
.busqueda-card {
  margin-bottom: 0.75rem;
}
.busqueda-wrap {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 0 14px;
  border: 1.5px solid var(--borde);
  border-radius: 10px;
  background: #FFFFFF;
  transition: border-color 0.2s, box-shadow 0.2s;
}
.busqueda-wrap.activo,
.busqueda-wrap:focus-within {
  border-color: var(--azul);
  box-shadow: 0 0 0 3px #DBEAFE;
}
.busqueda-icono { width: 18px; height: 18px; stroke: var(--gris); flex-shrink: 0; }
.busqueda-input {
  flex: 1;
  border: none;
  background: transparent;
  padding: 12px 0;
  font-size: 0.95rem;
  font-family: 'Montserrat', sans-serif;
  color: var(--texto);
  outline: none;
}
.busqueda-input::placeholder { color: #9CA3AF; }
.spinner-busqueda {
  width: 16px; height: 16px;
  border: 2px solid #E5E7EB;
  border-top-color: var(--azul);
  border-radius: 50%;
  animation: girar 0.7s linear infinite;
  flex-shrink: 0;
}
.btn-limpiar-input {
  background: none;
  border: none;
  color: var(--gris);
  cursor: pointer;
  display: flex;
  align-items: center;
  padding: 2px;
}

/* ── Filtros ── */
.filters-bar {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  margin-bottom: 1rem;
  flex-wrap: wrap;
}
.filter-select {
  padding: 7px 10px;
  border: 1px solid var(--borde);
  border-radius: 8px;
  font-size: 0.85rem;
  flex: 1 1 110px;
  min-width: 100px;
  background: #FFFFFF;
  color: var(--texto);
  font-family: 'Montserrat', sans-serif;
  cursor: pointer;
  outline: none;
}
.filter-select:focus { border-color: var(--azul); }
.btn-limpiar {
  display: flex;
  align-items: center;
  gap: 5px;
  background: #FFFFFF;
  color: var(--texto);
  border: 1px solid var(--borde);
  padding: 7px 12px;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  font-size: 0.85rem;
  white-space: nowrap;
  font-family: 'Montserrat', sans-serif;
  transition: background 0.15s;
}
.btn-limpiar:hover { background: var(--fondo); }

/* ── Tabla ── */
.table-container {
  background: #FFFFFF;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0,0,0,0.05);
  border: 1px solid var(--borde);
  overflow-x: auto;
}
.alumnos-table { width: 100%; border-collapse: collapse; outline: none; }
.alumnos-table th {
  background: var(--fondo);
  padding: 9px 12px;
  text-align: left;
  font-weight: 600;
  font-size: 0.82rem;
  color: var(--texto);
  border-bottom: 1px solid var(--borde);
  font-family: 'Montserrat', sans-serif;
  white-space: nowrap;
}
.alumnos-table th.centrado { text-align: center; }
.alumnos-table td {
  padding: 8px 12px;
  border-bottom: 1px solid var(--borde);
  color: var(--texto);
  font-size: 0.87rem;
  font-family: 'Montserrat', sans-serif;
}
.alumnos-table td.centrado { text-align: center; }
.alumnos-table tbody tr { transition: background 0.15s; cursor: pointer; }
.alumnos-table tbody tr:hover { background: #F8FAFC; }
.alumnos-table tbody tr:last-child td { border-bottom: none; }

.fila-seleccionada { background: #EFF6FF !important; }
.fila-activa-teclado { background: #DBEAFE !important; }

.celda-control { font-weight: 700; color: var(--azul); font-size: 0.87rem; font-family: 'Montserrat', sans-serif; }
.celda-nombre { font-weight: 500; }
.celda-carrera { color: var(--gris); font-size: 0.82rem; }
.celda-acciones { display: flex; gap: 6px; align-items: center; justify-content: center; }

.col-semestre { width: 50px; }

.estatus-badge {
  display: inline-block;
  padding: 3px 10px;
  border-radius: 20px;
  font-size: 0.78rem;
  font-weight: 600;
  font-family: 'Montserrat', sans-serif;
  white-space: nowrap;
}
.estatus-badge.activo           { background: #DCFCE7; color: #16A34A; }
.estatus-badge.baja-temporal    { background: #FEF3C7; color: #F59E0B; }
.estatus-badge.baja-definitiva  { background: #FEE2E2; color: #DC2626; }
.estatus-badge.titulado         { background: #EDE9FE; color: #7C3AED; }
.estatus-badge.egresado         { background: #DBEAFE; color: #1B396A; }

.btn-icono {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 30px; height: 30px;
  border-radius: 6px;
  cursor: pointer;
  border: 1px solid var(--borde);
  transition: background 0.15s;
  flex-shrink: 0;
}
.btn-icono svg { width: 14px; height: 14px; }
.btn-icono.ver { background: #FFFFFF; }
.btn-icono.ver svg { stroke: var(--gris); }
.btn-icono.ver:hover { background: var(--fondo); }
.btn-icono.editar { background: var(--azul); border-color: var(--azul); }
.btn-icono.editar svg { stroke: white; }
.btn-icono.editar:hover { background: var(--azul-hover); border-color: var(--azul-hover); }

.estado-vacio, .estado-cargando {
  text-align: center;
  padding: 3.5rem 2rem;
  color: var(--gris);
}
.icono-vacio { width: 52px; height: 52px; stroke: #9CA3AF; margin-bottom: 12px; }
.estado-vacio h3 { font-size: 1.15rem; color: var(--texto); margin: 0 0 6px; }
.estado-vacio p  { font-size: 0.9rem; margin: 0 0 1.2rem; }
.btn-limpiar-vacio {
  background: #FFFFFF;
  color: var(--texto);
  border: 1px solid var(--borde);
  padding: 8px 18px;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  font-family: 'Montserrat', sans-serif;
}
.spinner-tabla {
  display: inline-block;
  width: 32px; height: 32px;
  border: 3px solid #E5E7EB;
  border-top-color: var(--azul);
  border-radius: 50%;
  animation: girar 0.8s linear infinite;
  margin-bottom: 12px;
}

/* ── Paginación ── */
.paginacion {
  margin-top: 1rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 0.87rem;
  color: var(--gris);
  font-family: 'Montserrat', sans-serif;
  flex-wrap: wrap;
  gap: 0.4rem;
}
.paginacion-izquierda, .paginacion-centro, .paginacion-derecha { display: flex; align-items: center; gap: 6px; }
.select-filas { border: 1px solid var(--borde); border-radius: 6px; padding: 3px 6px; font-size: 0.87rem; background: #FFFFFF; font-family: 'Montserrat', sans-serif; }
.btn-pag {
  padding: 4px 10px;
  border: 1px solid var(--borde);
  background: #FFFFFF;
  border-radius: 6px;
  cursor: pointer;
  color: var(--texto);
  font-family: 'Montserrat', sans-serif;
  font-size: 0.87rem;
  transition: background 0.15s;
}
.btn-pag:hover:not(:disabled) { background: var(--fondo); }
.btn-pag:disabled { opacity: 0.4; cursor: not-allowed; }
.btn-pag.activo { background: var(--azul); color: white; border-color: var(--azul); }

/* ══════════════════════════════════════ */
/* PANEL DETALLE ALUMNO                  */
/* ══════════════════════════════════════ */
.panel-detalle {
  flex: 0 0 calc(45% - 1rem);
  background: #FFFFFF;
  border-radius: 14px;
  border: 1px solid var(--borde);
  box-shadow: 0 8px 24px rgba(0,0,0,0.08);
  overflow: hidden;
  position: sticky;
  top: 1rem;
  max-height: calc(100vh - 100px);
  display: flex;
  flex-direction: column;
}

.panel-slide-enter-active, .panel-slide-leave-active { transition: all 0.3s ease; }
.panel-slide-enter-from, .panel-slide-leave-to { opacity: 0; transform: translateX(20px); }

.detalle-header {
  background: var(--azul);
  padding: 1.2rem 1.4rem;
  display: flex;
  align-items: center;
  gap: 1rem;
  flex-shrink: 0;
}
.detalle-avatar { flex-shrink: 0; }
.avatar-img {
  width: 52px; height: 52px;
  border-radius: 12px;
  object-fit: cover;
  border: 2px solid rgba(255,255,255,0.3);
}
.avatar-placeholder {
  width: 52px; height: 52px;
  border-radius: 12px;
  background: rgba(255,255,255,0.15);
  display: flex;
  align-items: center;
  justify-content: center;
  border: 2px solid rgba(255,255,255,0.3);
}
.avatar-placeholder span {
  color: white;
  font-weight: 700;
  font-size: 1.1rem;
  font-family: 'Montserrat', sans-serif;
}
.detalle-header-info {
  flex: 1;
  min-width: 0;
  display: flex;
  flex-direction: column;
  gap: 4px;
}
.detalle-nombre {
  color: white;
  font-size: 1rem;
  font-weight: 700;
  margin: 0;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.detalle-control {
  color: rgba(255,255,255,0.7);
  font-size: 0.82rem;
  font-weight: 600;
  font-family: 'Montserrat', sans-serif;
  font-variant-numeric: tabular-nums;
}
.btn-cerrar-panel {
  background: rgba(255,255,255,0.15);
  border: none;
  color: white;
  cursor: pointer;
  width: 32px; height: 32px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  transition: background 0.15s;
}
.btn-cerrar-panel:hover { background: rgba(255,255,255,0.25); }

/* Tabs */
.detalle-tabs {
  display: flex;
  background: var(--fondo);
  border-bottom: 1px solid var(--borde);
  flex-shrink: 0;
}
.tab-btn {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 5px;
  padding: 10px 8px;
  background: none;
  border: none;
  cursor: pointer;
  font-family: 'Montserrat', sans-serif;
  font-size: 0.82rem;
  font-weight: 600;
  color: var(--gris);
  border-bottom: 2px solid transparent;
  transition: color 0.15s, border-color 0.15s;
}
.tab-btn svg { stroke: currentColor; }
.tab-btn:hover { color: var(--azul); }
.tab-btn.activo { color: var(--azul); border-bottom-color: var(--azul); background: white; }

/* Contenido del tab */
.tab-contenido {
  flex: 1;
  overflow-y: auto;
  padding: 1.2rem 1.4rem;
}

/* Tab: Datos generales */
.info-grid { display: flex; flex-direction: column; gap: 0; margin-bottom: 1.2rem; }
.info-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 9px 0;
  border-bottom: 1px solid var(--borde);
  gap: 1rem;
}
.info-item:last-child { border-bottom: none; }
.info-label { font-size: 0.82rem; color: var(--gris); font-weight: 600; flex-shrink: 0; }
.info-valor { font-size: 0.88rem; color: var(--texto); font-weight: 500; text-align: right; }
.info-valor.mono { font-family: monospace; font-size: 0.9rem; color: var(--azul); font-weight: 700; }
.mono { font-family: monospace; font-size: 0.88rem; color: var(--azul); }

.detalle-acciones {
  display: flex;
  gap: 0.75rem;
  flex-wrap: wrap;
  padding-top: 0.5rem;
}
.btn-accion-detalle {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  padding: 9px 14px;
  border-radius: 8px;
  font-weight: 600;
  font-size: 0.85rem;
  cursor: pointer;
  font-family: 'Montserrat', sans-serif;
  transition: background 0.15s;
  border: none;
  white-space: nowrap;
}
.btn-accion-detalle.primario { background: var(--azul); color: white; }
.btn-accion-detalle.primario:hover { background: var(--azul-hover); }
.btn-accion-detalle.secundario { background: #DBEAFE; color: var(--azul); }
.btn-accion-detalle.secundario:hover { background: #BFDBFE; }

/* Tab: Kardex */
.creditos-bloque {
  background: var(--fondo);
  border-radius: 10px;
  padding: 12px 14px;
  margin-bottom: 1rem;
  border: 1px solid var(--borde);
}
.creditos-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 8px;
  font-size: 0.85rem;
}
.creditos-label { font-weight: 700; color: var(--texto); }
.creditos-pct { font-weight: 700; color: var(--gris); font-size: 0.82rem; }
.creditos-pct.verde { color: var(--verde); }
.creditos-barra-track { height: 10px; background: #E5E7EB; border-radius: 5px; overflow: hidden; }
.creditos-barra-fill { height: 100%; border-radius: 5px; transition: width 0.8s ease; }

.kardex-semestres { display: flex; flex-direction: column; gap: 0.6rem; }
.semestre-bloque {
  border: 1px solid var(--borde);
  border-radius: 10px;
  overflow: hidden;
  background: white;
}
.semestre-btn {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 9px 12px;
  background: none;
  border: none;
  cursor: pointer;
  font-family: 'Montserrat', sans-serif;
  transition: background 0.15s;
}
.semestre-btn:hover { background: var(--fondo); }
.semestre-btn.abierto { background: #EFF6FF; }
.semestre-titulo { font-size: 0.88rem; font-weight: 700; color: var(--texto); }
.semestre-badges { display: flex; align-items: center; gap: 5px; }
.badge-mini {
  font-size: 0.72rem;
  font-weight: 700;
  padding: 2px 8px;
  border-radius: 20px;
}
.badge-mini.acreditadas { background: #DCFCE7; color: #16A34A; }
.badge-mini.reprobadas  { background: #FEE2E2; color: #DC2626; }
.flecha-semestre { stroke: var(--gris); transition: transform 0.2s; flex-shrink: 0; }
.flecha-semestre.girado { transform: rotate(180deg); }

.semestre-materias { border-top: 1px solid var(--borde); }
.tabla-mini { width: 100%; border-collapse: collapse; font-size: 0.8rem; }
.tabla-mini th {
  background: var(--fondo);
  padding: 6px 10px;
  text-align: left;
  font-size: 0.73rem;
  font-weight: 700;
  color: var(--gris);
  text-transform: uppercase;
  letter-spacing: 0.04em;
  border-bottom: 1px solid var(--borde);
}
.tabla-mini td { padding: 6px 10px; border-bottom: 1px solid #F9FAFB; vertical-align: middle; }
.tabla-mini tr:last-child td { border-bottom: none; }
.tabla-mini tr.fila-repr { background: #FEF2F2; }
.tabla-mini td.centrado { text-align: center; }
.clave-mono { font-family: monospace; font-size: 0.78rem; font-weight: 700; color: var(--texto); }
.badge-estado-mini { font-size: 0.7rem; font-weight: 700; padding: 2px 7px; border-radius: 10px; }
.text-verde { color: var(--verde); }
.text-rojo  { color: var(--rojo); }
.text-gris  { color: var(--gris); }

.kardex-cargando { display: flex; flex-direction: column; gap: 8px; }
.skeleton-linea { height: 14px; border-radius: 6px; background: linear-gradient(90deg, #E5E7EB 25%, #F3F4F6 50%, #E5E7EB 75%); background-size: 200% 100%; animation: shimmer 1.4s infinite; }
.skeleton-linea.largo { width: 70%; }
.skeleton-linea.medio { width: 45%; }
.skeleton-fila  { height: 40px; border-radius: 8px; background: linear-gradient(90deg, #F3F4F6 25%, #FFFFFF 50%, #F3F4F6 75%); background-size: 200% 100%; animation: shimmer 1.4s infinite; }
@keyframes shimmer { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }

.kardex-error {
  display: flex;
  align-items: center;
  gap: 8px;
  background: #FEF2F2;
  border: 1px solid #FECACA;
  border-radius: 8px;
  padding: 10px 14px;
  font-size: 0.85rem;
  color: var(--rojo);
  font-weight: 500;
}
.kardex-error svg { stroke: var(--rojo); flex-shrink: 0; }
.kardex-error button {
  margin-left: auto;
  background: var(--rojo);
  color: white;
  border: none;
  padding: 4px 12px;
  border-radius: 6px;
  font-size: 0.78rem;
  cursor: pointer;
  font-weight: 600;
  font-family: 'Montserrat', sans-serif;
}
.kardex-vacio {
  text-align: center;
  padding: 2rem;
  color: var(--gris);
  font-size: 0.88rem;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 4px;
}
.kardex-link { margin-top: 1rem; }

.expand-enter-active, .expand-leave-active { transition: all 0.2s ease; }
.expand-enter-from, .expand-leave-to { opacity: 0; transform: translateY(-4px); }

/* Tab: Horario */
.horario-lista { display: flex; flex-direction: column; gap: 0.6rem; }
.horario-item {
  display: flex;
  align-items: stretch;
  gap: 0;
  border: 1px solid var(--borde);
  border-radius: 8px;
  overflow: hidden;
  background: white;
}
.horario-color { width: 5px; flex-shrink: 0; }
.horario-info {
  padding: 10px 12px;
  display: flex;
  flex-direction: column;
  gap: 2px;
}
.horario-nombre { font-weight: 700; font-size: 0.88rem; color: var(--texto); }
.horario-meta   { font-size: 0.78rem; color: var(--gris); }
.horario-aula   { font-size: 0.75rem; color: #9CA3AF; }

/* ── Modales ── */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.55);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2000;
  padding: 1rem;  /* ← evita que el modal toque los bordes en móvil */
}
.modal-content {
  background: #FFFFFF;
  width: 520px;
  max-width: 92%;
  max-height: 90vh;          /* ← garantiza que el modal no salga de pantalla */
  overflow-y: auto;          /* ← scroll interno si el contenido es largo */
  border-radius: 14px;
  box-shadow: 0 20px 50px rgba(0,0,0,0.18);
  overflow: hidden;
  border: 1px solid var(--borde);
}
.modal-header {
  background: var(--azul);
  color: white;
  padding: 1.1rem 1.6rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.modal-header h3 { margin: 0; font-size: 1.1rem; font-weight: 700; font-family: 'Montserrat', sans-serif; }
.btn-cerrar-modal { background: none; border: none; color: white; font-size: 1.7rem; cursor: pointer; line-height: 1; opacity: 0.85; transition: opacity 0.15s; }
.btn-cerrar-modal:hover { opacity: 1; }
.modal-body { padding: 1.4rem 1.6rem; }
.form-grupo { margin-bottom: 1.1rem; }
.form-grupo-doble { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
.form-grupo label { display: block; margin-bottom: 6px; font-weight: 600; font-size: 0.88rem; color: var(--texto); font-family: 'Montserrat', sans-serif; }
.modal-input, .modal-select {
  width: 100%;
  padding: 9px 13px;
  border: 1.5px solid var(--borde);
  border-radius: 8px;
  font-size: 0.92rem;
  background: #FFFFFF;
  color: var(--texto);
  font-family: 'Montserrat', sans-serif;
  outline: none;
  transition: border-color 0.2s;
  box-sizing: border-box;
}
.modal-input:focus, .modal-select:focus { border-color: var(--azul); }
.modal-input.deshabilitado { background: var(--fondo); cursor: not-allowed; color: var(--gris); }
.modal-footer {
  padding: 1rem 1.6rem;
  background: var(--fondo);
  display: flex;
  gap: 10px;
  justify-content: flex-end;
  border-top: 1px solid var(--borde);
}
.btn-secundario {
  padding: 9px 20px;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  font-family: 'Montserrat', sans-serif;
  background: #FFFFFF;
  color: var(--texto);
  border: 1px solid var(--borde);
  transition: background 0.15s;
  font-size: 0.9rem;
}
.btn-secundario:hover { background: var(--fondo); }
.btn-secundario:disabled { opacity: 0.5; cursor: not-allowed; }
.btn-eliminar {
  padding: 9px 20px;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  font-family: 'Montserrat', sans-serif;
  background: var(--rojo);
  color: white;
  border: none;
  transition: background 0.15s;
  font-size: 0.9rem;
}
.btn-eliminar:hover { background: #B91C1C; }
.btn-eliminar:disabled { opacity: 0.5; cursor: not-allowed; }
.btn-guardar {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 9px 20px;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  font-family: 'Montserrat', sans-serif;
  background: var(--azul);
  color: white;
  border: none;
  transition: background 0.15s;
  font-size: 0.9rem;
}
.btn-guardar:hover:not(:disabled) { background: var(--azul-hover); }
.btn-guardar:disabled { opacity: 0.65; cursor: not-allowed; }
.spinner-btn {
  display: inline-block;
  width: 14px; height: 14px;
  border: 2px solid rgba(255,255,255,0.4);
  border-top-color: white;
  border-radius: 50%;
  animation: girar 0.7s linear infinite;
  flex-shrink: 0;
}
.detalle-fila {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 0;
  border-bottom: 1px solid var(--borde);
  font-size: 0.92rem;
  color: var(--texto);
  font-family: 'Montserrat', sans-serif;
}
.detalle-fila:last-child { border-bottom: none; }
.detalle-label { font-weight: 600; color: var(--gris); }
.detalle-valor { font-weight: 500; }

@keyframes girar { to { transform: rotate(360deg); } }

/* ══════════════════════════════════════
   RESPONSIVE
══════════════════════════════════════ */

/* Tablet ≤1200px: reducir columna detalle */
@media (max-width: 1200px) {
  .layout-principal.con-detalle .panel-lista { flex: 0 0 52%; }
  .panel-detalle { flex: 0 0 calc(48% - 1rem); }
  .col-carrera { display: none; }
}

/* Tablet ≤960px: panel detalle debajo */
@media (max-width: 960px) {
  .layout-principal { flex-direction: column; }
  .layout-principal.con-detalle .panel-lista { flex: none; width: 100%; }
  .panel-detalle {
    flex: none;
    width: 100%;
    position: static;
    max-height: none;
  }
  .col-carrera { display: table-cell; }
}

/* Mobile ≤768px */
@media (max-width: 768px) {
  .page-header { flex-direction: column; align-items: flex-start; gap: 0.5rem; margin-bottom: 0.9rem; }
  .page-title { font-size: 1.4rem; }
  .filters-bar { display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; }
  .col-carrera, .col-semestre { display: none; }
  .paginacion { flex-direction: column; align-items: center; gap: 0.5rem; }
  .paginacion-izquierda, .paginacion-centro, .paginacion-derecha { width: 100%; justify-content: center; }
  .modal-body { padding: 1.1rem; }
  .modal-footer { padding: 0.8rem 1.1rem; }
}

/* Mobile ≤480px */
@media (max-width: 480px) {
  .page-title { font-size: 1.2rem; }
  .filters-bar { grid-template-columns: 1fr; }
  .alumnos-table th, .alumnos-table td { padding: 6px 9px; font-size: 0.8rem; }
  .celda-acciones { gap: 4px; }
  .estatus-badge { padding: 2px 7px; font-size: 0.72rem; }
  .modal-footer { flex-direction: column; gap: 0.5rem; }
  .modal-footer button { width: 100%; justify-content: center; }
  .form-grupo-doble { grid-template-columns: 1fr; }
  .modal-input, .modal-select { font-size: 16px; }
  .detalle-acciones { flex-direction: column; }
  .btn-accion-detalle { width: 100%; }
}
</style>