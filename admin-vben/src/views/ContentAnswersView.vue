<template>
  <div class="dashboard">
    <section class="hero-card">
      <div>
        <span class="eyebrow">Content / Answers</span>
        <h3>回答管理</h3>
        <p>回答列表、正文编辑和删除链路已迁入新管理端，支持关键词筛选、状态标签和软删切换。</p>
      </div>
      <div class="toolbar-row">
        <label class="search-inline">
          <span>搜索</span>
          <input v-model.trim="keyword" placeholder="按问题、作者或回答内容筛选" @keydown.enter="reload" />
        </label>
        <div class="tab-row">
          <button
            v-for="item in payload?.tabs || []"
            :key="item.value"
            class="ghost-button"
            :class="{ 'is-current': currentStatus === item.value }"
            type="button"
            @click="switchStatus(item.value)"
          >
            {{ item.label }}
          </button>
        </div>
      </div>
      <div class="selection-toolbar">
        <span>已选 {{ selectedIds.length }} 条回答</span>
        <button class="ghost-button" type="button" @click="toggleSelectAll">
          {{ isAllSelected ? '取消全选' : '全选当前列表' }}
        </button>
        <button
          class="ghost-button danger-button"
          type="button"
          :disabled="!selectedIds.length"
          @click="deleteSelected(currentStatus === 0)"
        >
          {{ currentStatus === 0 ? '批量彻底删除' : '批量删除' }}
        </button>
      </div>
    </section>

    <section class="panel-grid config-panel-grid">
      <article class="panel-card">
        <span class="eyebrow">回答列表</span>
        <div class="config-table content-table">
          <div class="config-table-head answer-table-head">
            <span>选择</span>
            <span>问题</span>
            <span>作者</span>
            <span>内容</span>
            <span>互动</span>
            <span>操作</span>
          </div>
          <div
            v-for="item in payload?.list || []"
            :key="item.id"
            class="config-table-row answer-table-row"
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
              <strong>{{ item.nick_name || '未知用户' }}</strong>
              <small>{{ item.url_token || '-' }}</small>
            </span>
            <span>
              <strong>{{ item.content_preview || '无内容' }}</strong>
              <small>{{ item.create_time_text }}</small>
            </span>
            <span>
              <strong>赞同 {{ item.agree_count }}</strong>
              <small>反对 {{ item.against_count }} / 评论 {{ item.comment_count }} / 感谢 {{ item.thanks_count }}</small>
            </span>
            <span class="config-actions">
              <button class="text-button" type="button" @click="editItem(item.id)">编辑</button>
              <a v-if="item.preview_url" class="text-button" :href="item.preview_url" target="_blank" rel="noreferrer">
                预览
              </a>
              <button
                v-if="currentStatus === 1"
                class="text-button danger-button"
                type="button"
                @click="deleteItem(item.id, false)"
              >
                删除
              </button>
              <button
                v-if="currentStatus === 0"
                class="text-button danger-button"
                type="button"
                @click="deleteItem(item.id, true)"
              >
                彻底删除
              </button>
            </span>
          </div>
        </div>
      </article>

      <article class="panel-card">
        <span class="eyebrow">回答编辑器</span>
        <form class="editor-form" @submit.prevent="submitAnswer">
          <ContentDetailPanel
            :links="detailLinks"
            :detail-fields="detail?.detail_fields || []"
            :flags="detail?.flags || []"
          />
          <label>
            <span>问题标题</span>
            <input :value="detail?.question_title || ''" disabled />
          </label>
          <label>
            <span>回答正文</span>
            <textarea v-model="answerForm.content" rows="12" placeholder="请输入回答正文" />
          </label>
          <div class="form-actions">
            <button class="primary-button" type="submit" :disabled="saving || !answerForm.id">
              {{ saving ? '保存中...' : answerForm.id ? '保存回答' : '请选择回答' }}
            </button>
          </div>
        </form>
      </article>
    </section>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';
import ContentFlags from '@/components/ContentFlags.vue';
import ContentDetailPanel from '@/components/ContentDetailPanel.vue';
import { deleteContentAnswer, fetchContentAnswerDetail, fetchContentAnswers, saveContentAnswer } from '@/api/admin';
import type { ContentAnswerDetail, ContentAnswerOverviewPayload } from '@/types';

const payload = ref<ContentAnswerOverviewPayload | null>(null);
const detail = ref<ContentAnswerDetail | null>(null);
const keyword = ref('');
const currentStatus = ref(1);
const selectedId = ref(0);
const selectedIds = ref<number[]>([]);
const saving = ref(false);

const detailLinks = computed(() => {
  if (!detail.value) {
    return [];
  }

  return [{ label: '打开前台预览', href: detail.value.preview_url || '' }];
});

const answerForm = ref({
  id: 0,
  content: '',
});

function getErrorMessage(error: unknown) {
  return error instanceof Error ? error.message : '请求失败';
}

function resetForm() {
  answerForm.value = {
    id: 0,
    content: '',
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
  payload.value = await fetchContentAnswers(currentStatus.value, keyword.value);
  const validIds = new Set((payload.value?.list || []).map((item) => item.id));
  selectedIds.value = selectedIds.value.filter((id) => validIds.has(id));
}

async function switchStatus(status: number) {
  currentStatus.value = status;
  resetForm();
  await reload();
}

async function editItem(id: number) {
  detail.value = await fetchContentAnswerDetail(id);
  selectedId.value = id;
  answerForm.value = {
    id: detail.value.id,
    content: detail.value.content || '',
  };
}

async function submitAnswer() {
  if (!answerForm.value.id) {
    return;
  }
  saving.value = true;
  try {
    await saveContentAnswer(answerForm.value);
    await editItem(answerForm.value.id);
    await reload();
  } catch (error) {
    window.alert(getErrorMessage(error));
  } finally {
    saving.value = false;
  }
}

async function deleteItem(id: number, real: boolean) {
  const tips = real ? '确认彻底删除该回答？' : '确认删除该回答？';
  if (!window.confirm(tips)) {
    return;
  }
  try {
    await deleteContentAnswer(id, real);
    if (selectedId.value === id) {
      resetForm();
    }
    await reload();
  } catch (error) {
    window.alert(getErrorMessage(error));
  }
}

async function deleteSelected(real: boolean) {
  if (!selectedIds.value.length) {
    return;
  }
  const tips = real
    ? `确认彻底删除选中的 ${selectedIds.value.length} 条回答？`
    : `确认删除选中的 ${selectedIds.value.length} 条回答？`;
  if (!window.confirm(tips)) {
    return;
  }
  try {
    await deleteContentAnswer(selectedIds.value, real);
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
