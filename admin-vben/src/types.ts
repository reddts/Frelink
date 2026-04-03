export interface ApiEnvelope<T> {
  code: number;
  msg: string;
  data: T;
  request_id?: string;
  error_code?: string;
}

export interface AdminProfile {
  uid: number;
  user_name: string;
  nick_name: string;
  email: string;
  mobile: string;
  avatar: string;
  group_id: number;
  group_name: string;
  is_super_admin: boolean;
  permission: Record<string, string>;
}

export interface AdminMenuItem {
  id: number;
  pid: number;
  title: string;
  icon: string;
  rule_name: string;
  path: string;
  legacy_url: string;
  migration_status: string;
  children: AdminMenuItem[];
}

export interface AdminBootstrapPayload {
  user: AdminProfile;
  permissions: string[];
  menus: AdminMenuItem[];
  home: {
    title: string;
    path: string;
  };
}

export interface AdminDashboardStat {
  key: string;
  label: string;
  value: number;
}

export interface AdminDashboardPayload {
  title: string;
  subtitle: string;
  stats: AdminDashboardStat[];
  quick_links: Array<{
    title: string;
    path: string;
  }>;
}

export interface MenuGroupTab {
  label: string;
  value: string;
}

export interface SystemAuthNode {
  id: number;
  pid: number;
  icon: string;
  name: string;
  title: string;
  param: string;
  auth_open: number;
  status: number;
  sort: number;
  menu: number;
  type: number;
  children: SystemAuthNode[];
}

export interface SystemAuthListPayload {
  list: SystemAuthNode[];
  parent_options: SelectOption[];
}

export interface SystemAuthDetail {
  id: number;
  pid: number;
  icon: string;
  name: string;
  title: string;
  param: string;
  auth_open: number;
  status: number;
  sort: number;
  menu: number;
  type: number;
}

export interface SystemAuthMetaPayload {
  parent_options: SelectOption[];
  detail_template: SystemAuthDetail;
}

export interface SystemMenuNode {
  id: number;
  pid: number;
  group: string;
  title: string;
  icon: string;
  name: string;
  type: number;
  is_home: number;
  param: string;
  auth_open: number;
  status: number;
  sort: number;
  children: SystemMenuNode[];
}

export interface SystemMenuListPayload {
  group: string;
  groups: MenuGroupTab[];
  list: SystemMenuNode[];
  parent_options: Array<{
    label: string;
    value: number;
  }>;
}

export interface GroupRuleTreeNode {
  id: number;
  pid: number;
  text: string;
  state?: {
    opened?: boolean;
    selected?: boolean;
  };
  children?: GroupRuleTreeNode[];
}

export interface SystemGroupItem {
  id: number;
  title: string;
  status: number;
  system: number;
  rules: string;
  rule_count: number;
}

export interface SystemGroupListPayload {
  keyword: string;
  list: SystemGroupItem[];
}

export interface SystemGroupMetaPayload {
  rule_tree: GroupRuleTreeNode[];
}

export interface SystemGroupDetail extends SystemGroupItem {
  rule_ids: number[];
  rule_tree: GroupRuleTreeNode[];
}

export interface ConfigGroupTab {
  label: string;
  value: number;
}

export interface SystemConfigItem {
  id: number;
  group: number;
  group_name: string;
  name: string;
  title: string;
  type: string;
  type_label: string;
  tips: string;
  value_preview: string;
  sort: number;
}

export interface SystemConfigGroupItem {
  id: number;
  name: string;
  description: string;
  status: number;
  sort: number;
  config_count: number;
}

export interface SystemConfigOverviewPayload {
  group_id: number;
  group_tabs: ConfigGroupTab[];
  list: SystemConfigItem[];
  groups: SystemConfigGroupItem[];
}

export interface SelectOption {
  label: string;
  value: number | string;
}

export interface SystemConfigDetail extends SystemConfigItem {
  value: string;
  dict_code: number;
  source: number;
  option_text: string;
  settings: Record<string, string>;
}

export interface SystemConfigMetaPayload {
  group_options: SelectOption[];
  type_options: SelectOption[];
  dictionary_options: SelectOption[];
  detail_template: {
    id: number;
    group: number;
    type: string;
    name: string;
    title: string;
    value: string;
    tips: string;
    sort: number;
    dict_code: number;
    source: number;
    option_text: string;
    settings: Record<string, string>;
  };
}

export interface SystemConfigPageFieldOption {
  label: string;
  value: string;
}

export interface SystemConfigPageField {
  id: number;
  name: string;
  title: string;
  type: string;
  widget: string;
  multiple: boolean;
  tips: string;
  options: SystemConfigPageFieldOption[];
  value: string | string[] | Array<{ key: string; value: string }>;
}

export interface SystemConfigPagePayload {
  group_id: number;
  group_tabs: ConfigGroupTab[];
  fields: SystemConfigPageField[];
}

export interface SystemUserItem {
  uid: number;
  user_name: string;
  nick_name: string;
  avatar: string;
  email: string;
  mobile: string;
  group_id: number;
  group_name: string;
  reputation_group_id: number;
  reputation_group_name: string;
  integral_group_id: number;
  integral_group_name: string;
  status: number;
  forbidden_ip: number;
  status_label: string;
  create_time_text: string;
  last_login_time_text: string;
  last_login_ip: string;
  actions: string[];
}

export interface SystemUserOverviewPayload {
  status: number;
  forbidden_ip: number;
  tabs: Array<{
    label: string;
    value: number;
    forbidden_ip: number;
  }>;
  list: SystemUserItem[];
  meta: {
    status_options: SelectOption[];
    sex_options: SelectOption[];
    verified_options: SelectOption[];
    group_options: SelectOption[];
    reputation_group_options: SelectOption[];
    integral_group_options: SelectOption[];
  };
}

export interface SystemUserDetail extends SystemUserItem {
  signature: string;
  verified: string;
  sex: number;
  birthday_text: string;
  meta: SystemUserOverviewPayload['meta'];
}

export interface SystemUserIntegralLogItem {
  id?: number;
  action_type: string;
  record_id: number;
  record_db: string;
  integral: number;
  balance: number;
  remark: string;
  create_time_text: string;
}

export interface SystemUserIntegralLogPayload {
  uid: number;
  list: SystemUserIntegralLogItem[];
  pagination: {
    page: number;
    per_page: number;
  };
  page_html: string;
}

export interface DetailFieldItem {
  label: string;
  value: string;
}

export interface ContentStatusTab {
  label: string;
  value: number;
}

export interface ContentArticleItem {
  id: number;
  uid: number;
  title: string;
  user_name: string;
  url_token: string;
  preview_url: string;
  edit_url: string;
  comment_count: number;
  agree_count: number;
  view_count: number;
  set_top: number;
  is_recommend: number;
  flags: string[];
  create_time_text: string;
  update_time_text: string;
}

export interface ContentArticleDetail extends ContentArticleItem {
  message: string;
  seo_title: string;
  seo_keywords: string;
  seo_description: string;
  status: number;
  status_label: string;
  detail_fields: DetailFieldItem[];
}

export interface ContentArticleOverviewPayload {
  status: number;
  tabs: ContentStatusTab[];
  list: ContentArticleItem[];
}

export interface ContentQuestionItem {
  id: number;
  uid: number;
  title: string;
  user_name: string;
  url_token: string;
  preview_url: string;
  edit_url: string;
  answer_count: number;
  comment_count: number;
  agree_count: number;
  view_count: number;
  set_top: number;
  is_recommend: number;
  flags: string[];
  create_time_text: string;
  update_time_text: string;
}

export interface ContentQuestionDetail extends ContentQuestionItem {
  detail: string;
  seo_title: string;
  seo_keywords: string;
  seo_description: string;
  status: number;
  status_label: string;
  detail_fields: DetailFieldItem[];
}

export interface ContentQuestionOverviewPayload {
  status: number;
  tabs: ContentStatusTab[];
  list: ContentQuestionItem[];
}

export interface ContentAnswerItem {
  id: number;
  uid: number;
  question_id: number;
  title: string;
  nick_name: string;
  url_token: string;
  preview_url: string;
  content_preview: string;
  against_count: number;
  agree_count: number;
  comment_count: number;
  thanks_count: number;
  is_best: number;
  is_anonymous: number;
  status: number;
  flags: string[];
  create_time_text: string;
  update_time_text: string;
}

export interface ContentAnswerDetail {
  id: number;
  uid: number;
  question_id: number;
  question_title: string;
  nick_name: string;
  url_token: string;
  content: string;
  against_count: number;
  agree_count: number;
  comment_count: number;
  thanks_count: number;
  is_best: number;
  is_anonymous: number;
  status: number;
  status_label: string;
  best_label: string;
  preview_url: string;
  create_time_text: string;
  update_time_text: string;
  flags: string[];
  detail_fields: DetailFieldItem[];
}

export interface ContentAnswerOverviewPayload {
  status: number;
  keyword: string;
  tabs: ContentStatusTab[];
  list: ContentAnswerItem[];
}

export interface ContentApprovalTypeTab {
  label: string;
  value: string;
}

export interface ContentApprovalItem {
  id: number;
  uid: number;
  user_name: string;
  url_token: string;
  type: string;
  type_label: string;
  status: number;
  item_id: number;
  reason: string;
  is_agent: number;
  summary: string;
  target_url: string;
  create_time_text: string;
}

export interface ContentApprovalDetail {
  id: number;
  uid: number;
  user_name: string;
  url_token: string;
  type: string;
  type_label: string;
  status: number;
  reason: string;
  item_id: number;
  is_agent: number;
  create_time_text: string;
  summary: string;
  target_url: string;
  subject_title: string;
  preview_fields: DetailFieldItem[];
  payload: Record<string, any>;
  payload_json: string;
}

export interface ContentApprovalOverviewPayload {
  status: number;
  type: string;
  is_agent: string;
  status_tabs: ContentStatusTab[];
  type_tabs: ContentApprovalTypeTab[];
  list: ContentApprovalItem[];
}
