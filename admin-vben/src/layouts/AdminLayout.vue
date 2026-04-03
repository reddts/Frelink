<template>
  <div class="shell">
    <aside class="sidebar">
      <div class="brand">
        <div class="brand-badge">Vben</div>
        <div>
          <h1>Frelink Admin</h1>
          <p>新管理端迁移骨架</p>
        </div>
      </div>
      <nav class="sidebar-nav">
        <RouterLink class="menu-link menu-root" to="/dashboard" active-class="is-active">仪表盘</RouterLink>
        <AppMenuTree :items="auth.menus" />
      </nav>
    </aside>

    <main class="main-panel">
      <header class="topbar">
        <div>
          <div class="eyebrow">管理端 / M1</div>
          <h2>{{ route.meta.title || '管理端' }}</h2>
        </div>
        <div class="topbar-actions">
          <div class="user-card">
            <strong>{{ auth.user?.nick_name || auth.user?.user_name }}</strong>
            <span>{{ auth.user?.group_name || '管理员' }}</span>
          </div>
          <button class="ghost-button" type="button" @click="handleLogout">退出</button>
        </div>
      </header>

      <section class="content-panel">
        <RouterView />
      </section>
    </main>
  </div>
</template>

<script setup lang="ts">
import { useRoute, useRouter } from 'vue-router';
import AppMenuTree from '@/components/AppMenuTree.vue';
import { useAuthStore } from '@/stores/auth';

const auth = useAuthStore();
const route = useRoute();
const router = useRouter();

async function handleLogout() {
  await auth.logout();
  await router.replace('/login');
}
</script>
