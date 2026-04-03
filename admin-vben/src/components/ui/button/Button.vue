<template>
  <component :is="tag" :class="buttonClass" v-bind="$attrs">
    <slot />
  </component>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { cva } from 'class-variance-authority';
import { cn } from '@/lib/utils';

const buttonVariants = cva(
  'inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium transition-all outline-none disabled:pointer-events-none disabled:opacity-50',
  {
    variants: {
      variant: {
        default: 'bg-slate-900 text-white shadow-sm hover:bg-slate-800',
        outline: 'border border-slate-200 bg-white text-slate-700 hover:bg-slate-50',
        secondary: 'bg-slate-100 text-slate-800 hover:bg-slate-200',
        ghost: 'text-slate-700 hover:bg-slate-100',
        destructive: 'bg-red-600 text-white shadow-sm hover:bg-red-500',
      },
      size: {
        default: 'h-10 px-4 py-2',
        sm: 'h-9 px-3',
        lg: 'h-11 px-6',
      },
    },
    defaultVariants: {
      variant: 'default',
      size: 'default',
    },
  }
);

const props = withDefaults(
  defineProps<{
    tag?: string;
    variant?: 'default' | 'outline' | 'secondary' | 'ghost' | 'destructive';
    size?: 'default' | 'sm' | 'lg';
    class?: string;
  }>(),
  {
    tag: 'button',
    variant: 'default',
    size: 'default',
    class: '',
  }
);

const buttonClass = computed(() => cn(buttonVariants({ variant: props.variant, size: props.size }), props.class));
</script>
