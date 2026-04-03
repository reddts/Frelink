<template>
  <div :class="cn('text-sm text-slate-500', props.class)" v-bind="$attrs">
    <slot />
  </div>
</template>

<script setup lang="ts">
import { cn } from '@/lib/utils';

const props = withDefaults(
  defineProps<{
    class?: string;
  }>(),
  {
    class: '',
  }
);
</script>
