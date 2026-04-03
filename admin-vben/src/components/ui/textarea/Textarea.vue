<template>
  <textarea
    :class="
      cn(
        'flex min-h-[96px] w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-950 shadow-sm transition-colors placeholder:text-slate-400 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-900/15 disabled:cursor-not-allowed disabled:opacity-50',
        props.class
      )
    "
    v-bind="$attrs"
  />
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
