<template>
  <div class="login-page">
    <section class="login-hero">
      <Card class="login-hero-panel">
        <CardHeader>
          <span class="eyebrow">Frelink Backend Refresh</span>
          <CardTitle class="text-3xl leading-tight">新管理端从 M1 基线开始接管登录、菜单和路由。</CardTitle>
          <CardDescription>
            当前阶段先稳定后台会话、菜单和权限契约，再逐模块替换旧 Builder 页面。
          </CardDescription>
        </CardHeader>
      </Card>
    </section>

    <Card class="login-card">
      <CardHeader class="login-card-header">
        <CardTitle>管理员登录</CardTitle>
        <CardDescription>使用现有后台账号登录，进入 `/admin-vben/` 新壳层。</CardDescription>
      </CardHeader>

      <CardContent>
        <form class="login-form" @submit.prevent="handleSubmit">
          <label>
            <span>用户名</span>
            <Input v-model.trim="username" autocomplete="username" placeholder="请输入用户名" />
          </label>
          <label>
            <span>密码</span>
            <Input v-model="password" type="password" autocomplete="current-password" placeholder="请输入密码" />
          </label>
        <p v-if="errorMessage" class="error-text">{{ errorMessage }}</p>
          <Button type="submit" :disabled="loading">
          {{ loading ? '登录中...' : '登录' }}
          </Button>
        </form>
      </CardContent>
    </Card>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import Button from '@/components/ui/button/Button.vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import Input from '@/components/ui/input/Input.vue';
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
