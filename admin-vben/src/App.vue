<template>
  <RouterView />
  <Transition name="app-loading">
    <div v-if="loading.visible" class="app-loading-mask">
      <div class="app-loading-card">
        <div class="app-loading-orbit"></div>
        <div class="app-loading-core"></div>
        <strong>Frelink Admin</strong>
        <span>{{ loading.message }}</span>
      </div>
    </div>
  </Transition>
</template>

<script setup lang="ts">
import { useLoadingStore } from '@/stores/loading';

const loading = useLoadingStore();
</script>
