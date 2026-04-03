<template>
  <input
    :value="model ?? undefined"
    :class="
      cn(
        'flex h-10 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-950 shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-slate-400 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-900/15 disabled:cursor-not-allowed disabled:opacity-50',
        props.class
      )
    "
    v-bind="$attrs"
    @input="handleInput"
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

const [model, modifiers] = defineModel<string | number | null>();

function handleInput(event: Event) {
  const target = event.target as HTMLInputElement;
  let value: string | number = target.value;

  if (modifiers.trim) {
    value = value.trim();
  }

  if (modifiers.number) {
    model.value = value === '' ? '' : Number(value);
    return;
  }

  model.value = value;
}
</script>
