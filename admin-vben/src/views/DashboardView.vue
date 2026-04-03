<template>
  <div class="dashboard">
    <section class="hero-card">
      <div>
        <span class="eyebrow">M1 / Dashboard</span>
        <h3>{{ dashboard?.title || 'Frelink 管理端' }}</h3>
        <p>{{ dashboard?.subtitle || '正在加载仪表盘数据...' }}</p>
      </div>
      <a class="ghost-link" href="/admin.php" target="_blank" rel="noreferrer">打开旧后台</a>
    </section>

    <section class="stats-grid">
      <article v-for="item in dashboard?.stats || []" :key="item.key" class="stat-card">
        <span>{{ item.label }}</span>
        <strong>{{ item.value }}</strong>
      </article>
    </section>

    <section class="panel-grid dashboard-visual-grid">
      <article v-for="item in dashboard?.ratio_metrics || []" :key="item.key" class="panel-card ratio-card">
        <span class="eyebrow">核心指标</span>
        <h4>{{ item.label }}</h4>
        <div class="ratio-value">
          <strong>{{ item.value }}</strong>
          <small>{{ item.unit }}</small>
        </div>
        <div class="ratio-track">
          <div class="ratio-fill" :style="{ width: `${Math.min(100, item.value)}%` }"></div>
        </div>
        <p>{{ item.description }}</p>
      </article>
    </section>

    <section class="panel-grid dashboard-chart-grid">
      <article v-for="panel in dashboard?.trend_panels || []" :key="panel.key" class="panel-card trend-card">
        <span class="eyebrow">趋势图</span>
        <h4>{{ panel.title }}</h4>
        <p>{{ panel.description }}</p>
        <DashboardTrendChart :labels="panel.labels" :series="panel.series" />
      </article>
    </section>

    <section class="panel-grid">
      <article class="panel-card">
        <span class="eyebrow">管理员</span>
        <h4>{{ auth.user?.nick_name || auth.user?.user_name }}</h4>
        <p>{{ auth.user?.group_name }} / UID {{ auth.user?.uid }}</p>
      </article>

      <article class="panel-card">
        <span class="eyebrow">快捷入口</span>
        <div class="quick-links">
          <a v-for="link in dashboard?.quick_links || []" :key="link.path" :href="link.path" target="_blank" rel="noreferrer">
            {{ link.title }}
          </a>
        </div>
      </article>
    </section>
  </div>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue';
import DashboardTrendChart from '@/components/DashboardTrendChart.vue';
import { fetchAdminDashboard } from '@/api/admin';
import { useAuthStore } from '@/stores/auth';
import type { AdminDashboardPayload } from '@/types';

const auth = useAuthStore();
const dashboard = ref<AdminDashboardPayload | null>(null);

onMounted(async () => {
  dashboard.value = await fetchAdminDashboard();
});
</script>
