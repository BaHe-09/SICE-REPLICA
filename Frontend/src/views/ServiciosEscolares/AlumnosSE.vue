<template>
  <div class="se-vista">
    <div class="se-contenido">
      <AlumnosView v-if="ruta === '/alumnos/gestion'" />
      <template v-else-if="ruta === '/alumnos/expediente'">
        <h2>Expediente Académico</h2>
        <p>Módulo en construcción.</p>
      </template>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import AlumnosView from '@/views/AlumnosView.vue'
const ruta = computed(() => useRoute().path)
</script>