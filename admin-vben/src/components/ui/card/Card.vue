<template>
  <div :class="cn('rounded-xl border border-slate-200 bg-white text-slate-950 shadow-sm', props.class)" v-bind="$attrs">
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
