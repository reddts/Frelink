<template>
  <div class="dashboard">
    <section class="hero-card">
      <div>
        <span class="eyebrow">Content / Approvals</span>
        <h3>内容审核</h3>
        <p>审核列表、详情预览、通过、拒绝、封禁和封禁 IP 已迁入新管理端主链路。</p>
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
          <span>来源</span>
          <select v-model="currentAgentScope" @change="reload">
            <option value="">全部</option>
            <option value="1">仅 Agent</option>
            <option value="0">仅人工</option>
          </select>
        </label>
      </div>
      <div class="selection-toolbar">
        <span>已选 {{ selectedIds.length }} 条审核</span>
        <button class="ghost-button" type="button" @click="toggleSelectAll">
          {{ isAllSelected ? '取消全选' : '全选当前列表' }}
        </button>
        <button
          v-if="currentStatus === 0"
          class="ghost-button"
          type="button"
          :disabled="!selectedIds.length"
          @click="approveSelected"
        >
          批量通过
        </button>
        <button
          v-if="currentStatus === 0"
          class="ghost-button danger-button"
          type="button"
          :disabled="!selectedIds.length"
          @click="declineSelected"
        >
          批量拒绝
        </button>
        <button
          class="ghost-button"
          type="button"
          :disabled="!selectedUids.length"
          @click="forbidSelectedUsers"
        >
          批量封禁用户
        </button>
        <button
          class="ghost-button"
          type="button"
          :disabled="!selectedUids.length"
          @click="forbidSelectedUserIps"
        >
          批量封禁 IP
        </button>
      </div>
    </section>

    <article class="panel-card">
      <span class="eyebrow">审核类型</span>
      <div class="tab-row">
        <button
          v-for="item in payload?.type_tabs || []"
          :key="item.value || 'all'"
          class="ghost-button"
          :class="{ 'is-current': currentType === item.value }"
          type="button"
          @click="switchType(item.value)"
        >
          {{ item.label }}
        </button>
      </div>
    </article>

    <section class="panel-grid config-panel-grid">
      <article class="panel-card">
        <span class="eyebrow">审核列表</span>
        <div class="config-table content-table">
          <div class="config-table-head approval-table-head">
            <span>选择</span>
            <span>类型</span>
            <span>用户</span>
            <span>摘要</span>
            <span>状态</span>
            <span>操作</span>
          </div>
          <div
            v-for="item in payload?.list || []"
            :key="item.id"
            class="config-table-row approval-table-row"
            :class="{ 'is-current': selectedId === item.id }"
          >
            <span>
              <label class="table-check">
                <input v-model="selectedIds" :value="item.id" type="checkbox" />
                <small>选中</small>
              </label>
            </span>
            <span>
              <strong>{{ item.type_label }}</strong>
              <small>#{{ item.id }} / {{ item.is_agent ? 'Agent' : '人工' }}</small>
            </span>
            <span>
              <strong>{{ item.user_name || '未知用户' }}</strong>
              <small>{{ item.url_token || '-' }}</small>
            </span>
            <span>
              <strong>{{ item.summary }}</strong>
              <small>{{ item.create_time_text }}</small>
            </span>
            <span>
              <strong>{{ statusLabel(item.status) }}</strong>
              <small>{{ item.reason || '无备注' }}</small>
            </span>
            <span class="config-actions">
              <button class="text-button" type="button" @click="editItem(item.id)">查看</button>
              <a v-if="item.target_url" class="text-button" :href="item.target_url" target="_blank" rel="noreferrer">
                预览
              </a>
              <button
                v-if="item.status === 0"
                class="text-button"
                type="button"
                @click="approveItem(item.id)"
              >
                通过
              </button>
              <button
                v-if="item.status === 0"
                class="text-button danger-button"
                type="button"
                @click="declineItem(item.id)"
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
          <div class="detail-stack">
            <p><strong>类型：</strong>{{ detail.type_label }}</p>
            <p><strong>用户：</strong>{{ detail.user_name || '未知用户' }}</p>
            <p><strong>摘要：</strong>{{ detail.summary }}</p>
            <p v-if="detail.subject_title"><strong>关联标题：</strong>{{ detail.subject_title }}</p>
            <p><strong>状态：</strong>{{ statusLabel(detail.status) }}</p>
            <p><strong>拒绝理由：</strong>{{ detail.reason || '无' }}</p>
          </div>
          <div v-if="detail.target_url" class="inline-links">
            <a class="text-button" :href="detail.target_url" target="_blank" rel="noreferrer">打开前台预览</a>
          </div>
          <div v-if="detail.preview_fields?.length" class="detail-stack">
            <p v-for="field in detail.preview_fields" :key="field.label"><strong>{{ field.label }}：</strong>{{ field.value }}</p>
          </div>
          <label class="editor-form-group">
            <span>原始内容</span>
            <textarea :value="detail.payload_json" rows="14" disabled />
          </label>
          <div class="form-actions">
            <button
              v-if="detail.status === 0"
              class="primary-button"
              type="button"
              @click="approveItem(detail.id)"
            >
              通过审核
            </button>
            <button
              v-if="detail.status === 0"
              class="ghost-button danger-button"
              type="button"
              @click="declineItem(detail.id)"
            >
              拒绝审核
            </button>
            <button class="ghost-button" type="button" @click="forbidUser(detail.uid)">封禁用户</button>
            <button class="ghost-button" type="button" @click="forbidUserIp(detail.uid)">封禁 IP</button>
          </div>
        </template>
        <p v-else>请选择左侧审核记录查看详情。</p>
      </article>
    </section>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';
import {
  approveContentApproval,
  declineContentApproval,
  fetchContentApprovalDetail,
  fetchContentApprovals,
  forbidContentApprovalUser,
  forbidContentApprovalUserIp,
} from '@/api/admin';
import type { ContentApprovalDetail, ContentApprovalOverviewPayload } from '@/types';

const payload = ref<ContentApprovalOverviewPayload | null>(null);
const detail = ref<ContentApprovalDetail | null>(null);
const currentStatus = ref(0);
const currentType = ref('');
const currentAgentScope = ref('');
const selectedId = ref(0);
const selectedIds = ref<number[]>([]);

const isAllSelected = computed(() => {
  const total = payload.value?.list.length ?? 0;
  return total > 0 && selectedIds.value.length === total;
});

const selectedUids = computed(() => {
  const uidSet = new Set<number>();
  for (const item of payload.value?.list || []) {
    if (selectedIds.value.includes(item.id) && item.uid > 0) {
      uidSet.add(item.uid);
    }
  }
  return Array.from(uidSet);
});

function getErrorMessage(error: unknown) {
  return error instanceof Error ? error.message : '请求失败';
}

function statusLabel(status: number) {
  if (status === 1) return '已审核';
  if (status === 2) return '已拒绝';
  return '待审核';
}

async function reload() {
  payload.value = await fetchContentApprovals(currentStatus.value, currentType.value, currentAgentScope.value);
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

async function switchStatus(status: number) {
  currentStatus.value = status;
  detail.value = null;
  selectedId.value = 0;
  selectedIds.value = [];
  await reload();
}

async function switchType(type: string) {
  currentType.value = type;
  detail.value = null;
  selectedId.value = 0;
  selectedIds.value = [];
  await reload();
}

async function editItem(id: number) {
  detail.value = await fetchContentApprovalDetail(id);
  selectedId.value = id;
}

async function approveItem(id: number) {
  if (!window.confirm('确认通过这条审核记录？')) {
    return;
  }
  try {
    await approveContentApproval(id);
    await reload();
    await editItem(id);
  } catch (error) {
    window.alert(getErrorMessage(error));
  }
}

async function approveSelected() {
  if (!selectedIds.value.length) {
    return;
  }
  if (!window.confirm(`确认通过选中的 ${selectedIds.value.length} 条审核？`)) {
    return;
  }
  try {
    await approveContentApproval(selectedIds.value);
    await reload();
    if (selectedId.value && selectedIds.value.includes(selectedId.value)) {
      await editItem(selectedId.value);
    }
  } catch (error) {
    window.alert(getErrorMessage(error));
  }
}

async function declineItem(id: number) {
  const reason = window.prompt('请输入拒绝理由', '') ?? '';
  try {
    await declineContentApproval(id, reason);
    await reload();
    await editItem(id);
  } catch (error) {
    window.alert(getErrorMessage(error));
  }
}

async function declineSelected() {
  if (!selectedIds.value.length) {
    return;
  }
  const reason = window.prompt('请输入批量拒绝理由', '') ?? '';
  try {
    await declineContentApproval(selectedIds.value, reason);
    await reload();
    if (selectedId.value && selectedIds.value.includes(selectedId.value)) {
      await editItem(selectedId.value);
    }
  } catch (error) {
    window.alert(getErrorMessage(error));
  }
}

async function forbidUser(uid: number) {
  const forbiddenReason = window.prompt('请输入封禁原因');
  if (!forbiddenReason) {
    return;
  }
  const forbiddenTime = window.prompt('请输入解封时间，格式：2026-04-30 23:59', '');
  if (!forbiddenTime) {
    return;
  }
  try {
    await forbidContentApprovalUser(uid, forbiddenTime, forbiddenReason);
  } catch (error) {
    window.alert(getErrorMessage(error));
  }
}

async function forbidSelectedUsers() {
  if (!selectedUids.value.length) {
    return;
  }
  const forbiddenReason = window.prompt('请输入批量封禁原因');
  if (!forbiddenReason) {
    return;
  }
  const forbiddenTime = window.prompt('请输入解封时间，格式：2026-04-30 23:59', '');
  if (!forbiddenTime) {
    return;
  }
  try {
    await forbidContentApprovalUser(selectedUids.value, forbiddenTime, forbiddenReason);
  } catch (error) {
    window.alert(getErrorMessage(error));
  }
}

async function forbidUserIp(uid: number) {
  if (!window.confirm('确认封禁该用户的登录 IP？')) {
    return;
  }
  try {
    await forbidContentApprovalUserIp(uid);
  } catch (error) {
    window.alert(getErrorMessage(error));
  }
}

async function forbidSelectedUserIps() {
  if (!selectedUids.value.length) {
    return;
  }
  if (!window.confirm(`确认封禁选中记录对应的 ${selectedUids.value.length} 个用户 IP？`)) {
    return;
  }
  try {
    await forbidContentApprovalUserIp(selectedUids.value);
  } catch (error) {
    window.alert(getErrorMessage(error));
  }
}

onMounted(async () => {
  await reload();
});
</script>
