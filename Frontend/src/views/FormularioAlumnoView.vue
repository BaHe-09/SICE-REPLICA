<template>
  <MainLayout>
    <div class="formulario-page">

      <nav class="breadcrumb">
        <span class="breadcrumb-link" @click="cancelar">Servicios Escolares</span>
        <span class="breadcrumb-sep">›</span>
        <span class="breadcrumb-link" @click="cancelar">Alumnos</span>
        <span class="breadcrumb-sep">›</span>
        <span class="breadcrumb-actual">Nueva inscripción</span>
      </nav>


      <div class="page-header">
        <div>
          <h1 class="page-title">Nueva Inscripción</h1>
          <p class="subtitle">Complete todos los campos marcados con <span class="obligatorio">*</span> para registrar al alumno.</p>
        </div>
      </div>


      <transition name="toast-slide">
        <div v-if="notification.message"
             class="toast"
             :class="notification.type">
          <svg v-if="notification.type === 'success'" xmlns="http://www.w3.org/2000/svg" class="toast-icono" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <svg v-else xmlns="http://www.w3.org/2000/svg" class="toast-icono" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          {{ notification.message }}
        </div>
      </transition>


      <div class="form-card">


        <section class="seccion">
          <h2 class="seccion-titulo">
            <svg xmlns="http://www.w3.org/2000/svg" class="seccion-icono" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            Información Personal
          </h2>

          <div class="fila-campos">

            <div class="campo" :class="{ 'campo-error': errors.nombre, 'campo-valido': campoValido('nombre') }">
              <label class="etiqueta">Nombre <span class="obligatorio">*</span></label>
              <input
                type="text"
                v-model.trim="form.nombre"
                class="input-campo"
                :class="{ 'borde-error': errors.nombre, 'borde-valido': campoValido('nombre') }"
                placeholder="Ej. Juan"
                @input="validarCampo('nombre')"
                @blur="validarCampo('nombre')"
                @keydown.tab="validarCampo('nombre')"
              >
              <transition name="error-fade">
                <small v-if="errors.nombre" class="mensaje-error">{{ errors.nombre }}</small>
              </transition>
            </div>


            <div class="campo" :class="{ 'campo-error': errors.apellidoPaterno, 'campo-valido': campoValido('apellidoPaterno') }">
              <label class="etiqueta">Apellido Paterno <span class="obligatorio">*</span></label>
              <input
                type="text"
                v-model.trim="form.apellidoPaterno"
                class="input-campo"
                :class="{ 'borde-error': errors.apellidoPaterno, 'borde-valido': campoValido('apellidoPaterno') }"
                placeholder="Ej. García"
                @input="validarCampo('apellidoPaterno')"
                @blur="validarCampo('apellidoPaterno')"
              >
              <transition name="error-fade">
                <small v-if="errors.apellidoPaterno" class="mensaje-error">{{ errors.apellidoPaterno }}</small>
              </transition>
            </div>


            <div class="campo">
              <label class="etiqueta">Apellido Materno</label>
              <input
                type="text"
                v-model.trim="form.apellidoMaterno"
                class="input-campo"
                placeholder="Ej. López (opcional)"
              >
            </div>
          </div>

          <!-- Género -->
          <div class="fila-campos fila-parcial">
            <div class="campo" :class="{ 'campo-error': errors.genero, 'campo-valido': campoValido('genero') }">
              <label class="etiqueta">Género <span class="obligatorio">*</span></label>
              <select
                v-model="form.genero"
                class="input-campo"
                :class="{ 'borde-error': errors.genero, 'borde-valido': campoValido('genero') }"
                @change="validarCampo('genero')"
              >
                <option value="">Seleccionar</option>
                <option value="Masculino">Masculino</option>
                <option value="Femenino">Femenino</option>
                <option value="Otro">Otro</option>
              </select>
              <transition name="error-fade">
                <small v-if="errors.genero" class="mensaje-error">{{ errors.genero }}</small>
              </transition>
            </div>
          </div>
        </section>


        <section class="seccion">
          <h2 class="seccion-titulo">
            <svg xmlns="http://www.w3.org/2000/svg" class="seccion-icono" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
            Datos Académicos
          </h2>

          <div class="fila-campos">


            <div class="campo" :class="{ 'campo-error': errors.noControl, 'campo-valido': campoValido('noControl') }">
              <label class="etiqueta">
                Número de Control <span class="obligatorio">*</span>
                <span class="etiqueta-hint">Inicia con {{ prefijoAnio }} (año {{ anioActual }})</span>
              </label>
              <div class="input-con-prefijo">
                <span class="prefijo-fijo">{{ prefijoAnio }}</span>
                <input
                  type="text"
                  v-model="sufijoCodigo"
                  class="input-campo input-con-prefijo-campo"
                  :class="{ 'borde-error': errors.noControl, 'borde-valido': campoValido('noControl') }"
                  placeholder="000000"
                  maxlength="6"
                  @input="aplicarMascaraControl"
                  @blur="validarCampo('noControl')"
                >
              </div>
  
              <small class="vista-previa-control" v-if="form.noControl">
                Número completo: <strong>{{ form.noControl }}</strong>
              </small>
              <transition name="error-fade">
                <small v-if="errors.noControl" class="mensaje-error">{{ errors.noControl }}</small>
              </transition>
            </div>


            <div class="campo" :class="{ 'campo-error': errors.carrera, 'campo-valido': campoValido('carrera') }">
              <label class="etiqueta">Carrera <span class="obligatorio">*</span></label>
              <select
                v-model="form.carrera"
                class="input-campo"
                :class="{ 'borde-error': errors.carrera, 'borde-valido': campoValido('carrera') }"
                @change="validarCampo('carrera')"
              >
                <option value="">Seleccionar carrera</option>
                <option value="Contador Publico">Contador Público</option>
                <option value="Ingenieria Civil">Ingeniería Civil</option>
                <option value="Ingenieria en Gestion empresarial">Ing. en Gestión Empresarial</option>
                <option value="Ingenieria en Sistemas Computacionales">Ing. en Sistemas Computacionales</option>
                <option value="Ingenieria Industrial">Ingeniería Industrial</option>
              </select>
              <transition name="error-fade">
                <small v-if="errors.carrera" class="mensaje-error">{{ errors.carrera }}</small>
              </transition>
            </div>


            <div class="campo" :class="{ 'campo-error': errors.semestre, 'campo-valido': campoValido('semestre') }">
              <label class="etiqueta">Semestre <span class="obligatorio">*</span></label>
              <select
                v-model="form.semestre"
                class="input-campo"
                :class="{ 'borde-error': errors.semestre, 'borde-valido': campoValido('semestre') }"
                @change="validarCampo('semestre')"
              >
                <option value="">Seleccionar</option>
                <option v-for="n in 8" :key="n" :value="n">{{ n }}° Semestre</option>
              </select>
              <transition name="error-fade">
                <small v-if="errors.semestre" class="mensaje-error">{{ errors.semestre }}</small>
              </transition>
            </div>
          </div>
        </section>


        <section class="seccion seccion-sin-borde">
          <h2 class="seccion-titulo">
            <svg xmlns="http://www.w3.org/2000/svg" class="seccion-icono" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            Estado y Fecha de Ingreso
          </h2>

          <div class="fila-campos fila-parcial">


            <div class="campo">
              <label class="etiqueta">Estatus <span class="obligatorio">*</span></label>
              <select v-model="form.estatus" class="input-campo">
                <option value="Activo">Activo</option>
                <option value="Baja Temporal">Baja Temporal</option>
                <option value="Baja Definitiva">Baja Definitiva</option>
              </select>

              <div class="indicador-estatus" :class="form.estatus.toLowerCase().replace(/\s/g, '-')">
                {{ form.estatus }}
              </div>
            </div>


            <div class="campo" :class="{ 'campo-error': errors.fechaIngreso, 'campo-valido': campoValido('fechaIngreso') }">
              <label class="etiqueta">Fecha de Ingreso <span class="obligatorio">*</span></label>
              <input
                type="date"
                v-model="form.fechaIngreso"
                class="input-campo"
                :class="{ 'borde-error': errors.fechaIngreso, 'borde-valido': campoValido('fechaIngreso') }"
                :max="hoyISO"
                @change="validarCampo('fechaIngreso')"
              >
              <transition name="error-fade">
                <small v-if="errors.fechaIngreso" class="mensaje-error">{{ errors.fechaIngreso }}</small>
              </transition>
            </div>

          </div>
        </section>

        <!-- ══ Acciones ══ -->
        <div class="form-acciones">
          <button class="btn-cancelar" @click="cancelar" :disabled="isLoading">
            Cancelar
          </button>
          <button class="btn-guardar" @click="guardarAlumno" :disabled="isLoading">
            <span v-if="isLoading" class="spinner"></span>
            {{ isLoading ? 'Guardando...' : 'Guardar registro' }}
          </button>
        </div>

      </div>
    </div>
  </MainLayout>
</template>


<script setup>
import { ref, reactive, computed } from 'vue'
import { useRouter } from 'vue-router'
import MainLayout from '@/layouts/MainLayout.vue'

const router = useRouter()


const anioActual  = new Date().getFullYear()
const prefijoAnio = String(anioActual).slice(-2)   // "26" si es 2026
const hoyISO      = new Date().toISOString().split('T')[0]


const sufijoCodigo = ref('')


const form = reactive({
  nombre:          '',
  apellidoPaterno: '',
  apellidoMaterno: '',
  genero:          '',
  noControl:       '',
  carrera:         '',
  semestre:        '',
  estatus:         'Activo',
  fechaIngreso:    ''
})

const errors       = reactive({})
const tocados      = reactive({})
const notification = reactive({ message: '', type: '' })
const isLoading    = ref(false)


const aplicarMascaraControl = () => {

  sufijoCodigo.value = sufijoCodigo.value.replace(/\D/g, '').slice(0, 6)

  form.noControl = prefijoAnio + sufijoCodigo.value
  validarCampo('noControl')
}


const validarCampo = (campo) => {
  tocados[campo] = true

  switch (campo) {
    case 'nombre':
      if (!form.nombre.trim())
        errors.nombre = 'El nombre es obligatorio'
      else if (form.nombre.trim().length < 2)
        errors.nombre = 'Debe tener al menos 2 caracteres'
      else
        delete errors.nombre
      break

    case 'apellidoPaterno':
      if (!form.apellidoPaterno.trim())
        errors.apellidoPaterno = 'El apellido paterno es obligatorio'
      else if (form.apellidoPaterno.trim().length < 2)
        errors.apellidoPaterno = 'Debe tener al menos 2 caracteres'
      else
        delete errors.apellidoPaterno
      break

    case 'genero':
      if (!form.genero)
        errors.genero = 'Seleccione un género'
      else
        delete errors.genero
      break

    case 'noControl':
      if (!form.noControl || sufijoCodigo.value.length < 6)
        errors.noControl = `Debe tener 8 dígitos en total (prefijo ${prefijoAnio} + 6 dígitos)`
      else if (!/^\d{8}$/.test(form.noControl))
        errors.noControl = 'Solo se permiten números'
      else
        delete errors.noControl
      break

    case 'carrera':
      if (!form.carrera)
        errors.carrera = 'Seleccione una carrera'
      else
        delete errors.carrera
      break

    case 'semestre':
      if (!form.semestre)
        errors.semestre = 'Seleccione un semestre'
      else
        delete errors.semestre
      break

    case 'fechaIngreso':
      if (!form.fechaIngreso)
        errors.fechaIngreso = 'La fecha de ingreso es obligatoria'
      else if (form.fechaIngreso > hoyISO)
        errors.fechaIngreso = 'La fecha no puede ser futura'
      else
        delete errors.fechaIngreso
      break
  }
}


const campoValido = (campo) => tocados[campo] && !errors[campo]


const validarFormulario = () => {
  const camposRequeridos = ['nombre', 'apellidoPaterno', 'genero', 'noControl', 'carrera', 'semestre', 'fechaIngreso']
  camposRequeridos.forEach(c => validarCampo(c))
  return Object.keys(errors).length === 0
}


const getIdCarrera = (nombreCarrera) => {
  const mapa = {
    'Contador Publico': 1,
    'Ingenieria Civil': 2,
    'Ingenieria en Gestion empresarial': 3,
    'Ingenieria en Sistemas Computacionales': 4,
    'Ingenieria Industrial': 5,
  }
  return mapa[nombreCarrera] || null
}

const getEstatus = (estatus) => {
  return {
    'Activo': 1,
    'Baja Temporal': 0,
    'Baja Definitiva': 0
  }[estatus]
}


const guardarAlumno = async () => {
  if (!validarFormulario()) {
    showNotification('Corrige los errores marcados antes de continuar', 'error')
    return
  }

  isLoading.value = true

  const payload = {
    numero_control:  form.noControl,
    nombre:          form.nombre.trim(),
    apellido_paterno: form.apellidoPaterno.trim(),
    apellido_materno: form.apellidoMaterno.trim() || null,
    genero:          form.genero,
    id_carrera:      getIdCarrera(form.carrera),
    semestre_actual: parseInt(form.semestre),
    estatus:         getEstatus(form.estatus),
    fecha_ingreso:   form.fechaIngreso
  }

  try {
  
    console.log('Formulario completo:', form)
    console.log('Carrera seleccionada:', form.carrera)
    console.log('ID carrera:', getIdCarrera(form.carrera))
    console.log('Payload enviado:', payload)

    const response = await fetch('http://localhost:8000/api/alumnos', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify(payload)
    })

    const data = await response.json()
    console.log('Respuesta backend:', data)

    if (response.ok) {
      showNotification('Alumno registrado correctamente', 'success')
      setTimeout(() => router.push('/alumnos'), 1500)
    } else {
      throw new Error(JSON.stringify(data))
    }

  } catch (error) {
    console.error('ERROR REAL:', error)
    showNotification('Ocurrió un error al guardar el registro. Verifica la conexión con el servidor.', 'error')
  } finally {
    isLoading.value = false
  }
}

const cancelar = () => router.push('/alumnos')

const showNotification = (message, type) => {
  notification.message = message
  notification.type    = type
  setTimeout(() => { notification.message = '' }, 4000)
}
</script>


<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap');


.formulario-page {
  --azul:          #1B396A;
  --azul-hover:    #1D4ED8;
  --borde:         #E5E7EB;
  --fondo:         #F5F5F5;
  --texto:         #1A1A1A;
  --gris:          #6B7280;
  --verde:         #16A34A;
  --rojo:          #DC2626;

  width: 100%;
  background: var(--fondo);
  font-family: 'Montserrat', sans-serif;
}


.breadcrumb {
  display: flex;
  align-items: center;
  gap: 6px;
  color: var(--gris);
  font-size: 0.88rem;
  margin-bottom: 1rem;
}
.breadcrumb-link {
  cursor: pointer;
  color: var(--azul);
  font-weight: 500;
  transition: color 0.15s;
}
.breadcrumb-link:hover { color: var(--azul-hover); text-decoration: underline; }
.breadcrumb-sep { color: #9CA3AF; }
.breadcrumb-actual { color: var(--gris); font-weight: 600; }


.page-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  margin-bottom: 1.4rem;
}
.page-title {
  color: var(--texto);
  font-size: 1.75rem;
  font-weight: 700;
  margin: 0 0 0.25rem;
  letter-spacing: -0.02em;
}
.subtitle {
  color: var(--gris);
  font-size: 0.93rem;
  margin: 0;
}
.obligatorio { color: var(--rojo); }


.toast {
  position: fixed;
  top: 88px;
  right: 28px;
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 13px 20px;
  border-radius: 10px;
  color: white;
  font-weight: 500;
  font-size: 0.93rem;
  box-shadow: 0 6px 20px rgba(0,0,0,0.2);
  z-index: 9999;
  font-family: 'Montserrat', sans-serif;
  max-width: 380px;
}
.toast.success { background: #16A34A; }
.toast.error   { background: #DC2626; }
.toast-icono { width: 20px; height: 20px; flex-shrink: 0; }

.toast-slide-enter-active, .toast-slide-leave-active { transition: all 0.35s ease; }
.toast-slide-enter-from, .toast-slide-leave-to { opacity: 0; transform: translateX(110%); }


.form-card {
  background: #FFFFFF;
  border-radius: 14px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.06);
  padding: 2rem 2.2rem;
  width: 100%;
  max-width: 1000px;
  margin: 0 auto;
  border: 1px solid var(--borde);
}


.seccion {
  margin-bottom: 2rem;
  padding-bottom: 2rem;
  border-bottom: 1px solid var(--borde);
}
.seccion-sin-borde { border-bottom: none; margin-bottom: 1rem; padding-bottom: 0; }

.seccion-titulo {
  display: flex;
  align-items: center;
  gap: 8px;
  color: var(--azul);
  font-size: 1.1rem;
  font-weight: 700;
  margin: 0 0 1.2rem;
  padding-bottom: 0.6rem;
  border-bottom: 2px solid var(--borde);
}
.seccion-icono { width: 20px; height: 20px; stroke: var(--azul); flex-shrink: 0; }

.fila-campos {
  display: flex;
  gap: 1.2rem;
  flex-wrap: wrap;
}
.fila-parcial .campo { flex: 0 0 calc(33% - 0.8rem); min-width: 200px; }

.campo {
  flex: 1;
  min-width: 180px;
  position: relative;
}


.etiqueta {
  display: flex;
  align-items: center;
  gap: 6px;
  margin-bottom: 6px;
  font-weight: 600;
  font-size: 0.9rem;
  color: var(--texto);
  font-family: 'Montserrat', sans-serif;
}
.etiqueta-hint {
  font-weight: 400;
  font-size: 0.78rem;
  color: var(--azul);
  background: #DBEAFE;
  padding: 2px 7px;
  border-radius: 10px;
  margin-left: 4px;
}


.input-campo {
  width: 100%;
  padding: 10px 14px;
  font-size: 0.95rem;
  border: 1.5px solid var(--borde);
  border-radius: 8px;
  background: #FFFFFF;
  color: var(--texto);
  font-family: 'Montserrat', sans-serif;
  outline: none;
  transition: border-color 0.2s, box-shadow 0.2s;
  box-sizing: border-box;
}
.input-campo:focus {
  border-color: var(--azul);
  box-shadow: 0 0 0 3px #DBEAFE;
}
.input-campo::placeholder { color: #9CA3AF; }

.borde-error { border-color: var(--rojo) !important; }
.borde-error:focus { box-shadow: 0 0 0 3px #FEE2E2 !important; }
.borde-valido { border-color: #16A34A !important; }
.borde-valido:focus { box-shadow: 0 0 0 3px #DCFCE7 !important; }

.mensaje-error {
  display: flex;
  align-items: center;
  gap: 4px;
  color: var(--rojo);
  font-size: 0.82rem;
  margin-top: 5px;
  font-family: 'Montserrat', sans-serif;
}
.mensaje-error::before { content: '⚠'; font-size: 0.78rem; }

.error-fade-enter-active, .error-fade-leave-active { transition: all 0.25s ease; }
.error-fade-enter-from, .error-fade-leave-to { opacity: 0; transform: translateY(-4px); }


.input-con-prefijo {
  display: flex;
  align-items: stretch;
  border: 1.5px solid var(--borde);
  border-radius: 8px;
  overflow: hidden;
  transition: border-color 0.2s, box-shadow 0.2s;
  background: #FFFFFF;
}
.input-con-prefijo:focus-within {
  border-color: var(--azul);
  box-shadow: 0 0 0 3px #DBEAFE;
}
.prefijo-fijo {
  display: flex;
  align-items: center;
  padding: 10px 14px;
  background: #1B396A;
  color: #FFFFFF;
  font-weight: 700;
  font-size: 1rem;
  font-family: 'Montserrat', sans-serif;
  white-space: nowrap;
  letter-spacing: 0.05em;
  flex-shrink: 0;
}
.input-con-prefijo-campo {
  border: none !important;
  border-radius: 0 !important;
  box-shadow: none !important;
  flex: 1;
  padding: 10px 12px;
}
.input-con-prefijo-campo:focus { box-shadow: none !important; }


.campo.campo-error   .input-con-prefijo { border-color: var(--rojo); }
.campo.campo-valido  .input-con-prefijo { border-color: #16A34A; }

.vista-previa-control {
  display: block;
  margin-top: 5px;
  font-size: 0.82rem;
  color: var(--gris);
  font-family: 'Montserrat', sans-serif;
}
.vista-previa-control strong { color: var(--azul); font-weight: 700; }


.indicador-estatus {
  display: inline-flex;
  align-items: center;
  margin-top: 7px;
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 0.82rem;
  font-weight: 600;
  font-family: 'Montserrat', sans-serif;
}
.indicador-estatus.activo        { background: #DCFCE7; color: #16A34A; }
.indicador-estatus.baja-temporal { background: #FEF3C7; color: #F59E0B; }
.indicador-estatus.baja-definitiva { background: #FEE2E2; color: #DC2626; }


.form-acciones {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 1.6rem;
  padding-top: 1.4rem;
  border-top: 1px solid var(--borde);
}

.btn-cancelar {
  padding: 11px 26px;
  background: #FFFFFF;
  color: var(--texto);
  border: 1px solid var(--borde);
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  font-family: 'Montserrat', sans-serif;
  font-size: 0.95rem;
  transition: background 0.15s;
}
.btn-cancelar:hover:not(:disabled) { background: var(--fondo); }
.btn-cancelar:disabled { opacity: 0.5; cursor: not-allowed; }

.btn-guardar {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 11px 28px;
  background: var(--azul);
  color: white;
  border: none;
  border-radius: 8px;
  font-weight: 700;
  cursor: pointer;
  font-family: 'Montserrat', sans-serif;
  font-size: 0.95rem;
  transition: background 0.2s;
}
.btn-guardar:hover:not(:disabled) { background: var(--azul-hover); }
.btn-guardar:disabled { opacity: 0.7; cursor: not-allowed; }


.spinner {
  display: inline-block;
  width: 16px;
  height: 16px;
  border: 2px solid rgba(255,255,255,0.3);
  border-top-color: white;
  border-radius: 50%;
  animation: girar 0.8s linear infinite;
  flex-shrink: 0;
}
@keyframes girar { to { transform: rotate(360deg); } }
</style>