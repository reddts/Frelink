<template>
  <div class="shell" :class="{ 'is-sidebar-collapsed': isSidebarCollapsed }">
    <aside class="sidebar">
      <div class="brand">
        <div class="brand-badge">Vben</div>
        <div v-if="!isSidebarCollapsed">
          <h1>Frelink Admin</h1>
          <p>新管理端迁移骨架</p>
        </div>
      </div>
      <nav class="sidebar-nav">
        <RouterLink class="menu-link menu-root" to="/dashboard" active-class="is-active" :title="isSidebarCollapsed ? '仪表盘' : ''">
          <span class="menu-icon">DP</span>
          <span v-if="!isSidebarCollapsed" class="menu-title">仪表盘</span>
        </RouterLink>
        <AppMenuTree :items="auth.menus" :collapsed="isSidebarCollapsed" />
      </nav>
    </aside>

    <main class="main-panel">
      <header class="topbar">
        <div class="topbar-main">
          <Button class="sidebar-toggle" variant="outline" type="button" @click="toggleSidebar">
            {{ isSidebarCollapsed ? '展开菜单' : '折叠菜单' }}
          </Button>
          <div>
          <div class="eyebrow">管理端 / M1</div>
          <h2>{{ route.meta.title || '管理端' }}</h2>
          </div>
        </div>
        <div class="topbar-actions">
          <div class="version-card" title="当前新管理端框架基线与本地工程版本">
            <strong>框架基线 {{ frameworkBaseline }}</strong>
            <span>本地版本 {{ localAppVersion }}</span>
          </div>
          <div class="user-card">
            <strong>{{ auth.user?.nick_name || auth.user?.user_name }}</strong>
            <span>{{ auth.user?.group_name || '管理员' }}</span>
          </div>
          <Button variant="outline" type="button" @click="handleLogout">退出</Button>
        </div>
      </header>

      <section class="content-panel">
        <RouterView />
      </section>
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import AppMenuTree from '@/components/AppMenuTree.vue';
import Button from '@/components/ui/button/Button.vue';
import { useAuthStore } from '@/stores/auth';
import { FRAMEWORK_BASELINE, LOCAL_APP_VERSION } from '@/version';

const SIDEBAR_STATE_KEY = 'frelink-admin-sidebar-collapsed';
const auth = useAuthStore();
const route = useRoute();
const router = useRouter();
const isSidebarCollapsed = ref(readSidebarState());
const frameworkBaseline = FRAMEWORK_BASELINE;
const localAppVersion = `v${LOCAL_APP_VERSION}`;

function readSidebarState() {
  if (typeof window === 'undefined') {
    return true;
  }

  return window.localStorage.getItem(SIDEBAR_STATE_KEY) !== '0';
}

function toggleSidebar() {
  isSidebarCollapsed.value = !isSidebarCollapsed.value;
  if (typeof window !== 'undefined') {
    window.localStorage.setItem(SIDEBAR_STATE_KEY, isSidebarCollapsed.value ? '1' : '0');
  }
}

async function handleLogout() {
  await auth.logout();
  await router.replace('/login');
}
</script>
