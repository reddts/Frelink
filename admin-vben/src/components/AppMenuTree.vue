<template>
  <ul class="menu-tree">
    <li v-for="item in items" :key="item.id" class="menu-node">
      <RouterLink :to="item.path" class="menu-link" active-class="is-active" :title="collapsed ? item.title : ''">
        <span class="menu-icon">{{ iconText(item.icon) }}</span>
        <span v-if="!collapsed" class="menu-title">{{ item.title }}</span>
      </RouterLink>
      <AppMenuTree v-if="item.children?.length" :items="item.children" :collapsed="collapsed" />
    </li>
  </ul>
</template>

<script setup lang="ts">
import type { AdminMenuItem } from '@/types';

defineProps<{
  items: AdminMenuItem[];
  collapsed?: boolean;
}>();

function iconText(icon: string) {
  const normalized = icon.trim();
  if (!normalized) {
    return '•';
  }
  return normalized.split(' ').slice(-1)[0].replace('fa-', '').slice(0, 2).toUpperCase();
}
</script>
