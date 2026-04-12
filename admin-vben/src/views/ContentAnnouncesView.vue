<template>
  <div class="dashboard">
    <section class="hero-card">
      <div>
        <span class="eyebrow">Content / Announces</span>
        <h3>公告管理</h3>
        <p>公告管理链路已迁入新管理端，支持状态筛选、单条/批量删除与公告内容编辑。</p>
      </div>
      <div class="toolbar-row">
        <label class="search-inline">
          <span>搜索</span>
          <Input v-model.trim="keyword" placeholder="按公告标题筛选" @keydown.enter="reload" />
        </label>
        <div class="tab-row">
          <Button
            v-for="item in payload?.tabs || []"
            :key="item.value"
            type="button"
            :variant="currentStatus === item.value ? 'default' : 'outline'"
            size="sm"
            @click="switchStatus(item.value)"
          >
            {{ item.label }}
          </Button>
        </div>
      </div>
      <div class="selection-toolbar">
        <span>已选 {{ selectedIds.length }} 条公告</span>
        <Button variant="outline" size="sm" type="button" @click="toggleSelectAll">
          {{ isAllSelected ? '取消全选' : '全选当前列表' }}
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
        <span class="eyebrow">公告列表</span>
        <div class="config-table content-table">
          <div class="config-table-head content-table-head">
            <span>选择</span>
            <span>公告</span>
            <span>发布用户</span>
            <span>状态</span>
            <span>时间</span>
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
              <strong>{{ item.title }}</strong>
              <small>#{{ item.id }} / 浏览 {{ item.view_count }}</small>
              <ContentFlags :flags="item.flags" />
            </span>
            <span>
              <strong>{{ item.user_name || '-' }}</strong>
              <small>UID {{ item.uid }}</small>
            </span>
            <span>
              <strong>{{ item.status === 1 ? '启用' : '禁用' }}</strong>
              <small>排序 {{ item.sort }}</small>
            </span>
            <span>
              <strong>{{ item.create_time_text }}</strong>
              <small>{{ item.update_time_text }}</small>
            </span>
            <span class="config-actions">
              <button class="text-button" type="button" @click="editItem(item.id)">编辑</button>
              <a v-if="item.preview_url" class="text-button" :href="item.preview_url" target="_blank" rel="noreferrer">
                预览
              </a>
              <button class="text-button danger-button" type="button" @click="deleteItem(item.id)">删除</button>
            </span>
          </div>
        </div>
      </article>

      <article class="panel-card">
        <span class="eyebrow">公告编辑器</span>
        <ContentRecordEditor
          :form="editorForm"
          :links="detailLinks"
          :detail-fields="detail?.detail_fields || []"
          :flags="detail?.flags || []"
          title-label="公告标题"
          title-placeholder="请输入公告标题"
          body-label="公告内容"
          body-placeholder="请输入公告内容"
          submit-label="保存公告"
          empty-label="请选择公告"
          :show-seo-fields="false"
          :saving="saving"
          @submit="submitAnnounce"
        />
        <div v-if="detail" class="announce-switches">
          <label class="switch-item">
            <input v-model="state.set_top" type="checkbox" />
            <span>置顶公告</span>
          </label>
          <label class="pid-input">
            <span>排序值</span>
            <input v-model.number="state.sort" type="number" />
          </label>
          <label class="pid-input">
            <span>状态</span>
            <select v-model.number="state.status">
              <option :value="1">启用</option>
              <option :value="0">禁用</option>
            </select>
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
  deleteContentAnnounce,
  fetchContentAnnounceDetail,
  fetchContentAnnounces,
  saveContentAnnounce,
} from '@/api/admin';
import type { ContentAnnounceDetail, ContentAnnounceOverviewPayload, ContentRecordFormState } from '@/types';

const payload = ref<ContentAnnounceOverviewPayload | null>(null);
const detail = ref<ContentAnnounceDetail | null>(null);
const keyword = ref('');
const currentStatus = ref(-1);
const selectedId = ref(0);
const selectedIds = ref<number[]>([]);
const saving = ref(false);

const state = ref({
  set_top: false,
  sort: 0,
  status: 1,
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
    set_top: false,
    sort: 0,
    status: 1,
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
  payload.value = await fetchContentAnnounces(currentStatus.value, keyword.value);
  const validIds = new Set((payload.value?.list || []).map((item) => item.id));
  selectedIds.value = selectedIds.value.filter((id) => validIds.has(id));
}

async function switchStatus(status: number) {
  currentStatus.value = status;
  resetForm();
  await reload();
}

async function editItem(id: number) {
  detail.value = await fetchContentAnnounceDetail(id);
  selectedId.value = id;
  editorForm.value = {
    id: detail.value.id,
    title: detail.value.title || '',
    body: detail.value.message || '',
    seo_title: '',
    seo_keywords: '',
    seo_description: '',
  };
  state.value = {
    set_top: detail.value.set_top === 1,
    sort: detail.value.sort || 0,
    status: detail.value.status,
  };
}

async function submitAnnounce() {
  if (!editorForm.value.id) {
    return;
  }

  saving.value = true;
  try {
    await saveContentAnnounce({
      id: editorForm.value.id,
      title: editorForm.value.title,
      message: editorForm.value.body,
      set_top: state.value.set_top ? 1 : 0,
      sort: state.value.sort,
      status: state.value.status,
    });
    await editItem(editorForm.value.id);
    await reload();
  } catch (error) {
    window.alert(getErrorMessage(error));
  } finally {
    saving.value = false;
  }
}

async function deleteItem(id: number) {
  if (!window.confirm('确认删除该公告？该操作不可恢复。')) {
    return;
  }
  try {
    await deleteContentAnnounce(id);
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
  if (!window.confirm(`确认删除选中的 ${selectedIds.value.length} 条公告？该操作不可恢复。`)) {
    return;
  }
  try {
    await deleteContentAnnounce(selectedIds.value);
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
.announce-switches {
  margin-top: 12px;
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
}

.switch-item {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  color: #334155;
}

.pid-input {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  color: #334155;
}

.pid-input input,
.pid-input select {
  min-width: 120px;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 6px 10px;
}
</style>
