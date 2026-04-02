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
