<template>
  <div class="dashboard">
    <section class="hero-card">
      <div>
        <span class="eyebrow">Member / Forbidden IP</span>
        <h3>IP 封禁管理</h3>
        <p>封禁 IP 列表、手动封禁与批量解封已迁入新管理端主链路。</p>
      </div>
      <label class="search-inline">
        <span>搜索 IP</span>
        <Input v-model.trim="keyword" placeholder="输入完整 IP，例如 1.1.1.1" @keydown.enter="reload" />
      </label>
      <Button type="button" @click="reload">刷新</Button>
      <div class="selection-toolbar">
        <span>已选 {{ selectedIds.length }} 条</span>
        <Button variant="outline" size="sm" type="button" @click="toggleSelectAll">
          {{ isAllSelected ? '取消全选' : '全选当前列表' }}
        </Button>
        <button class="ghost-button danger-button" type="button" :disabled="!selectedIds.length" @click="removeSelected">
          批量解封
        </button>
      </div>
    </section>

    <section class="panel-grid config-panel-grid">
      <article class="panel-card">
        <span class="eyebrow">封禁列表</span>
        <div class="config-table content-table">
          <div class="config-table-head">
            <span>选择</span>
            <span>IP</span>
            <span>地址</span>
            <span>封禁时间</span>
            <span>操作</span>
          </div>
          <div v-for="item in payload?.list || []" :key="item.id" class="config-table-row">
            <span>
              <label class="table-check">
                <input v-model="selectedIds" :value="item.id" type="checkbox" />
                <small>选中</small>
              </label>
            </span>
            <span>
              <strong>{{ item.ip }}</strong>
              <small>ID #{{ item.id }}</small>
            </span>
            <span>
              <strong>{{ item.address || '-' }}</strong>
              <small>{{ item.uid > 0 ? `用户 #${item.uid}` : '手动封禁' }}</small>
            </span>
            <span>
              <strong>{{ item.time_text }}</strong>
            </span>
            <span class="config-actions">
              <button class="text-button danger-button" type="button" @click="removeOne(item.id)">解除封禁</button>
            </span>
          </div>
        </div>
      </article>

      <article class="panel-card">
        <span class="eyebrow">手动封禁 IP</span>
        <form class="editor-form" @submit.prevent="submitAdd">
          <label>
            <span>IP 地址</span>
            <Input v-model.trim="ipInput" placeholder="多个 IP 用英文逗号分隔" />
          </label>
          <div class="form-actions">
            <Button type="submit" :disabled="adding || !ipInput.trim()">
              {{ adding ? '提交中...' : '执行封禁' }}
            </Button>
            <Button variant="outline" type="button" @click="ipInput = ''">清空</Button>
          </div>
        </form>
      </article>
    </section>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';
import {
  addSystemForbiddenIp,
  fetchSystemForbiddenIps,
  removeSystemForbiddenIp,
} from '@/api/admin';
import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';
import type { SystemForbiddenIpOverviewPayload } from '@/types';

const payload = ref<SystemForbiddenIpOverviewPayload | null>(null);
const keyword = ref('');
const ipInput = ref('');
const selectedIds = ref<number[]>([]);
const adding = ref(false);

const isAllSelected = computed(() => {
  const total = payload.value?.list.length ?? 0;
  return total > 0 && selectedIds.value.length === total;
});

function getErrorMessage(error: unknown) {
  return error instanceof Error ? error.message : '请求失败';
}

async function reload() {
  payload.value = await fetchSystemForbiddenIps(keyword.value);
  const validIds = new Set((payload.value?.list || []).map((item) => item.id));
  selectedIds.value = selectedIds.value.filter((id) => validIds.has(id));
}

function toggleSelectAll() {
  const list = payload.value?.list || [];
  if (isAllSelected.value) {
    selectedIds.value = [];
    return;
  }
  selectedIds.value = list.map((item) => item.id);
}

async function submitAdd() {
  if (!ipInput.value.trim()) {
    return;
  }

  adding.value = true;
  try {
    await addSystemForbiddenIp(ipInput.value.trim());
    ipInput.value = '';
    await reload();
  } catch (error) {
    window.alert(getErrorMessage(error));
  } finally {
    adding.value = false;
  }
}

async function removeOne(id: number) {
  if (!window.confirm('确认解除该 IP 封禁？')) {
    return;
  }

  try {
    await removeSystemForbiddenIp(id);
    await reload();
  } catch (error) {
    window.alert(getErrorMessage(error));
  }
}

async function removeSelected() {
  if (!selectedIds.value.length) {
    return;
  }
  if (!window.confirm(`确认解除选中的 ${selectedIds.value.length} 条 IP 封禁？`)) {
    return;
  }

  try {
    await removeSystemForbiddenIp(selectedIds.value);
    await reload();
  } catch (error) {
    window.alert(getErrorMessage(error));
  }
}

onMounted(async () => {
  await reload();
});
</script>
