<template>
  <div class="dashboard">
    <section class="hero-card">
      <div>
        <span class="eyebrow">Content / Columns</span>
        <h3>专栏管理</h3>
        <p>专栏列表与审核链路已迁入新管理端，支持状态筛选、审核动作与基础信息编辑。</p>
      </div>
      <div class="toolbar-row">
        <label class="search-inline">
          <span>搜索</span>
          <Input v-model.trim="keyword" placeholder="按专栏标题筛选" @keydown.enter="reload" />
        </label>
        <div class="tab-row">
          <Button
            v-for="item in payload?.tabs || []"
            :key="item.value"
            type="button"
            :variant="currentVerify === item.value ? 'default' : 'outline'"
            size="sm"
            @click="switchVerify(item.value)"
          >
            {{ item.label }}
          </Button>
        </div>
      </div>
      <div class="selection-toolbar">
        <span>已选 {{ selectedIds.length }} 个专栏</span>
        <Button variant="outline" size="sm" type="button" @click="toggleSelectAll">
          {{ isAllSelected ? '取消全选' : '全选当前列表' }}
        </Button>
        <Button
          v-if="currentVerify === 0"
          type="button"
          variant="outline"
          size="sm"
          :disabled="!selectedIds.length"
          @click="approveSelected"
        >
          批量通过
        </Button>
        <Button
          v-if="currentVerify === 0"
          type="button"
          variant="outline"
          size="sm"
          :disabled="!selectedIds.length"
          @click="declineSelected"
        >
          批量拒绝
        </Button>
        <Button
          type="button"
          variant="destructive"
          size="sm"
          :disabled="!selectedIds.length"
          @click="deleteSelected"
        >
          批量删除
        </Button>
      </div>
    </section>

    <section class="panel-grid config-panel-grid">
      <article class="panel-card">
        <span class="eyebrow">专栏列表</span>
        <div class="config-table content-table">
          <div class="config-table-head content-table-head">
            <span>选择</span>
            <span>专栏</span>
            <span>作者</span>
            <span>数据</span>
            <span>状态</span>
            <span>操作</span>
          </div>
          <div
            v-for="item in payload?.list || []"
            :key="item.id"
            class="config-table-row content-table-row"
            :class="{ 'is-current': selectedId === item.id }"
          >
            <span>
              <label class="table-check">
                <input v-model="selectedIds" :value="item.id" type="checkbox" />
                <small>选中</small>
              </label>
            </span>
            <span>
              <strong>{{ item.name }}</strong>
              <small>#{{ item.id }} / 排序 {{ item.sort }}</small>
              <small v-if="item.cover" class="cover-hint">已配置封面</small>
              <ContentFlags :flags="item.flags" />
            </span>
            <span>
              <strong>{{ item.user_name || '-' }}</strong>
              <small>UID {{ item.uid }}</small>
            </span>
            <span>
              <strong>文章 {{ item.post_count }}</strong>
              <small>关注 {{ item.focus_count }} / 浏览 {{ item.view_count }}</small>
            </span>
            <span>
              <strong>{{ verifyLabel(item.verify) }}</strong>
              <small>{{ item.create_time_text }}</small>
            </span>
            <span class="config-actions">
              <button class="text-button" type="button" @click="editItem(item.id)">编辑</button>
              <a v-if="item.preview_url" class="text-button" :href="item.preview_url" target="_blank" rel="noreferrer">
                预览
              </a>
              <button
                v-if="item.verify === 0"
                class="text-button"
                type="button"
                @click="approveItem(item.id)"
              >
                通过
              </button>
              <button
                v-if="item.verify === 0"
                class="text-button"
                type="button"
                @click="declineItem(item.id)"
              >
                拒绝
              </button>
              <button class="text-button danger-button" type="button" @click="deleteItem(item.id)">删除</button>
            </span>
          </div>
        </div>
      </article>

      <article class="panel-card">
        <span class="eyebrow">专栏编辑器</span>
        <ContentRecordEditor
          :form="editorForm"
          :links="detailLinks"
          :detail-fields="detail?.detail_fields || []"
          :flags="detail?.flags || []"
          title-label="专栏标题"
          title-placeholder="请输入专栏标题"
          body-label="专栏简介"
          body-placeholder="请输入专栏简介"
          submit-label="保存专栏"
          empty-label="请选择专栏"
          :show-seo-fields="false"
          :saving="saving"
          @submit="submitColumn"
        />
        <div v-if="detail" class="column-switches">
          <label class="switch-item">
            <input v-model="state.recommend" type="checkbox" />
            <span>推荐专栏</span>
          </label>
          <label class="pid-input">
            <span>排序值</span>
            <input v-model.number="state.sort" type="number" />
          </label>
          <label class="pid-input">
            <span>审核状态</span>
            <select v-model.number="state.verify">
              <option :value="0">待审核</option>
              <option :value="1">已审核</option>
              <option :value="2">已拒绝</option>
            </select>
          </label>
          <label class="pid-input cover-input">
            <span>封面链接</span>
            <input v-model.trim="state.cover" type="text" placeholder="请输入封面图片URL" />
          </label>
        </div>
      </article>
    </section>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';
import ContentFlags from '@/components/ContentFlags.vue';
import ContentRecordEditor from '@/components/ContentRecordEditor.vue';
import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';
import {
  approveContentColumn,
  declineContentColumn,
  deleteContentColumn,
  fetchContentColumnDetail,
  fetchContentColumns,
  saveContentColumn,
} from '@/api/admin';
import type { ContentColumnDetail, ContentColumnOverviewPayload, ContentRecordFormState } from '@/types';

const payload = ref<ContentColumnOverviewPayload | null>(null);
const detail = ref<ContentColumnDetail | null>(null);
const keyword = ref('');
const currentVerify = ref(1);
const selectedId = ref(0);
const selectedIds = ref<number[]>([]);
const saving = ref(false);

const state = ref({
  recommend: false,
  sort: 0,
  verify: 1,
  cover: '',
});

const detailLinks = computed(() => {
  if (!detail.value) {
    return [];
  }

  return [{ label: '打开前台预览', href: detail.value.preview_url || '' }];
});

const editorForm = ref<ContentRecordFormState>({
  id: 0,
  title: '',
  body: '',
  seo_title: '',
  seo_keywords: '',
  seo_description: '',
});

function getErrorMessage(error: unknown) {
  return error instanceof Error ? error.message : '请求失败';
}

function verifyLabel(verify: number) {
  if (verify === 1) {
    return '已审核';
  }
  if (verify === 2) {
    return '已拒绝';
  }
  return '待审核';
}

function resetForm() {
  editorForm.value = {
    id: 0,
    title: '',
    body: '',
    seo_title: '',
    seo_keywords: '',
    seo_description: '',
  };
  state.value = {
    recommend: false,
    sort: 0,
    verify: 1,
    cover: '',
  };
  detail.value = null;
  selectedId.value = 0;
  selectedIds.value = [];
}

const isAllSelected = computed(() => {
  const total = payload.value?.list.length ?? 0;
  return total > 0 && selectedIds.value.length === total;
});

function toggleSelectAll() {
  const list = payload.value?.list || [];
  if (isAllSelected.value) {
    selectedIds.value = [];
    return;
  }
  selectedIds.value = list.map((item) => item.id);
}

async function reload() {
  payload.value = await fetchContentColumns(currentVerify.value, keyword.value);
  const validIds = new Set((payload.value?.list || []).map((item) => item.id));
  selectedIds.value = selectedIds.value.filter((id) => validIds.has(id));
}

async function switchVerify(verify: number) {
  currentVerify.value = verify;
  resetForm();
  await reload();
}

async function editItem(id: number) {
  detail.value = await fetchContentColumnDetail(id);
  selectedId.value = id;
  editorForm.value = {
    id: detail.value.id,
    title: detail.value.name || '',
    body: detail.value.description || '',
    seo_title: '',
    seo_keywords: '',
    seo_description: '',
  };
  state.value = {
    recommend: detail.value.recommend === 1,
    sort: detail.value.sort || 0,
    verify: detail.value.verify,
    cover: detail.value.cover || '',
  };
}

async function submitColumn() {
  if (!editorForm.value.id) {
    return;
  }

  saving.value = true;
  try {
    await saveContentColumn({
      id: editorForm.value.id,
      name: editorForm.value.title,
      description: editorForm.value.body,
      recommend: state.value.recommend ? 1 : 0,
      sort: state.value.sort,
      verify: state.value.verify,
      cover: state.value.cover,
    });
    await editItem(editorForm.value.id);
    await reload();
  } catch (error) {
    window.alert(getErrorMessage(error));
  } finally {
    saving.value = false;
  }
}

async function approveItem(id: number) {
  try {
    await approveContentColumn(id);
    if (selectedId.value === id) {
      await editItem(id);
    }
    await reload();
  } catch (error) {
    window.alert(getErrorMessage(error));
  }
}

async function declineItem(id: number) {
  try {
    await declineContentColumn(id);
    if (selectedId.value === id) {
      await editItem(id);
    }
    await reload();
  } catch (error) {
    window.alert(getErrorMessage(error));
  }
}

async function approveSelected() {
  if (!selectedIds.value.length) {
    return;
  }

  try {
    await approveContentColumn(selectedIds.value);
    if (selectedId.value && selectedIds.value.includes(selectedId.value)) {
      resetForm();
    }
    await reload();
  } catch (error) {
    window.alert(getErrorMessage(error));
  }
}

async function declineSelected() {
  if (!selectedIds.value.length) {
    return;
  }

  try {
    await declineContentColumn(selectedIds.value);
    if (selectedId.value && selectedIds.value.includes(selectedId.value)) {
      resetForm();
    }
    await reload();
  } catch (error) {
    window.alert(getErrorMessage(error));
  }
}

async function deleteItem(id: number) {
  if (!window.confirm('确认删除该专栏？该操作不可恢复。')) {
    return;
  }
  try {
    await deleteContentColumn(id);
    if (selectedId.value === id) {
      resetForm();
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
  if (!window.confirm(`确认删除选中的 ${selectedIds.value.length} 个专栏？该操作不可恢复。`)) {
    return;
  }
  try {
    await deleteContentColumn(selectedIds.value);
    if (selectedId.value && selectedIds.value.includes(selectedId.value)) {
      resetForm();
    }
    await reload();
  } catch (error) {
    window.alert(getErrorMessage(error));
  }
}

onMounted(async () => {
  await reload();
});
</script>

<style scoped>
.column-switches {
  margin-top: 12px;
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
}

.switch-item {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 13px;
  color: #cbd5e1;
}

.pid-input {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  color: #cbd5e1;
}

.pid-input input,
.pid-input select {
  width: 120px;
  border: 1px solid rgba(148, 163, 184, 0.35);
  background: rgba(15, 23, 42, 0.45);
  color: #e2e8f0;
  border-radius: 8px;
  padding: 8px 10px;
}

.pid-input.cover-input input {
  width: 240px;
}

.cover-hint {
  display: inline-block;
  margin-left: 8px;
  color: #7dd3fc;
}
</style>
