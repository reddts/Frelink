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
