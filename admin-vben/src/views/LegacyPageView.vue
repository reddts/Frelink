<template>
  <div class="legacy-page">
    <section class="hero-card">
      <div>
        <span class="eyebrow">Legacy Bridge</span>
        <h3>{{ menu?.title || '未迁移页面' }}</h3>
        <p>
          当前菜单已接入新壳层，但业务页面仍使用旧后台链路。后续按 `管理端模板更新计划.md` 的模块优先级逐步替换。
        </p>
      </div>
      <a v-if="menu?.legacy_url" class="primary-link" :href="menu.legacy_url" target="_blank" rel="noreferrer">
        打开旧页面
      </a>
    </section>

    <article class="panel-card">
      <span class="eyebrow">迁移信息</span>
      <p><strong>规则名：</strong>{{ menu?.rule_name || route.path }}</p>
      <p><strong>新路由：</strong>{{ menu?.path || route.path }}</p>
      <p><strong>状态：</strong>{{ menu?.migration_status || 'legacy' }}</p>
    </article>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const route = useRoute();
const auth = useAuthStore();

const menu = computed(() => auth.findMenuByPath(route.path));
</script>
