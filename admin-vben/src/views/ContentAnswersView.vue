<template>
  <div class="dashboard">
    <section class="hero-card">
      <div>
        <span class="eyebrow">Content / Answers</span>
        <h3>回答管理</h3>
        <p>回答列表、正文编辑和删除链路已迁入新管理端，支持软删和彻底删除切换。</p>
      </div>
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
    </section>

    <section class="panel-grid config-panel-grid">
      <article class="panel-card">
        <span class="eyebrow">回答列表</span>
        <div class="config-table content-table">
          <div class="config-table-head answer-table-head">
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
              <strong>{{ item.title }}</strong>
              <small>#{{ item.id }}</small>
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
              <small>反对 {{ item.against_count }} / 评论 {{ item.comment_count }}</small>
            </span>
            <span class="config-actions">
              <button class="text-button" type="button" @click="editItem(item.id)">编辑</button>
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
import { onMounted, ref } from 'vue';
import { deleteContentAnswer, fetchContentAnswerDetail, fetchContentAnswers, saveContentAnswer } from '@/api/admin';
import type { ContentAnswerDetail, ContentAnswerOverviewPayload } from '@/types';

const payload = ref<ContentAnswerOverviewPayload | null>(null);
const detail = ref<ContentAnswerDetail | null>(null);
const currentStatus = ref(1);
const selectedId = ref(0);
const saving = ref(false);

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
}

async function reload() {
  payload.value = await fetchContentAnswers(currentStatus.value);
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

onMounted(async () => {
  await reload();
});
</script>
