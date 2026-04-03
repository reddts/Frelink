<template>
  <ul class="menu-tree" :class="{ 'menu-tree-root': depth === 0 }">
    <li v-for="item in items" :key="item.id" class="menu-node">
      <template v-if="item.children?.length">
        <button
          class="menu-link menu-group-toggle"
          :class="{ 'is-active': isExpanded(item.id) }"
          type="button"
          :title="collapsed ? item.title : ''"
          @click="toggleGroup(item.id)"
        >
          <span class="menu-icon">{{ iconText(item.icon) }}</span>
          <span v-if="!collapsed" class="menu-title">{{ item.title }}</span>
          <span v-if="!collapsed" class="menu-caret">{{ isExpanded(item.id) ? '−' : '+' }}</span>
        </button>
        <Transition name="menu-group">
          <AppMenuTree
            v-if="!collapsed && isExpanded(item.id)"
            :items="item.children"
            :collapsed="collapsed"
            :depth="depth + 1"
          />
        </Transition>
      </template>
      <RouterLink v-else :to="item.path" class="menu-link" active-class="is-active" :title="collapsed ? item.title : ''">
        <span class="menu-icon">{{ iconText(item.icon) }}</span>
        <span v-if="!collapsed" class="menu-title">{{ item.title }}</span>
      </RouterLink>
    </li>
  </ul>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import type { AdminMenuItem } from '@/types';

const MENU_GROUP_STORAGE_KEY = 'frelink-admin-menu-groups';
const expandedGroupIds = ref<number[]>(readExpandedGroupIds());

withDefaults(defineProps<{
  items: AdminMenuItem[];
  collapsed?: boolean;
  depth?: number;
}>(), {
  collapsed: false,
  depth: 0,
});

function iconText(icon: string) {
  const normalized = icon.trim();
  if (!normalized) {
    return '•';
  }
  return normalized.split(' ').slice(-1)[0].replace('fa-', '').slice(0, 2).toUpperCase();
}

function readExpandedGroupIds() {
  if (typeof window === 'undefined') {
    return [];
  }

  try {
    const raw = window.localStorage.getItem(MENU_GROUP_STORAGE_KEY);
    if (!raw) {
      return [];
    }

    const parsed = JSON.parse(raw);
    if (!Array.isArray(parsed)) {
      return [];
    }

    return parsed.map((item) => Number(item)).filter((item) => Number.isInteger(item) && item > 0);
  } catch (error) {
    return [];
  }
}

function persistExpandedGroupIds() {
  if (typeof window === 'undefined') {
    return;
  }

  window.localStorage.setItem(MENU_GROUP_STORAGE_KEY, JSON.stringify(expandedGroupIds.value));
}

function isExpanded(id: number) {
  return expandedGroupIds.value.includes(id);
}

function toggleGroup(id: number) {
  if (isExpanded(id)) {
    expandedGroupIds.value = expandedGroupIds.value.filter((item) => item !== id);
  } else {
    expandedGroupIds.value = [...expandedGroupIds.value, id];
  }

  persistExpandedGroupIds();
}
</script>
