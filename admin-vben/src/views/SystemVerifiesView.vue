<template>
  <div class="dashboard">
    <section class="hero-card">
      <div>
        <span class="eyebrow">Member / Verify</span>
        <h3>用户认证审核</h3>
        <p>用户认证审核列表、详情预览、通过和拒绝已迁入新管理端主链路。</p>
      </div>
      <div class="toolbar-row">
        <div class="tab-row">
          <button
            v-for="item in payload?.status_tabs || []"
            :key="item.value"
            class="ghost-button"
            :class="{ 'is-current': currentStatus === item.value }"
            type="button"
            @click="switchStatus(item.value)"
          >
            {{ item.label }}
          </button>
        </div>
        <label class="search-inline">
          <span>认证类型</span>
          <select v-model="currentType" @change="reload">
            <option v-for="item in payload?.type_tabs || []" :key="item.value || 'all'" :value="item.value">
              {{ item.label }}
            </option>
          </select>
        </label>
      </div>
      <div class="selection-toolbar">
        <span>已选 {{ selectedIds.length }} 条</span>
        <Button variant="outline" size="sm" type="button" @click="toggleSelectAll">
          {{ isAllSelected ? '取消全选' : '全选当前列表' }}
        </Button>
        <button v-if="currentStatus === 1" class="ghost-button" type="button" :disabled="!selectedIds.length" @click="approveSelected">
          批量通过
        </button>
        <button
          v-if="currentStatus === 1"
          class="ghost-button danger-button"
          type="button"
          :disabled="!selectedIds.length"
          @click="declineSelected"
        >
          批量拒绝
        </button>
      </div>
    </section>

    <section class="panel-grid config-panel-grid">
      <article class="panel-card">
        <span class="eyebrow">审核列表</span>
        <div class="config-table content-table">
          <div class="config-table-head">
            <span>选择</span>
            <span>用户</span>
            <span>认证类型</span>
            <span>摘要</span>
            <span>状态</span>
            <span>操作</span>
          </div>
          <div
            v-for="item in payload?.list || []"
            :key="item.id"
            class="config-table-row"
            :class="{ 'is-current': selectedId === item.id }"
          >
            <span>
              <label class="table-check">
                <input v-model="selectedIds" :value="item.id" type="checkbox" />
                <small>选中</small>
              </label>
            </span>
            <span>
              <strong>{{ item.nick_name || item.user_name || '-' }}</strong>
              <small>{{ item.user_name || '-' }} / {{ item.url_token || '-' }}</small>
            </span>
            <span>
              <strong>{{ item.type_label }}</strong>
              <small>#{{ item.id }}</small>
            </span>
            <span>
              <strong>{{ item.summary }}</strong>
              <small>{{ item.create_time_text }}</small>
            </span>
            <span>
              <strong>{{ statusLabel(item.status) }}</strong>
              <small>{{ item.reason || '无' }}</small>
            </span>
            <span class="config-actions">
              <button class="text-button" type="button" @click="viewDetail(item.id)">查看</button>
              <button v-if="item.status === 1" class="text-button" type="button" @click="approveOne(item.id)">通过</button>
              <button
                v-if="item.status === 1"
                class="text-button danger-button"
                type="button"
                @click="declineOne(item.id)"
              >
                拒绝
              </button>
            </span>
          </div>
        </div>
      </article>

      <article class="panel-card">
        <span class="eyebrow">审核详情</span>
        <template v-if="detail">
          <ContentDetailPanel :summary-fields="detailSummaryFields" :detail-fields="detail.preview_fields" />
          <label class="editor-form-group">
            <span>原始内容</span>
            <textarea :value="detail.payload_json" rows="14" disabled />
          </label>
          <div class="form-actions">
            <Button v-if="detail.status === 1" type="button" @click="approveOne(detail.id)">通过审核</Button>
            <Button
              v-if="detail.status === 1"
              variant="outline"
              type="button"
              class="danger-button"
              @click="declineOne(detail.id)"
            >
              拒绝审核
            </Button>
          </div>
        </template>
        <p v-else>请选择左侧认证记录查看详情。</p>
      </article>
    </section>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';
import {
  approveSystemVerify,
  declineSystemVerify,
  fetchSystemVerifyDetail,
  fetchSystemVerifies,
} from '@/api/admin';
import ContentDetailPanel from '@/components/ContentDetailPanel.vue';
import Button from '@/components/ui/button/Button.vue';
import type { SystemVerifyDetail, SystemVerifyOverviewPayload } from '@/types';

const payload = ref<SystemVerifyOverviewPayload | null>(null);
const detail = ref<SystemVerifyDetail | null>(null);
const currentStatus = ref(1);
const currentType = ref('');
const selectedId = ref(0);
const selectedIds = ref<number[]>([]);

const isAllSelected = computed(() => {
  const total = payload.value?.list.length ?? 0;
  return total > 0 && selectedIds.value.length === total;
});

const detailSummaryFields = computed(() => {
  if (!detail.value) {
    return [];
  }

  return [
    { label: '认证类型', value: detail.value.type_label },
    { label: '认证用户', value: detail.value.nick_name || detail.value.user_name || '-' },
    { label: '状态', value: statusLabel(detail.value.status) },
    { label: '拒绝原因', value: detail.value.reason || '无' },
    { label: '提交时间', value: detail.value.create_time_text },
  ];
});

function getErrorMessage(error: unknown) {
  return error instanceof Error ? error.message : '请求失败';
}

function statusLabel(status: number) {
  if (status === 2) return '已审核';
  if (status === 3) return '已拒绝';
  return '待审核';
}

async function reload() {
  payload.value = await fetchSystemVerifies(currentStatus.value, currentType.value);
  const validIds = new Set((payload.value?.list || []).map((item) => item.id));
  selectedIds.value = selectedIds.value.filter((id) => validIds.has(id));
}

async function switchStatus(status: number) {
  currentStatus.value = status;
  detail.value = null;
  selectedId.value = 0;
  selectedIds.value = [];
  await reload();
}

function toggleSelectAll() {
  const list = payload.value?.list || [];
  if (isAllSelected.value) {
    selectedIds.value = [];
    return;
  }
  selectedIds.value = list.map((item) => item.id);
}

async function viewDetail(id: number) {
  detail.value = await fetchSystemVerifyDetail(id);
  selectedId.value = id;
}

async function approveOne(id: number) {
  if (!window.confirm('确认通过这条认证记录？')) {
    return;
  }
  try {
    await approveSystemVerify(id);
    await reload();
    await viewDetail(id);
  } catch (error) {
    window.alert(getErrorMessage(error));
  }
}

async function approveSelected() {
  if (!selectedIds.value.length) {
    return;
  }
  if (!window.confirm(`确认通过选中的 ${selectedIds.value.length} 条认证记录？`)) {
    return;
  }

  try {
    await approveSystemVerify(selectedIds.value);
    await reload();
    if (selectedId.value && selectedIds.value.includes(selectedId.value)) {
      await viewDetail(selectedId.value);
    }
  } catch (error) {
    window.alert(getErrorMessage(error));
  }
}

async function declineOne(id: number) {
  const reason = window.prompt('请输入拒绝原因', '') ?? '';
  try {
    await declineSystemVerify(id, reason);
    await reload();
    await viewDetail(id);
  } catch (error) {
    window.alert(getErrorMessage(error));
  }
}

async function declineSelected() {
  if (!selectedIds.value.length) {
    return;
  }
  const reason = window.prompt('请输入批量拒绝原因', '') ?? '';
  try {
    await declineSystemVerify(selectedIds.value, reason);
    await reload();
    if (selectedId.value && selectedIds.value.includes(selectedId.value)) {
      await viewDetail(selectedId.value);
    }
  } catch (error) {
    window.alert(getErrorMessage(error));
  }
}

onMounted(async () => {
  await reload();
});
</script>
