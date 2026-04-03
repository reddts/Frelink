<template>
  <div class="login-screen min-h-screen overflow-hidden bg-[radial-gradient(circle_at_top_left,rgba(20,101,255,0.18),transparent_24%),radial-gradient(circle_at_bottom_right,rgba(15,160,140,0.14),transparent_22%),linear-gradient(180deg,#eef4fb_0%,#f8fbff_100%)] px-4 py-3 md:px-6 md:py-4 lg:px-8 lg:py-4">
    <div class="login-screen-grid mx-auto grid h-[calc(100vh-1.5rem)] max-w-7xl grid-cols-1 gap-3 md:h-[calc(100vh-2rem)] lg:grid-cols-[1.04fr_0.96fr] lg:gap-4">
      <Card class="login-screen-hero relative hidden overflow-hidden border-slate-200/70 bg-slate-950 text-slate-50 shadow-[0_24px_72px_rgba(15,23,42,0.24)] lg:flex lg:min-h-0">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,rgba(59,130,246,0.32),transparent_30%),radial-gradient(circle_at_80%_20%,rgba(45,212,191,0.22),transparent_18%),linear-gradient(135deg,rgba(15,23,42,0.96),rgba(15,23,42,0.82))]"></div>
        <CardHeader class="relative z-10 flex h-full min-h-0 flex-col gap-5 p-7 xl:p-8">
          <div class="inline-flex w-fit items-center rounded-full border border-white/12 bg-white/8 px-3.5 py-1 text-[11px] font-semibold uppercase tracking-[0.22em] text-sky-100">
            Frelink Admin Console
          </div>
          <div class="max-w-xl space-y-3">
            <CardTitle class="text-3xl font-semibold leading-tight text-white xl:text-[2.5rem]">
              Frelink 知识平台管理中心
            </CardTitle>
            <CardDescription class="max-w-lg text-sm leading-6 text-slate-200/84 xl:text-base">
              统一处理用户、内容、审核与系统配置，保持站点运营链路可追踪、可控制、可快速响应。
            </CardDescription>
          </div>

          <div class="grid gap-3 xl:grid-cols-3">
            <div class="rounded-2xl border border-white/10 bg-white/6 p-3.5 backdrop-blur">
              <div class="text-xs uppercase tracking-[0.22em] text-slate-300">访问态势</div>
              <div class="mt-2 text-2xl font-semibold text-white">92.4%</div>
              <div class="mt-1.5 text-xs leading-5 text-slate-300">内容访问覆盖率保持稳定</div>
            </div>
            <div class="rounded-2xl border border-white/10 bg-white/6 p-3.5 backdrop-blur">
              <div class="text-xs uppercase tracking-[0.22em] text-slate-300">活跃用户</div>
              <div class="mt-2 text-2xl font-semibold text-white">18.7k</div>
              <div class="mt-1.5 text-xs leading-5 text-slate-300">近 7 日保持持续活跃</div>
            </div>
            <div class="rounded-2xl border border-white/10 bg-white/6 p-3.5 backdrop-blur">
              <div class="text-xs uppercase tracking-[0.22em] text-slate-300">Agent 参与</div>
              <div class="mt-2 text-2xl font-semibold text-white">31.2%</div>
              <div class="mt-1.5 text-xs leading-5 text-slate-300">智能协同进入常态化阶段</div>
            </div>
          </div>

          <div class="grid min-h-0 flex-1 gap-4 xl:grid-cols-[1.15fr_0.85fr]">
            <div class="rounded-[28px] border border-white/10 bg-white/6 p-4 backdrop-blur">
              <div class="flex items-center justify-between">
                <div>
                  <div class="text-xs uppercase tracking-[0.24em] text-slate-300">运营视图</div>
                  <div class="mt-1.5 text-lg font-semibold text-white">站点运行概览</div>
                </div>
                <div class="rounded-full border border-emerald-400/25 bg-emerald-400/12 px-3 py-1 text-xs text-emerald-200">
                  Online
                </div>
              </div>
              <div class="mt-4 grid h-36 grid-cols-12 items-end gap-2 xl:h-40">
                <div
                  v-for="(height, index) in chartHeights"
                  :key="index"
                  class="rounded-t-full bg-gradient-to-t from-sky-500 via-blue-400 to-cyan-300 shadow-[0_10px_24px_rgba(56,189,248,0.25)]"
                  :style="{ height: `${height}%` }"
                />
              </div>
              <div class="mt-3 flex justify-between text-[11px] text-slate-300/80">
                <span>内容流量</span>
                <span>审核效率</span>
                <span>用户活跃</span>
              </div>
            </div>

            <div class="grid gap-3">
              <div class="rounded-2xl border border-white/10 bg-white/6 p-4 backdrop-blur">
                <div class="text-xs uppercase tracking-[0.22em] text-slate-300">受控访问</div>
                <div class="mt-1.5 text-base font-semibold text-white">管理员身份验证</div>
                <p class="mt-2 text-sm leading-6 text-slate-300">
                  仅授权管理员可进入控制台，所有关键操作通过统一会话与权限链路校验。
                </p>
              </div>
              <div class="rounded-2xl border border-white/10 bg-white/6 p-4 backdrop-blur">
                <div class="text-xs uppercase tracking-[0.22em] text-slate-300">职责覆盖</div>
                <ul class="mt-3 space-y-2.5 text-sm text-slate-200/90">
                  <li class="flex items-center gap-2.5">
                    <span class="h-2 w-2 rounded-full bg-cyan-300"></span>
                    内容审核与发布流程
                  </li>
                  <li class="flex items-center gap-2.5">
                    <span class="h-2 w-2 rounded-full bg-emerald-300"></span>
                    用户与权限管理
                  </li>
                  <li class="flex items-center gap-2.5">
                    <span class="h-2 w-2 rounded-full bg-amber-300"></span>
                    系统配置与运行监控
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </CardHeader>
      </Card>

      <section class="login-screen-form-wrap flex min-h-0 items-center justify-center">
        <Card class="login-screen-card w-full max-w-[500px] border-slate-200/80 bg-white/92 shadow-[0_24px_80px_rgba(15,23,42,0.12)] backdrop-blur">
          <CardHeader class="space-y-3 px-6 pt-6 pb-3 md:px-7 md:pt-7">
            <div class="inline-flex w-fit items-center rounded-full bg-slate-900 px-3 py-1 text-xs font-medium uppercase tracking-[0.24em] text-white">
              Secure Access
            </div>
            <CardTitle class="text-3xl font-semibold tracking-tight text-slate-950">管理员登录</CardTitle>
            <CardDescription class="text-sm leading-6 text-slate-500">
              请输入管理员账号信息，进入 Frelink 管理中心。
            </CardDescription>
          </CardHeader>

          <CardContent class="px-6 pb-6 md:px-7 md:pb-7">
            <form class="login-form" @submit.prevent="handleSubmit">
              <label>
                <span class="text-sm font-medium text-slate-700">用户名</span>
                <Input v-model.trim="username" autocomplete="username" placeholder="请输入用户名" class="h-11 rounded-xl" />
              </label>
              <label>
                <span class="text-sm font-medium text-slate-700">密码</span>
                <Input
                  v-model="password"
                  type="password"
                  autocomplete="current-password"
                  placeholder="请输入密码"
                  class="h-11 rounded-xl"
                />
              </label>
              <p v-if="errorMessage" class="error-text">{{ errorMessage }}</p>
              <Button type="submit" :disabled="loading" size="lg" class="mt-1 h-11 rounded-xl">
                {{ loading ? '登录中...' : '进入管理中心' }}
              </Button>
            </form>
          </CardContent>
        </Card>
      </section>
    </div>
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
const chartHeights = [42, 58, 54, 68, 61, 73, 66, 78, 72, 84, 76, 88];

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
