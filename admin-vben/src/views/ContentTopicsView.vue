<template>
  <div class="dashboard">
    <section class="hero-card">
      <div>
        <span class="eyebrow">Content / Topics</span>
        <h3>话题管理</h3>
        <p>话题列表与编辑链路已迁入新管理端，支持根话题筛选与批量删除。</p>
      </div>
      <div class="toolbar-row">
        <label class="search-inline">
          <span>搜索</span>
          <Input v-model.trim="keyword" placeholder="按话题标题筛选" @keydown.enter="reload" />
        </label>
        <div class="tab-row">
          <Button
            v-for="item in payload?.tabs || []"
            :key="item.value"
            type="button"
            :variant="currentRootOnly === item.value ? 'default' : 'outline'"
            size="sm"
            @click="switchRootOnly(item.value)"
          >
            {{ item.label }}
          </Button>
        </div>
      </div>
      <div class="selection-toolbar">
        <span>已选 {{ selectedIds.length }} 个话题</span>
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
        <span class="eyebrow">话题列表</span>
        <div class="config-table content-table">
          <div class="config-table-head content-table-head">
            <span>选择</span>
            <span>话题</span>
            <span>结构</span>
            <span>互动</span>
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
              <small>#{{ item.id }} / {{ item.url_token || '-' }}</small>
              <ContentFlags :flags="item.flags" />
            </span>
            <span>
              <strong>{{ item.parent_title || '无父级' }}</strong>
              <small>{{ item.is_parent === 1 ? '根话题' : '子话题' }}</small>
            </span>
            <span>
              <strong>讨论 {{ item.discuss }}</strong>
              <small>周 {{ item.discuss_week }} / 月 {{ item.discuss_month }} / 关注 {{ item.focus }}</small>
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
        <span class="eyebrow">话题编辑器</span>
        <ContentRecordEditor
          :form="editorForm"
          :links="detailLinks"
          :detail-fields="detail?.detail_fields || []"
          :flags="detail?.flags || []"
          title-label="话题标题"
          title-placeholder="请输入话题标题"
          body-label="话题描述"
          body-placeholder="请输入话题描述"
          submit-label="保存话题"
          empty-label="请选择话题"
          :saving="saving"
          @submit="submitTopic"
        />
        <div v-if="detail" class="topic-switches">
          <label class="switch-item">
            <input v-model="state.is_parent" type="checkbox" />
            <span>根话题</span>
          </label>
          <label class="switch-item">
            <input v-model="state.lock" type="checkbox" />
            <span>锁定</span>
          </label>
          <label class="switch-item">
            <input v-model="state.top" type="checkbox" />
            <span>推荐</span>
          </label>
          <label class="pid-input">
            <span>父级ID</span>
            <input v-model.number="state.pid" type="number" min="0" :disabled="state.is_parent" />
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
import { deleteContentTopic, fetchContentTopicDetail, fetchContentTopics, saveContentTopic } from '@/api/admin';
import type { ContentRecordFormState, ContentTopicDetail, ContentTopicOverviewPayload } from '@/types';

const payload = ref<ContentTopicOverviewPayload | null>(null);
const detail = ref<ContentTopicDetail | null>(null);
const keyword = ref('');
const currentRootOnly = ref(0);
const selectedId = ref(0);
const selectedIds = ref<number[]>([]);
const saving = ref(false);

const state = ref({
  is_parent: false,
  lock: false,
  top: false,
  pid: 0,
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
    is_parent: false,
    lock: false,
    top: false,
    pid: 0,
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
  payload.value = await fetchContentTopics(currentRootOnly.value, keyword.value);
  const validIds = new Set((payload.value?.list || []).map((item) => item.id));
  selectedIds.value = selectedIds.value.filter((id) => validIds.has(id));
}

async function switchRootOnly(rootOnly: number) {
  currentRootOnly.value = rootOnly;
  resetForm();
  await reload();
}

async function editItem(id: number) {
  detail.value = await fetchContentTopicDetail(id);
  selectedId.value = id;
  editorForm.value = {
    id: detail.value.id,
    title: detail.value.title || '',
    body: detail.value.description || '',
    seo_title: detail.value.seo_title || '',
    seo_keywords: detail.value.seo_keywords || '',
    seo_description: detail.value.seo_description || '',
  };
  state.value = {
    is_parent: detail.value.is_parent === 1,
    lock: detail.value.lock === 1,
    top: detail.value.top === 1,
    pid: detail.value.pid || 0,
  };
}

async function submitTopic() {
  if (!editorForm.value.id) {
    return;
  }

  saving.value = true;
  try {
    await saveContentTopic({
      id: editorForm.value.id,
      title: editorForm.value.title,
      description: editorForm.value.body,
      seo_title: editorForm.value.seo_title,
      seo_keywords: editorForm.value.seo_keywords,
      seo_description: editorForm.value.seo_description,
      is_parent: state.value.is_parent ? 1 : 0,
      lock: state.value.lock ? 1 : 0,
      top: state.value.top ? 1 : 0,
      pid: state.value.is_parent ? 0 : state.value.pid,
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
  if (!window.confirm('确认删除该话题？该操作会清理关联关系。')) {
    return;
  }
  try {
    await deleteContentTopic(id);
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
  if (!window.confirm(`确认删除选中的 ${selectedIds.value.length} 个话题？该操作会清理关联关系。`)) {
    return;
  }
  try {
    await deleteContentTopic(selectedIds.value);
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
.topic-switches {
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

.pid-input input {
  width: 96px;
  border: 1px solid rgba(148, 163, 184, 0.35);
  background: rgba(15, 23, 42, 0.45);
  color: #e2e8f0;
  border-radius: 8px;
  padding: 6px 8px;
}
</style>
