<template>
  <div class="dashboard">
    <section class="hero-card">
      <div>
        <span class="eyebrow">System / Configs</span>
        <h3>系统配置</h3>
        <p>配置页现在不只是读取，已经开始接入配置项和配置分组的编辑保存链路。</p>
      </div>
      <div class="toolbar-row">
        <label class="search-inline">
          <span>搜索</span>
          <input v-model.trim="keyword" placeholder="按变量名或标题筛选" @keydown.enter="reload" />
        </label>
        <div class="quick-links">
          <button class="primary-button" type="button" @click="startCreateConfig">新增配置</button>
          <button class="ghost-button" type="button" @click="startCreateGroup">新增分组</button>
        </div>
      </div>
    </section>

    <article class="panel-card">
      <span class="eyebrow">配置分组</span>
      <div class="tab-row">
        <button
          v-for="item in payload?.group_tabs || []"
          :key="item.value"
          class="ghost-button"
          :class="{ 'is-current': currentGroupId === item.value }"
          type="button"
          @click="switchGroup(item.value)"
        >
          {{ item.label }}
        </button>
      </div>
    </article>

    <section class="panel-grid config-panel-grid">
      <article class="panel-card">
        <span class="eyebrow">配置项列表</span>
        <div class="config-table">
          <div class="config-table-head">
            <span>变量名</span>
            <span>标题</span>
            <span>类型</span>
            <span>分组</span>
            <span>排序</span>
          </div>

          <div
            v-for="item in payload?.list || []"
            :key="item.id"
            class="config-table-row"
            :class="{ 'is-current': selectedConfigId === item.id }"
          >
            <span>
              <strong>{{ item.name }}</strong>
              <small>{{ item.value_preview || '无预览值' }}</small>
            </span>
            <span>
              <strong>{{ item.title }}</strong>
              <small>{{ item.tips || '无说明' }}</small>
            </span>
            <span>{{ item.type_label }}</span>
            <span>{{ item.group_name }}</span>
            <span>
              <strong>{{ item.sort }}</strong>
              <small class="config-actions">
                <button type="button" class="text-button" @click="editConfig(item.id)">编辑</button>
                <button type="button" class="text-button danger-button" @click="removeConfig(item.id)">删除</button>
              </small>
            </span>
          </div>
        </div>
      </article>

      <article class="panel-card">
        <span class="eyebrow">配置编辑器</span>
        <form class="editor-form" @submit.prevent="submitConfig">
          <label>
            <span>配置分组</span>
            <select v-model.number="configForm.group">
              <option v-for="item in configMeta?.group_options || []" :key="item.value" :value="Number(item.value)">
                {{ item.label }}
              </option>
            </select>
          </label>
          <label>
            <span>配置类型</span>
            <select v-model="configForm.type">
              <option v-for="item in configMeta?.type_options || []" :key="item.value" :value="String(item.value)">
                {{ item.label }}
              </option>
            </select>
          </label>
          <label>
            <span>变量名</span>
            <input v-model.trim="configForm.name" placeholder="如 site_name" />
          </label>
          <label>
            <span>配置标题</span>
            <input v-model.trim="configForm.title" placeholder="请输入配置标题" />
          </label>
          <label>
            <span>默认值</span>
            <textarea v-model="configForm.value" rows="3" placeholder="请输入默认值" />
          </label>
          <label>
            <span>数据源</span>
            <select v-model.number="configForm.source">
              <option :value="0">本身数据</option>
              <option :value="1">字典数据</option>
            </select>
          </label>
          <label v-if="configForm.source === 1">
            <span>字典类型</span>
            <select v-model.number="configForm.dict_code">
              <option v-for="item in configMeta?.dictionary_options || []" :key="item.value" :value="Number(item.value)">
                {{ item.label }}
              </option>
            </select>
          </label>
          <label v-else>
            <span>配置信息</span>
            <textarea v-model="configForm.option_text" rows="4" placeholder="格式：配置值|配置名，一行一个" />
          </label>
          <label>
            <span>提示信息</span>
            <textarea v-model="configForm.tips" rows="3" placeholder="请输入提示信息" />
          </label>
          <label>
            <span>排序值</span>
            <input v-model.number="configForm.sort" type="number" />
          </label>
          <div class="form-actions">
            <button class="primary-button" type="submit" :disabled="savingConfig">
              {{ savingConfig ? '保存中...' : configForm.id ? '保存配置' : '创建配置' }}
            </button>
            <button class="ghost-button" type="button" @click="startCreateConfig">重置</button>
          </div>
        </form>
      </article>
    </section>

    <section class="panel-grid config-panel-grid">
      <article class="panel-card">
        <span class="eyebrow">配置分组概览</span>
        <div class="quick-links group-list">
          <div
            v-for="item in payload?.groups || []"
            :key="item.id"
            class="ghost-button group-card-button config-group-card"
            :class="{ 'is-current': selectedGroupId === item.id }"
          >
            <div class="config-group-main">
              <strong>{{ item.name }}</strong>
              <span>{{ item.status ? '正常' : '锁定' }} / {{ item.config_count }} 项配置</span>
              <small>{{ item.description || '暂无备注' }}</small>
            </div>
            <div class="config-group-actions">
              <button type="button" class="text-button" @click="editGroup(item.id)">编辑</button>
              <button type="button" class="text-button danger-button" @click="removeGroup(item.id)">删除</button>
            </div>
          </div>
        </div>
      </article>

      <article class="panel-card">
        <span class="eyebrow">分组编辑器</span>
        <form class="editor-form" @submit.prevent="submitGroup">
          <label>
            <span>分组名称</span>
            <input v-model.trim="groupForm.name" placeholder="请输入分组名称" />
          </label>
          <label>
            <span>备注</span>
            <textarea v-model="groupForm.description" rows="4" placeholder="请输入分组备注" />
          </label>
          <label>
            <span>排序值</span>
            <input v-model.number="groupForm.sort" type="number" />
          </label>
          <label>
            <span>状态</span>
            <select v-model.number="groupForm.status">
              <option :value="1">正常</option>
              <option :value="0">锁定</option>
            </select>
          </label>
          <div class="form-actions">
            <button class="primary-button" type="submit" :disabled="savingGroup">
              {{ savingGroup ? '保存中...' : groupForm.id ? '保存分组' : '创建分组' }}
            </button>
            <button class="ghost-button" type="button" @click="startCreateGroup">重置</button>
          </div>
        </form>
      </article>
    </section>
  </div>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue';
import {
  deleteSystemConfig,
  deleteSystemConfigGroup,
  fetchSystemConfigDetail,
  fetchSystemConfigGroupDetail,
  fetchSystemConfigMeta,
  fetchSystemConfigs,
  saveSystemConfig,
  saveSystemConfigGroup,
} from '@/api/admin';
import type { SystemConfigMetaPayload, SystemConfigOverviewPayload } from '@/types';

const payload = ref<SystemConfigOverviewPayload | null>(null);
const configMeta = ref<SystemConfigMetaPayload | null>(null);
const keyword = ref('');
const currentGroupId = ref(0);
const selectedConfigId = ref(0);
const selectedGroupId = ref(0);
const savingConfig = ref(false);
const savingGroup = ref(false);

const configForm = ref({
  id: 0,
  group: 0,
  type: 'text',
  name: '',
  title: '',
  value: '',
  tips: '',
  sort: 0,
  dict_code: 0,
  source: 0,
  option_text: '',
  settings: {} as Record<string, string>,
});

const groupForm = ref({
  id: 0,
  name: '',
  description: '',
  sort: 0,
  status: 1,
});

function getErrorMessage(error: unknown) {
  return error instanceof Error ? error.message : '请求失败';
}

async function ensureConfigMeta() {
  if (!configMeta.value) {
    configMeta.value = await fetchSystemConfigMeta();
  }
}

async function reload() {
  payload.value = await fetchSystemConfigs(currentGroupId.value, keyword.value);
  currentGroupId.value = payload.value.group_id;
}

async function switchGroup(groupId: number) {
  currentGroupId.value = groupId;
  await reload();
}

async function startCreateConfig() {
  await ensureConfigMeta();
  const template = configMeta.value?.detail_template;
  if (!template) {
    return;
  }

  selectedConfigId.value = 0;
  configForm.value = {
    ...template,
    group: currentGroupId.value || template.group,
    settings: { ...template.settings },
  };
}

async function editConfig(id: number) {
  const detail = await fetchSystemConfigDetail(id);
  selectedConfigId.value = id;
  configForm.value = {
    id: detail.id,
    group: detail.group,
    type: detail.type,
    name: detail.name,
    title: detail.title,
    value: detail.value || '',
    tips: detail.tips,
    sort: detail.sort,
    dict_code: detail.dict_code,
    source: detail.source,
    option_text: detail.option_text,
    settings: detail.settings || {},
  };
}

async function submitConfig() {
  savingConfig.value = true;
  try {
    const payloadData = {
      ...configForm.value,
      dict_code: configForm.value.source === 1 ? configForm.value.dict_code : 0,
      option_text: configForm.value.source === 0 ? configForm.value.option_text : '',
    };
    const result = await saveSystemConfig(payloadData);
    selectedConfigId.value = Number(result.id || 0);
    await reload();
    await editConfig(selectedConfigId.value);
  } catch (error) {
    window.alert(getErrorMessage(error));
  } finally {
    savingConfig.value = false;
  }
}

async function removeConfig(id: number) {
  if (!window.confirm('确认删除这个配置项？')) {
    return;
  }

  try {
    await deleteSystemConfig(id);
    if (selectedConfigId.value === id) {
      await startCreateConfig();
    }
    await reload();
  } catch (error) {
    window.alert(getErrorMessage(error));
  }
}

function startCreateGroup() {
  selectedGroupId.value = 0;
  groupForm.value = {
    id: 0,
    name: '',
    description: '',
    sort: 0,
    status: 1,
  };
}

async function editGroup(id: number) {
  const detail = await fetchSystemConfigGroupDetail(id);
  selectedGroupId.value = id;
  groupForm.value = {
    id: detail.id,
    name: detail.name,
    description: detail.description,
    sort: detail.sort,
    status: detail.status,
  };
}

async function submitGroup() {
  savingGroup.value = true;
  try {
    const result = await saveSystemConfigGroup(groupForm.value);
    selectedGroupId.value = Number(result.id || 0);
    await reload();
    if (selectedGroupId.value > 0) {
      await editGroup(selectedGroupId.value);
    }
  } catch (error) {
    window.alert(getErrorMessage(error));
  } finally {
    savingGroup.value = false;
  }
}

async function removeGroup(id: number) {
  if (!window.confirm('确认删除这个配置分组？')) {
    return;
  }

  try {
    await deleteSystemConfigGroup(id);
    if (selectedGroupId.value === id) {
      startCreateGroup();
    }
    if (currentGroupId.value === id) {
      currentGroupId.value = 0;
    }
    await reload();
  } catch (error) {
    window.alert(getErrorMessage(error));
  }
}

onMounted(async () => {
  await ensureConfigMeta();
  await reload();
  await startCreateConfig();
  startCreateGroup();
});
</script>
