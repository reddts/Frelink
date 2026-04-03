<template>
  <div class="dashboard">
    <section class="hero-card">
      <div>
        <span class="eyebrow">Content / Questions</span>
        <h3>问题管理</h3>
        <p>问题列表、SEO 和回收操作已迁入新管理端，旧页开始退到兜底入口。</p>
      </div>
      <div class="toolbar-row">
        <label class="search-inline">
          <span>搜索</span>
          <Input v-model.trim="keyword" placeholder="按问题标题筛选" @keydown.enter="reload" />
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
        <span>已选 {{ selectedIds.length }} 个问题</span>
        <Button variant="outline" size="sm" type="button" @click="toggleSelectAll">
          {{ isAllSelected ? '取消全选' : '全选当前列表' }}
        </Button>
        <Button
          v-if="currentStatus === 1"
          type="button"
          variant="destructive"
          size="sm"
          :disabled="!selectedIds.length"
          @click="deleteSelected"
        >
          批量删除
        </Button>
        <Button
          v-if="currentStatus === 0"
          type="button"
          variant="outline"
          size="sm"
          :disabled="!selectedIds.length"
          @click="manageSelected('recover')"
        >
          批量恢复
        </Button>
        <Button
          v-if="currentStatus === 0"
          type="button"
          variant="destructive"
          size="sm"
          :disabled="!selectedIds.length"
          @click="manageSelected('remove')"
        >
          批量彻底删除
        </Button>
      </div>
    </section>

    <section class="panel-grid config-panel-grid">
      <article class="panel-card">
        <span class="eyebrow">问题列表</span>
        <div class="config-table content-table">
          <div class="config-table-head content-table-head">
            <span>选择</span>
            <span>标题</span>
            <span>用户</span>
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
              <small>#{{ item.id }}</small>
              <ContentFlags :flags="item.flags" />
            </span>
            <span>
              <strong>{{ item.user_name || '未知用户' }}</strong>
              <small>{{ item.url_token || '-' }}</small>
            </span>
            <span>
              <strong>回答 {{ item.answer_count }}</strong>
              <small>评论 {{ item.comment_count }} / 赞同 {{ item.agree_count }} / 浏览 {{ item.view_count }}</small>
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
              <a v-if="item.edit_url" class="text-button" :href="item.edit_url" target="_blank" rel="noreferrer">
                发布页
              </a>
              <button
                v-if="currentStatus === 1"
                class="text-button danger-button"
                type="button"
                @click="deleteItem(item.id)"
              >
                删除
              </button>
              <button
                v-if="currentStatus === 0"
                class="text-button"
                type="button"
                @click="manageItem(item.id, 'recover')"
              >
                恢复
              </button>
              <button
                v-if="currentStatus === 0"
                class="text-button danger-button"
                type="button"
                @click="manageItem(item.id, 'remove')"
              >
                彻底删除
              </button>
            </span>
          </div>
        </div>
      </article>

      <article class="panel-card">
        <span class="eyebrow">内容编辑器</span>
        <ContentRecordEditor
          :form="editorForm"
          :links="detailLinks"
          :detail-fields="detail?.detail_fields || []"
          :flags="detail?.flags || []"
          title-label="问题标题"
          title-placeholder="请输入问题标题"
          body-label="问题详情"
          body-placeholder="请输入问题详情"
          submit-label="保存问题"
          empty-label="请选择问题"
          :saving="saving"
          @submit="submitQuestion"
        />
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
  deleteContentQuestion,
  fetchContentQuestionDetail,
  fetchContentQuestions,
  manageContentQuestion,
  saveContentQuestion,
} from '@/api/admin';
import type { ContentQuestionDetail, ContentQuestionOverviewPayload, ContentRecordFormState } from '@/types';

const payload = ref<ContentQuestionOverviewPayload | null>(null);
const detail = ref<ContentQuestionDetail | null>(null);
const keyword = ref('');
const currentStatus = ref(1);
const selectedId = ref(0);
const selectedIds = ref<number[]>([]);
const saving = ref(false);

const detailLinks = computed(() => {
  if (!detail.value) {
    return [];
  }

  return [
    { label: '打开前台预览', href: detail.value.preview_url || '' },
    { label: '打开发布页', href: detail.value.edit_url || '' },
  ];
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

function resetSeoForm() {
  editorForm.value = {
    id: 0,
    title: '',
    body: '',
    seo_title: '',
    seo_keywords: '',
    seo_description: '',
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
  payload.value = await fetchContentQuestions(currentStatus.value, keyword.value);
  const validIds = new Set((payload.value?.list || []).map((item) => item.id));
  selectedIds.value = selectedIds.value.filter((id) => validIds.has(id));
}

async function switchStatus(status: number) {
  currentStatus.value = status;
  resetSeoForm();
  await reload();
}

async function editItem(id: number) {
  detail.value = await fetchContentQuestionDetail(id);
  selectedId.value = id;
  editorForm.value = {
    id: detail.value.id,
    title: detail.value.title || '',
    body: detail.value.detail || '',
    seo_title: detail.value.seo_title || '',
    seo_keywords: detail.value.seo_keywords || '',
    seo_description: detail.value.seo_description || '',
  };
}

async function submitQuestion() {
  if (!editorForm.value.id) {
    return;
  }

  saving.value = true;
  try {
    await saveContentQuestion({
      id: editorForm.value.id,
      title: editorForm.value.title,
      detail: editorForm.value.body,
      seo_title: editorForm.value.seo_title,
      seo_keywords: editorForm.value.seo_keywords,
      seo_description: editorForm.value.seo_description,
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
  if (!window.confirm('确认删除该问题？')) {
    return;
  }
  try {
    await deleteContentQuestion(id);
    if (selectedId.value === id) {
      resetSeoForm();
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
  if (!window.confirm(`确认删除选中的 ${selectedIds.value.length} 个问题？`)) {
    return;
  }
  try {
    await deleteContentQuestion(selectedIds.value);
    if (selectedId.value && selectedIds.value.includes(selectedId.value)) {
      resetSeoForm();
    }
    await reload();
  } catch (error) {
    window.alert(getErrorMessage(error));
  }
}

async function manageItem(id: number, type: 'recover' | 'remove') {
  const tips = type === 'recover' ? '确认恢复该问题？' : '确认彻底删除该问题？';
  if (!window.confirm(tips)) {
    return;
  }
  try {
    await manageContentQuestion(id, type);
    if (selectedId.value === id) {
      resetSeoForm();
    }
    await reload();
  } catch (error) {
    window.alert(getErrorMessage(error));
  }
}

async function manageSelected(type: 'recover' | 'remove') {
  if (!selectedIds.value.length) {
    return;
  }
  const tips =
    type === 'recover'
      ? `确认恢复选中的 ${selectedIds.value.length} 个问题？`
      : `确认彻底删除选中的 ${selectedIds.value.length} 个问题？`;
  if (!window.confirm(tips)) {
    return;
  }
  try {
    await manageContentQuestion(selectedIds.value, type);
    if (selectedId.value && selectedIds.value.includes(selectedId.value)) {
      resetSeoForm();
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
