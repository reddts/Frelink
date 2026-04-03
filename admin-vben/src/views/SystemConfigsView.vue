<template>
  <div class="dashboard">
    <section class="hero-card">
      <div>
        <span class="eyebrow">System / Configs</span>
        <h3>系统配置</h3>
        <p>配置页已进入第二阶段，动态配置、配置项和分组编辑继续向统一后台表单与动作基线收口。</p>
      </div>
      <div class="toolbar-row">
        <label class="search-inline">
          <span>搜索</span>
          <Input v-model.trim="keyword" placeholder="按变量名或标题筛选" @keydown.enter="reload" />
        </label>
        <div class="quick-links">
          <Button type="button" @click="startCreateConfig">新增配置</Button>
          <Button variant="outline" type="button" @click="startCreateGroup">新增分组</Button>
        </div>
      </div>
    </section>

    <section class="stats-grid">
      <article class="stat-card">
        <span class="eyebrow">Group</span>
        <strong>{{ currentGroupId || '-' }}</strong>
        <small>当前加载的配置分组</small>
      </article>
      <article class="stat-card">
        <span class="eyebrow">Configs</span>
        <strong>{{ payload?.list.length || 0 }}</strong>
        <small>当前筛选下的配置项数量</small>
      </article>
      <article class="stat-card">
        <span class="eyebrow">Schema</span>
        <strong>{{ configPage?.fields.length || 0 }}</strong>
        <small>当前分组动态配置字段数</small>
      </article>
    </section>

    <article class="panel-card">
      <span class="eyebrow">配置分组</span>
      <div class="tab-row">
        <Button
          v-for="item in payload?.group_tabs || []"
          :key="item.value"
          :variant="currentGroupId === item.value ? 'default' : 'outline'"
          size="sm"
          type="button"
          @click="switchGroup(item.value)"
        >
          {{ item.label }}
        </Button>
      </div>
    </article>

    <section class="panel-grid config-panel-grid">
      <article class="panel-card">
        <span class="eyebrow">动态配置页</span>
        <template v-if="configPage?.fields.length">
          <form class="editor-form" @submit.prevent="submitConfigPage">
            <template v-for="field in configPage.fields" :key="field.id">
              <label v-if="field.widget === 'text'">
                <span>{{ field.title }}</span>
                <Input :type="resolveInputType(field.type)" :value="getScalarValue(field.name)" @input="setScalarValue(field.name, $event)" />
                <small>{{ field.tips }}</small>
              </label>

              <label v-else-if="field.widget === 'number'">
                <span>{{ field.title }}</span>
                <Input :value="getScalarValue(field.name)" type="number" @input="setScalarValue(field.name, $event)" />
                <small>{{ field.tips }}</small>
              </label>

              <label v-else-if="field.widget === 'textarea'">
                <span>{{ field.title }}</span>
                <Textarea :value="getScalarValue(field.name)" rows="5" @input="setScalarValue(field.name, $event)" />
                <small>{{ field.tips }}</small>
              </label>

              <label v-else-if="field.widget === 'boolean'">
                <span>{{ field.title }}</span>
                <select :value="getScalarValue(field.name)" @change="setSelectValue(field.name, $event)">
                  <option value="1">是</option>
                  <option value="0">否</option>
                </select>
                <small>{{ field.tips }}</small>
              </label>

              <label v-else-if="field.widget === 'select'">
                <span>{{ field.title }}</span>
                <select :value="getScalarValue(field.name)" @change="setSelectValue(field.name, $event)">
                  <option value="">请选择</option>
                  <option v-for="option in field.options" :key="option.value" :value="option.value">
                    {{ option.label }}
                  </option>
                </select>
                <small>{{ field.tips }}</small>
              </label>

              <div v-else-if="field.widget === 'multi-select'" class="editor-form-group">
                <span>{{ field.title }}</span>
                <div class="check-grid">
                  <label v-for="option in field.options" :key="option.value" class="check-item">
                    <input
                      :checked="includesArrayValue(configPageValues[field.name], option.value)"
                      type="checkbox"
                      @change="toggleConfigPageValue(field.name, option.value, $event)"
                    />
                    <span>{{ option.label }}</span>
                  </label>
                </div>
                <small>{{ field.tips }}</small>
              </div>

              <div v-else-if="field.widget === 'list-text'" class="editor-form-group">
                <span>{{ field.title }}</span>
                <div class="kv-list">
                  <div
                    v-for="(item, index) in getStringList(field.name)"
                    :key="`${field.name}-${index}`"
                    class="kv-row"
                  >
                    <Input v-model="getStringList(field.name)[index]" type="text" />
                    <Button variant="outline" type="button" @click="removeListItem(field.name, index)">移除</Button>
                  </div>
                </div>
                <Button variant="outline" type="button" @click="appendListItem(field.name)">追加一项</Button>
                <small>{{ field.tips }}</small>
              </div>

              <div v-else-if="field.widget === 'key-value'" class="editor-form-group">
                <span>{{ field.title }}</span>
                <div class="kv-list">
                  <div
                    v-for="(item, index) in getPairList(field.name)"
                    :key="`${field.name}-${index}`"
                    class="kv-row kv-pair"
                  >
                    <Input v-model="item.key" placeholder="键名" type="text" />
                    <Input v-model="item.value" placeholder="键值" type="text" />
                    <Button variant="outline" type="button" @click="removePairItem(field.name, index)">移除</Button>
                  </div>
                </div>
                <Button variant="outline" type="button" @click="appendPairItem(field.name)">追加一项</Button>
                <small>{{ field.tips }}</small>
              </div>
            </template>

            <div class="form-actions">
              <Button type="submit" :disabled="savingConfigPage">
                {{ savingConfigPage ? '保存中...' : '保存当前分组配置' }}
              </Button>
              <Button variant="outline" type="button" @click="handleReloadConfigPage">重载当前分组</Button>
            </div>
          </form>
        </template>
        <p v-else>当前分组暂无动态配置项。</p>
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
            <Input v-model.trim="configForm.name" placeholder="如 site_name" />
          </label>
          <label>
            <span>配置标题</span>
            <Input v-model.trim="configForm.title" placeholder="请输入配置标题" />
          </label>
          <label>
            <span>默认值</span>
            <Textarea v-model="configForm.value" rows="3" placeholder="请输入默认值" />
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
            <Textarea v-model="configForm.option_text" rows="4" placeholder="格式：配置值|配置名，一行一个" />
          </label>
          <label>
            <span>提示信息</span>
            <Textarea v-model="configForm.tips" rows="3" placeholder="请输入提示信息" />
          </label>
          <label>
            <span>排序值</span>
            <Input v-model.number="configForm.sort" type="number" />
          </label>
          <div class="form-actions">
            <Button type="submit" :disabled="savingConfig">
              {{ savingConfig ? '保存中...' : configForm.id ? '保存配置' : '创建配置' }}
            </Button>
            <Button variant="outline" type="button" @click="startCreateConfig">重置</Button>
          </div>
        </form>
      </article>
    </section>

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
        <span class="eyebrow">分组编辑器</span>
        <form class="editor-form" @submit.prevent="submitGroup">
          <label>
            <span>分组名称</span>
            <Input v-model.trim="groupForm.name" placeholder="请输入分组名称" />
          </label>
          <label>
            <span>备注</span>
            <Textarea v-model="groupForm.description" rows="4" placeholder="请输入分组备注" />
          </label>
          <label>
            <span>排序值</span>
            <Input v-model.number="groupForm.sort" type="number" />
          </label>
          <label>
            <span>状态</span>
            <select v-model.number="groupForm.status">
              <option :value="1">正常</option>
              <option :value="0">锁定</option>
            </select>
          </label>
          <div class="form-actions">
            <Button type="submit" :disabled="savingGroup">
              {{ savingGroup ? '保存中...' : groupForm.id ? '保存分组' : '创建分组' }}
            </Button>
            <Button variant="outline" type="button" @click="startCreateGroup">重置</Button>
          </div>
        </form>

        <div class="quick-links group-list group-list-panel">
          <Button
            v-for="item in payload?.groups || []"
            :key="item.id"
            tag="div"
            class="group-card-button config-group-card"
            :variant="selectedGroupId === item.id ? 'default' : 'outline'"
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
          </Button>
        </div>
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
  fetchSystemConfigPage,
  fetchSystemConfigs,
  saveSystemConfig,
  saveSystemConfigGroup,
  saveSystemConfigPage,
} from '@/api/admin';
import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';
import Textarea from '@/components/ui/textarea/Textarea.vue';
import type {
  SystemConfigMetaPayload,
  SystemConfigOverviewPayload,
  SystemConfigPageField,
  SystemConfigPagePayload,
} from '@/types';

type ConfigPageValue = string | string[] | Array<{ key: string; value: string }>;

const payload = ref<SystemConfigOverviewPayload | null>(null);
const configMeta = ref<SystemConfigMetaPayload | null>(null);
const configPage = ref<SystemConfigPagePayload | null>(null);
const configPageValues = ref<Record<string, ConfigPageValue>>({});
const keyword = ref('');
const currentGroupId = ref(0);
const selectedConfigId = ref(0);
const selectedGroupId = ref(0);
const savingConfig = ref(false);
const savingGroup = ref(false);
const savingConfigPage = ref(false);

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

function resolveInputType(type: string) {
  if (type === 'password') {
    return 'password';
  }
  if (type === 'color') {
    return 'color';
  }
  if (type === 'date') {
    return 'date';
  }
  if (type === 'time') {
    return 'time';
  }
  if (type === 'datetime') {
    return 'datetime-local';
  }
  return 'text';
}

function normalizePageFieldValue(field: SystemConfigPageField): ConfigPageValue {
  if (field.widget === 'multi-select' || field.widget === 'list-text') {
    return Array.isArray(field.value) ? field.value.filter((item): item is string => typeof item === 'string') : [];
  }
  if (field.widget === 'key-value') {
    return Array.isArray(field.value)
      ? field.value
          .filter((item): item is { key: string; value: string } => typeof item === 'object' && item !== null && 'key' in item && 'value' in item)
          .map((item) => ({ key: item.key, value: item.value }))
      : [];
  }
  return typeof field.value === 'string' ? field.value : '';
}

function getScalarValue(name: string): string {
  const value = configPageValues.value[name];
  return typeof value === 'string' ? value : '';
}

function setScalarValue(name: string, event: Event) {
  configPageValues.value[name] = (event.target as HTMLInputElement | HTMLTextAreaElement).value;
}

function setSelectValue(name: string, event: Event) {
  configPageValues.value[name] = (event.target as HTMLSelectElement).value;
}

async function ensureConfigMeta() {
  if (!configMeta.value) {
    configMeta.value = await fetchSystemConfigMeta();
  }
}

async function reloadConfigPage(groupId = currentGroupId.value) {
  configPage.value = await fetchSystemConfigPage(groupId);
  const nextValues: Record<string, ConfigPageValue> = {};
  for (const field of configPage.value.fields) {
    nextValues[field.name] = normalizePageFieldValue(field);
  }
  configPageValues.value = nextValues;
}

async function reload() {
  payload.value = await fetchSystemConfigs(currentGroupId.value, keyword.value);
  currentGroupId.value = payload.value.group_id;
  await reloadConfigPage(currentGroupId.value);
}

async function switchGroup(groupId: number) {
  currentGroupId.value = groupId;
  await reload();
}

function includesArrayValue(value: ConfigPageValue | undefined, target: string) {
  return Array.isArray(value) && value.some((item) => typeof item === 'string' && item === target);
}

function toggleConfigPageValue(name: string, optionValue: string, event: Event) {
  const checked = (event.target as HTMLInputElement).checked;
  const current = Array.isArray(configPageValues.value[name]) ? [...(configPageValues.value[name] as string[])] : [];
  if (checked) {
    if (!current.includes(optionValue)) {
      current.push(optionValue);
    }
  } else {
    const index = current.indexOf(optionValue);
    if (index >= 0) {
      current.splice(index, 1);
    }
  }
  configPageValues.value[name] = current;
}

function handleReloadConfigPage() {
  return reloadConfigPage();
}

function getStringList(name: string): string[] {
  if (!Array.isArray(configPageValues.value[name])) {
    configPageValues.value[name] = [];
  }
  return configPageValues.value[name] as string[];
}

function appendListItem(name: string) {
  getStringList(name).push('');
}

function removeListItem(name: string, index: number) {
  getStringList(name).splice(index, 1);
}

function getPairList(name: string): Array<{ key: string; value: string }> {
  if (!Array.isArray(configPageValues.value[name])) {
    configPageValues.value[name] = [];
  }
  return configPageValues.value[name] as Array<{ key: string; value: string }>;
}

function appendPairItem(name: string) {
  getPairList(name).push({ key: '', value: '' });
}

function removePairItem(name: string, index: number) {
  getPairList(name).splice(index, 1);
}

async function submitConfigPage() {
  if (!configPage.value?.group_id) {
    return;
  }

  savingConfigPage.value = true;
  try {
    await saveSystemConfigPage(configPage.value.group_id, configPageValues.value);
    await reload();
  } catch (error) {
    window.alert(getErrorMessage(error));
  } finally {
    savingConfigPage.value = false;
  }
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
