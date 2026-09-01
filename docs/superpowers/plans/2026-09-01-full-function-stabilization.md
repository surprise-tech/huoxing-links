# 非支付全功能稳定化总 Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** 在不实现任何支付能力的前提下，让仓库内全部现有非支付功能完成可重复安装、自动化测试、管理端操作、六类真实跳转、白标、部署和设备验收。

**Architecture:** Laravel 是业务事实源，会员/额度、访问策略、六类目标解析分别形成独立服务；Vue 管理端、跳转壳和 uni-app 只消费稳定契约。实施拆成四份可独立审查的子计划，最终通过同一发布检查和测试域名一次性交付。

**Tech Stack:** PHP 8.3/Laravel 13/Sanctum 4/MySQL 8/Redis、Vue 3.5/Vite 7/TypeScript 5.9/Pinia 3、Vitest 4.1.10/Vue Test Utils 2.5.0/Playwright 1.62.1、uni-app Vue2 CLI 2.0.2-5020420260813001、Nginx/systemd/cron。

**Spec:** `docs/superpowers/specs/2026-09-01-full-function-stabilization-design.md`

## Global Constraints

- 第一阶段一次性交付全部非支付功能，但任务必须按依赖顺序和独立测试 gate 执行。
- 支付价格、购买、回调、退款、佣金、自动扣款和财务对账均不实现；现有入口必须隐藏或稳定禁用且无状态变更。
- 一个用户即一个租户；所有普通资源按 `user_id` 隔离，管理员跨租户操作审计。
- 链接永久保留；会员到期暂停，重新开通恢复；无链接续期接口。
- UV 为账号级滚动月、所有链接合计，窗口 `[period_start, period_end)`，时区 `Asia/Shanghai`。
- 用户界面、构建产物和营销文案不得出现旧品牌；许可证和必要 NOTICE 必须保留。
- `admin/dist` 作为当前仓库既有的预构建部署产物继续跟踪，但只能由锁定依赖的 `pnpm build-only` 生成；禁止手改，最终 CI 必须重建并验证 `git diff --exit-code -- admin/dist`。
- 真实外部平台、DNS、HTTPS、服务器和手机证据不得用 mock、HTTP 200 或代码构建冒充。
- 不提交 `.env`、AppID、Secret、短信密钥、服务器地址、真实测试链接或客户数据。
- 不在 EOL 的 PHP 8.1/Laravel 10 或含未处置 high/critical 公告的锁文件上部署；依赖升级与审计是功能实现前置 gate。
- 每个行为变更先写失败测试；每个任务只暂存自己的文件并形成独立 commit。

---

## File Structure Map

```text
serve/app/Data|DTO/                 immutable inputs and outputs
serve/app/Contracts/                resolver contracts
serve/app/Services/                 membership, entitlement, usage, brand and security services
serve/app/Services/Resolvers/       six type-specific target resolvers
serve/app/Models/                   new audit and usage records
serve/database/migrations/          forward-only schema completion
serve/database/seeders/             idempotent non-secret initialization
serve/tests/{Unit,Feature,Integration}/
admin/src/{api,components,stores,types}/
admin/e2e/                           Playwright user journeys
mini_programs/tests/                 Node built-in behavior tests
deploy/{config,nginx,systemd,cron,scripts,tests}/
scripts/                             API, brand and full release gates
docs/{deployment,acceptance}/        operator instructions and evidence
```

## Reuse and Dependency Decisions

| Need | Candidates checked | Decision | Version / license / reason |
| --- | --- | --- | --- |
| External HTTP | Laravel HTTP Client, existing Guzzle, Symfony HttpClient | Laravel HTTP Client | Provided by Laravel 13 (MIT), supports `Http::fake`, timeout and retry; no new production dependency. Guzzle remains underneath; Symfony is unnecessary duplication. |
| Backend runtime | Keep Laravel 10/PHP 8.1, Laravel 12/PHP 8.2, Laravel 13/PHP 8.3 | Laravel 13 + PHP 8.3 | Laravel 10 and PHP 8.1 are EOL; Laravel 12 security support ends 2027-02. Laravel 13 supports PHP 8.3–8.5 through Q1 2028. Upgrade after isolating tests and replace incompatible `mews/captcha`. |
| Image captcha | `mews/captcha`, Gregwar Captcha, custom GD code | Gregwar Captcha 2.1.1 | `mews/captcha` does not support Laravel 13; Gregwar is active, MIT, framework-independent and supports hashed one-time answers. Custom GD code adds unnecessary security/maintenance surface. |
| SMS delivery | Existing custom `AliDySms`, existing EasySMS, Alibaba SDK | EasySMS 3.2.1 Aliyun gateway | Already a production dependency, active MIT project, supports PHP 8.3. Version 3.3 requires PHP 8.4, so pin the compatible 3.2 line; delete custom signing code. |
| Admin build | Keep Vite 4, Vite 7, Vite 8 | Vite 7.3.6 + Vue 3.5.42 | Vite 4 lock contains known vulnerable transitive packages; Vite 7 is stable with Node 24 and has lower migration risk than the newer Vite 8 bundler transition. |
| Vue unit tests | Vitest, Jest, bare Vue Test Utils | Vitest + Vue Test Utils | Upgrade Vite first, then pin Vitest `4.1.10` (MIT) and Vue Test Utils `2.5.0` (MIT). Jest `30.5.1` is maintained but duplicates Vite transforms. |
| Browser E2E | Playwright, Cypress, Selenium | Playwright | Pin `@playwright/test 1.62.1` (Apache-2.0); current, headless CI friendly, multi-browser. Cypress `15.21.1` (MIT) is maintained but heavier and single-browser-flow oriented. |
| uni-app build | Manual HBuilderX, HBuilderX CLI, official Vue2 CLI packages | Official Vue2 CLI | Pin DCloud packages `2.0.2-5020420260813001` (Apache-2.0) and use `cross-env NODE_ENV=production UNI_PLATFORM=mp-weixin vue-cli-service uni-build`; output `dist/build/mp-weixin`. HBuilderX remains only for final upload/preview. |
| UV atomicity | Redis Set/Lua, database lock+unique visitor rows, approximate log aggregation | Database lock + unique rows | MySQL is already required; correctness survives Redis loss and concurrency. Redis stays dedicated to QR rotation counters. |
| TLS | Manual certificate upload, acme.sh, Certbot | Certbot webroot | Certbot is the documented automated path; certificate files live outside Git and renewal can be dry-run verified. No TLS private key enters the repository. |

Official references used by executors:

- Laravel: `https://github.com/laravel/framework`
- EasyWeChat: `https://github.com/w7corp/easywechat`
- uni-app Vue2: `https://github.com/dcloudio/uni-app` and `https://uniapp.dcloud.net.cn/quickstart-cli`
- Vitest: `https://github.com/vitest-dev/vitest`
- Vue Test Utils: `https://github.com/vuejs/test-utils`
- Playwright: `https://github.com/microsoft/playwright`

## Subplans and Dependency Order

1. `docs/superpowers/plans/2026-09-01-foundation-membership.md`
2. `docs/superpowers/plans/2026-09-01-link-resolution.md`
3. `docs/superpowers/plans/2026-09-01-frontend-mini-white-label.md`
4. `docs/superpowers/plans/2026-09-01-deployment-acceptance.md`

### Task 0: Capture baseline and create reusable research record

**Files:**
- Create: `docs/engineering/reuse-decisions.md`
- Create: `docs/compliance/license-inventory.md`
- Modify: none
- Test: repository/remote/secret preflight commands

**Interfaces:**
- Consumes: current branch, fork `origin`, upstream `surprise-tech/huoxing-links`, approved spec.
- Produces: immutable baseline SHA and dependency adoption/rejection log used by all subplans.

- [ ] **Step 1: Verify clean branch and expected remotes**

Run:

```bash
git status -sb
git branch --show-current
git rev-parse HEAD
git remote -v
```

Expected: branch `codex/full-function-stabilization`, clean worktree, `origin` is the fork and `upstream` is the source repository.

- [ ] **Step 2: Write the reuse record with exact fields**

Each candidate row must contain:

```markdown
| Capability | Candidate | URL | Version/commit | License | Maintenance signal | Adopt/reject reason |
```

Record the six decisions above plus rejected alternatives; never include private code, domains, credentials or test data.

`license-inventory.md` must record the root Apache-2.0 license, the inconsistent `serve/composer.json` MIT metadata, every Composer/npm production dependency license, required NOTICE text and a release decision: do not delete or relabel upstream copyright; resolve the root/composer metadata mismatch with the repository owner before commercial launch. This legal/commercial gate is separate from code correctness and may remain `外部阻塞`, but it cannot be omitted from the release report.

- [ ] **Step 3: Validate dependency compatibility without installing**

Run:

```bash
npm view vitest@4.1.10 version license engines peerDependencies --json
npm view @vue/test-utils@2.5.0 version license peerDependencies --json
npm view @playwright/test@1.62.1 version license engines --json
npm view @dcloudio/uni-mp-weixin@2.0.2-5020420260813001 version license --json
npm view vite@7.3.6 version license engines peerDependencies --json
npm view vue@3.5.42 version license --json
composer validate --working-dir=serve --no-check-publish
composer licenses --working-dir=serve --format=json
```

Expected: npm metadata matches the table; Composer currently reports the known lock mismatch, which foundation Task 1 must resolve before its green commit.

- [ ] **Step 4: Commit**

```bash
git add docs/engineering/reuse-decisions.md docs/compliance/license-inventory.md
git commit -m "docs: record reuse and dependency decisions"
```

### Task 1: Execute foundation, membership and usage plan

**Files:**
- Plan: `docs/superpowers/plans/2026-09-01-foundation-membership.md`
- Produces: safe test harness, complete migrations/seeders, authentication/referrals, membership/entitlement/usage services and idempotent scheduler.

**Interfaces:**
- Consumes: Task 0 dependency decisions.
- Produces: `MembershipService`, `EntitlementService`, `RollingPeriod`, `UsageMeter`, safe User/VIP/usage schema.

- [ ] **Step 1: Execute every checkbox in the foundation plan in order**

Run after each task: use the exact targeted PHPUnit command recorded in that task.

Expected: every red test fails for the stated reason before implementation and passes afterward.

- [ ] **Step 2: Run foundation aggregate gate**

```bash
docker compose -f compose.test.yaml up -d --wait
serve/bin/test-env php artisan migrate:fresh --seed
serve/bin/test-env php artisan db:seed
serve/bin/test-env php artisan test
composer --working-dir=serve validate --no-check-publish
composer --working-dir=serve audit --locked --ignore-severity=low --ignore-severity=medium --abandoned=fail --no-interaction
composer --working-dir=serve show laravel/framework
composer --working-dir=serve show laravel/sanctum
composer --working-dir=serve show phpunit/phpunit
```

Expected: migrations and repeated seed are idempotent; no real mail/token output; all tests PASS; Composer lock matches composer.json；Laravel 13/Sanctum 4/PHPUnit 12 are selected and no unhandled high/critical Composer advisory remains.

- [ ] **Step 3: Verify no tracked secret/default credential remains**

```bash
git ls-files | rg '(^|/)(\.env($|\.)|.*\.(pem|key|p12|pfx))' | rg -v '\.env(\.[^.]+)*\.example$' && exit 1 || true
git ls-files 'serve/vendor/**' | grep . && exit 1 || true
rg -n 'admin123|open\.mayilink\.cn|APP_KEY=base64:' README.md serve admin jump mini_programs -g '!serve/phpunit.xml' -g '!serve/bin/test-env' && exit 1 || true
```

Expected: no matches outside explicit safe examples with empty/sample-only values.

### Task 2: Execute link access and six resolver plan

**Files:**
- Plan: `docs/superpowers/plans/2026-09-01-link-resolution.md`
- Produces: permanent link state, canonical share URL, tenant policy, visitor identity/token, account UV, QR rotation, safe external clients and six resolvers.

**Interfaces:**
- Consumes: Task 1 `EntitlementService`, `RollingPeriod`, `UsageMeter` and schema.
- Produces: `LinkAccessPolicy`, `LinkResolutionCoordinator`, `TargetResult` and stable public API error codes.

- [ ] **Step 1: Execute every checkbox in the link plan in order**

Expected: each resolver has success/invalid/timeout/structure/protocol tests; rejected requests never write access logs.

- [ ] **Step 2: Run link aggregate gate**

```bash
docker compose -f compose.test.yaml up -d --wait
serve/bin/test-env php artisan test tests/Unit/DTO tests/Unit/Services tests/Feature/LinkResolutionTest.php tests/Feature/LinkCrudTest.php tests/Feature/LinkHealthCheckTest.php tests/Feature/Security
serve/bin/test-env php artisan route:list --path=api
```

Expected: all PASS; all six types return non-empty canonical share URLs; ordinary mini-program responses do not carry visitor identity; logs/cache contain no Secret.

### Task 3: Execute management, white-label, jump and mini-program plan

**Files:**
- Plan: `docs/superpowers/plans/2026-09-01-frontend-mini-white-label.md`
- Produces: aligned API inventory, recoverable bootstrap, safe login/logout, no payment surfaces, six CRUD journeys, BrandConfig, secure postMessage and reproducible mp-weixin build.

**Interfaces:**
- Consumes: Tasks 1–2 API DTOs, routes and error codes.
- Produces: production admin build, `dist/build/mp-weixin`, Playwright test suite and brand/API scan commands.

- [ ] **Step 1: Execute every checkbox in the frontend plan in order**

Expected: unit and E2E tests demonstrate each UI behavior; no dead API call or hidden 404 remains.

- [ ] **Step 2: Run frontend aggregate gate**

```bash
cd admin
pnpm install --frozen-lockfile
pnpm type-check
pnpm test:unit
pnpm build
pnpm audit --prod --audit-level high
pnpm test:e2e
cd ../mini_programs
pnpm install --frozen-lockfile
pnpm audit --prod --audit-level high
pnpm test
pnpm build:mp-weixin
test -f dist/build/mp-weixin/pages/views/tools/news.js
```

Expected: all PASS; source and artifacts contain no old brand/default credentials; landing mini program only sends `code/visitor_token`.

### Task 4: Execute deployment and acceptance plan

**Files:**
- Plan: `docs/superpowers/plans/2026-09-01-deployment-acceptance.md`
- Produces: server audit, Nginx/TLS/queue/scheduler, atomic releases, backup/rollback, CI and evidence matrix.

**Interfaces:**
- Consumes: a commit that passed Tasks 1–3 and the user-controlled ECS/external credentials.
- Produces: test-domain deployment plus separate code/runtime/API/browser/device/external verdicts.

- [ ] **Step 1: Execute deployment Tasks 1–4 locally and in CI**

Expected: shell tests, Nginx template tests, CI workflow and `scripts/check-release.sh` PASS without touching production.

- [ ] **Step 2: Execute deployment Task 5 on the approved ECS**

Expected: pre-change backup exists, Nginx/HTTPS/services pass route-specific health, current symlink resolves to target SHA, rollback rehearsal succeeds.

- [ ] **Step 3: Record browser, phone and external gates independently**

Expected: Android/iOS and each of six link types have a real evidence row; missing credentials are `外部阻塞`, never PASS.

### Task 5: Final non-payment release gate and handoff

**Files:**
- Modify: `docs/acceptance/full-function-evidence.md`
- Modify: `docs/acceptance/device-matrix.md`
- Modify: `README.md`

**Interfaces:**
- Consumes: all subplan commits and evidence.
- Produces: one release candidate SHA and explicit residual blockers.

- [ ] **Step 1: Run the full local release gate for the release candidate**

Run: `bash scripts/check-release.sh`

Expected: PASS with no dirty tracked files.

- [ ] **Step 2: Verify payment surfaces cannot mutate state**

Run: `docker compose -f compose.test.yaml up -d --wait && serve/bin/test-env php artisan test tests/Feature/PaymentDisabledTest.php`

Expected: PASS; payment and VIP audit row counts do not change.

- [ ] **Step 3: Commit the completed evidence before minting the final SHA**

Update `docs/acceptance/full-function-evidence.md`, `docs/acceptance/device-matrix.md` and `README.md` with the exact commands/results already observed. External credentials or device rows that have not actually passed stay marked `外部阻塞`.

```bash
git add docs/acceptance README.md
git diff --cached --check
git commit -m "docs: finalize non-payment release evidence"
```

- [ ] **Step 4: Re-run the gate on the final clean SHA**

Run: `test -z "$(git status --porcelain)" && bash scripts/check-release.sh`

Expected: PASS on the exact documentation-inclusive SHA that will be pushed; working tree remains clean.

- [ ] **Step 5: Verify remote branch contains the tested SHA**

```bash
tested_sha="$(git rev-parse HEAD)"
git push origin codex/full-function-stabilization
remote_sha="$(git ls-remote origin refs/heads/codex/full-function-stabilization | awk '{print $1}')"
test "$tested_sha" = "$remote_sha"
```

Expected: local and remote SHA match exactly.

- [ ] **Step 6: Update Draft PR with evidence and unresolved external gates**

PR must list changed files, why, impact, exact commands/results, tested SHA, deployment URL, rollback evidence and every `外部阻塞`; never claim real platform success from mocks.
