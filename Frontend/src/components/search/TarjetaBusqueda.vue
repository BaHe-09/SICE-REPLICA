<script setup lang="ts">
/**
 * TarjetaBusqueda.vue — Tarjeta de resultado del Buscador Global
 * Ubicación: src/components/search/TarjetaBusqueda.vue
 * Sprint 1 · Equipo 3
 */

import { computed } from 'vue'
import { User, GraduationCap, Hash, Building2, ChevronRight } from 'lucide-vue-next'

// ── Tipos exportados ───────────────────────────────────────────────────────
export type TipoResultado = 'ALUMNO' | 'DOCENTE'

export interface ResultadoBusqueda {
  /** Tipo de entidad encontrada */
  tipo: TipoResultado
  /** Nombre completo ya en MAYÚSCULAS */
  nombre_completo: string
  /** Número de control (alumno) o clave (docente) */
  identificador: string
  /** Carrera (alumno) o departamento (docente) */
  area?: string
  /** Estado actual del registro */
  estatus?: string
  /** ID original para navegación */
  id?: number | string
}

// ── Props y Emits ──────────────────────────────────────────────────────────
interface Props {
  resultado: ResultadoBusqueda
  /** Resalta las coincidencias del término buscado */
  termino?: string
}

const props = defineProps<Props>()
const emit  = defineEmits<{ seleccionar: [r: ResultadoBusqueda] }>()

// ── Helpers de tipo ────────────────────────────────────────────────────────
const esAlumno  = computed(() => props.resultado.tipo === 'ALUMNO')
const iconoTipo = computed(() => esAlumno.value ? GraduationCap : User)

const labelTipo = computed(() =>
  esAlumno.value ? 'Alumno Registrado' : 'Personal Docente'
)

const estatusNorm = computed(() =>
  (props.resultado.estatus ?? '').toUpperCase()
)

const badgeClase = computed(() => {
  switch (estatusNorm.value) {
    case 'ACTIVO':    return 'badge-activo'
    case 'BAJA':      return 'badge-baja'
    case 'EGRESADO':  return 'badge-egresado'
    case 'IRREGULAR': return 'badge-irregular'
    default:          return 'badge-inactivo'
  }
})

// ── Resaltado de texto ─────────────────────────────────────────────────────
function resaltar(texto: string): string {
  if (!props.termino || props.termino.trim() === '') return texto
  const escaped = props.termino.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')
  const re = new RegExp(`(${escaped})`, 'gi')
  return texto.replace(re, '<mark class="sice-mark">$1</mark>')
}
</script>

<template>
  <button
  type="button"
  class="tarjeta"
  @mousedown.prevent="emit('seleccionar', resultado)"
>
    <!-- Avatar -->
    <div
      class="avatar"
      :class="esAlumno ? 'avatar--alumno' : 'avatar--docente'"
    >
      <component :is="iconoTipo" :size="22" stroke-width="2" />
    </div>

    <!-- Información principal -->
    <div class="info">
      <p class="subtipo">{{ labelTipo }}</p>
      <p
        class="nombre"
        v-html="resaltar(resultado.nombre_completo)"
      />
      <div class="meta">
        <span class="meta-item">
          <Hash :size="11" />
          <span>{{ resultado.identificador }}</span>
        </span>
        <template v-if="resultado.area">
          <span class="meta-sep" aria-hidden="true">·</span>
          <span class="meta-item meta-area">
            <Building2 :size="11" />
            <span>{{ resultado.area.toUpperCase() }}</span>
          </span>
        </template>
      </div>
    </div>

    <!-- Badges de estatus -->
    <div class="badges">
      <span
        v-if="resultado.estatus"
        class="badge"
        :class="badgeClase"
      >
        {{ estatusNorm }}
      </span>
    </div>

    <!-- Flecha -->
    <div class="flecha" aria-hidden="true">
      <ChevronRight :size="16" stroke-width="2.5" />
    </div>
  </button>
</template>

<style scoped>
/* ─── Variables locales ────────────────────────────────────────────────── */
.tarjeta {
  --azul:        #1D52B7;
  --azul-claro:  #2F80ED;
  --azul-bg:     #EEF4FF;
  --azul-border: #D8E5FF;
  --texto:       #1F2937;
  --texto-sub:   #64748B;
  --texto-meta:  #6B7280;
  --borde:       #E6ECF5;
  --radio:       12px;
}

/* ─── Tarjeta base ─────────────────────────────────────────────────────── */
.tarjeta {
  width: 100%;
  display: grid;
  grid-template-columns: 40px minmax(0, 1fr) auto 16px;
  align-items: center;
  gap: 10px;
  padding: 8px 12px;
  background: #ffffff;
  border: 1px solid var(--borde);
  border-radius: var(--radio);
  cursor: pointer;
  text-align: left;
  transition:
    border-color .15s ease,
    box-shadow .15s ease,
    background .15s ease;
}

.tarjeta:hover {
  border-color: rgba(29, 82, 183, .22);
  box-shadow: 0 4px 16px rgba(29, 82, 183, .08);
  background: #FAFCFF;
}

.tarjeta:focus-visible {
  outline: none;
  box-shadow: 0 0 0 3px rgba(29, 82, 183, .18);
}

.tarjeta:active {
  background: #F0F5FF;
}

/* ─── Avatar ───────────────────────────────────────────────────────────── */
.avatar {
  width: 40px;
  height: 40px;
  min-width: 40px;
  border-radius: 9px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  transition: transform .15s ease;
}

.tarjeta:hover .avatar {
  transform: scale(1.04);
}

.avatar--alumno {
  background: var(--azul-bg);
  color: var(--azul);
  border: 1px solid var(--azul-border);
}

.avatar--docente {
  background: #F0F8FF;
  color: var(--azul-claro);
  border: 1px solid #DDEEFF;
}

/* ─── Info ─────────────────────────────────────────────────────────────── */
.info {
  min-width: 0;
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.subtipo {
  margin: 0;
  font-family: 'Montserrat', sans-serif;
  font-size: 10px;
  font-weight: 600;
  color: var(--texto-sub);
  letter-spacing: .06em;
  text-transform: uppercase;
  line-height: 1.2;
}

.nombre {
  margin: 0;
  font-family: 'Montserrat', sans-serif;
  font-size: 13px;
  font-weight: 700;
  color: var(--texto);
  line-height: 1.3;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* ─── Meta ─────────────────────────────────────────────────────────────── */
.meta {
  display: flex;
  align-items: center;
  gap: 5px;
  flex-wrap: nowrap;
  margin-top: 2px;
  overflow: hidden;
}

.meta-item {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  font-family: 'Montserrat', sans-serif;
  font-size: 11px;
  font-weight: 500;
  color: var(--texto-meta);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  flex-shrink: 0;
}

.meta-area {
  flex-shrink: 1;
  min-width: 0;
}

.meta-area span {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.meta-sep {
  color: #CBD5E1;
  font-size: 12px;
  flex-shrink: 0;
}

/* ─── Badges ───────────────────────────────────────────────────────────── */
.badges {
  display: flex;
  align-items: center;
  flex-shrink: 0;
}

.badge {
  display: inline-flex;
  align-items: center;
  height: 22px;
  padding: 0 8px;
  border-radius: 6px;
  font-family: 'Montserrat', sans-serif;
  font-size: 10px;
  font-weight: 700;
  letter-spacing: .05em;
  text-transform: uppercase;
  white-space: nowrap;
  border: 1px solid transparent;
}

.badge-activo    { background: #EDFBF2; color: #15803D; border-color: #BBF7D0; }
.badge-baja      { background: #FFF1F2; color: #BE123C; border-color: #FECDD3; }
.badge-egresado  { background: #EFF6FF; color: #1D4ED8; border-color: #BFDBFE; }
.badge-irregular { background: #FFFBEB; color: #B45309; border-color: #FDE68A; }
.badge-inactivo  { background: #F8FAFC; color: #64748B; border-color: #E2E8F0; }

/* ─── Flecha ───────────────────────────────────────────────────────────── */
.flecha {
  display: flex;
  align-items: center;
  justify-content: center;
  color: #CBD5E1;
  flex-shrink: 0;
  transition: color .15s ease, transform .15s ease;
}

.tarjeta:hover .flecha {
  color: var(--azul);
  transform: translateX(3px);
}

/* ─── Resaltado de búsqueda ────────────────────────────────────────────── */
:deep(.sice-mark) {
  background: #FEF08A;
  color: #111827;
  padding: 1px 3px;
  border-radius: 4px;
  font-weight: 800;
}

/* ─── Responsive móvil ─────────────────────────────────────────────────── */
@media (max-width: 640px) {
  .tarjeta {
    grid-template-columns: 44px minmax(0, 1fr) auto;
    gap: 10px;
    padding: 10px 12px;
  }

  .avatar {
    width: 44px;
    height: 44px;
    min-width: 44px;
  }

  .flecha {
    display: none;
  }

  .nombre {
    font-size: 13px;
  }

  .meta-area {
    display: none;
  }

  .meta-sep {
    display: none;
  }
}
</style>