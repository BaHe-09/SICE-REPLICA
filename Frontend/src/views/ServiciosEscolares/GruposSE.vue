<template>
  <div class="se-vista">
    <div class="se-tabs">
      <router-link to="/gestion-grupos"         class="se-tab">Por Carrera</router-link>
      <router-link to="/gestion-grupos/detalle" class="se-tab">Detalle de Grupo</router-link>
    </div>
    <div class="se-contenido">
      <GestionGruposView v-if="ruta === '/gestion-grupos'" />
      <GrupoDetailView   v-else-if="ruta === '/gestion-grupos/detalle'" />
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import GestionGruposView from '@/views/GestionGruposView.vue'
import GrupoDetailView   from '@/views/ServiciosEscolares/GrupoDetailView.vue'
const ruta = computed(() => useRoute().path)
</script>
<style src="@/styles/se-tabs.css" />