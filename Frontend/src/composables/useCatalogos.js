// src/composables/useCatalogos.js
// Composable centralizado para cargar catálogos desde el backend.
// Uso: importar las funciones y listas que necesites en cada vista.

import { ref } from 'vue'

const API = `${import.meta.env.VITE_API_URL}/api`

export function useCatalogos() {

  // ── Listas reactivas ──────────────────────────────────────────
  const carreras   = ref([])
  const estatus    = ref([])
  const generos    = ref([])
  const materias   = ref([])
  const periodos   = ref([])
  const grupos     = ref([])
  const turnos     = ref([])
  const tiposSolicitud = ref([])

  // ── Helper interno ────────────────────────────────────────────
  async function fetchCatalogo(endpoint, lista) {
    try {
      const res = await fetch(`${API}/${endpoint}`)
      if (!res.ok) throw new Error(`Error al cargar ${endpoint}`)
      lista.value = await res.json()
    } catch (error) {
      console.error(`[useCatalogos] ${error.message}`)
      lista.value = []
    }
  }

  // ── Funciones de carga ────────────────────────────────────────
  const cargarCarreras      = () => fetchCatalogo('carreras',        carreras)
  const cargarEstatus       = () => fetchCatalogo('estatus',         estatus)
  const cargarGeneros       = () => fetchCatalogo('generos',         generos)
  const cargarMaterias      = () => fetchCatalogo('materias',        materias)
  const cargarPeriodos      = () => fetchCatalogo('periodos',        periodos)
  const cargarGrupos        = () => fetchCatalogo('grupos',          grupos)
  const cargarTurnos        = () => fetchCatalogo('turnos',          turnos)
  const cargarTiposSolicitud= () => fetchCatalogo('tipos-solicitud', tiposSolicitud)

  return {
    // Listas
    carreras,
    estatus,
    generos,
    materias,
    periodos,
    grupos,
    turnos,
    tiposSolicitud,

    // Funciones
    cargarCarreras,
    cargarEstatus,
    cargarGeneros,
    cargarMaterias,
    cargarPeriodos,
    cargarGrupos,
    cargarTurnos,
    cargarTiposSolicitud,
  }
}