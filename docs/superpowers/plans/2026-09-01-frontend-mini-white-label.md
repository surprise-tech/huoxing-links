# 前端、落地页与小程序白标稳定化 Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** 在不包含支付和部署动作的前提下，把 Vue 3 管理端、Laravel 同源分享壳与 DCloud Vue2 uni-app 收敛到稳定 API 契约，完成六类链接管理、可恢复启动、白标配置、受控跳转和微信落地页构建验收。

**Architecture:** `serve/` 是 API 事实来源，管理端通过一套类型化 `http` 客户端消费 `/api`，`BrandConfig` 由 `/config` 注入并贯穿登录、布局、分享页和小程序。链接列表/详情/启停/删除/分享使用统一契约；`jump` 只承载 iframe 并执行严格 `postMessage` 校验，`news.vue` 只提交 `code` 与签名 `visitor_token`。

**Tech Stack:** Vue 3.5 + TypeScript 5.9 + Vite 7 + Element Plus 2.14；Vitest 4 + Playwright E2E；原生安全 JavaScript；DCloud Vue2 CLI 固定版本 `2.0.2-5020420260813001`，微信小程序 `mp-weixin`。

**Spec:** `docs/superpowers/specs/2026-09-01-full-function-stabilization-design.md`

## Global Constraints

- 本计划主要覆盖 `admin/`、`jump/`、`mini_programs/`，并只在前端契约需要时修改对应 Laravel 配置/支付禁用端点与测试 Seeder；DNS、Nginx、HTTPS、PHP-FPM、生产 MySQL/Redis、队列、Scheduler、服务器重启和发布回滚由部署计划负责。
- 管理端、分享页、小程序只使用相对 `/api` 或部署注入的 `${PUBLIC_ORIGIN}/api`；不得把 `PUBLIC_ORIGIN` 写死进源码或构建产物。
- 第一阶段完全隐藏或移除 `link-renewal`、`agent-packages`、`agent-level`、`commission-logs`、`payments`、`buy-vip`、支付配置和支付回调入口；不保留可点击但返回 404 的支付相邻入口。
- `/config` 失败时应用仍须挂载并显示可恢复错误页；不得通过 `ApiGetSet().then(...createApp...)` 让页面白屏。
- 登录状态以真实 token 字符串判断；退出只删除本应用使用的 `token` 键（local/session），不得调用 `localStorage.clear()`。
- 链接 API 契约固定为 `POST /links`、`GET /links/{id}`、`PUT /links/{id}`、`PATCH /links/{id}/status`、`DELETE /links/{id}`；详情必须含 `manual_status`、`health_status`、`effective_status` 和非空 canonical `share_link`。
- 六类 `allow_type` 键必须齐全：`MINI_PROGRAM`、`KING_DOC`、`CLI_QR`、`WORK_WECHAT`、`LANDING_MINI`、`QR_QQ`；前端不重命名后端枚举。
- `BrandConfig` 至少包含 `productName`、`shortName`、`logo`、`favicon`、`pageTitle`、`customerServiceUrl`、`copyrightOwner`、`icp`、`helpCenterUrl`；正式品牌未确定时使用非公开开发占位值，绝不能使用“火星快链”。
- Laravel `jump/shell.blade.php` 只接受预期 iframe 的 `event.source` 与当前允许 origin；目标地址只允许协议和域名策略校验通过的 `https://` 或处理器产生的 `weixin://`。
- `news.vue` 只读取 `code`、`visitor_token`，请求 `${PUBLIC_ORIGIN}/api/link-show-qr/{code}`；原始 `visitor_id` 不得进入 URL。
- uni-app 使用 DCloud Vue2 CLI `2.0.2-5020420260813001`，所有名称以 `@dcloudio/` 开头的依赖均必须精确 pin 到该版本；构建命令必须是 `cross-env NODE_ENV=production UNI_PLATFORM=mp-weixin vue-cli-service uni-build`；官方产物固定为 `mini_programs/dist/build/mp-weixin`，必须包含固定页面 `pages/views/tools/news`，并可被微信开发者工具导入。
- 管理端测试依赖精确固定为 `vitest 4.1.10`、`@vue/test-utils 2.5.0`、`jsdom 29.0.1`、`@playwright/test 1.62.1`；不引入 Jest。
- 不运行会自动改写源码的 lint 命令作为只读检查；使用 `pnpm --dir admin type-check`、`pnpm --dir admin build-only`、Playwright 和明确的扫描脚本。
- 首次引入上述管理端依赖时先运行 `pnpm --dir admin install` 并提交 `admin/pnpm-lock.yaml`；该 lockfile 提交后所有后续安装均使用 `pnpm --dir admin install --frozen-lockfile`。
- 首次建立 uni-app CLI 依赖时运行 `pnpm --dir mini_programs install` 并提交 `mini_programs/pnpm-lock.yaml`；此后所有小程序安装均使用 `--frozen-lockfile`。
- 基线 `pnpm audit --prod` 已记录 56 个生产依赖漏洞（20 high、1 critical）；Task 1 升级后 high/critical 必须归零，不能用 audit ignore 或仅构建成功代替安全 gate。
- 每个任务先写红测试再实现绿测试；每个任务末尾由执行者只暂存本任务文件并提交一个范围清晰的 commit。
- `admin/dist` 保持仓库既有的预构建部署产物身份，但只能在 Task 8 用锁定依赖完整重建并提交；任何任务不得手工修改 dist 文件。

## 文件地图与边界

| 文件 | 职责 |
| --- | --- |
| `admin/src/models/brand.ts` | `BrandConfig`、配置错误和掩码字段的类型 |
| `admin/src/models/api.ts` | API 错误、分页、链接六类状态和统一详情返回类型 |
| `admin/src/api/*.ts` | 仅声明最终 API 清单中的调用，不保留支付相邻 wrapper |
| `admin/src/utils/http.ts` | `/api` base URL、token 注入、稳定错误展示和 401 收敛 |
| `admin/src/main.ts`、`admin/src/App.vue`、`admin/src/stores/modules/config.ts` | 可恢复启动及全局品牌注入 |
| `admin/src/stores/modules/user.ts`、`admin/src/router/index.ts` | 登录、退出、权限和动态路由 |
| `admin/src/router/apiRouter.ts`、`admin/src/layout/components/Logo.vue`、`admin/src/layout/components/Navbar/*` | 非支付导航、品牌标题、客服/帮助入口 |
| `admin/src/views/link/addLink/**`、`admin/src/hooks/curd.ts` | 六类链接表单、详情、启停、删除、分享和回读 |
| `admin/src/views/library/**`、`admin/src/views/super/{notices,domains,users,vipSetMeal}/**`、`admin/src/views/link/fallApp/**` | 素材、公告、域名、小程序池、用户和权益管理 |
| `serve/resources/views/jump/shell.blade.php` | `/?code=` 的同源 iframe 分享壳和安全 `postMessage`；替代独立 `jump/a_dev.html` |
| `mini_programs/pages/views/tools/news.vue`、`mini_programs/pages.json`、`mini_programs/utils/request.js`、`mini_programs/manifest.json` | 二维码展示页、路径注册、API 基地址及 AppID 私有配置契约 |
| `admin/e2e/**`、`admin/playwright.config.ts`、`mini_programs/scripts/**` | 浏览器验收、API 清单/路由对比、白标残留和小程序产物契约 |

## 最终 API 清单（实现与 E2E 必须逐项对齐）

保留并验证：`GET /config`；`GET /captcha/image`；`POST /captcha/sms`；`POST /register`；`POST /login`；`POST /reset-password`；受保护的 `POST /change-password`、`GET /userinfo`；`GET/POST/PUT/DELETE /materials-cate`；`GET/POST /material-upload`；`POST /material-upload-del`；`POST /material-base64`；`GET/POST/PUT/DELETE /materials`；`GET /home`；`GET /notice`；管理员 `GET/POST/PUT/DELETE /notices` 与 `PUT /notice`；`GET/POST/PUT/DELETE /domains`（列表可读）；`GET/POST/PUT/DELETE /min-program`；`GET /min-programs`；`GET/POST/PUT/PATCH/DELETE /links`；`GET /link-list`；`GET /agent-invite`；管理员 `GET/POST/PUT/DELETE /vip-packages`、`GET config/{type}`、`PUT /config`、`GET/POST/PUT /users`、`GET /agent-tree`；公开 `GET /link-target/{code}`、`GET /link-show-qr/{code}`。裁剪组件当前调用 `/material-base64`，因此该接口是固定范围，不是可选项。

移除/隐藏且不由管理端调用：`POST /link-renewal`、`GET/POST/PUT/DELETE /agent-packages`、`GET /agent-level`、`GET /commission-logs`、`GET /payments`、`POST /buy-vip`、支付配置、支付回调和任何价格/购买入口。第一阶段选择不注册这些支付路由，旧请求固定得到 404 且不发生任何状态变更；本计划不新增支付调用。

### Task 1: 建立 API/类型清单和可测试 HTTP 边界

**Files:**
- Create: `admin/src/models/api.ts`
- Create: `admin/src/models/brand.ts`
- Create: `admin/src/api/links.ts`
- Create: `admin/src/api/admin.ts`
- Create: `admin/src/api/__tests__/contract.test.ts`
- Modify: `admin/src/utils/http.ts`
- Modify: `admin/src/api/comment.ts`, `admin/src/api/user.ts`, `admin/src/api/app.ts`, `admin/src/api/domain.ts`, `admin/src/api/home.ts`, `admin/src/api/super.ts`
- Create: `admin/scripts/check-api-contract.mjs`
- Modify: `admin/index.html`
- Modify: `admin/package.json`, `admin/pnpm-lock.yaml`, `admin/vite.config.ts`, `admin/tsconfig.app.json`, `admin/tsconfig.node.json`
- Delete: `admin/yarn.lock`（统一使用 pnpm，避免双锁文件漂移）
- Modify: `admin/pnpm-lock.yaml`

**Interfaces:**
- `type AllowType = 'MINI_PROGRAM' | 'KING_DOC' | 'CLI_QR' | 'WORK_WECHAT' | 'LANDING_MINI' | 'QR_QQ'`
- `interface LinkRecord { id: number; type: number; code: string; user_id: number; manual_status: boolean; health_status: boolean; effective_status: boolean; share_link: string; config: Record<string, unknown>; }`
- `interface ApiError { message: string; error_code: string; errors?: Record<string, string[]> }`
- `getLinks(params: LinkQuery): Promise<Page<LinkRecord>>`, `getLink(id: number): Promise<LinkRecord>`, `createLink<T extends LinkPayload>(payload: T): Promise<LinkRecord>`, `updateLink(id: number, payload: LinkPayload): Promise<LinkRecord>`, `setLinkStatus(id: number, manualStatus: boolean): Promise<LinkRecord>`, `deleteLink(id: number): Promise<void>`
- `formatApiError(error: unknown): string` must map 422 field errors, 403 permission errors, timeout, network and `error_code` without exposing response stacks or credentials.
- `check-api-contract.mjs` reads a generated frontend request set and `php artisan route:list --json` output, failing on an unexcluded 404 candidate and failing if an excluded payment path is referenced.

- [ ] **Step 1: Write the failing contract tests.**

```ts
import { describe, expect, it, vi } from 'vitest'
import { readFileSync } from 'node:fs'
import { setLinkStatus } from '@/api/links'
import http from '@/utils/http'

vi.mock('@/utils/http', () => ({ default: { patch: vi.fn() } }))

it('uses the status route and preserves the response contract', async () => {
  vi.mocked(http.patch).mockResolvedValue({ id: 7, manual_status: false, effective_status: false })
  await expect(setLinkStatus(7, false)).resolves.toMatchObject({ id: 7, manual_status: false })
  expect(http.patch).toHaveBeenCalledWith('/links/7/status', { manual_status: false })
})

it('rejects an unlisted frontend endpoint', async () => {
  const result = await import('../../scripts/check-api-contract.mjs')
  expect(result.check(['/payments'], ['/payments'])).toEqual({ ok: false, reason: 'PAYMENT_ENDPOINT_REFERENCED' })
})

it('contains no tracked map credential bootstrap', () => {
  const html = readFileSync(new URL('../../../index.html', import.meta.url), 'utf8')
  expect(html).not.toMatch(/_AMapSecurityConfig|securityJsCode/)
})
```

Add the test harness declarations to `admin/package.json` in this step so the first install resolves the exact approved versions:

```json
{
  "engines": { "node": ">=24.0.0" },
  "dependencies": {
    "@element-plus/icons-vue": "2.3.2",
    "@vueuse/core": "14.4.0",
    "axios": "1.20.0",
    "echarts": "6.1.0",
    "element-plus": "2.14.5",
    "pinia": "3.0.4",
    "pinia-plugin-persistedstate": "4.7.1",
    "qrcode.vue": "3.10.0",
    "qs": "6.16.0",
    "sortablejs": "1.15.7",
    "vue": "3.5.42"
  },
  "devDependencies": {
    "@playwright/test": "1.62.1",
    "@vue/test-utils": "2.5.0",
    "@vitejs/plugin-vue": "6.0.8",
    "jsdom": "29.0.1",
    "sass": "1.103.1",
    "typescript": "5.9.3",
    "vite": "7.3.6",
    "vitest": "4.1.10",
    "vue-tsc": "3.3.11"
  },
  "scripts": {
    "test:unit": "vitest run"
  },
  "pnpm": {
    "overrides": {
      "@babel/runtime": "7.29.7",
      "follow-redirects": "1.16.0",
      "form-data": "4.0.6",
      "lodash": "4.18.1",
      "lodash-es": "4.18.1",
      "nanoid@^3.0.0": "3.3.18",
      "postcss": "8.5.26",
      "prismjs": "1.30.0"
    }
  }
}
```

- [ ] **Step 2: Resolve the pinned test dependencies and run red tests.**

Run the current lock audit first: `pnpm --dir admin audit --prod --audit-level high`

Expected: FAIL on the baseline lock with the recorded critical/high advisories; save the machine-readable package/severity summary in the task notes without copying tokens or local paths.

Run next (this is the only non-frozen admin install; it must update the lockfile): `pnpm --dir admin install`

Expected: PASS for dependency resolution and `admin/pnpm-lock.yaml` records `vitest 4.1.10`, `@vue/test-utils 2.5.0`, `jsdom 29.0.1` and `@playwright/test 1.62.1`.

Run the upgraded production audit and compile gate: `pnpm --dir admin audit --prod --audit-level high && pnpm --dir admin type-check && pnpm --dir admin build-only`

Expected: no unhandled high/critical production advisory; type-check/build PASS after adapting Vite 7, Vue 3.5, Pinia 3 and ECharts 6 API/type changes. Record any lower-severity residual advisory and its non-exploitability/upgrade path in `docs/compliance/license-inventory.md` rather than suppressing it silently.

Then run: `pnpm --dir admin exec vitest run src/api/__tests__/contract.test.ts`

Expected: FAIL because the pinned `vitest` runner is installed but `http.patch`, `setLinkStatus` and the contract checker are not defined yet.

- [ ] **Step 3: Implement the minimal typed boundary.**

```ts
// admin/src/api/links.ts
import http from '@/utils/http'
import type { LinkPayload, LinkQuery, LinkRecord, Page } from '@/models/api'
export const getLinks = (params: LinkQuery) => http.get<Page<LinkRecord>>('/links', { params })
export const getLink = (id: number) => http.get<LinkRecord>(`/links/${id}`)
export const createLink = <T extends LinkPayload>(payload: T) => http.post<LinkRecord, T>('/links', payload)
export const updateLink = (id: number, payload: LinkPayload) => http.put<LinkRecord, LinkPayload>(`/links/${id}`, payload)
export const setLinkStatus = (id: number, manualStatus: boolean) => http.patch<LinkRecord, { manual_status: boolean }>(`/links/${id}/status`, { manual_status: manualStatus })
export const deleteLink = (id: number) => http.delete<void>(`/links/${id}`)
```

Extend `http` with `patch`, guard `error.response` before reading `status`, and display `formatApiError(error)` for 422/403/408/5xx/network failures. Remove `ApiRenewalLink`, `ApiPayVip`, `ApiAgentList`, `ApiLevelList` and `ApiPayVip` imports/callers rather than leaving dead endpoint wrappers. Remove the unused `@amap/amap-jsapi-loader` production dependency and delete the trailing `window._AMapSecurityConfig.securityJsCode` block from `admin/index.html`; do not replace it with another tracked key. Keep the `test:unit` script and add a deterministic checker that parses `admin/src/api` plus `admin/src/hooks/curd.ts` request literals.

- [ ] **Step 4: Run green tests and the route comparison.**

Run: `pnpm --dir admin exec vitest run src/api/__tests__/contract.test.ts` and `node admin/scripts/check-api-contract.mjs --routes <(serve/bin/test-env php artisan route:list --json)`.

Expected: PASS; output contains every listed non-payment endpoint and no `/buy-vip`, `/payments`, `/link-renewal`, `/agent-packages`, `/agent-level` or `/commission-logs` reference.

- [ ] **Step 5: Commit.**

```bash
git add admin/src/models/api.ts admin/src/models/brand.ts admin/src/api admin/src/utils/http.ts admin/scripts/check-api-contract.mjs admin/index.html admin/package.json admin/pnpm-lock.yaml admin/yarn.lock admin/vite.config.ts admin/tsconfig.app.json admin/tsconfig.node.json docs/compliance/license-inventory.md
git commit -m "refactor(admin): align frontend API contract"
```

### Task 2: 可恢复启动与统一 BrandConfig

**Files:**
- Create: `admin/src/components/ConfigFailure.vue`
- Modify: `admin/src/main.ts`, `admin/src/App.vue`, `admin/src/stores/modules/config.ts`, `admin/src/stores/modules/app.ts`
- Modify: `admin/src/views/auth/LoginView.vue`, `admin/src/views/auth/register.vue`, `admin/src/views/auth/resetPassword.vue`
- Modify: `admin/src/layout/components/Logo.vue`, `admin/src/layout/components/Navbar/Customer.vue`, `admin/src/router/index.ts`
- Create: `admin/src/__tests__/bootstrap.test.ts`
- Create: `serve/app/Services/BrandConfig.php`
- Modify: `serve/app/Http/Controllers/Api/IndexController.php`, `serve/app/Http/Controllers/Api/ConfigController.php`, `serve/app/Forms/BaseConfig.php`
- Create: `serve/tests/Feature/BrandConfigTest.php`

**Interfaces:**
- `BrandConfigStore.load(): Promise<void>` always settles and sets `loadError: string | null`.
- `BrandConfigStore.brand: BrandConfig` is a complete object with non-public development fallback values.
- `ConfigFailure` emits `retry`; its retry button calls `load()` without reloading the page.
- `getBrandAsset(path: string): string` joins the `/config` asset origin without duplicate slashes.
- `BrandConfig::public(): array` returns only safe brand/captcha/resource fields; `BrandConfig::admin(): array` adds non-secret settings and `*_configured` booleans; `BrandConfig::update(array $input): void` validates/admin-saves brand fields without returning SMS/mail/payment Secret. Secret masking/read/write consumes the foundation-owned `SecretConfigService`; this plan does not reimplement encryption.

- [ ] **Step 1: Write the failing bootstrap tests.**

```ts
it('mounts an error page when config fails and retries', async () => {
  const createApp = vi.fn(() => ({ use: vi.fn().mockReturnThis(), mount: vi.fn() }))
  vi.mock('@/api/comment', () => ({ ApiGetSet: vi.fn().mockRejectedValue(new Error('offline')) }))
  await bootstrap(createApp)
  expect(createApp().mount).toHaveBeenCalledWith('#app')
  expect(document.body.textContent).toContain('配置加载失败')
})

it('never uses the old product name as a fallback', () => {
  expect(defaultBrand.productName).not.toContain('火星快链')
})
```

`BrandConfigTest` must first assert that `/api/config` contains all nine brand keys and excludes `package.price`, `ali_sms_secret`, `mail_password`, every `wechat_pay_*` field and raw mini-program Secret. Admin `GET /api/config/base` returns configured booleans/masks rather than plaintext; `PUT /api/config` updates the public brand and an empty Secret field does not erase an already configured Secret.

- [ ] **Step 2: Run red.**

Run: `pnpm --dir admin exec vitest run src/__tests__/bootstrap.test.ts`

Expected: FAIL because bootstrap is coupled to a successful `ApiGetSet()` and no `BrandConfig`/failure component exists.

- [ ] **Step 3: Implement resilient bootstrap and brand state.**

```ts
// admin/src/main.ts
export async function bootstrap(factory = createApp) {
  try { configStore.refresh(await ApiGetSet()) }
  catch (error) { configStore.setLoadError(formatApiError(error)) }
  const app = factory(App)
  app.use(store).use(ElementPlus, { locale: zhCn }).use(router).mount('#app')
  return app
}
bootstrap()
```

```ts
// admin/src/stores/modules/config.ts
const fallback: BrandConfig = { productName: 'Link Portal Dev', shortName: 'Link', logo: '', favicon: '', pageTitle: 'Link Portal', customerServiceUrl: '', copyrightOwner: 'Development', icp: '', helpCenterUrl: '' }
const useConfigStore = defineStore('brand-config', { state: () => ({ brand: fallback, loadError: null as string | null, loaded: false }), actions: { refresh(raw: unknown) { this.brand = normalizeBrandConfig(raw); this.loadError = null; this.loaded = true }, setLoadError(message: string) { this.loadError = message } } })
```

Implement the backend source of truth before connecting the store:

```php
final class BrandConfig
{
    public function public(): array
    {
        return [
            'productName' => (string) SystemConfig::get('brand_name', 'Link Portal Dev'),
            'shortName' => (string) SystemConfig::get('brand_short_name', 'Link'),
            'logo' => (string) SystemConfig::get('brand_logo', ''),
            'favicon' => (string) SystemConfig::get('brand_favicon', ''),
            'pageTitle' => (string) SystemConfig::get('brand_page_title', 'Link Portal'),
            'customerServiceUrl' => (string) SystemConfig::get('brand_customer_service_url', ''),
            'copyrightOwner' => (string) SystemConfig::get('brand_copyright_owner', 'Development'),
            'icp' => (string) SystemConfig::get('brand_icp', ''),
            'helpCenterUrl' => (string) SystemConfig::get('brand_help_center_url', ''),
        ];
    }
}
```

`IndexController::config()` returns `{ brand, code_mode, verify_code_is_open, asset_url }` plus only authenticated safe mini-program IDs/names; it must not return package prices or payment fields. `BaseConfig` validates the nine brand fields, trial/captcha settings and configured integrations；Secret defaults call `SecretConfigService::mask/configured`, updates call `SecretConfigService::set` only for non-empty inputs, and never place decrypted values in the response. `SystemConfig`/`SysConfig` encryption and deterministic JSON semantics are already provided by the foundation plan and are not modified again here.

Render `<config-failure v-if="configStore.loadError" @retry="retryConfig" />` while keeping the router/layout mounted. Set document title and favicon from `brand`; replace login/register/reset strings and Logo source with `brand` fields. `normalizeBrandConfig` must not copy payment prices or credentials into the client store.

- [ ] **Step 4: Run green and type-check.**

Run: `docker compose -f compose.test.yaml up -d --wait && serve/bin/test-env php artisan test tests/Feature/BrandConfigTest.php -v && pnpm --dir admin exec vitest run src/__tests__/bootstrap.test.ts && pnpm --dir admin type-check`.

Expected: PASS; a rejected `/config` still renders a retryable page, and TypeScript reports zero errors.

- [ ] **Step 5: Commit.**

```bash
git add admin/src/main.ts admin/src/App.vue admin/src/components/ConfigFailure.vue admin/src/stores/modules/config.ts admin/src/stores/modules/app.ts admin/src/views/auth admin/src/layout/components/Logo.vue admin/src/layout/components/Navbar/Customer.vue admin/src/router/index.ts admin/src/__tests__/bootstrap.test.ts serve/app/Services/BrandConfig.php serve/app/Http/Controllers/Api/IndexController.php serve/app/Http/Controllers/Api/ConfigController.php serve/app/Forms/BaseConfig.php serve/tests/Feature/BrandConfigTest.php
git commit -m "feat(admin): add resilient white-label bootstrap"
```

### Task 3: 登录、退出和全局错误反馈

**Files:**
- Modify: `admin/src/stores/modules/user.ts`, `admin/src/utils/http.ts`, `admin/src/router/index.ts`
- Modify: `admin/src/layout/components/Navbar/AccountSet.vue`, `admin/src/layout/components/Navbar/User.vue`
- Modify: `admin/src/views/auth/LoginView.vue`, `admin/src/views/auth/register.vue`, `admin/src/views/auth/resetPassword.vue`
- Create: `admin/src/stores/__tests__/user-session.test.ts`

**Interfaces:**
- `getToken(): string` returns only a non-empty token string; malformed JSON returns `''`.
- `logout(): Promise<void>` removes `token` from both `localStorage` and `sessionStorage`, clears in-memory token/user state, then routes to `/login`.
- `login(data: ILoginRequest, remember: boolean): Promise<void>` rejects with `ApiError` and never treats a missing token as success.
- `mapStatusToMessage(status: number, errorCode?: string): string` is the one error-message mapping used by HTTP and forms.

- [ ] **Step 1: Write the failing session tests.**

```ts
it('does not accept an object as an authenticated token', () => {
  localStorage.setItem('token', JSON.stringify({ user: 1 }))
  expect(userStore.getToken()).toBe('')
})

it('logout only removes application keys', async () => {
  localStorage.setItem('token', JSON.stringify({ token: 'abc' }))
  localStorage.setItem('other-app-key', 'keep')
  await userStore.logout()
  expect(localStorage.getItem('token')).toBeNull()
  expect(localStorage.getItem('other-app-key')).toBe('keep')
})
```

- [ ] **Step 2: Run red.**

Run: `pnpm --dir admin exec vitest run src/stores/__tests__/user-session.test.ts`

Expected: FAIL because current `logout()` calls `localStorage.clear()` and `getToken()` accepts a parsed non-token object.

- [ ] **Step 3: Implement the minimal session boundary.**

```ts
logout() {
  localStorage.removeItem(TOKEN_KEY)
  sessionStorage.removeItem(TOKEN_KEY)
  this.tokenInfo = {} as ILoginResponse
  this.userInfo = {} as IUserInfoResponse
  return Promise.resolve()
}

getToken() {
  const token = this.tokenInfo.token || readStorageToken()
  return typeof token === 'string' && token.length > 0 ? token : ''
}
```

Update the router guard to call `getToken()` (not truthiness of `tokenInfo`), clear session on 401, and show the stable error code/message for 403, 422, 408 and network timeout. Form submit handlers must show field-level validation text while preserving server error text. Add an explicit logout action in `AccountSet.vue` and test redirect to `/login`.

- [ ] **Step 4: Run green and route tests.**

Run: `pnpm --dir admin exec vitest run src/stores/__tests__/user-session.test.ts && pnpm --dir admin type-check`.

Expected: PASS; unrelated storage keys remain, missing/expired tokens redirect to `/login`, and no interceptor accesses `error.response.status` when response is absent.

- [ ] **Step 5: Commit.**

```bash
git add admin/src/stores/modules/user.ts admin/src/utils/http.ts admin/src/router/index.ts admin/src/layout/components/Navbar admin/src/views/auth admin/src/stores/__tests__/user-session.test.ts
git commit -m "fix(admin): make auth session and errors deterministic"
```

### Task 4: 非支付导航与管理端资源 CRUD

**Files:**
- Modify: `admin/src/router/apiRouter.ts`, `admin/src/layout/components/MenuItem.vue`, `admin/src/layout/components/Navbar/Document.vue`
- Modify: `admin/src/views/home/home.vue`, `admin/src/views/home/components/Welcome.vue`, `admin/src/views/home/components/BuyVip.vue`, `admin/src/views/home/components/PayButton.vue`
- Modify: `admin/src/views/super/payRecord/index.vue`, `admin/src/views/super/setting/components/pay.vue`, `admin/src/views/super/setting/components/sales.vue`, `admin/src/views/super/setting/components/codeSetUp.vue`
- Modify: `admin/src/views/super/notices/index.vue`, `admin/src/views/super/domains/index.vue`, `admin/src/views/super/users/index.vue`, `admin/src/views/super/userManage/index.vue`, `admin/src/views/super/vipSetMeal/index.vue`, `admin/src/views/link/fallApp/index.vue`, `admin/src/views/library/index.vue`, `admin/src/views/library/cateIndex.vue`
- Modify: `admin/src/hooks/curd.ts`, `admin/src/components/MaterialSelect/dialog.vue`, `admin/src/components/tailorUpload.vue`
- Create: `admin/src/views/super/users/components/membership-action.vue`
- Create: `admin/src/views/__tests__/navigation-contract.test.ts`
- Modify: `serve/app/Http/Controllers/Api/UploadController.php`, `serve/routes/api.php`
- Create: `serve/tests/Feature/Base64UploadTest.php`
- Modify: `serve/app/Http/Controllers/Api/AuthController.php`, `serve/app/Http/Controllers/Api/IndexController.php`
- Delete: `serve/app/PayChannels/WeChatPayNative.php`, `serve/app/Jobs/PayVip.php`
- Create: `serve/tests/Feature/PaymentDisabledTest.php`

**Interfaces:**
- `resourceRefresh(resource: 'notices' | 'domains' | 'min-programs' | 'materials' | 'users' | 'vip-packages'): Promise<void>` reads the list endpoint after every mutation.
- Task 4 复用 Task 1 的 `setLinkStatus(id: number, manualStatus: boolean): Promise<LinkRecord>`，只发送 `{ manual_status: boolean }` 到 `PATCH /links/{id}/status`，成功后同时回读列表和详情；不新增字符串状态适配层。
- `MembershipAction` emits `submit({ userId: number; vipId: number; action: 'open' | 'renew' | 'upgrade' | 'revoke'; reason: string })` and renders no price or buy control.
- `POST /material-base64` accepts only `data:image/png|jpeg|webp;base64,...`, rejects decoded payloads over 2 MiB and invalid image bytes, stores through Laravel Storage, and returns only `{ path: string }`.
- Payment-disabled contract removes `/buy-vip`, `/wechat/payment_notify`, `/payments` and refund mutations from the route collection; requests return 404 and cannot change `payments`, `vip_logs` or users. Historical tables remain for future migration/audit only.

- [ ] **Step 1: Write the failing navigation/API tests.**

```ts
it('does not expose payment-adjacent navigation or requests', () => {
  const source = readFileSync('src/router/apiRouter.ts', 'utf8')
  expect(source).not.toMatch(/payRecord|buy-vip|agent-packages|agent-level|commission|payments/)
})

it('renders all six link type keys in the creation selector', () => {
  expect(Object.keys(allowType)).toEqual(['MINI_PROGRAM', 'KING_DOC', 'CLI_QR', 'WORK_WECHAT', 'LANDING_MINI', 'QR_QQ'])
})
```

Add `Base64UploadTest` with a 1×1 PNG data URI success case plus invalid MIME, malformed base64 and >2 MiB rejection cases; every rejection must be 422 and create no storage file.

Add `PaymentDisabledTest` that records payment/VIP/user counts, calls the three legacy payment paths with authenticated and anonymous clients, asserts 404, then asserts all counts and membership timestamps are unchanged.

- [ ] **Step 2: Run red.**

Run: `pnpm --dir admin exec vitest run src/views/__tests__/navigation-contract.test.ts`

Expected: FAIL because the current router contains `payRecord`, the home shows `BuyVip`/`PayButton`, and existing forms use `/link-renewal`.

- [ ] **Step 3: Implement resource UI and remove payment surfaces.**

Delete payment/commission menu entries and imports, hide price/buy/renewal copy, and remove `ApiRenewalLink` calls from all six type pages. Keep historical payment data untouched in the backend but provide no frontend list or mutation. Add manual membership action with reason/audit fields and re-read `/users/{id}` or `/users` after success. Preserve CRUD for notices, domains, materials/categories, mini-program pool, users and VIP entitlement configuration; all success handlers call the relevant list/detail GET.

In `serve/routes/api.php`, remove imports and registrations for `WeChatPayNative`, `/buy-vip`, `/wechat/payment_notify` and `/payments`. Remove `AuthController::buyVip()` and payment imports, remove payment revenue queries from `IndexController`, and delete the known-broken `PayVip`/`WeChatPayNative` implementations; payment history migrations/tables are not dropped. This plan chooses route removal (404), so no compatibility controller may return success or mutate state.

```vue
<el-switch
  :model-value="row.manual_status"
  active-text="启用"
  inactive-text="停用"
  @change="(value: boolean) => toggle(row.id, value)"
/>
<el-button link @click="showDetail(row.id)">详情</el-button>
<el-button link @click="copyShareLink(row.share_link)">分享地址</el-button>
```

Normalize `useCurd` to use `getDetail` after create/update, expose explicit status mutation, and surface validation/permission/external/network errors. Keep the existing cropper caller and route it through the authenticated, validated `POST /material-base64` contract.

Implement `UploadController::base64(Request $request): JsonResponse` using strict `base64_decode($payload, true)`, `getimagesizefromstring`, an allowlist for PNG/JPEG/WebP, a 2 MiB decoded-size limit and `Storage::put` with a server-generated filename. Register `POST /material-base64` inside the authenticated API group. Never accept SVG, client filenames or filesystem paths.

`codeSetUp.vue` keeps both SMS and email modes: SMS shows only configured/masked Aliyun fields, email shows only configured/masked SMTP fields. Empty Secret/password values mean “keep current value”, not “clear”; no plaintext credential is ever placed into Pinia, HTML or a Playwright trace. Add a component test that switches both modes and asserts the correct validation labels and mask behavior.

- [ ] **Step 4: Run green and inspect the request set.**

Run: `docker compose -f compose.test.yaml up -d --wait && pnpm --dir admin exec vitest run src/views/__tests__/navigation-contract.test.ts && node admin/scripts/check-api-contract.mjs --source admin/src --routes <(serve/bin/test-env php artisan route:list --json) && serve/bin/test-env php artisan test tests/Feature/Base64UploadTest.php tests/Feature/PaymentDisabledTest.php -v`.

Expected: PASS; the request set has no payment-adjacent endpoint and every visible non-payment CRUD action has a route entry; each mutation is followed by a GET in the test spy.

- [ ] **Step 5: Commit.**

```bash
git add admin/src/router/apiRouter.ts admin/src/layout/components admin/src/views admin/src/hooks/curd.ts admin/src/components/MaterialSelect admin/src/components/tailorUpload.vue admin/src/views/__tests__/navigation-contract.test.ts serve/app/Http/Controllers/Api/UploadController.php serve/app/Http/Controllers/Api/AuthController.php serve/app/Http/Controllers/Api/IndexController.php serve/app/PayChannels/WeChatPayNative.php serve/app/Jobs/PayVip.php serve/routes/api.php serve/tests/Feature/Base64UploadTest.php serve/tests/Feature/PaymentDisabledTest.php
git commit -m "feat(admin): complete non-payment resource management"
```

### Task 5: 六类链接统一 CRUD、状态、详情和分享地址

**Files:**
- Create: `admin/src/components/LinkActions.vue`
- Create: `admin/src/components/LinkStatusTag.vue`
- Modify: `admin/src/views/link/addLink/index.vue`, `admin/src/views/link/addLink/components/{app,kingDoc,qr,firmWx,tengxun,wx}/index.vue`
- Modify: `admin/src/views/link/addLink/components/{app,kingDoc,qr,firmWx,tengxun,wx}/create-or-look.vue`
- Modify: `admin/src/models/link.ts`, `admin/src/utils/index.ts`
- Create: `admin/src/views/link/__tests__/link-crud-contract.test.ts`

**Interfaces:**
- `const LINK_TYPES: readonly LinkType[] = ['MINI_PROGRAM', 'KING_DOC', 'CLI_QR', 'WORK_WECHAT', 'LANDING_MINI', 'QR_QQ']` maps respectively to current numeric UI types `1,2,3,4,5,11`.
- `LinkActions` props: `{ row: LinkRecord }`; emits `refresh`; methods call `setLinkStatus`, `deleteLink`, `getLink` and `navigator.clipboard.writeText(row.share_link)`.
- `normalizePayload(type: LinkType, form: LinkForm): LinkPayload` rejects missing `config.min_id`, invalid `pages/...` path and cross-type fields before POST/PUT.
- `copyShareLink(link: string): Promise<void>` rejects an empty/non-URL link and reports a user-readable error.

- [ ] **Step 1: Write the failing link contract tests.**

```ts
it.each([
  ['MINI_PROGRAM', 1], ['KING_DOC', 2], ['CLI_QR', 3], ['WORK_WECHAT', 4],
  ['LANDING_MINI', 5], ['QR_QQ', 11]
])('creates and reads canonical share data for %s', async (type, numericType) => {
  const page = mount(LinkActions, { props: { row: { id: 9, type: numericType, share_link: 'https://example.test/?code=x', manual_status: true, health_status: true, effective_status: true } } })
  expect(page.text()).toContain('分享地址')
  expect(type).toBeTruthy()
})

it('re-reads after status and delete and never calls link-renewal', async () => {
  const calls: string[] = []
  vi.spyOn(http, 'patch').mockImplementation(async (url) => { calls.push(String(url)); return {} as never })
  vi.spyOn(http, 'get').mockImplementation(async (url) => { calls.push(String(url)); return {} as never })
  await setLinkStatus(9, false)
  expect(calls).toEqual(['/links/9/status'])
  expect(calls.some((url) => url.includes('renewal'))).toBe(false)
})
```

- [ ] **Step 2: Run red.**

Run: `pnpm --dir admin exec vitest run src/views/link/__tests__/link-crud-contract.test.ts`

Expected: FAIL because the six pages currently show only legacy `status`, have no status/detail action, and some import `ApiRenewalLink`.

- [ ] **Step 3: Implement the shared actions and per-type payload validation.**

```vue
<!-- LinkActions.vue -->
<template>
  <el-button link @click="emit('refresh')">详情</el-button>
  <el-button link @click="toggle">{{ row.manual_status ? '停用' : '启用' }}</el-button>
  <el-button link @click="share">复制分享地址</el-button>
  <el-button link type="danger" @click="remove">删除</el-button>
</template>
<script setup lang="ts">
const props = defineProps<{ row: LinkRecord }>(); const emit = defineEmits<{ refresh: [] }>()
const toggle = async () => { await setLinkStatus(props.row.id, !props.row.manual_status); emit('refresh') }
const share = async () => { await copyShareLink(props.row.share_link); ElMessage.success('已复制分享地址') }
const remove = async () => { await ElMessageBox.confirm('删除后公开地址返回不存在，确认删除？'); await deleteLink(props.row.id); emit('refresh') }
</script>
```

Replace each page’s inline status and action column with `LinkStatusTag` and `LinkActions`; after create/edit call `GET /links/{id}` and assert returned type, owner and `share_link`. Show Secret only as `configured: boolean`/masked value, never the raw field. Validate numeric type mapping and enforce page path regex `^pages/[A-Za-z0-9_/-]+$` for `MINI_PROGRAM`; all external URLs are sent to backend for allowlist validation.

- [ ] **Step 4: Run green and production type-check.**

Run: `pnpm --dir admin exec vitest run src/views/link/__tests__/link-crud-contract.test.ts && pnpm --dir admin type-check`.

Expected: PASS; all six tabs expose create/edit/status/delete/detail/share, status uses the PATCH route, and TypeScript reports zero errors.

- [ ] **Step 5: Commit.**

```bash
git add admin/src/components/LinkActions.vue admin/src/components/LinkStatusTag.vue admin/src/views/link admin/src/models/link.ts admin/src/utils/index.ts admin/src/views/link/__tests__/link-crud-contract.test.ts
git commit -m "feat(admin): unify six link CRUD actions"
```

### Task 6: 白标扫描、同源分享壳 postMessage 安全与生产配置注入

**Files:**
- Create: `serve/resources/views/jump/shell.blade.php`
- Modify: `serve/app/Http/Controllers/Controller.php`, `serve/routes/web.php`, `serve/resources/views/welcome.blade.php`
- Delete: `serve/app/Http/Controllers/Install/IndexController.php`, `serve/resources/views/install/step1.blade.php`, `step2.blade.php`, `step3.blade.php`, `step4.blade.php`
- Create: `serve/resources/views/errors/503.blade.php`
- Delete: `jump/a_dev.html`
- Delete: `说明/安装说明.docx`（旧品牌二进制安装文档，改由版本化 Markdown 部署文档替代）
- Delete: `serve/public/image/logo.png`, `serve/public/image/toplogo.png`, `serve/public/image/web-qr.png`, `serve/public/image/yanshi.png`（原品牌/客服/演示资产；品牌配置为空时隐藏对应 UI）
- Modify: `admin/public/config.js`; Task 8 通过完整构建生成 `admin/dist/**`（不得手改）
- Modify: `admin/index.html`（再次验证不含地图或任何第三方内联凭据）
- Create: `serve/tests/Feature/ShareShellTest.php`, `serve/resources/js/__tests__/share-shell.security.test.mjs`
- Create: `admin/scripts/scan-brand-residue.mjs`
- Modify: `admin/src/layout/components/Logo.vue`, `admin/src/router/index.ts`, `admin/src/App.vue`

**Interfaces:**
- `Controller::welcome(Request $request)` renders `jump.shell` only for `code` matching `^[A-Za-z0-9_-]{4,128}$`; no code renders the white-label marketing page.
- `buildFrameUrl(origin: string, code: string): URL` accepts an HTTPS origin and the validated short code; otherwise throws.
- `isTrustedMessage(event: MessageEvent, frame: HTMLIFrameElement, allowedOrigins: Set<string>): boolean` requires `event.source === frame.contentWindow` and exact origin membership.
- `applyTargetMessage(data: unknown): void` accepts only `{ type: 4, target: string, title?: string, description?: string, icon?: string }` with a validated target.
- `scan-brand-residue.mjs` scans `admin/index.html`, source and build output and exits 1 for `火星快链`, `mayilink`, original author WeChat, demo credentials, original demo domain, `_AMapSecurityConfig/securityJsCode` or tracked AppID/Secret patterns; it ignores root license/NOTICE files only.

- [ ] **Step 1: Write failing security and residue tests.**

```js
it('rejects an evil origin and a message from another window', () => {
  const frame = { contentWindow: {} }
  expect(isTrustedMessage({ origin: 'https://evil.test', source: frame.contentWindow }, frame, new Set(['https://app.test']))).toBe(false)
  expect(isTrustedMessage({ origin: 'https://app.test', source: {} }, frame, new Set(['https://app.test']))).toBe(false)
})

it('rejects javascript and private targets', () => {
  expect(() => validateTarget('javascript:alert(1)')).toThrow('INVALID_TARGET')
  expect(() => validateTarget('https://127.0.0.1/admin')).toThrow('INVALID_TARGET')
})
```

`ShareShellTest` asserts `GET /` returns the marketing page, `GET /?code=abc123` returns the shell with same-origin iframe `/j/abc123`, invalid code returns 422, `/install` returns 404, and the response contains no hard-coded host/domain. Database-unavailable handling renders a generic 503 page and never exposes an installer form, environment path or exception details.

- [ ] **Step 2: Run red.**

Run: `docker compose -f compose.test.yaml up -d --wait && node --test serve/resources/js/__tests__/share-shell.security.test.mjs && serve/bin/test-env php artisan test tests/Feature/ShareShellTest.php -v`

Expected: FAIL because the root route always renders `welcome`, the independent static file accepts every `message` event, and no Laravel-rendered share shell exists.

- [ ] **Step 3: Implement strict message handling and injected brand data.**

```js
const runtime = window.__LINK_RUNTIME__
const frame = document.createElement('iframe')
const frameOrigin = location.origin
frame.src = `/j/${encodeURIComponent(runtime.code)}`
window.addEventListener('message', (event) => {
  if (!isTrustedMessage(event, frame, new Set([frameOrigin]))) return
  const data = parseTargetMessage(event.data)
  document.title = data.title || runtime.brand.pageTitle
  if (data.type === 4) window.open(validateTarget(data.target), '_blank', 'noopener,noreferrer')
})
```

Move the share-shell logic into `shell.blade.php`; pass only validated `code`, current origin and safe brand fields. `Controller::welcome` chooses shell versus marketing page, and the marketing page removes price/purchase UI for the non-payment release. Remove the unauthenticated web installer route/controller/views completely; operators use the migration/Seeder/admin-provision CLI from the foundation plan, and database failure returns a non-sensitive 503 page. Delete `jump/a_dev.html` and its README deployment instructions so there is one runtime implementation. Remove original brand/logo/customer-service/demo binary assets; when replacement brand fields are empty, render no broken image or legacy fallback. Replace `admin/public/config.js` with a non-secret runtime contract (`window.__LINK_RUNTIME__ = { publicOrigin, apiOrigin, brand }`) populated by deployment tooling; do not add credentials. Recheck `admin/index.html` after merges and keep it free of inline third-party keys. This task scans `admin/index.html`, `admin/src`, `admin/public`, `serve/resources`, `jump`, and `mini_programs`; Task 8 rebuilds and scans `admin/dist`. Keep legal `LICENSE`/`NOTICE` attribution but remove marketing brand strings, old author contacts, demo accounts/passwords and original demo URLs from UI/runtime output.

- [ ] **Step 4: Run green, scan and inspect HTML.**

Run: `docker compose -f compose.test.yaml up -d --wait && node --test serve/resources/js/__tests__/share-shell.security.test.mjs && serve/bin/test-env php artisan test tests/Feature/ShareShellTest.php -v && node admin/scripts/scan-brand-residue.mjs admin/index.html admin/src admin/public serve/resources jump mini_programs`.

Expected: PASS; untrusted messages do not alter title/open windows, all targets are policy-checked, and the residue scanner prints `0 forbidden matches`.

- [ ] **Step 5: Commit.**

```bash
git add serve/app/Http/Controllers/Controller.php serve/app/Http/Controllers/Install/IndexController.php serve/routes/web.php serve/resources/views/jump/shell.blade.php serve/resources/views/welcome.blade.php serve/resources/views/install serve/resources/views/errors/503.blade.php serve/resources/js/__tests__/share-shell.security.test.mjs serve/tests/Feature/ShareShellTest.php serve/public/image/logo.png serve/public/image/toplogo.png serve/public/image/web-qr.png serve/public/image/yanshi.png jump/a_dev.html 说明/安装说明.docx README.md admin/index.html admin/public/config.js admin/src/layout/components/Logo.vue admin/src/router/index.ts admin/src/App.vue admin/scripts/scan-brand-residue.mjs
git commit -m "feat(white-label): secure share shell and residue scan"
```

### Task 7: uni-app 二维码落地页、visitor_token 与 mp-weixin 产物契约

**Files:**
- Modify: `mini_programs/pages/views/tools/news.vue`, `mini_programs/pages.json`, `mini_programs/utils/request.js`, `mini_programs/manifest.json`
- Create: `mini_programs/utils/runtime-config.js`
- Create: `mini_programs/utils/brand.js`
- Create: `mini_programs/tests/news-contract.test.js`
- Create: `mini_programs/scripts/assert-dcloud-version.mjs`
- Create: `mini_programs/scripts/assert-mp-weixin-output.mjs`
- Modify: `mini_programs/package.json`
- Create: `mini_programs/pnpm-lock.yaml`
- Create: `mini_programs/.env.example`

**Interfaces:**
- `parseNewsOptions(options: Record<string, string>): { code: string; visitor_token?: string }` rejects missing `code`, `device_uid`, and all unknown identity fields.
- `getQr(code: string, visitorToken?: string): Promise<{ title: string; avatar: string; qr: string }>` sends `GET ${PUBLIC_ORIGIN}/api/link-show-qr/{code}` with `visitor_token` as a query parameter only when present.
- `loadBrand(): Promise<{ productName: string; pageTitle: string; logo: string }>` reads the safe `/api/config.data.brand` DTO; it never reads local hard-coded brand text or integration credentials.
- `showFailure(code: 'INVALID_LINK' | 'TOKEN_EXPIRED' | 'NO_QR' | 'NETWORK_ERROR'): void` renders an in-page failure view and never calls `JSON.stringify(e)` for an undeclared variable.
- `assertMpWeixinOutput(dir: string)` requires `pages/views/tools/news.js`, `pages.json` registration with exact path, no AppID/Secret literals, and no `device_uid` forwarding.

- [ ] **Step 1: Write the failing page and output tests.**

```js
it('accepts only code and visitor_token', () => {
  expect(parseNewsOptions({ code: 'abc', visitor_token: 'signed' })).toEqual({ code: 'abc', visitor_token: 'signed' })
  expect(() => parseNewsOptions({ code: 'abc', device_uid: 'phone-1' })).toThrow('INVALID_QUERY')
})

it('posts the visitor token without the raw visitor id', async () => {
  await getQr('abc', 'signed-token')
  expect(uni.request).toHaveBeenCalledWith(expect.objectContaining({ url: 'https://app.test/api/link-show-qr/abc?visitor_token=signed-token' }))
})

it('uses the shared public BrandConfig for the page fallback', async () => {
  mockConfig({ brand: { productName: 'Test Brand', pageTitle: 'Test Landing', logo: '' } })
  await loadNewsPage({ code: 'abc', visitor_token: 'signed-token' })
  expect(uni.setNavigationBarTitle).toHaveBeenCalledWith({ title: 'Test Landing' })
})
```

- [ ] **Step 2: Run red.**

Run: `node --test mini_programs/tests/news-contract.test.js`

Expected: FAIL because `news.vue` currently requires/forwards `device_uid`, uses an unresolved `e` in `catch`, and `request.js` has a hard-coded placeholder base URL.

- [ ] **Step 3: Implement the page, runtime URL and exact build pin.**

```js
// mini_programs/pages/views/tools/news.vue
onLoad(options) {
  const { code, visitor_token } = parseNewsOptions(options)
  getQr(code, visitor_token).then((data) => {
    this.data = data
    uni.setNavigationBarTitle({ title: data.title || brand.pageTitle })
  }).catch((error) => this.showFailure(error.error_code || 'NETWORK_ERROR'))
}
```

Use `${PUBLIC_ORIGIN}/api` from `runtime-config.js`/build environment and URL-encode `visitor_token`; do not send `device_uid` and do not add a token to normal `MINI_PROGRAM` links. `brand.js` fetches the same public `/api/config` BrandConfig used by the management UI; `news.vue` loads brand and QR concurrently, then uses QR title first and `brand.pageTitle` only as fallback. Register `pages/views/tools/news` in `pages.json` with a neutral compile-time title that is immediately replaced by BrandConfig, remove unrelated template pages from first-launch navigation, and set official landing pool `url` contract to `pages/views/tools/news`. Add a visible loading/error/success state showing brand/title, avatar and QR, with `show-menu-by-longpress="true"` only on a successful QR.

Pin every declared `@dcloudio/*` package in the Vue2 DCloud CLI dependency/toolchain to `2.0.2-5020420260813001`, and add the exact script. The package manifest must include the official Vue2 CLI plugin and Weixin platform package at that same version; no `@dcloudio/*` range, caret, tilde or mixed version is accepted:

```json
{
  "dependencies": {
    "@dcloudio/uni-app": "2.0.2-5020420260813001",
    "@dcloudio/uni-mp-vue": "2.0.2-5020420260813001",
    "@dcloudio/uni-mp-weixin": "2.0.2-5020420260813001",
    "vue": "2.6.14"
  },
  "devDependencies": {
    "@dcloudio/uni-cli-i18n": "2.0.2-5020420260813001",
    "@dcloudio/uni-cli-shared": "2.0.2-5020420260813001",
    "@dcloudio/uni-template-compiler": "2.0.2-5020420260813001",
    "@dcloudio/vue-cli-plugin-hbuilderx": "2.0.2-5020420260813001",
    "@dcloudio/vue-cli-plugin-uni": "2.0.2-5020420260813001",
    "@dcloudio/vue-cli-plugin-uni-optimize": "2.0.2-5020420260813001",
    "@dcloudio/webpack-uni-mp-loader": "2.0.2-5020420260813001",
    "@dcloudio/webpack-uni-pages-loader": "2.0.2-5020420260813001",
    "@vue/cli-plugin-babel": "5.0.9",
    "@vue/cli-service": "5.0.9",
    "cross-env": "7.0.3",
    "sass": "1.77.8",
    "sass-loader": "8.0.2",
    "vue-template-compiler": "2.6.14"
  },
  "scripts": {
    "test": "node --test tests/*.test.js",
    "build:mp-weixin": "cross-env NODE_ENV=production UNI_PLATFORM=mp-weixin vue-cli-service uni-build"
  }
}
```

Keep AppID empty in tracked `manifest.json`; bind AppID only in local private configuration. `.env.example` contains `PUBLIC_ORIGIN=https://app.example.test` and no Secret/AppID.

Add a build preflight that enumerates every key beginning with `@dcloudio/` in `package.json` and exits nonzero unless its value is exactly `2.0.2-5020420260813001`; this keeps future package additions on the approved CLI line.

After editing `package.json`, run `pnpm --dir mini_programs install` once to resolve the exact graph and create `mini_programs/pnpm-lock.yaml`; inspect the lock for mixed `@dcloudio/*` versions before using the frozen install below.

- [ ] **Step 4: Run green and assert the build artifact.**

Run: `node --test mini_programs/tests/news-contract.test.js && node mini_programs/scripts/assert-dcloud-version.mjs mini_programs/package.json && pnpm --dir mini_programs install --frozen-lockfile && pnpm --dir mini_programs audit --prod --audit-level high && pnpm --dir mini_programs build:mp-weixin && node mini_programs/scripts/assert-mp-weixin-output.mjs mini_programs/dist/build/mp-weixin`.

Expected: PASS; no unhandled high/critical mini-program production dependency advisory；`mini_programs/dist/build/mp-weixin` exists, includes `pages/views/tools/news`, contains no tracked AppID/Secret or raw `device_uid`, and is directly importable by微信开发者工具. If the local CLI prints a version different from `2.0.2-5020420260813001`, stop with a version error instead of treating the build as valid.

- [ ] **Step 5: Commit.**

```bash
git add mini_programs/pages/views/tools/news.vue mini_programs/pages.json mini_programs/utils/request.js mini_programs/utils/runtime-config.js mini_programs/utils/brand.js mini_programs/manifest.json mini_programs/package.json mini_programs/pnpm-lock.yaml mini_programs/.env.example mini_programs/tests mini_programs/scripts
git commit -m "feat(mini-program): harden QR landing page build"
```

### Task 8: Playwright 登录/会员/六类链接 E2E 与最终前端门

**Files:**
- Create: `admin/playwright.config.ts`
- Create: `admin/e2e/auth.spec.ts`
- Create: `admin/e2e/links.spec.ts`
- Create: `admin/e2e/non-payment.spec.ts`
- Create: `admin/e2e/fixtures.ts`
- Create: `scripts/prepare-e2e.sh`
- Create: `serve/tests/Support/E2ETestSeeder.php`
- Modify: `admin/package.json`, `admin/tsconfig.app.json`
- Create: `admin/scripts/verify-frontend-gates.mjs`

**Interfaces:**
- `loginAs(page: Page, credentials: { username: string; password: string }): Promise<void>` waits for `/login` response body containing a non-empty token and `/userinfo` before navigating.
- `createLinkAndReadBack(page: Page, typeLabel: string, payload: Record<string, unknown>): Promise<{ id: number; share_link: string; effective_status: boolean }>` asserts POST then GET response bodies, not just HTTP 200.
- `verifyFrontendGates(): Promise<{ typeCheck: boolean; build: boolean; apiContract: boolean; brandScan: boolean; miniOutput: boolean }>` returns false on any missing gate and exits nonzero.
- `scripts/prepare-e2e.sh` 只在未设 `E2E_ORIGIN` 时重建隔离测试库；指向外部域名时禁止执行 `migrate:fresh`/Seeder，并强制由运营方注入专用测试管理员凭据与专用测试会员标识。

- [ ] **Step 1: Write the failing E2E specs.**

```ts
test('admin can log in, manually open membership, and log out', async ({ page }) => {
  await loginAs(page, { username: process.env.E2E_ADMIN_USER!, password: process.env.E2E_ADMIN_PASSWORD! })
  await expect(page.getByText('购买会员')).toHaveCount(0)
  await page.getByRole('link', { name: '会员管理' }).click()
  await page.getByRole('button', { name: '人工开通' }).click()
  await page.getByLabel('原因').fill('E2E entitlement test')
  await page.getByRole('button', { name: '确定' }).click()
  await expect(page.getByText('生效中')).toBeVisible()
  await page.getByRole('button', { name: '退出登录' }).click()
  await expect(page).toHaveURL(/\/login/)
})

for (const type of ['微信小程序', '金山文档', '草料二维码', '企业微信', '个人微信二维码', '腾讯优码']) {
  test(`creates ${type} and exposes canonical share link`, async ({ page }) => {
    await loginAs(page, adminCredentials)
    const result = await createLinkAndReadBack(page, type, fixtures[type])
    expect(result.share_link).toMatch(/^https:\/\/[^/]+\/\?code=/)
    expect(result.effective_status).toBe(true)
  })
}
```

- [ ] **Step 2: Run red.**

Run: `pnpm --dir admin test:e2e`

Expected: FAIL because the E2E preparation script/configuration does not exist, payment UI is visible, and the six pages do not consistently expose detail/status/share actions or response-body assertions.

- [ ] **Step 3: Implement deterministic browser fixtures and gate script.**

`playwright.config.ts` 把 `E2E_ORIGIN` 解释为不带路径的 origin，`baseURL` 固定为 `${E2E_ORIGIN}/web/`。未设该变量时，配置必须以两个 `webServer` 启动真实本地栈：

```ts
const externalOrigin = process.env.E2E_ORIGIN?.replace(/\/$/, '')
const localOrigin = 'http://127.0.0.1:13000'

export default defineConfig({
  testDir: './e2e',
  use: { baseURL: `${externalOrigin ?? localOrigin}/web/`, trace: 'on-first-retry' },
  projects: [{ name: 'chromium', use: { ...devices['Desktop Chrome'] } }],
  webServer: externalOrigin ? undefined : [
    {
      command: '../serve/bin/test-env php artisan serve --host=127.0.0.1 --port=18080',
      url: 'http://127.0.0.1:18080/api/config',
      reuseExistingServer: false
    },
    {
      command: 'VITE_PUBLIC_PATH=/web/ VITE_PROXY_PATH=/api VITE_API_URL=http://127.0.0.1:18080/api pnpm exec vite --host=127.0.0.1 --port=13000 --strictPort',
      url: 'http://127.0.0.1:13000/web/',
      reuseExistingServer: false
    }
  ]
})
```

`scripts/prepare-e2e.sh` 使用 `set -Eeuo pipefail`；本地模式执行 `docker compose -f compose.test.yaml up -d --wait`、`serve/bin/test-env php artisan migrate:fresh --seed`和 `serve/bin/test-env php artisan db:seed --class='Tests\\Support\\E2ETestSeeder'`。`E2ETestSeeder` 先断言 `app()->environment('testing')`，再幂等创建 `e2e-admin` 和 `e2e-member`；它只在测试库把 `e2e-admin.must_change_password` 设为 false，保证浏览器验收能进入业务页，绝不改动生产 Provisioner 的强制改密契约。管理员密码是仅用于隔离测试库的固定 fixture，不写入生产 `.env`。外部模式只验证 `E2E_ORIGIN`为 HTTPS 且要求 `E2E_ADMIN_USER/E2E_ADMIN_PASSWORD/E2E_MEMBER_USERNAME`，不运行任何数据库命令。

`fixtures.ts` 在本地模式使用上述测试管理员/会员；外部模式只使用运营方预先建立的专用 `E2E_MEMBER_USERNAME`，因为最终 API 不提供删除用户路由，不虚构“创建后删用户”能力。测试前读取该会员权益快照，测试后通过支持的管理员更新/撤销动作恢复状态；链接、素材、公告、域名和小程序池 fixture 使用唯一前缀，只删除本次 API 返回的 ID。只允许 mock 真实外部平台调用，不得 mock 被验收的管理端 API 响应。`verify-frontend-gates.mjs` 按固定顺序运行精确命令，包括 `node ../mini_programs/scripts/assert-dcloud-version.mjs ../mini_programs/package.json` 和 `node ../mini_programs/scripts/assert-mp-weixin-output.mjs ../mini_programs/dist/build/mp-weixin`，并将环境/工具链失败与代码断言失败分开标记。

```json
{
  "scripts": {
    "pretest:e2e": "bash ../scripts/prepare-e2e.sh",
    "test:e2e": "playwright test e2e --project=chromium",
    "verify": "node scripts/verify-frontend-gates.mjs"
  }
}
```

The test dependency versions are already installed and locked by Task 1 (`vitest 4.1.10`, `@vue/test-utils 2.5.0`, `jsdom 29.0.1`, `@playwright/test 1.62.1`); do not re-add or upgrade them in this task.

The six link specs must assert the exact response fields: `type`, `user_id`, `manual_status`, `health_status`, `effective_status`, and non-empty `share_link`; then exercise disable/detail/reenable/delete and assert a second GET. Error spec must cover 422 field errors, 403 cross-tenant access, `MEMBERSHIP_EXPIRED`, and network timeout text. Non-payment spec must assert no visible price, purchase, payment, renewal, commission or agent-level action.

- [ ] **Step 4: Run green and the complete frontend gate.**

Run: `pnpm --dir admin install --frozen-lockfile && pnpm --dir admin type-check && pnpm --dir admin build-only && pnpm --dir admin test:e2e`.

Expected: PASS; Playwright reports login/logout, manual membership action, six CRUD flows, canonical share links and stable errors. Then run `pnpm --dir admin verify`.

Expected: PASS with separate lines for `TYPE_CHECK=PASS`, `ADMIN_BUILD=PASS`, `PLAYWRIGHT=PASS`, `API_CONTRACT=PASS`, `BRAND_SCAN=PASS`, and `MINI_OUTPUT=PASS`; no deployment/service claim is made by this task.

- [ ] **Step 5: Commit.**

```bash
git add admin/playwright.config.ts admin/e2e admin/scripts/verify-frontend-gates.mjs admin/package.json admin/tsconfig.app.json admin/dist scripts/prepare-e2e.sh serve/tests/Support/E2ETestSeeder.php
git commit -m "test(admin): add non-payment browser acceptance gates"
```

## Final self-review before execution handoff

- [ ] API 清单已覆盖配置、认证、验证码、注册/找回/改密、素材、公告、域名、小程序池、用户、会员权益、邀请和六类链接；每个前端请求都有路由比对测试。
- [ ] 404 对齐和支付排除已明确；`link-renewal`、支付/佣金/代理相邻入口不再有可点击 UI 或调用。
- [ ] `/config` 失败仍挂载；登录/退出使用真实 token 字符串且只清理本应用键。
- [ ] 六类链接都有创建、编辑、启停、删除、详情、分享和成功后 GET 回读，状态字段使用 `manual_status/health_status/effective_status`。
- [ ] `BrandConfig` 贯穿管理端、登录注册、分享壳和小程序；旧品牌扫描覆盖源码与构建产物并保留许可证/NOTICE。
- [ ] `postMessage` 同时校验 `event.origin`、`event.source`、iframe 目标和最终 URL 协议/域名。
- [ ] `news.vue` 使用 `code + visitor_token`，不接收/转发 `device_uid`；DCloud CLI 版本、构建命令和微信开发者工具产物契约已写成可执行命令。
- [ ] 所有测试步骤都有红/绿命令、预期输出和独立 commit；部署与真实外部平台凭据验收明确留给主代理/用户控制的外部门槛。
