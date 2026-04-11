<template>
  <div class="dashboard">
    <section class="hero-card">
      <div>
        <span class="eyebrow">Content / Approvals</span>
        <h3>内容审核</h3>
        <p>审核列表、详情预览、通过、拒绝、封禁和封禁 IP 已迁入新管理端主链路。</p>
      </div>
      <div class="toolbar-row">
        <label class="search-inline">
          <span>搜索</span>
          <input v-model.trim="keyword" placeholder="按审核摘要或用户名筛选" @keydown.enter="reload" />
        </label>
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
        <button
          class="ghost-button danger-button"
          type="button"
          :disabled="!selectedIds.length"
          @click="deleteSelected"
        >
          批量删除记录
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
              <small v-if="item.content_review?.score">评分 {{ item.content_review.score }} / 100</small>
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
              <button
                class="text-button danger-button"
                type="button"
                @click="deleteItem(item.id)"
              >
                删除
              </button>
            </span>
          </div>
        </div>
      </article>

      <article class="panel-card">
        <span class="eyebrow">审核详情</span>
        <template v-if="detail">
          <ContentDetailPanel :summary-fields="detailSummaryFields" :links="detailLinks" />
          <ContentRecordEditor
            v-if="previewEditorForm"
            :form="previewEditorForm"
            :detail-fields="detail.preview_fields || []"
            :title-label="previewEditorMeta.titleLabel"
            :body-label="previewEditorMeta.bodyLabel"
            :show-title-field="previewEditorMeta.showTitle"
            :show-seo-fields="previewEditorMeta.showSeo"
            :show-actions="false"
            readonly
            readonly-title
            readonly-body
          />
          <ContentDetailPanel
            v-else
            :detail-fields="detail.preview_fields || []"
          />
          <ContentDetailPanel
            v-if="detailReviewFields.length"
            :summary-fields="detailReviewSummaryFields"
            :detail-fields="detailReviewFields"
          />
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
import ContentDetailPanel from '@/components/ContentDetailPanel.vue';
import ContentRecordEditor from '@/components/ContentRecordEditor.vue';
import {
  approveContentApproval,
  deleteContentApproval,
  declineContentApproval,
  fetchContentApprovalDetail,
  fetchContentApprovals,
  forbidContentApprovalUser,
  forbidContentApprovalUserIp,
} from '@/api/admin';
import type {
  ContentApprovalDetail,
  ContentApprovalOverviewPayload,
  ContentRecordFormState,
  ContentReviewMeta,
  DetailFieldItem,
} from '@/types';

const payload = ref<ContentApprovalOverviewPayload | null>(null);
const detail = ref<ContentApprovalDetail | null>(null);
const currentStatus = ref(0);
const currentType = ref('');
const currentAgentScope = ref('');
const keyword = ref('');
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

const detailSummaryFields = computed(() => {
  if (!detail.value) {
    return [];
  }

  return [
    { label: '类型', value: detail.value.type_label },
    { label: '用户', value: detail.value.user_name || '未知用户' },
    { label: '摘要', value: detail.value.summary },
    { label: '关联标题', value: detail.value.subject_title || '' },
    { label: '审核评分', value: detail.value.content_review?.score ? `${detail.value.content_review.score} / 100` : '' },
    { label: '审核建议', value: detail.value.content_review?.recommendation || '' },
    { label: '状态', value: statusLabel(detail.value.status) },
    { label: '拒绝理由', value: detail.value.reason || '无' },
  ];
});

const detailLinks = computed(() => {
  if (!detail.value) {
    return [];
  }

  return [{ label: '打开前台预览', href: detail.value.target_url || '' }];
});

const detailReviewSummaryFields = computed<DetailFieldItem[]>(() => {
  const review = detail.value?.content_review;
  if (!review) {
    return [];
  }
  return [
    { label: '正文字符数', value: String(review.metrics?.plain_text_chars || 0) },
    { label: '段落数', value: String(review.metrics?.paragraphs || 0) },
    { label: '小标题数', value: String(review.metrics?.headings || 0) },
    { label: '列表项数', value: String(review.metrics?.list_items || 0) },
  ];
});

const detailReviewFields = computed<DetailFieldItem[]>(() => {
  return buildReviewDetailFields(detail.value?.content_review);
});

const previewEditorMeta = computed(() => {
  const type = detail.value?.type || '';
  if (type === 'question' || type === 'modify_question') {
    return {
      titleLabel: '问题标题',
      bodyLabel: '问题详情',
      showTitle: true,
      showSeo: true,
    };
  }
  if (type === 'article' || type === 'modify_article') {
    return {
      titleLabel: '文章标题',
      bodyLabel: '文章正文',
      showTitle: true,
      showSeo: true,
    };
  }
  if (type === 'answer' || type === 'modify_answer') {
    return {
      titleLabel: '标题',
      bodyLabel: '回答正文',
      showTitle: false,
      showSeo: false,
    };
  }
  return {
    titleLabel: '标题',
    bodyLabel: '正文',
    showTitle: true,
    showSeo: false,
  };
});

const previewEditorForm = computed<ContentRecordFormState | null>(() => {
  if (!detail.value) {
    return null;
  }

  const payload = detail.value.payload || {};
  const type = detail.value.type || '';
  if (type === 'question' || type === 'modify_question') {
    return {
      id: detail.value.id,
      title: String(payload.title || ''),
      body: String(payload.detail || ''),
      seo_title: String(payload.seo_title || ''),
      seo_keywords: String(payload.seo_keywords || ''),
      seo_description: String(payload.seo_description || ''),
    };
  }
  if (type === 'article' || type === 'modify_article') {
    return {
      id: detail.value.id,
      title: String(payload.title || ''),
      body: String(payload.message || ''),
      seo_title: String(payload.seo_title || ''),
      seo_keywords: String(payload.seo_keywords || ''),
      seo_description: String(payload.seo_description || ''),
    };
  }
  if (type === 'answer' || type === 'modify_answer') {
    return {
      id: detail.value.id,
      title: '',
      body: String(payload.content || ''),
      seo_title: '',
      seo_keywords: '',
      seo_description: '',
    };
  }

  return null;
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
  payload.value = await fetchContentApprovals(
    currentStatus.value,
    currentType.value,
    currentAgentScope.value,
    keyword.value,
  );
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

function buildReviewDetailFields(review?: ContentReviewMeta): DetailFieldItem[] {
  if (!review) {
    return [];
  }
  const categoryStatus = review.completeness?.category?.status || '';
  const categoryValue = review.completeness?.category?.value || '';
  const coverStatus = review.completeness?.cover?.status || '';
  const coverValue = review.completeness?.cover?.value || '';
  const articleType = review.completeness?.article_type || '';
  const issues = Array.isArray(review.issues) ? review.issues.filter(Boolean) : [];

  return [
    { label: '分类完整度', value: categoryStatus ? `${categoryStatus}${categoryValue ? `（${categoryValue}）` : ''}` : '' },
    { label: '封面完整度', value: coverStatus ? `${coverStatus}${coverValue ? `（${coverValue}）` : ''}` : '' },
    { label: '文章类型', value: articleType },
    { label: '风险项', value: issues.length ? issues.join('；') : '未检测到明显风险项' },
  ].filter((item) => item.value);
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

async function deleteItem(id: number) {
  if (!window.confirm('确认删除这条审核记录？')) {
    return;
  }
  try {
    await deleteContentApproval(id);
    if (selectedId.value === id) {
      detail.value = null;
      selectedId.value = 0;
    }
    await reload();
  } catch (error) {
    window.alert(getErrorMessage(error));
  }
}

async function deleteSelected() {
  if (!selectedIds.value.length) {
    return;
  }
  if (!window.confirm(`确认删除选中的 ${selectedIds.value.length} 条审核记录？`)) {
    return;
  }
  try {
    await deleteContentApproval(selectedIds.value);
    if (selectedId.value && selectedIds.value.includes(selectedId.value)) {
      detail.value = null;
      selectedId.value = 0;
    }
    await reload();
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
