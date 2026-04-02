<template>
  <div class="dashboard">
    <section class="hero-card">
      <div>
        <span class="eyebrow">System / Configs</span>
        <h3>系统配置</h3>
        <p>配置列表和配置分组已经开始脱离旧后台模板，当前先完成读取、筛选和分组治理。</p>
      </div>
      <label class="search-inline">
        <span>搜索</span>
        <input v-model.trim="keyword" placeholder="按变量名或标题筛选" @keydown.enter="reload" />
      </label>
    </section>

    <article class="panel-card">
      <span class="eyebrow">配置分组</span>
      <div class="tab-row">
        <button
          v-for="item in payload?.group_tabs || []"
          :key="item.value"
          class="ghost-button"
          :class="{ 'is-current': currentGroupId === item.value }"
          type="button"
          @click="switchGroup(item.value)"
        >
          {{ item.label }}
        </button>
      </div>
    </article>

    <section class="panel-grid config-panel-grid">
      <article class="panel-card">
        <span class="eyebrow">配置项列表</span>
        <div class="config-table">
          <div class="config-table-head">
            <span>变量名</span>
            <span>标题</span>
            <span>类型</span>
            <span>分组</span>
            <span>排序</span>
          </div>

          <div v-for="item in payload?.list || []" :key="item.id" class="config-table-row">
            <span>
              <strong>{{ item.name }}</strong>
              <small>{{ item.value_preview || '无预览值' }}</small>
            </span>
            <span>
              <strong>{{ item.title }}</strong>
              <small>{{ item.tips || '无说明' }}</small>
            </span>
            <span>{{ item.type_label }}</span>
            <span>{{ item.group_name }}</span>
            <span>{{ item.sort }}</span>
          </div>
        </div>
      </article>

      <article class="panel-card">
        <span class="eyebrow">配置分组概览</span>
        <div class="quick-links group-list">
          <div v-for="item in payload?.groups || []" :key="item.id" class="ghost-button group-card-button config-group-card">
            <strong>{{ item.name }}</strong>
            <span>{{ item.status ? '正常' : '锁定' }} / {{ item.config_count }} 项配置</span>
            <small>{{ item.description || '暂无备注' }}</small>
          </div>
        </div>
      </article>
    </section>
  </div>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { fetchSystemConfigs } from '@/api/admin';
import type { SystemConfigOverviewPayload } from '@/types';

const payload = ref<SystemConfigOverviewPayload | null>(null);
const keyword = ref('');
const currentGroupId = ref(0);

async function reload() {
  payload.value = await fetchSystemConfigs(currentGroupId.value, keyword.value);
  currentGroupId.value = payload.value.group_id;
}

async function switchGroup(groupId: number) {
  currentGroupId.value = groupId;
  await reload();
}

onMounted(async () => {
  await reload();
});
</script>
