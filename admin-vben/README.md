# admin-vben

Frelink 新管理端业务子工程，当前完成 `M1` 基线接入：

- `Vue 3 + TypeScript + Vite`
- 登录态获取
- 当前管理员信息
- 动态菜单读取
- 基础布局与仪表盘壳层
- 旧后台页面迁移占位路由
- 后台请求统一走独立 `adminapi` 体系，不复用前台开放 `api`

## 本地开发

```bash
pnpm install
pnpm dev
```

## 生产构建

```bash
pnpm install
pnpm build
```

默认输出目录为 `public/admin-vben/`，构建后可直接通过 `/admin-vben/` 访问。
