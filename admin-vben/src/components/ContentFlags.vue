<template>
  <div v-if="flags.length" class="content-flags">
    <span v-for="flag in flags" :key="flag" class="content-flag">
      {{ flag }}
    </span>
  </div>
</template>

<script setup lang="ts">
withDefaults(
  defineProps<{
    flags?: string[];
  }>(),
  {
    flags: () => [],
  },
);
</script>
