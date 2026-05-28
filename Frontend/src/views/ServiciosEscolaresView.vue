<template>
  <MainLayout v-slot="{ busquedaGlobal }">
    <div class="page">

      <!-- ── BREADCRUMB ── -->
      <div class="bc">
        <router-link to="/inicio" class="bc-lnk">Inicio</router-link>
        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" class="bc-sep" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="bc-cur">Servicios Escolares</span>
      </div>

      <!-- ── TOAST ── -->
      <Transition name="toast-slide">
        <div v-if="notif.visible" class="toast" :class="notif.tipo" role="alert">
          <svg v-if="notif.tipo==='exito'" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          <svg v-else xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          {{ notif.mensaje }}
        </div>
      </Transition>

      <!-- ══════════════════════════════════
           HERO BANNER (igual que la maqueta)
      ══════════════════════════════════ -->
      <div class="hero">
        <div class="hero-deco"  aria-hidden="true"></div>
        <div class="hero-deco2" aria-hidden="true"></div>

        <div class="hero-left">
          <div class="hero-badge">
            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/>
            </svg>
            Servicios Escolares
          </div>
          <div class="hero-title">Consulta de alumnos</div>
          <div class="hero-sub">Busca por número de control para acceder al expediente completo del alumno</div>

          <div class="hero-form">
            <div class="hero-input-wrap" :class="{ 'hi-focus': inputFocused, 'hi-error': estadoBusqueda === 'error' }">
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="hi-lupa" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
              </svg>
              <input
                ref="inputRef"
                v-model="numCtrl"
                class="hi-inp"
                type="text"
                placeholder="Número de control (ej. 22031234)"
                maxlength="8"
                inputmode="numeric"
                aria-label="Número de control del alumno"
                @keydown.enter="buscar"
                @focus="inputFocused = true"
                @blur="inputFocused = false"
                @input="onInput"
              />
              <div v-if="estadoBusqueda === 'cargando'" class="hi-spinner" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="spin">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 100 16 8 8 0 01-8-8z"/>
                </svg>
              </div>
              <button v-else-if="numCtrl" class="hi-clear" @click="limpiar" type="button" aria-label="Limpiar">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
              </button>
            </div>
            <button
              class="hero-btn"
              :disabled="estadoBusqueda === 'cargando' || !numCtrl.trim()"
              @click="buscar"
              type="button"
            >
              <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
              </svg>
              {{ estadoBusqueda === 'cargando' ? 'Buscando...' : 'Buscar' }}
            </button>
          </div>

          <Transition name="fade-slide">
            <p v-if="estadoBusqueda === 'error'" class="hero-hint hint-err">
              <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              {{ mensajeError }}
            </p>
            <p v-else-if="estadoBusqueda === 'no-encontrado'" class="hero-hint hint-warn">
              <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              No se encontró ningún alumno con el número <strong>{{ ultimaBusq }}</strong>.
            </p>
            <p v-else class="hero-hint">
              <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              Ingresa los 8 dígitos del número de control y presiona Enter o el botón Buscar
            </p>
          </Transition>
        </div>

        <div class="hero-stats">
          <div class="hstat">
            <div class="hstat-n">{{ fmt(kpis.alumnosActivos) }}</div>
            <div class="hstat-l">Alumnos activos</div>
          </div>
          <div class="hstat">
            <div class="hstat-n">Ene–Jun</div>
            <div class="hstat-l">Periodo 2025</div>
          </div>
        </div>
      </div>

      <!-- ══════════════════════════════════
           RESULTADO DEL ALUMNO
      ══════════════════════════════════ -->
      <Transition name="resultado-appear">
        <section v-if="alumno && estadoBusqueda === 'exito'" class="resultado">

          <!-- Header alumno -->
          <div class="al-hdr">
            <div class="al-av">{{ iniciales(alumno.nombre) }}</div>
            <div class="al-info">
              <div class="al-nombre-row">
                <h2 class="al-nombre">{{ alumno.nombre }}</h2>
                <span class="al-estatus" :class="estatusClass(alumno.estatus)">{{ alumno.estatus }}</span>
              </div>
              <p class="al-meta">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0"/></svg>
                N° Control: <strong>{{ alumno.numero_control }}</strong>
              </p>
              <p class="al-meta">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/></svg>
                {{ alumno.carrera }} · Semestre {{ alumno.semestre_actual ?? alumno.semestre }}
              </p>
              <p class="al-meta">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                {{ alumno.email }}
              </p>
            </div>
            <div class="al-acciones">
              <button class="btn-outline" @click="limpiar" type="button">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                Nueva búsqueda
              </button>
              <button class="btn-primary" @click="irExpediente" type="button">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Ver expediente completo
              </button>
            </div>
          </div>

          <!-- Tabs -->
          <div class="tabs-bar">
            <button v-for="tab in tabs" :key="tab.id" class="tab-btn" :class="{ 'tab-act': tabActivo === tab.id }" @click="tabActivo = tab.id" type="button">
              {{ tab.label }}
            </button>
          </div>

          <!-- Contenido tabs -->
          <Transition name="tab-fade" mode="out-in">

            <!-- General -->
            <div v-if="tabActivo === 'general'" key="general" class="tab-body">
              <div class="info-grid">
                <div class="info-card">
                  <h3 class="info-t">Información Personal</h3>
                  <dl class="info-list">
                    <div class="if"><dt>Nombre completo</dt><dd>{{ alumno.nombre }}</dd></div>
                    <div class="if"><dt>Número de control</dt><dd class="mono">{{ alumno.numero_control }}</dd></div>
                    <div class="if"><dt>CURP</dt><dd class="mono">{{ alumno.curp ?? 'No registrado' }}</dd></div>
                    <div class="if"><dt>Fecha de nacimiento</dt><dd>{{ fFecha(alumno.fecha_nacimiento) }}</dd></div>
                    <div class="if"><dt>Correo institucional</dt><dd>{{ alumno.email }}</dd></div>
                    <div class="if"><dt>Teléfono</dt><dd>{{ alumno.telefono ?? 'No registrado' }}</dd></div>
                  </dl>
                </div>
                <div class="info-card">
                  <h3 class="info-t">Información Académica</h3>
                  <dl class="info-list">
                    <div class="if"><dt>Carrera</dt><dd>{{ alumno.carrera }}</dd></div>
                    <div class="if"><dt>Semestre actual</dt><dd>{{ alumno.semestre_actual ?? alumno.semestre }}° Semestre</dd></div>
                    <div class="if"><dt>Periodo de ingreso</dt><dd>{{ alumno.fecha_ingreso ?? alumno.periodo_ingreso ?? 'No registrado' }}</dd></div>
                    <div class="if"><dt>Plan de estudios</dt><dd>{{ alumno.plan_estudios ?? 'No registrado' }}</dd></div>
                    <div class="if"><dt>Modalidad</dt><dd>{{ alumno.modalidad ?? 'Escolarizado' }}</dd></div>
                    <div class="if"><dt>Estatus</dt><dd><span class="chip" :class="estatusClass(alumno.estatus)">{{ alumno.estatus }}</span></dd></div>
                  </dl>
                </div>
              </div>
            </div>

            <!-- Académico -->
            <div v-else-if="tabActivo === 'academico'" key="academico" class="tab-body">
              <div class="acad-grid">
                <div class="acad-kpi"><div class="acad-val azul">{{ alumno.estado_academico?.promedio ?? '—' }}</div><div class="acad-lbl">Promedio General</div></div>
                <div class="acad-kpi"><div class="acad-val verde">{{ alumno.estado_academico?.creditos_obtenidos ?? alumno.kardex_resumen?.creditos_acumulados ?? '—' }}</div><div class="acad-lbl">Créditos Obtenidos</div></div>
                <div class="acad-kpi"><div class="acad-val naranja">{{ alumno.estado_academico?.materias_cursadas ?? '—' }}</div><div class="acad-lbl">Materias Cursadas</div></div>
                <div class="acad-kpi"><div class="acad-val" :class="(alumno.estado_academico?.materias_reprobadas ?? 0) > 0 ? 'rojo' : 'verde'">{{ alumno.estado_academico?.materias_reprobadas ?? '0' }}</div><div class="acad-lbl">Materias Reprobadas</div></div>
              </div>
              <div v-if="alumno.estado_academico" class="prog-card">
                <div class="prog-hdr">
                  <span class="prog-lbl">Avance en créditos de la carrera</span>
                  <span class="prog-pct">{{ Math.round((alumno.estado_academico.creditos_obtenidos / alumno.estado_academico.creditos_totales) * 100) }}%</span>
                </div>
                <div class="prog-bg"><div class="prog-fill" :style="{ width: Math.round((alumno.estado_academico.creditos_obtenidos / alumno.estado_academico.creditos_totales) * 100) + '%' }"></div></div>
                <p class="prog-det">{{ alumno.estado_academico.creditos_obtenidos }} de {{ alumno.estado_academico.creditos_totales }} créditos</p>
              </div>
            </div>

            <!-- Kardex -->
            <div v-else-if="tabActivo === 'kardex'" key="kardex" class="tab-body">
              <div v-if="alumno.kardex && alumno.kardex.length > 0">
                <div v-for="(per, pi) in alumno.kardex" :key="pi" class="kardex-per">
                  <div class="kardex-per-hdr">
                    <span class="kardex-per-n">{{ per.nombre }}</span>
                    <span class="kardex-per-p">Promedio: <strong>{{ per.promedio }}</strong></span>
                  </div>
                  <div class="tabla-wrap">
                    <table class="tabla">
                      <thead><tr><th>Clave</th><th>Materia</th><th>Créd.</th><th>Calif.</th><th>Estatus</th></tr></thead>
                      <tbody>
                        <tr v-for="(m, mi) in per.materias" :key="mi">
                          <td class="mono sm">{{ m.clave }}</td>
                          <td>{{ m.nombre }}</td>
                          <td class="tc">{{ m.creditos }}</td>
                          <td class="tc"><span class="calif" :class="califClass(m.calificacion)">{{ m.calificacion }}</span></td>
                          <td class="tc"><span class="chip-mini" :class="m.estatus==='Aprobada' ? 'ch-v' : m.estatus==='Reprobada' ? 'ch-r' : 'ch-g'">{{ m.estatus }}</span></td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
              <div v-else class="tab-vacio">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="none" viewBox="0 0 24 24" stroke="#BDBDBD" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <p>No hay información de kardex disponible para este alumno.</p>
              </div>
            </div>

            <!-- Horario -->
            <div v-else-if="tabActivo === 'horario'" key="horario" class="tab-body">
              <div v-if="alumno.horario && alumno.horario.length > 0" class="horario-list">
                <div v-for="(c, ci) in alumno.horario" :key="ci" class="horario-card">
                  <div class="hor-dia">{{ c.dia }}</div>
                  <div class="hor-info"><p class="hor-mat">{{ c.materia }}</p><p class="hor-det">{{ c.hora_inicio }} – {{ c.hora_fin }} · {{ c.aula }}</p></div>
                  <div class="hor-doc"><span class="hor-doc-l">Docente</span><span class="hor-doc-n">{{ c.docente }}</span></div>
                </div>
              </div>
              <div v-else class="tab-vacio">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="none" viewBox="0 0 24 24" stroke="#BDBDBD" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <p>No hay horario registrado para el periodo actual.</p>
              </div>
            </div>

            <!-- Adicional -->
            <div v-else-if="tabActivo === 'adicional'" key="adicional" class="tab-body">
              <div class="info-grid">
                <div class="info-card">
                  <h3 class="info-t">Adeudos y Documentos</h3>
                  <dl class="info-list">
                    <div class="if"><dt>Adeudo financiero</dt><dd><span class="chip" :class="(alumno.adeudo ?? 0) > 0 ? 'ch-r' : 'ch-v'">{{ (alumno.adeudo ?? 0) > 0 ? `$${alumno.adeudo} MXN` : 'Sin adeudo' }}</span></dd></div>
                    <div class="if"><dt>Acta de nacimiento</dt><dd>{{ alumno.documentos?.acta_nacimiento ? 'Entregado' : 'Pendiente' }}</dd></div>
                    <div class="if"><dt>CURP certificada</dt><dd>{{ alumno.documentos?.curp ? 'Entregado' : 'Pendiente' }}</dd></div>
                    <div class="if"><dt>Certificado de secundaria</dt><dd>{{ alumno.documentos?.cert_secundaria ? 'Entregado' : 'Pendiente' }}</dd></div>
                    <div class="if"><dt>Fotografías</dt><dd>{{ alumno.documentos?.fotografias ? 'Entregado' : 'Pendiente' }}</dd></div>
                  </dl>
                </div>
                <div class="info-card">
                  <h3 class="info-t">Contacto de Emergencia</h3>
                  <dl class="info-list">
                    <div class="if"><dt>Nombre</dt><dd>{{ alumno.contacto_emergencia?.nombre ?? 'No registrado' }}</dd></div>
                    <div class="if"><dt>Parentesco</dt><dd>{{ alumno.contacto_emergencia?.parentesco ?? 'No registrado' }}</dd></div>
                    <div class="if"><dt>Teléfono</dt><dd>{{ alumno.contacto_emergencia?.telefono ?? 'No registrado' }}</dd></div>
                  </dl>
                </div>
              </div>
            </div>

          </Transition>
        </section>
      </Transition>

      <!-- ══════════════════════════════════
           DASHBOARD (cuando no hay resultado)
      ══════════════════════════════════ -->
      <Transition name="fade">
        <div v-if="!alumno || estadoBusqueda !== 'exito'" class="dash">

          <!-- KPI GRID -->
          <div class="kpi-grid">

            <div class="kpi kpi-feat" @click="router.push('/alumnos')" role="button" tabindex="0">
              <div class="kpi-ico ki-bw" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
              </div>
              <div class="kpi-body">
                <div class="kpi-lbl">Alumnos Activos</div>
                <div v-if="cargando" class="kpi-sk kpi-sk-d"></div>
                <div v-else class="kpi-val">{{ fmt(kpis.alumnosActivos) }}</div>
                <div class="kpi-lnk">
                  <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                  Ver alumnos
                </div>
              </div>
            </div>

            <div class="kpi" @click="router.push('/inscripciones')" role="button" tabindex="0">
              <div class="kpi-ico ki-g" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
              </div>
              <div class="kpi-body">
                <div class="kpi-lbl">Inscripciones del Periodo</div>
                <div v-if="cargando" class="kpi-sk"></div>
                <div v-else class="kpi-val">{{ fmt(kpis.inscripcionesPeriodo) }}</div>
                <div class="kpi-lnk">
                  <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                  Ver inscripciones
                </div>
              </div>
            </div>

            <div class="kpi" @click="router.push('/gestion-grupos')" role="button" tabindex="0">
              <div class="kpi-ico ki-a" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
              </div>
              <div class="kpi-body">
                <div class="kpi-lbl">Grupos Abiertos</div>
                <div v-if="cargando" class="kpi-sk"></div>
                <div v-else class="kpi-val">{{ kpis.gruposAbiertos }}</div>
                <div class="kpi-lnk">
                  <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                  Ver grupos
                </div>
              </div>
            </div>

            <div class="kpi" @click="router.push('/evaluaciones')" role="button" tabindex="0">
              <div class="kpi-ico ki-v" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
              </div>
              <div class="kpi-body">
                <div class="kpi-lbl">Evaluaciones Pendientes</div>
                <div v-if="cargando" class="kpi-sk"></div>
                <div v-else class="kpi-val">{{ kpis.evaluacionesPendientes }}</div>
                <div class="kpi-lnk">
                  <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                  Ver evaluaciones
                </div>
              </div>
            </div>

          </div>

          <!-- MAIN GRID: Accesos + Actividad -->
          <div class="main-grid">

            <!-- Accesos rápidos -->
            <div class="card">
              <div class="card-h">
                <span class="card-t">Accesos Rápidos</span>
                <span class="card-lk">Ver todos</span>
              </div>
              <div class="acc-list">

                <div class="acc-item" @click="router.push('/inscripcion')" role="button" tabindex="0">
                  <div class="acc-ico ai-g" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                  </div>
                  <div class="acc-body"><div class="acc-t">Gestionar Inscripciones</div><div class="acc-d">Da de alta a los alumnos al periodo actual</div></div>
                  <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="acc-arrow"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </div>

                <div class="acc-item" @click="router.push('/gestion-grupos')" role="button" tabindex="0">
                  <div class="acc-ico ai-b" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                  </div>
                  <div class="acc-body"><div class="acc-t">Administrar Grupos</div><div class="acc-d">Visualiza y organiza grupos por carrera y turno</div></div>
                  <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="acc-arrow"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </div>

                <div class="acc-item" @click="router.push('/evaluaciones')" role="button" tabindex="0">
                  <div class="acc-ico ai-a" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                  </div>
                  <div class="acc-body"><div class="acc-t">Supervisar Evaluaciones</div><div class="acc-d">Revisa evaluaciones pendientes y actas de calificación</div></div>
                  <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="acc-arrow"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </div>

                <div class="acc-item prox">
                  <div class="acc-ico ai-gr" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                  </div>
                  <div class="acc-body"><div class="acc-t">Expediente Académico</div><div class="acc-d">Consulta el historial académico completo</div></div>
                  <span class="prox-bdg">Próximamente</span>
                </div>

                <div class="acc-item" @click="router.push('/kardex')" role="button" tabindex="0">
                  <div class="acc-ico ai-g" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                  </div>
                  <div class="acc-body"><div class="acc-t">Kardex del Alumno</div><div class="acc-d">Historial de calificaciones por semestre</div></div>
                  <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="acc-arrow"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </div>

              </div>
            </div>

            <!-- Actividad reciente -->
            <div class="card">
              <div class="card-h">
                <span class="card-t">Actividad Reciente</span>
                <router-link to="/bitacora" class="card-lk">
                  <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                  Ver bitácora
                </router-link>
              </div>
              <div class="bit-list">
                <div v-if="cargandoBit" class="bit-load">
                  <div class="spinner" aria-hidden="true"></div>
                  <span>Cargando...</span>
                </div>
                <template v-else-if="bitacora.length > 0">
                  <div v-for="(item, i) in bitacora" :key="item.id_bitacora || i" class="bit-item">
                    <div class="bit-av" aria-hidden="true">{{ (item.usuario || item.nombre_usuario || '?').slice(0,2).toUpperCase() }}</div>
                    <div class="bit-body">
                      <div class="bit-r1">
                        <span class="bit-usr">{{ item.usuario || item.nombre_usuario }}</span>
                        <span class="bdg" :class="claseBadge(item.accion)">{{ item.accion }}</span>
                      </div>
                      <div class="bit-desc">{{ item.accion }}</div>
                      <div class="bit-t">{{ tRel(item.fecha_hora) }} · {{ item.nombre_modulo || 'Sistema' }}</div>
                    </div>
                  </div>
                </template>
                <div v-else class="ev">Sin actividad reciente</div>
              </div>
            </div>

          </div>

          <!-- BOTTOM GRID: Gráficas -->
          <div class="bottom-grid">

            <div class="card">
              <div class="card-h">
                <span class="card-t">Alumnos por carrera</span>
                <span class="card-lk">
                  <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                  Detalle
                </span>
              </div>
              <canvas ref="c1" height="180" role="img" aria-label="Alumnos por carrera"></canvas>
            </div>

            <div class="card">
              <div class="card-h">
                <span class="card-t">Inscripciones por semestre</span>
                <span class="card-lk">
                  <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                  Analítica
                </span>
              </div>
              <canvas ref="c2" height="180" role="img" aria-label="Inscripciones por semestre"></canvas>
            </div>

            <div class="card">
              <div class="card-h">
                <span class="card-t">Estado de inscripciones</span>
                <span class="bdg bg-g" style="font-size:9px;font-weight:700">Periodo activo</span>
              </div>
              <canvas ref="c3" height="140" role="img" aria-label="Porcentaje de inscripciones"></canvas>
              <div class="ins-mini" style="margin-top:12px">
                <div class="im"><div class="im-v">{{ fmt(kpis.inscripcionesCompletas) }}</div><div class="im-l">Completadas</div></div>
                <div class="im"><div class="im-v" style="color:#F2994A">{{ fmt(kpis.inscripcionesPendientes) }}</div><div class="im-l">Pendientes</div></div>
              </div>
            </div>

          </div>

          <!-- Error carga -->
          <div v-if="errorCarga" class="alerta-err" role="alert">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>No se pudieron cargar los datos. Verifica la conexión con el servidor.</span>
            <button class="btn-reintentar" @click="cargarDatos" type="button">Reintentar</button>
          </div>

        </div>
      </Transition>

      <div class="spacer"></div>
      <footer class="pie">© 2026 Tecnológico Nacional de México · Todos los derechos reservados</footer>

    </div>
  </MainLayout>
</template>

<script setup>
import { ref, onMounted, nextTick, watch } from 'vue'
import { useRouter } from 'vue-router'
import MainLayout from '@/layouts/MainLayout.vue'

const router = useRouter()
const API    = `${import.meta.env.VITE_API_URL}/api`

// ── Refs canvas ───────────────────────────────────────────────────────
const c1 = ref(null)
const c2 = ref(null)
const c3 = ref(null)
let chart1 = null, chart2 = null, chart3 = null

// ── Búsqueda ──────────────────────────────────────────────────────────
const inputRef      = ref(null)
const numCtrl       = ref('')
const ultimaBusq    = ref('')
const inputFocused  = ref(false)
const estadoBusqueda = ref('idle')  // idle | cargando | exito | no-encontrado | error
const mensajeError  = ref('')
const alumno        = ref(null)
const tabActivo     = ref('general')

const tabs = [
  { id: 'general',   label: 'Datos Generales'  },
  { id: 'academico', label: 'Estado Académico' },
  { id: 'kardex',    label: 'Kardex'           },
  { id: 'horario',   label: 'Horario'          },
  { id: 'adicional', label: 'Adicional'        },
]

const limpiar = () => {
  numCtrl.value        = ''
  alumno.value         = null
  estadoBusqueda.value = 'idle'
  mensajeError.value   = ''
  ultimaBusq.value     = ''
  tabActivo.value      = 'general'
  nextTick(() => inputRef.value?.focus())
}

const onInput = () => {
  if (estadoBusqueda.value !== 'cargando') {
    alumno.value         = null
    estadoBusqueda.value = 'idle'
  }
}

const buscar = async () => {
  const nc = numCtrl.value.trim()
  if (!nc) return
  estadoBusqueda.value = 'cargando'
  ultimaBusq.value     = nc
  alumno.value         = null
  tabActivo.value      = 'general'
  try {
    const res = await fetch(`${API}/alumnos/buscar?numero_control=${encodeURIComponent(nc)}`)
    if (res.status === 404) { estadoBusqueda.value = 'no-encontrado'; return }
    if (!res.ok) throw new Error(`Error ${res.status}`)
    const data = await res.json()
    if (!data || (!data.id && !data.numero_control)) { estadoBusqueda.value = 'no-encontrado'; return }
    alumno.value         = data
    estadoBusqueda.value = 'exito'
    mostrarNotif(`Alumno encontrado: ${data.nombre}`, 'exito')
  } catch (e) {
    console.error('[Escolares] buscar:', e)
    estadoBusqueda.value = 'error'
    mensajeError.value   = 'Ocurrió un error al conectar con el servidor. Intenta de nuevo.'
  }
}

const irExpediente = () => { if (alumno.value?.id) router.push(`/alumnos/${alumno.value.id}`) }

// ── KPIs ──────────────────────────────────────────────────────────────
const cargando   = ref(false)
const errorCarga = ref(false)
const kpis = ref({
  alumnosActivos:          0,
  inscripcionesPeriodo:    0,
  inscripcionesCompletas:  0,
  inscripcionesPendientes: 0,
  pctInscripciones:        0,
  gruposAbiertos:          0,
  evaluacionesPendientes:  0,
})

const cargarDatos = async () => {
  cargando.value   = true
  errorCarga.value = false
  try {
    const res  = await fetch(`${API}/dashboard`)
    if (!res.ok) throw new Error()
    const data = await res.json()
    kpis.value.alumnosActivos          = data.kpis?.alumnos       ?? 0
    kpis.value.inscripcionesPeriodo    = data.kpis?.inscripciones ?? 0
    kpis.value.gruposAbiertos          = data.kpis?.grupos        ?? 0
    kpis.value.evaluacionesPendientes  = data.kpis?.evaluaciones  ?? 0
    kpis.value.inscripcionesCompletas  = data.kpis?.ins_completas ?? Math.round((data.kpis?.inscripciones ?? 0) * 0.89)
    kpis.value.inscripcionesPendientes = data.kpis?.ins_pendientes ?? Math.round((data.kpis?.inscripciones ?? 0) * 0.11)
    kpis.value.pctInscripciones        = data.kpis?.pct_ins       ?? 89
  } catch {
    errorCarga.value = true
    // Fallback
    kpis.value = { alumnosActivos:1235, inscripcionesPeriodo:7020, inscripcionesCompletas:1147, inscripcionesPendientes:137, pctInscripciones:89, gruposAbiertos:72, evaluacionesPendientes:0 }
  } finally {
    cargando.value = false
    nextTick(() => inicializarCharts())
  }
}

// ── Bitácora ──────────────────────────────────────────────────────────
const bitacora   = ref([])
const cargandoBit = ref(false)

const cargarBitacora = async () => {
  cargandoBit.value = true
  try {
    const res  = await fetch(`${API}/bitacora?limit=5`)
    if (!res.ok) throw new Error()
    const data = await res.json()
    bitacora.value = Array.isArray(data) ? data : (data.bitacora ?? [])
  } catch { /* silencioso */ }
  finally { cargandoBit.value = false }
}

// ── Charts con Chart.js CDN ───────────────────────────────────────────
const inicializarCharts = () => {
  if (typeof window === 'undefined' || !window.Chart) return

  const ChartJS = window.Chart
  ChartJS.defaults.font.family = "'Montserrat', system-ui, sans-serif"
  ChartJS.defaults.font.size   = 11
  ChartJS.defaults.color       = '#828282'

  // Chart 1: Barras por carrera
  if (c1.value) {
    if (chart1) chart1.destroy()
    chart1 = new ChartJS(c1.value, {
      type: 'bar',
      data: {
        labels: ['ISC', 'Industrial', 'Eléctrica', 'Bioquímica', 'Mecánica', 'Empresarial'],
        datasets: [{
          data: [312, 268, 198, 176, 174, 156],
          backgroundColor: ['#132B4F','#1A4184','#1D52B7','#2F80ED','#27AE60','#F2994A'],
          borderRadius: 6,
          borderSkipped: false,
        }]
      },
      options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false }, tooltip: { callbacks: { label: c => ' ' + c.parsed.y + ' alumnos' } } },
        scales: {
          x: { grid: { display: false }, border: { display: false }, ticks: { font: { size: 10 } } },
          y: { grid: { color: '#F4F6F9' }, border: { display: false }, ticks: { font: { size: 10 } } },
        }
      }
    })
  }

  // Chart 2: Línea por semestre
  if (c2.value) {
    if (chart2) chart2.destroy()
    chart2 = new ChartJS(c2.value, {
      type: 'line',
      data: {
        labels: ['1°','2°','3°','4°','5°','6°','7°','8°'],
        datasets: [{
          data: [180, 165, 158, 144, 152, 138, 175, 172],
          borderColor: '#1D52B7',
          backgroundColor: 'rgba(29,82,183,0.07)',
          borderWidth: 2.5,
          fill: true,
          tension: 0.4,
          pointBackgroundColor: '#fff',
          pointBorderColor: '#1D52B7',
          pointBorderWidth: 2,
          pointRadius: 4,
          pointHoverRadius: 6,
        }]
      },
      options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false }, tooltip: { callbacks: { label: c => ' ' + c.parsed.y + ' alumnos' } } },
        scales: {
          x: { grid: { display: false }, border: { display: false }, ticks: { font: { size: 10 } } },
          y: { grid: { color: '#F4F6F9' }, border: { display: false }, min: 100, ticks: { font: { size: 10 } } },
        }
      }
    })
  }

  // Chart 3: Donut inscripciones
  if (c3.value) {
    if (chart3) chart3.destroy()
    const pct = kpis.value.pctInscripciones || 89
    chart3 = new ChartJS(c3.value, {
      type: 'doughnut',
      data: {
        labels: [`Completadas ${pct}%`, `Pendientes ${100 - pct}%`],
        datasets: [{
          data: [pct, 100 - pct],
          backgroundColor: ['#1D52B7', 'rgba(242,153,74,0.15)'],
          borderColor: ['#1D52B7', '#F2994A'],
          borderWidth: 1,
          hoverOffset: 4,
        }]
      },
      options: {
        responsive: true, maintainAspectRatio: false,
        cutout: '72%',
        plugins: {
          legend: { display: false },
          tooltip: { callbacks: { label: c => ' ' + c.label } },
        }
      }
    })
  }
}

// ── Toast ─────────────────────────────────────────────────────────────
const notif = ref({ visible: false, mensaje: '', tipo: 'exito' })
let timerNotif = null
const mostrarNotif = (mensaje, tipo = 'exito') => {
  if (timerNotif) clearTimeout(timerNotif)
  notif.value = { visible: true, mensaje, tipo }
  timerNotif = setTimeout(() => { notif.value.visible = false }, 3500)
}

// ── Helpers ───────────────────────────────────────────────────────────
const fmt      = (n) => n?.toLocaleString('es-MX') ?? '0'
const iniciales = (n = '') => n.split(' ').slice(0,2).map(p => p[0]).join('').toUpperCase()

const fFecha = (iso) => {
  if (!iso) return 'No registrado'
  try { return new Date(iso).toLocaleDateString('es-MX', { day:'2-digit', month:'long', year:'numeric' }) }
  catch { return iso }
}

const estatusClass = (e = '') => {
  const m = { 'Activo':'es-act','Inactivo':'es-ina','Egresado':'es-egr','Suspendido':'es-sus','Baja':'es-baj' }
  return m[e] ?? 'es-ina'
}

const califClass = (c) => {
  if (c === null || c === undefined) return 'ca-nd'
  if (c >= 80) return 'ca-v'
  if (c >= 70) return 'ca-a'
  return 'ca-r'
}

const tRel = (f) => {
  if (!f) return ''
  const m = Math.floor((Date.now() - new Date(f).getTime()) / 60000)
  if (m < 1)  return 'Ahora'
  if (m < 60) return `Hace ${m} min`
  const h = Math.floor(m/60)
  if (h < 24) return `Hace ${h} h`
  return `Hace ${Math.floor(h/24)} día(s)`
}

const claseBadge = (a = '') => {
  const s = a.toLowerCase()
  if (s.includes('inscri') || s.includes('cre') || s.includes('alta')) return 'bg-g'
  if (s.includes('edit') || s.includes('actualiz'))                      return 'bg-a'
  if (s.includes('elim') || s.includes('baja'))                          return 'bg-r'
  return 'bg-b'
}

// ── Lifecycle ─────────────────────────────────────────────────────────
onMounted(() => {
  cargarDatos()
  cargarBitacora()
  nextTick(() => inputRef.value?.focus())

  // Cargar Chart.js desde CDN si no está disponible
  if (!window.Chart) {
    const script = document.createElement('script')
    script.src = 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js'
    script.onload = () => nextTick(() => inicializarCharts())
    document.head.appendChild(script)
  } else {
    nextTick(() => inicializarCharts())
  }
})
</script>

<style scoped>
/* ══════════════════════════════════════════════════════
   BASE
══════════════════════════════════════════════════════ */
.page {
  font-family: 'Montserrat', system-ui, sans-serif;
  background: #F4F6F9;
  display: flex;
  flex-direction: column;
  gap: 14px;
  min-height: 100%;
  padding-bottom: 2rem;
}
.spacer { flex: 1; }

/* Breadcrumb */
.bc { display:flex; align-items:center; gap:5px; font-size:11px; color:#828282; }
.bc-sep { color:#BDBDBD; }
.bc-lnk { color:#828282; text-decoration:none; transition:color .15s; }
.bc-lnk:hover { color:#1D52B7; }
.bc-cur { color:#1D52B7; font-weight:600; }

/* Toast */
.toast { position:fixed; bottom:2rem; right:2rem; display:flex; align-items:center; gap:10px; padding:.9rem 1.4rem; border-radius:10px; color:#FFFFFF; font-weight:700; font-size:.9rem; box-shadow:0 8px 24px rgba(0,0,0,.15); z-index:3000; font-family:'Montserrat',sans-serif; max-width:380px; }
.toast.exito { background:#0B2545; }
.toast.error { background:#EB5757; }
.toast svg { flex-shrink:0; stroke:#FFFFFF; }
.toast-slide-enter-active,.toast-slide-leave-active { transition:all .3s ease; }
.toast-slide-enter-from,.toast-slide-leave-to { opacity:0; transform:translateX(100%); }

/* ══ HERO ══ */
.hero { background:#0B2545; border-radius:14px; padding:22px 26px; display:flex; align-items:center; gap:20px; position:relative; overflow:hidden; }
.hero-deco  { position:absolute; right:-40px; top:-40px; width:220px; height:220px; border-radius:50%; background:rgba(29,82,183,.08); pointer-events:none; }
.hero-deco2 { position:absolute; right:60px; bottom:-60px; width:140px; height:140px; border-radius:50%; background:rgba(29,82,183,.05); pointer-events:none; }
.hero-left  { flex:1; position:relative; }

.hero-badge { display:inline-flex; align-items:center; gap:5px; background:rgba(47,128,237,.18); border:1px solid rgba(47,128,237,.3); border-radius:20px; padding:3px 10px; font-size:10px; font-weight:600; color:#2F80ED; margin-bottom:10px; letter-spacing:.03em; }
.hero-badge svg { stroke:#2F80ED; }
.hero-title { font-size:24px; font-weight:700; color:#FFFFFF; margin-bottom:5px; line-height:1.2; font-family:'Montserrat',sans-serif; }
.hero-sub   { font-size:12px; color:rgba(255,255,255,.5); margin-bottom:16px; font-weight:300; font-family:'Montserrat',sans-serif; }

.hero-form { display:flex; gap:8px; max-width:520px; }
.hero-input-wrap { flex:1; display:flex; align-items:center; background:rgba(255,255,255,.09); border:1px solid rgba(255,255,255,.18); border-radius:9px; padding:0 12px; transition:border-color .15s; }
.hi-focus  { border-color:rgba(29,82,183,.7); background:rgba(29,82,183,.15); }
.hi-error  { border-color:#EB5757 !important; }
.hi-lupa   { stroke:#828282; flex-shrink:0; margin-right:8px; }
.hi-inp    { flex:1; background:transparent; border:none; outline:none; font-family:'Montserrat',sans-serif; font-size:13px; color:#FFFFFF; padding:10px 0; }
.hi-inp::placeholder { color:#828282; }
.hi-spinner,.hi-clear { flex-shrink:0; display:flex; align-items:center; justify-content:center; }
.hi-clear { background:none; border:none; cursor:pointer; color:#828282; padding:4px; border-radius:4px; transition:color .15s; }
.hi-clear:hover { color:#EB5757; }
.hi-clear svg { stroke:currentColor; }
.spin { animation:girar .8s linear infinite; stroke:#1D52B7; }
@keyframes girar { to { transform:rotate(360deg); } }

.hero-btn { background:#1D52B7; border:none; border-radius:9px; padding:10px 20px; color:#FFFFFF; font-size:13px; font-weight:600; cursor:pointer; display:flex; align-items:center; gap:6px; font-family:'Montserrat',sans-serif; white-space:nowrap; transition:background .15s; }
.hero-btn:hover:not(:disabled) { background:#1A4184; }
.hero-btn:disabled { opacity:.5; cursor:not-allowed; }
.hero-btn svg { stroke:#FFFFFF; }

.hero-hint { font-size:10px; color:#828282; margin-top:8px; display:flex; align-items:center; gap:4px; font-weight:400; font-family:'Montserrat',sans-serif; }
.hero-hint svg { stroke:#828282; flex-shrink:0; }
.hint-err  { color:rgba(235,87,87,.8) !important; }
.hint-err svg { stroke:rgba(235,87,87,.8); }
.hint-warn { color:rgba(255,255,255,.8) !important; }

.hero-stats { display:flex; flex-direction:column; gap:10px; position:relative; }
.hstat { background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.1); border-radius:10px; padding:12px 16px; min-width:130px; text-align:center; }
.hstat-n { font-size:20px; font-weight:700; color:#FFFFFF; font-family:'Montserrat',sans-serif; }
.hstat-l { font-size:10px; color:#828282; margin-top:2px; font-family:'Montserrat',sans-serif; }

/* ══ RESULTADO ALUMNO ══ */
.resultado { background:#FFFFFF; border-radius:14px; border:1px solid #E0E0E0; box-shadow:0 4px 20px rgba(0,0,0,.06); overflow:hidden; }
.resultado-appear-enter-active { transition:all .35s cubic-bezier(.16,1,.3,1); }
.resultado-appear-enter-from   { opacity:0; transform:translateY(20px); }

.al-hdr { display:flex; align-items:flex-start; gap:1.5rem; padding:1.75rem 2rem; background:linear-gradient(to right,rgba(29,82,183,.05),#FFFFFF); border-bottom:1px solid #E0E0E0; flex-wrap:wrap; }
.al-av  { width:80px; height:80px; border-radius:50%; background:#0B2545; color:#FFFFFF; display:flex; align-items:center; justify-content:center; font-size:1.6rem; font-weight:700; flex-shrink:0; border:3px solid #FFFFFF; box-shadow:0 4px 12px rgba(11,37,69,.25); font-family:'Montserrat',sans-serif; }
.al-info { flex:1; min-width:0; }
.al-nombre-row { display:flex; align-items:center; gap:.75rem; flex-wrap:wrap; margin-bottom:.4rem; }
.al-nombre { font-size:1.35rem; font-weight:700; color:#333333; margin:0; font-family:'Montserrat',sans-serif; }
.al-estatus { font-size:.78rem; font-weight:700; padding:3px 12px; border-radius:20px; white-space:nowrap; flex-shrink:0; }
.al-meta { display:flex; align-items:center; gap:5px; font-size:.88rem; color:#828282; margin:0 0 .2rem; font-family:'Montserrat',sans-serif; }
.al-meta svg { stroke:#828282; flex-shrink:0; }
.al-meta strong { color:#333333; }
.al-acciones { display:flex; flex-direction:column; gap:.6rem; flex-shrink:0; align-items:flex-end; }

.btn-primary { display:flex; align-items:center; gap:6px; padding:.55rem 1.1rem; border-radius:8px; font-family:'Montserrat',sans-serif; font-size:.85rem; font-weight:600; cursor:pointer; transition:all .15s; white-space:nowrap; background:#0B2545; color:#FFFFFF; border:none; }
.btn-primary:hover { background:#1A4184; }
.btn-outline { display:flex; align-items:center; gap:6px; padding:.55rem 1.1rem; border-radius:8px; font-family:'Montserrat',sans-serif; font-size:.85rem; font-weight:600; cursor:pointer; transition:all .15s; white-space:nowrap; background:transparent; color:#0B2545; border:1.5px solid #0B2545; }
.btn-outline:hover { background:rgba(29,82,183,.05); }
.btn-primary svg,.btn-outline svg { stroke:currentColor; }

/* Estatus chips */
.es-act { background:rgba(39,174,96,.10);  color:#27AE60; }
.es-ina { background:#F2F4F7; color:#828282; }
.es-egr { background:rgba(47,128,237,.10); color:#1D52B7; }
.es-sus { background:rgba(242,153,74,.10); color:#F2994A; }
.es-baj { background:rgba(235,87,87,.10);  color:#EB5757; }

/* Tabs */
.tabs-bar { display:flex; overflow-x:auto; border-bottom:1px solid #E0E0E0; padding:0 1.5rem; scrollbar-width:none; }
.tabs-bar::-webkit-scrollbar { display:none; }
.tab-btn { display:flex; align-items:center; padding:.85rem 1.1rem; border:none; background:none; font-family:'Montserrat',sans-serif; font-size:.88rem; font-weight:600; color:#828282; cursor:pointer; white-space:nowrap; border-bottom:3px solid transparent; margin-bottom:-1px; transition:color .15s,border-color .15s; }
.tab-btn:hover { color:#1D52B7; }
.tab-act { color:#1D52B7; border-bottom-color:#1D52B7; }
.tab-body { padding:1.75rem 2rem; }
.tab-fade-enter-active,.tab-fade-leave-active { transition:opacity .18s ease,transform .18s ease; }
.tab-fade-enter-from { opacity:0; transform:translateY(6px); }
.tab-fade-leave-to   { opacity:0; transform:translateY(-4px); }

/* Info cards */
.info-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:1.25rem; }
.info-card { background:#F4F6F9; border-radius:12px; border:1px solid #E0E0E0; padding:1.25rem; }
.info-t    { font-size:.88rem; font-weight:700; color:#1D52B7; margin:0 0 1rem; text-transform:uppercase; letter-spacing:.05em; font-family:'Montserrat',sans-serif; }
.info-list { margin:0; padding:0; display:flex; flex-direction:column; gap:.65rem; }
.if  { display:flex; justify-content:space-between; align-items:baseline; gap:.5rem; }
.if dt { font-size:.83rem; color:#828282; font-weight:500; flex-shrink:0; font-family:'Montserrat',sans-serif; }
.if dd { font-size:.88rem; color:#333333; font-weight:600; text-align:right; margin:0; font-family:'Montserrat',sans-serif; }
.mono { font-family:monospace; letter-spacing:.05em; }
.sm { font-size:.82rem; color:#828282; }

/* Académico */
.acad-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:1rem; margin-bottom:1.25rem; }
.acad-kpi  { background:#F4F6F9; border:1px solid #E0E0E0; border-radius:12px; padding:1.25rem 1rem; text-align:center; }
.acad-val  { font-size:2.25rem; font-weight:700; line-height:1; margin-bottom:.4rem; font-family:'Montserrat',sans-serif; }
.acad-lbl  { font-size:.8rem; color:#828282; font-weight:500; font-family:'Montserrat',sans-serif; }
.azul    { color:#1D52B7; } .verde   { color:#27AE60; } .naranja { color:#F2994A; } .rojo { color:#EB5757; }

.prog-card { background:#F4F6F9; border:1px solid #E0E0E0; border-radius:12px; padding:1.25rem; }
.prog-hdr  { display:flex; justify-content:space-between; align-items:center; margin-bottom:.75rem; }
.prog-lbl  { font-size:.88rem; font-weight:600; color:#333333; font-family:'Montserrat',sans-serif; }
.prog-pct  { font-size:1.1rem; font-weight:700; color:#1D52B7; font-family:'Montserrat',sans-serif; }
.prog-bg   { height:10px; background:#E0E0E0; border-radius:99px; overflow:hidden; }
.prog-fill { height:100%; background:linear-gradient(to right,#0B2545,#1D52B7); border-radius:99px; transition:width .6s; }
.prog-det  { font-size:.82rem; color:#828282; margin:.5rem 0 0; font-family:'Montserrat',sans-serif; }

/* Kardex */
.kardex-per { margin-bottom:1.5rem; }
.kardex-per-hdr { display:flex; justify-content:space-between; align-items:center; margin-bottom:.6rem; }
.kardex-per-n { font-size:.95rem; font-weight:700; color:#333333; font-family:'Montserrat',sans-serif; }
.kardex-per-p { font-size:.88rem; color:#828282; font-family:'Montserrat',sans-serif; }
.kardex-per-p strong { color:#1D52B7; }
.tabla-wrap { overflow-x:auto; border-radius:10px; border:1px solid #E0E0E0; }
.tabla { width:100%; border-collapse:collapse; min-width:480px; }
.tabla th { background:#F4F6F9; padding:.65rem 1rem; font-size:.8rem; font-weight:700; color:#828282; text-align:left; border-bottom:1px solid #E0E0E0; font-family:'Montserrat',sans-serif; }
.tabla tbody tr td { padding:.6rem 1rem; font-size:.875rem; color:#333333; border-bottom:1px solid #F4F6F9; font-family:'Montserrat',sans-serif; }
.tabla tbody tr:last-child td { border-bottom:none; }
.tabla tbody tr:hover td { background:rgba(29,82,183,.05); }
.tc { text-align:center; }
.calif { display:inline-flex; align-items:center; justify-content:center; width:38px; height:28px; border-radius:6px; font-size:.85rem; font-weight:700; }
.ca-v  { background:rgba(39,174,96,.10);  color:#27AE60; }
.ca-a  { background:rgba(242,153,74,.10); color:#F2994A; }
.ca-r  { background:rgba(235,87,87,.10);  color:#EB5757; }
.ca-nd { background:#F2F4F7; color:#828282; }
.chip-mini { display:inline-flex; padding:2px 10px; border-radius:20px; font-size:.75rem; font-weight:700; white-space:nowrap; font-family:'Montserrat',sans-serif; }
.ch-v { background:rgba(39,174,96,.10);  color:#27AE60; }
.ch-r { background:rgba(235,87,87,.10);  color:#EB5757; }
.ch-g { background:#F2F4F7; color:#828282; }

/* Horario */
.horario-list { display:flex; flex-direction:column; gap:.75rem; }
.horario-card { display:flex; align-items:center; gap:1.25rem; background:#F4F6F9; border:1px solid #E0E0E0; border-radius:10px; padding:1rem 1.25rem; transition:border-color .15s; }
.horario-card:hover { border-color:#1D52B7; }
.hor-dia { font-size:.78rem; font-weight:700; color:#1D52B7; background:rgba(29,82,183,.08); padding:4px 10px; border-radius:6px; white-space:nowrap; flex-shrink:0; min-width:60px; text-align:center; font-family:'Montserrat',sans-serif; }
.hor-info { flex:1; min-width:0; }
.hor-mat { font-size:.93rem; font-weight:700; color:#333333; margin:0 0 2px; font-family:'Montserrat',sans-serif; }
.hor-det { font-size:.82rem; color:#828282; margin:0; font-family:'Montserrat',sans-serif; }
.hor-doc { display:flex; flex-direction:column; align-items:flex-end; text-align:right; flex-shrink:0; }
.hor-doc-l { font-size:.72rem; color:#828282; margin-bottom:2px; font-family:'Montserrat',sans-serif; }
.hor-doc-n { font-size:.83rem; font-weight:600; color:#333333; font-family:'Montserrat',sans-serif; }

/* Tab vacío */
.tab-vacio { display:flex; flex-direction:column; align-items:center; justify-content:center; gap:.75rem; padding:3rem 1rem; color:#828282; text-align:center; }
.tab-vacio p { font-size:.9rem; margin:0; font-family:'Montserrat',sans-serif; }

/* Chip genérico */
.chip { display:inline-flex; align-items:center; padding:3px 12px; border-radius:20px; font-size:.78rem; font-weight:700; white-space:nowrap; font-family:'Montserrat',sans-serif; }

/* ══ DASHBOARD ══ */
.dash { display:flex; flex-direction:column; gap:14px; }

/* KPI Grid */
.kpi-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:12px; }
.kpi { background:#FFFFFF; border:1px solid #E0E0E0; border-radius:12px; padding:16px 18px; display:flex; align-items:flex-start; gap:13px; cursor:pointer; transition:all .15s; box-shadow:0 2px 8px rgba(29,82,183,.05); }
.kpi:hover { border-color:#2F80ED; box-shadow:0 0 0 3px rgba(29,82,183,.07); }
.kpi-feat { background:#0B2545; border-color:#0B2545; }
.kpi-ico { width:42px; height:42px; border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.kpi-ico svg { stroke:currentColor; }
.ki-bw { background:rgba(255,255,255,.1);  color:#2F80ED; }
.ki-g  { background:rgba(39,174,96,.10);   color:#27AE60; }
.ki-a  { background:rgba(242,153,74,.10);  color:#F2994A; }
.ki-v  { background:rgba(47,128,237,.10);  color:#1A4184; }
.kpi-body { flex:1; min-width:0; }
.kpi-lbl  { font-size:10px; font-weight:600; text-transform:uppercase; letter-spacing:.07em; color:#828282; margin-bottom:4px; font-family:'Montserrat',sans-serif; }
.kpi-feat .kpi-lbl { color:rgba(255,255,255,.5); }
.kpi-val  { font-size:32px; font-weight:700; color:#333333; line-height:1; font-family:'Montserrat',sans-serif; }
.kpi-feat .kpi-val { color:#FFFFFF; }
.kpi-lnk  { font-size:11px; font-weight:500; color:#2F80ED; margin-top:5px; display:flex; align-items:center; gap:3px; font-family:'Montserrat',sans-serif; }
.kpi-lnk svg { stroke:#2F80ED; }
.kpi-sk   { width:80px; height:32px; border-radius:6px; background:linear-gradient(90deg,#E0E0E0 25%,#F2F4F7 50%,#E0E0E0 75%); background-size:200% 100%; animation:shimmer 1.4s infinite; }
.kpi-sk-d { background:linear-gradient(90deg,rgba(255,255,255,.1) 25%,rgba(255,255,255,.2) 50%,rgba(255,255,255,.1) 75%); background-size:200% 100%; }
@keyframes shimmer { 0%{background-position:200% 0}100%{background-position:-200% 0} }

/* Main grid */
.main-grid { display:grid; grid-template-columns:minmax(0,1.4fr) minmax(0,1fr); gap:14px; }
.card   { background:#FFFFFF; border:1px solid #E0E0E0; border-radius:12px; padding:18px; box-shadow:0 2px 8px rgba(29,82,183,.05); }
.card-h { display:flex; align-items:center; justify-content:space-between; margin-bottom:16px; }
.card-t { font-size:14px; font-weight:600; color:#333333; font-family:'Montserrat',sans-serif; }
.card-lk { font-size:11px; font-weight:600; color:#2F80ED; cursor:pointer; display:flex; align-items:center; gap:3px; text-decoration:none; font-family:'Montserrat',sans-serif; }
.card-lk:hover { color:#1A4184; }

/* Accesos rápidos */
.acc-list { display:flex; flex-direction:column; gap:8px; }
.acc-item { display:flex; align-items:center; gap:12px; padding:12px 14px; border-radius:10px; border:1px solid #E0E0E0; background:#F2F4F7; cursor:pointer; transition:all .15s; }
.acc-item:hover { border-color:#2F80ED; background:rgba(29,82,183,.05); }
.acc-item.prox  { opacity:.55; cursor:default; }
.acc-item.prox:hover { border-color:#E0E0E0; background:#F2F4F7; }
.acc-ico  { width:38px; height:38px; border-radius:9px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.acc-ico svg { stroke:currentColor; }
.ai-g  { background:rgba(39,174,96,.10);  color:#27AE60; }
.ai-b  { background:rgba(47,128,237,.10); color:#1D52B7; }
.ai-a  { background:rgba(242,153,74,.10); color:#F2994A; }
.ai-gr { background:#F2F4F7; color:#828282; }
.acc-body { flex:1; min-width:0; }
.acc-t { font-size:12px; font-weight:600; color:#333333; margin-bottom:2px; font-family:'Montserrat',sans-serif; }
.acc-d { font-size:11px; color:#4F4F4F; font-family:'Montserrat',sans-serif; }
.acc-arrow { stroke:#BDBDBD; flex-shrink:0; transition:stroke .15s; }
.acc-item:not(.prox):hover .acc-arrow { stroke:#1D52B7; }
.prox-bdg { font-size:9px; font-weight:700; color:#828282; background:#F2F4F7; border-radius:20px; padding:2px 8px; flex-shrink:0; letter-spacing:.04em; border:1px solid #E0E0E0; }

/* Bitácora */
.bit-list { display:flex; flex-direction:column; }
.bit-load { display:flex; align-items:center; gap:8px; padding:1rem 0; color:#828282; font-size:11px; }
.spinner  { width:15px; height:15px; border:2px solid #E0E0E0; border-top-color:#1D52B7; border-radius:50%; animation:girar .75s linear infinite; flex-shrink:0; }
.bit-item { display:flex; align-items:flex-start; gap:10px; padding:10px 0; border-bottom:1px solid #F4F6F9; }
.bit-item:last-child { border-bottom:none; padding-bottom:0; }
.bit-av  { width:30px; height:30px; border-radius:50%; background:#0B2545; color:#FFFFFF; font-size:10px; font-weight:700; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-family:'Montserrat',sans-serif; }
.bit-body { flex:1; min-width:0; }
.bit-r1  { display:flex; align-items:center; gap:6px; margin-bottom:2px; flex-wrap:wrap; }
.bit-usr  { font-size:12px; font-weight:600; color:#333333; font-family:'Montserrat',sans-serif; }
.bit-desc { font-size:11px; color:#4F4F4F; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; font-family:'Montserrat',sans-serif; }
.bit-t    { font-size:10px; color:#828282; margin-top:2px; font-family:'Montserrat',sans-serif; }
.bdg  { font-size:9px; font-weight:700; padding:2px 8px; border-radius:20px; letter-spacing:.02em; font-family:'Montserrat',sans-serif; }
.bg-g { background:rgba(39,174,96,.10);  color:#27AE60; }
.bg-b { background:rgba(47,128,237,.10); color:#1D52B7; }
.bg-a { background:rgba(242,153,74,.10); color:#F2994A; }
.bg-r { background:rgba(235,87,87,.10);  color:#EB5757; }

/* Bottom grid */
.bottom-grid { display:grid; grid-template-columns:minmax(0,1fr) minmax(0,1fr) minmax(0,1fr); gap:14px; }
.ev { display:flex; align-items:center; justify-content:center; padding:2rem; color:#828282; font-size:11px; font-family:'Montserrat',sans-serif; }

/* Inscripciones mini */
.ins-mini { display:flex; gap:8px; }
.im      { flex:1; background:#F4F6F9; border:1px solid #E0E0E0; border-radius:8px; padding:10px; text-align:center; }
.im-v    { font-size:16px; font-weight:700; color:#333333; font-family:'Montserrat',sans-serif; }
.im-l    { font-size:10px; color:#4F4F4F; margin-top:2px; font-family:'Montserrat',sans-serif; }

/* Alerta error */
.alerta-err { display:flex; align-items:center; gap:10px; background:#FFF0F0; border:1px solid rgba(235,87,87,.20); border-radius:10px; padding:12px 16px; font-size:.9rem; color:#EB5757; font-weight:500; font-family:'Montserrat',sans-serif; }
.alerta-err svg { stroke:#EB5757; flex-shrink:0; }
.btn-reintentar { margin-left:auto; background:#EB5757; color:#FFFFFF; border:none; padding:6px 16px; border-radius:6px; font-weight:600; font-size:.85rem; cursor:pointer; font-family:'Montserrat',sans-serif; transition:background .15s; white-space:nowrap; }
.btn-reintentar:hover { background:#c0392b; }

/* Footer */
.pie { text-align:center; color:#BDBDBD; font-size:.82rem; padding:2rem 0; border-top:1px solid #E0E0E0; font-family:'Montserrat',sans-serif; }

/* Transiciones */
.fade-enter-active,.fade-leave-active { transition:opacity .2s ease; }
.fade-enter-from,.fade-leave-to { opacity:0; }
.fade-slide-enter-active,.fade-slide-leave-active { transition:all .25s ease; }
.fade-slide-enter-from,.fade-slide-leave-to { opacity:0; transform:translateY(-6px); }

/* ══ RESPONSIVE ══ */
@media (max-width:1024px) { .kpi-grid { grid-template-columns:repeat(2,1fr); } .info-grid,.acad-grid { grid-template-columns:1fr; } .bottom-grid { grid-template-columns:1fr 1fr; } .main-grid { grid-template-columns:1fr; } }
@media (max-width:768px)  { .hero { flex-direction:column; align-items:flex-start; gap:14px; } .hero-stats { flex-direction:row; } .hero-title { font-size:18px; } .hero-form { flex-direction:column; } .hero-btn { width:100%; justify-content:center; } .bottom-grid { grid-template-columns:1fr; } .al-hdr { flex-direction:column; gap:1rem; padding:1.25rem; } .al-acciones { flex-direction:row; width:100%; } .tab-body { padding:1.25rem; } }
@media (max-width:640px)  { .kpi-grid { grid-template-columns:1fr; } .al-acciones { flex-direction:column; } .btn-primary,.btn-outline { width:100%; justify-content:center; } .acad-grid { grid-template-columns:repeat(2,1fr); } }
</style>