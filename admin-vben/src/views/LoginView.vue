<template>
  <div class="login-page">
    <section class="login-hero">
      <div class="login-hero-panel">
        <span class="eyebrow">Frelink Backend Refresh</span>
        <h1>新管理端从 M1 基线开始接管登录、菜单和路由。</h1>
        <p>
          当前阶段先稳定后台会话、菜单和权限契约，再逐模块替换旧 Builder 页面。
        </p>
      </div>
    </section>

    <section class="login-card">
      <div class="login-card-header">
        <h2>管理员登录</h2>
        <p>使用现有后台账号登录，进入 `/admin-vben/` 新壳层。</p>
      </div>

      <form class="login-form" @submit.prevent="handleSubmit">
        <label>
          <span>用户名</span>
          <input v-model.trim="username" autocomplete="username" placeholder="请输入用户名" />
        </label>
        <label>
          <span>密码</span>
          <input v-model="password" type="password" autocomplete="current-password" placeholder="请输入密码" />
        </label>
        <p v-if="errorMessage" class="error-text">{{ errorMessage }}</p>
        <button class="primary-button" type="submit" :disabled="loading">
          {{ loading ? '登录中...' : '登录' }}
        </button>
      </form>
    </section>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const auth = useAuthStore();
const route = useRoute();
const router = useRouter();

const username = ref('');
const password = ref('');
const errorMessage = ref('');
const loading = ref(false);

async function handleSubmit() {
  errorMessage.value = '';
  if (!username.value || !password.value) {
    errorMessage.value = '用户名和密码不能为空';
    return;
  }

  loading.value = true;
  try {
    await auth.login(username.value, password.value);
    const redirect = typeof route.query.redirect === 'string' ? route.query.redirect : auth.homePath;
    await router.replace(redirect || '/dashboard');
  } catch (error) {
    errorMessage.value = error instanceof Error ? error.message : '登录失败';
  } finally {
    loading.value = false;
  }
}
</script>
