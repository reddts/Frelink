<template>
  <div class="trend-chart">
    <svg class="trend-chart-svg" viewBox="0 0 320 180" preserveAspectRatio="none" aria-hidden="true">
      <line v-for="tick in 4" :key="tick" x1="0" :y1="tickY(tick - 1)" x2="320" :y2="tickY(tick - 1)" class="trend-grid-line" />
      <g v-for="item in normalizedSeries" :key="item.label">
        <polyline :points="item.points" :stroke="item.color" class="trend-line" />
        <circle
          v-for="point in item.pointList"
          :key="`${item.label}-${point.x}-${point.y}`"
          :cx="point.x"
          :cy="point.y"
          r="3"
          :fill="item.color"
        />
      </g>
    </svg>
    <div class="trend-axis">
      <span v-for="label in labels" :key="label">{{ label }}</span>
    </div>
    <div class="trend-legend">
      <div v-for="item in series" :key="item.label" class="trend-legend-item">
        <span class="trend-legend-dot" :style="{ backgroundColor: item.color }"></span>
        <strong>{{ item.label }}</strong>
        <small>{{ item.values[item.values.length - 1] ?? 0 }}</small>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import type { AdminDashboardTrendSeries } from '@/types';

const props = defineProps<{
  labels: string[];
  series: AdminDashboardTrendSeries[];
}>();

const maxValue = computed(() => {
  const values = props.series.flatMap((item) => item.values);
  return Math.max(...values, 1);
});

const normalizedSeries = computed(() => {
  const count = Math.max(props.labels.length - 1, 1);

  return props.series.map((item) => {
    const pointList = item.values.map((value, index) => {
      const x = (320 / count) * index;
      const y = 160 - (Math.max(value, 0) / maxValue.value) * 130;
      return { x, y };
    });

    return {
      ...item,
      pointList,
      points: pointList.map((point) => `${point.x},${point.y}`).join(' '),
    };
  });
});

function tickY(index: number) {
  return 30 + index * 43;
}
</script>
