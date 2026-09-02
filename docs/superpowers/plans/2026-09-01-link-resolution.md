# 链接解析与访问稳定化实施计划

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** 在不实现支付的前提下，把六种链接的永久记录、租户安全、访客识别、账号级 UV、二维码原子轮换、外部目标解析和两个公开 API 收敛为可测试、可审计的稳定链路。

**Architecture:** `LinkResolutionCoordinator` 是唯一的公开跳转编排入口，先调用 `LinkAccessPolicy` 做链接/用户/会员/权益校验，再调用基础计划提供的 `UsageMeter::consume` 原子占用账号 UV，最后把已验证配置交给六个 `TargetResolver` 之一。`QrRotationService` 只处理个人微信二维码选择与单码额度；`SafeHttpClient` 负责所有外部 HTTP、allowlist、SSRF 和重定向检查。响应、缓存和访问日志只保存脱敏后的 `TargetResult`，不保存 Secret、原始 IP、原始 User-Agent 或客户端自报 `device_uid`。

**Tech Stack:** PHP 8.3、Laravel 13、Eloquent/MySQL、Redis（Lua `EVAL`）、Laravel HTTP Client/Guzzle 7、Sanctum 4、PHPUnit 12、Vue 3/uni-app。

**Spec:** `docs/superpowers/specs/2026-09-01-full-function-stabilization-design.md`

## Global Constraints

- 六种固定映射：`MINI_PROGRAM=1`、`KING_DOC=2`、`CLI_QR=3`、`WORK_WECHAT=4`、`LANDING_MINI=5`、`QR_QQ=11`；任何未知类型返回 `LINK_TYPE_UNSUPPORTED`。
- 链接记录永久保留，只有人工 `DELETE /links/{id}` 删除；`expired_at` 不再参与访问判断，会员到期只暂停访问，重新开通后恢复。
- 链接状态分为 `manual_status`（用户/管理员写）和 `health_status`（健康任务写），`effective_status` 由状态和会员实时计算；健康任务不得重新启用人工停用链接。
- 分享地址只由服务端生成：已选择且启用、在 `ALLOWED_SHARE_HOSTS` 中的域名优先，否则 `PUBLIC_ORIGIN`；格式必须是 `{origin}/?code={code}`，六类均非空。
- `config.min_id` 只能引用当前用户自己的小程序，或管理员标记为官方池且当前套餐允许的小程序；跨租户引用稳定返回 `MINI_PROGRAM_FORBIDDEN`，管理员跨租户操作写审计。
- 服务端签发 `visitor_id` Cookie（`Secure`、`HttpOnly`、`SameSite=Lax`、当前分享域名、一年）；客户端 `device_uid` 永远不是额度凭据。
- 只有 `LANDING_MINI` 在首次 `link-target` 成功解析后签发十分钟 Laravel Encrypter `aes-256-gcm` `visitor_token`；密钥来自独立的 base64 `APP_VISITOR_TOKEN_KEY`；`MINI_PROGRAM` 不携带 token，不把访客身份传给客户小程序；`link-show-qr` 用 token 取回首次选择的二维码且不重复计账号 UV。
- 账号 UV、滚动月周期和原子去重由基础计划提供的 `RollingPeriod::forAnchor`、`EntitlementService::resolve/assertActive`、`UsageMeter::consume` 负责；链接计划只传入用户、服务端签发的原始 `visitor_id` 和 `Asia/Shanghai` 时间，不重建其表、模型或测试环境。
- 单码二维码的选择和计数占用必须在一次 Redis Lua/等价原子操作中完成；过期读取每项 `expired_at`；自然日键保留 48 小时，累计键保留到链接人工删除。
- 外部 HTTP 只能经 `SafeHttpClient`；连接/响应超时、有限重试、HTTPS allowlist、私网/环回/链路本地/保留地址拒绝，最多跟随 3 次且每跳重新校验。
- Secret 仅加密保存；写入接口只接受 Secret、不回显明文；API、缓存、`link_visit_logs`、异常消息、前端状态和构建产物不得包含明文凭据。
- 两个公开入口固定为 `GET /api/link-target/{code}` 和 `GET /api/link-show-qr/{code}`；拒绝请求不写访问日志，错误码必须稳定且不能只凭 HTTP 200 判定成功。
- 每个任务遵循 TDD：先写失败测试、运行红灯、写最小实现、运行绿灯、再运行相关回归；每个任务末尾只暂存本任务文件并创建一个独立 commit。执行者不得把支付入口或支付表引用重新引入本链路。

## File Map

| 文件 | 责任 |
| --- | --- |
| `serve/database/migrations/2026_09_01_000101_link_resolution_stability.php` | `links` 状态迁移与 `link_visit_logs` 脱敏字段/索引；排在基础计划 `000001–000005` 之后，UV 表和迁移由基础计划负责 |
| `serve/database/migrations/2026_09_01_000102_link_health_check.php` | 健康检查时间与稳定错误字段；不改变人工状态 |
| `serve/tests/Concerns/CreatesLinkFixtures.php` | 所有链接测试共用的、可重复的用户/套餐/小程序/六类型链接 fixture，不创建 UV 表或计量模型 |
| `serve/app/Models/Link.php`、`LinkVisitLog.php` | 人工/健康状态、配置和脱敏访问日志模型；UV 模型由基础计划负责 |
| `serve/app/Support/LinkError.php`、`serve/app/DTO/TargetResult.php`、`VisitorTokenPayload.php` | 稳定错误码和不可变解析/签名 DTO |
| `serve/app/DTO/AccessDecision.php`、`VisitorIdentity.php`、`VisitorContext.php`、`QrSelection.php` | 链接解析跨服务共享的只读值对象；权益与 UV 决策值对象由基础计划负责 |
| `serve/app/DTO/CoordinatorResponse.php` | 控制器适配用的 JSON 状态/Cookie 响应值对象 |
| `serve/app/Exceptions/LinkResolutionException.php`、`MiniProgramForbidden.php`、`InvalidVisitorToken.php`、`UnsafeUrl.php`、`QrUnavailable.php` | 可映射到稳定错误码的领域异常 |
| `serve/app/Services/LinkAccessPolicy.php`、`MiniProgramReferencePolicy.php`、`LinkShareUrl.php` | 消费基础计划权益服务的访问门禁、`min_id` 租户校验和 canonical 分享地址 |
| `serve/config/app.php`、`serve/.env.example` | `PUBLIC_ORIGIN`、`ALLOWED_SHARE_HOSTS` 和测试/生产密钥的无密钥配置入口 |
| `serve/app/Services/VisitorIdentityService.php`、`VisitorTokenService.php`、`LandingSelectionStore.php` | Cookie、AES-GCM 加密 token 和首次二维码选择短缓存；账号 UV 仅调用基础计划 `UsageMeter` |
| `serve/app/Services/QrRotationService.php` | 顺序/随机二维码过滤、Redis Lua 原子计数和自然日/累计键 |
| `serve/app/Services/Health/*`、`serve/app/Console/Commands/ChkLinkStatus.php`、`serve/app/Console/Kernel.php` | 六类型轻量健康检查、稳定健康错误和每十分钟无重叠调度；只写健康状态 |
| `serve/app/Services/Http/SafeHttpClient.php`、`UrlPolicy.php` | 外部 HTTP 超时、重试、allowlist、SSRF、重定向和响应结构校验 |
| `serve/app/Services/Resolvers/TargetResolver.php`、`MiniProgramTargetResolver.php`、`KingDocTargetResolver.php`、`CliQrTargetResolver.php`、`WorkWechatTargetResolver.php`、`LandingMiniTargetResolver.php`、`QqQrTargetResolver.php` | 六种类型的目标生成，禁止访问策略和日志逻辑渗入处理器 |
| `serve/app/Services/LinkResolutionCoordinator.php` | 两个公开 API 共用的校验、UV、缓存、resolver、token、日志和 Cookie 编排 |
| `serve/app/Http/Controllers/Api/JumpController.php`、`LinkController.php`、`serve/routes/api.php` | 两个公开方法和链接 CRUD/状态/分享契约的接线 |
| `serve/tests/Unit/Services/*`、`serve/tests/Unit/DTO/*`、`serve/tests/Feature/LinkResolutionTest.php`、`serve/tests/Feature/LinkCrudTest.php`、`serve/tests/Feature/Security/LinkSecretTest.php` | 单元、功能、并发/安全和六类型回归测试 |

### Task 1: 建立永久链接状态与访问日志脱敏基础

**Files:**
- Create: `serve/database/migrations/2026_09_01_000101_link_resolution_stability.php`
- Modify: `serve/app/Models/Link.php`
- Modify: `serve/app/Models/LinkVisitLog.php`
- Test: `serve/tests/Feature/LinkResolutionTest.php`

**Interfaces:**
- Consumes: 现有 `links.status/expired_at`、`link_visit_logs.ip/device_uid/cache`。
- Produces: `links.manual_status/health_status`、`link_visit_logs.visitor_hash/ip_hash/user_agent_hash`；UV 周期、访客表和测试隔离由基础计划提供。

- [ ] **Step 1: Write the failing migration/model tests**

```php
public function test_schema_keeps_links_without_an_expiry_gate_and_sanitizes_visit_logs(): void
{
    $this->assertTrue(Schema::hasColumns('links', ['manual_status', 'health_status']));
    $this->assertTrue(Schema::hasColumns('link_visit_logs', ['visitor_hash', 'ip_hash', 'user_agent_hash']));
    $this->assertSame(0, DB::table('links')->whereNotNull('expired_at')->count());
}
```

- [ ] **Step 2: Run the test to verify it fails**

Run: `docker compose -f compose.test.yaml up -d --wait && serve/bin/test-env php artisan test tests/Feature/LinkResolutionTest.php --filter=schema_keeps_links -v`

Expected: FAIL because the new link status and sanitized log columns do not exist.

- [ ] **Step 3: Write the migration and models**

Use a forward-compatible migration: copy `status` into `manual_status`, default `health_status` to `1`, set every existing `expired_at` to `NULL`, then stop using it for access. Add indexes on `(user_id, manual_status)`, `(link_id, created_at)` and `(link_id, visitor_hash)`. Do not create or alter any UV tables in this migration and do not drop legacy columns.

```php
Schema::table('links', function (Blueprint $table): void {
    $table->unsignedTinyInteger('manual_status')->default(1)->after('status');
    $table->unsignedTinyInteger('health_status')->default(1)->after('manual_status');
    $table->index(['user_id', 'manual_status']);
});
DB::table('links')->update(['manual_status' => DB::raw('status'), 'expired_at' => null]);
Schema::table('link_visit_logs', function (Blueprint $table): void {
    $table->char('visitor_hash', 64)->nullable()->after('device_uid');
    $table->char('ip_hash', 64)->nullable()->after('visitor_hash');
    $table->char('user_agent_hash', 64)->nullable()->after('ip_hash');
    $table->index(['link_id', 'visitor_hash']);
});
```

The `Link` cast exposes `manual_status` and `health_status` as booleans; `LinkVisitLog::$guarded` accepts only `visitor_hash`, `ip_hash`, `user_agent_hash`, link/user IDs and sanitized public cache fields. The existing `status` and `expired_at` columns remain only for forward-compatible data reads; no request path may use `expired_at` as an access gate.

- [ ] **Step 4: Run the test to verify it passes**

Run: `docker compose -f compose.test.yaml up -d --wait && serve/bin/test-env php artisan migrate:fresh && serve/bin/test-env php artisan test tests/Feature/LinkResolutionTest.php --filter=schema_keeps_links_without_an_expiry_gate -v`

Expected: PASS; migration creates only link status/log sanitization columns and indexes, and no link is rejected merely because `expired_at` is null.

- [ ] **Step 5: Commit**

```bash
cd serve && git add database/migrations/2026_09_01_000101_link_resolution_stability.php app/Models/Link.php app/Models/LinkVisitLog.php tests/Feature/LinkResolutionTest.php && git commit -m "feat: add permanent link state and sanitized visit logs"
```

### Task 2: 定义稳定错误、解析 DTO、实时访问策略与 canonical 分享地址

**Files:**
- Create: `serve/app/Support/LinkError.php`
- Create: `serve/app/DTO/TargetResult.php`
- Create: `serve/app/DTO/VisitorTokenPayload.php`
- Create: `serve/app/DTO/AccessDecision.php`
- Create: `serve/app/Exceptions/LinkResolutionException.php`
- Create: `serve/app/Exceptions/MiniProgramForbidden.php`
- Create: `serve/app/Services/LinkAccessPolicy.php`
- Create: `serve/app/Services/MiniProgramReferencePolicy.php`
- Create: `serve/app/Services/LinkShareUrl.php`
- Create: `serve/tests/Concerns/CreatesLinkFixtures.php`
- Modify: `serve/config/app.php`
- Modify: `serve/.env.example`
- Modify: `serve/app/Http/Controllers/Api/LinkController.php`
- Test: `serve/tests/Unit/DTO/TargetResultTest.php`, `serve/tests/Unit/Services/LinkAccessPolicyTest.php`, `serve/tests/Feature/LinkCrudTest.php`

**Interfaces:**
- Consumes: `Link`, `User`, `VipPackage`, `Domain`, `config('app.public_origin')`, `config('app.allowed_share_hosts')`。
- Produces: `TargetResult::from(...)`、`LinkAccessPolicy::check(?Link $link, CarbonImmutable $at): AccessDecision`、`MiniProgramReferencePolicy::assertAllowed(User $actor, int $miniId): MiniProgram`、`LinkShareUrl::for(Link $link): string`；`LinkAccessPolicy` 只消费基础计划的 `EntitlementService::resolve/assertActive`。

- [ ] **Step 1: Write failing policy/DTO/CRUD tests**

```php
public function test_share_url_falls_back_to_public_origin_and_is_non_empty_for_a_mini_link(): void
{
    config(['app.public_origin' => 'https://short.example', 'app.allowed_share_hosts' => ['short.example']]);
    $link = $this->miniProgramLink();
    $this->assertSame('https://short.example/?code='.$link->code, app(LinkShareUrl::class)->for($link));
}

public function test_expired_at_does_not_disable_a_link_but_manual_status_does(): void
{
    $link = $this->landingLinkWithQrs([]);
    $this->assertTrue(app(LinkAccessPolicy::class)->check($link, now())->allowed);
    $link->update(['manual_status' => 0]);
    $this->assertSame(LinkError::LINK_DISABLED, app(LinkAccessPolicy::class)->check($link->fresh(), now())->errorCode);
}

public function test_status_endpoint_accepts_only_boolean_manual_status(): void
{
    $link = $this->landingLinkWithQrs([]);
    $this->actingAs($link->user, 'api')->patchJson('/api/links/'.$link->id.'/status', ['manual_status' => false])
        ->assertOk()->assertJsonPath('data.manual_status', false);
    $this->actingAs($link->user, 'api')->patchJson('/api/links/'.$link->id.'/status', ['manual_status' => 'false'])
        ->assertStatus(422)->assertJsonPath('code', 'VALIDATION_ERROR');
}

public function test_cross_tenant_min_id_is_rejected(): void
{
    $owner = $this->activeMemberWithUvLimit(100);
    $other = $this->activeMemberWithUvLimit(100);
    $mini = MiniProgram::query()->create([
        'user_id' => $other->id, 'name' => 'Other mini', 'app_id' => 'wxother1234567890', 'secret' => 'test-secret',
        'url' => 'pages/index/index', 'type' => MiniType::OWN, 'is_pre_min' => false, 'is_enable' => true,
    ]);
    $this->expectException(MiniProgramForbidden::class);
    app(MiniProgramReferencePolicy::class)->assertAllowed($owner, $mini->id);
}

public function test_share_origin_rejects_non_https_userinfo_query_fragment_non443_port_and_path(): void
{
    config(['app.public_origin' => 'https://short.example', 'app.allowed_share_hosts' => ['short.example']]);
    $link = $this->landingLinkWithQrs([]);
    foreach (['http://short.example', 'https://user:pass@short.example', 'https://short.example/?x=1', 'https://short.example/#frag', 'https://short.example:8443', 'https://short.example/path'] as $bad) {
        config(['app.public_origin' => $bad]);
        try {
            app(LinkShareUrl::class)->for($link);
            $this->fail('invalid origin was accepted: '.$bad);
        } catch (LinkResolutionException) {
            $this->addToAssertionCount(1);
        }
    }
}
```

- [ ] **Step 2: Run tests to verify they fail**

Run: `docker compose -f compose.test.yaml up -d --wait && serve/bin/test-env php artisan test tests/Unit/DTO/TargetResultTest.php tests/Unit/Services/LinkAccessPolicyTest.php tests/Feature/LinkCrudTest.php -v`

Expected: FAIL because the services, DTO and status fields are not defined and `LinkController::show` still returns an empty/non-canonical share URL.

- [ ] **Step 3: Implement the exact contracts**

```php
final class LinkError
{
    public const LINK_NOT_FOUND = 'LINK_NOT_FOUND';
    public const LINK_DISABLED = 'LINK_DISABLED';
    public const USER_DISABLED = 'USER_DISABLED';
    public const MEMBERSHIP_EXPIRED = 'MEMBERSHIP_EXPIRED';
    public const QUOTA_EXCEEDED = 'QUOTA_EXCEEDED';
    public const MINI_PROGRAM_FORBIDDEN = 'MINI_PROGRAM_FORBIDDEN';
    public const SHARE_ORIGIN_UNAVAILABLE = 'SHARE_ORIGIN_UNAVAILABLE';
    public const LINK_TYPE_UNSUPPORTED = 'LINK_TYPE_UNSUPPORTED';
    public const VISITOR_TOKEN_INVALID = 'VISITOR_TOKEN_INVALID';
}

final readonly class TargetResult
{
    public function __construct(
        public string $title,
        public string $description,
        public ?string $icon,
        public string $target,
        public ?array $qr = null,
        public ?string $visitorToken = null,
    ) {}
}

final readonly class AccessDecision
{
    public function __construct(public bool $allowed, public ?string $errorCode = null) {}
}

class LinkResolutionException extends RuntimeException
{
    public function __construct(public readonly string $errorCode, string $message = '')
    { parent::__construct($message ?: $errorCode); }
}

final class MiniProgramForbidden extends LinkResolutionException {}

public function for(Link $link): string
{
    $selected = Domain::query()->find(data_get($link->config, 'domain_id'));
    $origin = null;
    if ($selected && $selected->enable) {
        $origin = $this->validatedOrigin($selected->url);
    }
    $origin ??= $this->validatedOrigin((string) config('app.public_origin'));
    if ($origin === null) {
        throw new LinkResolutionException(LinkError::SHARE_ORIGIN_UNAVAILABLE);
    }
    return $origin.'/?code='.$link->code;
}

private function validatedOrigin(string $raw): ?string
{
    $parts = parse_url($raw);
    $host = isset($parts['host']) ? strtolower($parts['host']) : null;
    $port = $parts['port'] ?? null;
    $allowedHosts = array_map('strtolower', config('app.allowed_share_hosts', []));
    if (strtolower((string) ($parts['scheme'] ?? '')) !== 'https' || $host === null || isset($parts['user'], $parts['pass'], $parts['query'], $parts['fragment']) || ($port !== null && $port !== 443) || (($parts['path'] ?? '') !== '' && ($parts['path'] ?? '') !== '/') || ! in_array($host, $allowedHosts, true)) {
        return null;
    }
    return 'https://'.$host;
}
```

`LinkAccessPolicy` must call the foundation-owned `EntitlementService::resolve($user, $at)` and `EntitlementService::assertActive($user, $at)` rather than calculate membership dates or quota itself. It checks, in order, link existence/`manual_status`/`health_status`, owner `status`, real-time entitlement and allowed type. It returns `LINK_DISABLED`, `USER_DISABLED` or `MEMBERSHIP_EXPIRED` without writing a log; `QUOTA_EXCEEDED` is raised only by `UsageMeter` after access policy succeeds. `RollingPeriod::forAnchor($user->start_at, $at)` remains the foundation value object’s responsibility and is consumed indirectly through the resolved entitlement. `LinkController` must validate `config.min_id` through `MiniProgramReferencePolicy`, permit only same-tenant or allowed official-pool rows, return `manual_status`, `health_status`, computed `effective_status`, and always use `LinkShareUrl` for `share_link`. `PATCH /links/{id}/status` accepts exactly JSON `{ "manual_status": true|false }`; Laravel boolean validation must reject strings/numbers with 422 `VALIDATION_ERROR`, and both backend tests and frontend callers use this body. `DELETE` is idempotent and permanently removes only on explicit user action.

Add to `serve/config/app.php`:

```php
'public_origin' => env('PUBLIC_ORIGIN', ''),
'allowed_share_hosts' => array_values(array_filter(array_map('trim', explode(',', env('ALLOWED_SHARE_HOSTS', ''))))),
'visitor_hash_key' => env('APP_VISITOR_HASH_KEY'),
'visitor_token_key' => env('APP_VISITOR_TOKEN_KEY'),
```

Add the same variable names with empty, non-secret values to `serve/.env.example`; production values are injected by the deployment environment and never committed.

`LinkShareUrl::validatedOrigin` is the only origin parser: it requires scheme `https`, no userinfo/query/fragment, port absent or `443`, path empty or `/`, and an exact (case-normalized) host member of `ALLOWED_SHARE_HOSTS`. It applies the same checks to `PUBLIC_ORIGIN`; an invalid selected domain falls back to a valid public origin, while an invalid public origin throws `SHARE_ORIGIN_UNAVAILABLE` and never emits an empty URL.

Put the link-only fixture helpers in `serve/tests/Concerns/CreatesLinkFixtures.php` and import the trait in link tests; they may create a test user/package/mini-program, but must not create or query any UV storage. The trait exposes `activeMemberWithUvLimit(int $limit)`, `linkForType(LinkType $type, array $config)`, `miniProgramLink()` and `landingLinkWithQrs(array $qrs)` so later snippets have concrete fixtures.

- [ ] **Step 4: Run tests to verify they pass**

Run: `docker compose -f compose.test.yaml up -d --wait && serve/bin/test-env php artisan test tests/Unit/DTO/TargetResultTest.php tests/Unit/Services/LinkAccessPolicyTest.php tests/Feature/LinkCrudTest.php -v`

Expected: PASS; expired records remain addressable when active, disabled records return stable errors, cross-tenant `min_id` is rejected, and all six link types receive the same canonical URL format.

- [ ] **Step 5: Commit**

```bash
cd serve && git add app/Support/LinkError.php app/DTO/TargetResult.php app/DTO/VisitorTokenPayload.php app/DTO/AccessDecision.php app/Exceptions/LinkResolutionException.php app/Exceptions/MiniProgramForbidden.php app/Services/LinkAccessPolicy.php app/Services/MiniProgramReferencePolicy.php app/Services/LinkShareUrl.php config/app.php .env.example tests/Concerns/CreatesLinkFixtures.php app/Http/Controllers/Api/LinkController.php tests/Unit/DTO/TargetResultTest.php tests/Unit/Services/LinkAccessPolicyTest.php tests/Feature/LinkCrudTest.php && git commit -m "feat: enforce link access and canonical share contracts"
```

### Task 3: 实现服务端访客 Cookie、LANDING token 与账号级原子 UV

**Files:**
- Create: `serve/app/Services/VisitorIdentityService.php`
- Create: `serve/app/Services/VisitorTokenService.php`
- Create: `serve/app/Services/LandingSelectionStore.php`
- Modify: `serve/app/DTO/VisitorTokenPayload.php`
- Create: `serve/app/DTO/VisitorIdentity.php`, `serve/app/DTO/VisitorContext.php`
- Create: `serve/app/Exceptions/InvalidVisitorToken.php`
- Test: `serve/tests/Unit/Services/VisitorTokenServiceTest.php`, `serve/tests/Feature/Security/VisitorIdentityTest.php`, `serve/tests/Feature/LinkResolutionTest.php`

**Interfaces:**
- Consumes: `Request`, `User`, `Link`, `TargetResult`, `UsageMeter::consume(User, string, CarbonImmutable)` and `APP_VISITOR_HASH_KEY`/`APP_VISITOR_TOKEN_KEY`；不创建或直接访问基础计划的 UV 表/模型。
- Produces: `VisitorIdentityService::resolve(Request $request): VisitorIdentity`、`VisitorIdentityService::cookie(VisitorIdentity $identity): Cookie`、`VisitorTokenService::issue(string $code, string $visitorId, CarbonImmutable $expiresAt): string`、`VisitorTokenService::verify(string $token, string $code, CarbonImmutable $at): VisitorTokenPayload`、`LandingSelectionStore::put/get`。

- [ ] **Step 1: Write failing identity/token/UV tests**

```php
public function test_cookie_is_server_issued_and_does_not_trust_device_uid(): void
{
    $identity = app(VisitorIdentityService::class)->resolve(Request::create('/api/link-target/x', 'GET', ['device_uid' => 'attacker-controlled']));
    $this->assertMatchesRegularExpression('/^[0-9a-f-]{36}$/', $identity->visitorId);
    $cookie = app(VisitorIdentityService::class)->cookie($identity);
    $this->assertTrue($cookie->isSecure());
    $this->assertTrue($cookie->isHttpOnly());
    $this->assertSame('lax', strtolower($cookie->getSameSite()));
    $this->assertNotSame('attacker-controlled', $identity->visitorId);
}

public function test_token_is_bound_to_code_and_expires_after_ten_minutes(): void
{
    $token = app(VisitorTokenService::class)->issue('abc123', 'visitor-1', now()->addMinutes(10));
    $this->assertStringNotContainsString('visitor-1', $token);
    $decodedEnvelope = base64_decode($token, true);
    if (is_string($decodedEnvelope)) {
        $this->assertStringNotContainsString('visitor-1', $decodedEnvelope);
    }
    $payload = app(VisitorTokenService::class)->verify($token, 'abc123', now()->addMinutes(9));
    $this->assertSame('visitor-1', $payload->visitorId);
    $this->expectException(InvalidVisitorToken::class);
    app(VisitorTokenService::class)->verify($token, 'other-code', now()->addMinutes(9));
}

public function test_token_tamper_and_expiry_are_rejected(): void
{
    $service = app(VisitorTokenService::class);
    $token = $service->issue('abc123', 'visitor-1', now()->addMinutes(10));
    try {
        $service->verify(substr($token, 0, -2).'xx', 'abc123', now()->addMinute());
        $this->fail('tampered token was accepted');
    } catch (InvalidVisitorToken) {
        $this->addToAssertionCount(1);
    }
    $this->expectException(InvalidVisitorToken::class);
    $service->verify($token, 'abc123', now()->addMinutes(11));
}

public function test_account_uv_uses_the_foundation_usage_meter_and_existing_visitor_survives_full_quota(): void
{
    $user = $this->activeMemberWithUvLimit(1);
    $meter = app(UsageMeter::class);
    $at = CarbonImmutable::now('Asia/Shanghai');
    $meter->consume($user, 'v1', $at);
    $meter->consume($user, 'v1', $at);
    try {
        $meter->consume($user, 'v2', $at);
        $this->fail('满额后的新访客必须被拒绝');
    } catch (\App\Exceptions\BusinessRuleException $exception) {
        $this->assertSame('QUOTA_EXCEEDED', $exception->errorCode);
    }
}
```

- [ ] **Step 2: Run tests to verify they fail**

Run: `docker compose -f compose.test.yaml up -d --wait && serve/bin/test-env php artisan test tests/Unit/Services/VisitorTokenServiceTest.php tests/Feature/Security/VisitorIdentityTest.php tests/Feature/LinkResolutionTest.php --filter=account_uv -v`

Expected: FAIL because the cookie/token services are absent and the integration path does not yet call the foundation `UsageMeter`.

- [ ] **Step 3: Implement server identity, context conversion and encrypted landing selection storage**

```php
final readonly class VisitorIdentity
{
    public function __construct(public string $visitorId, public string $hash, public bool $setCookie) {}
}

final readonly class VisitorContext
{
    public function __construct(public string $visitorId, public string $hash, public ?string $token = null) {}
    public static function fromIdentity(VisitorIdentity $identity): self { return new self($identity->visitorId, $identity->hash); }
    public static function anonymous(): self { return new self('anonymous', hash('sha256', 'anonymous'), null); }
}

final class InvalidVisitorToken extends LinkResolutionException {}
```

```php
private function encrypter(): Encrypter
{
    $key = base64_decode((string) config('app.visitor_token_key'), true);
    if ($key === false || strlen($key) !== 32) {
        throw new LinkResolutionException('VISITOR_TOKEN_KEY_INVALID');
    }
    return new Encrypter($key, 'aes-256-gcm');
}

public function issue(string $code, string $visitorId, CarbonImmutable $expiresAt): string
{
    return $this->encrypter()->encryptString(json_encode([
        'code' => $code, 'visitor_id' => $visitorId, 'exp' => $expiresAt->timestamp, 'jti' => (string) Str::uuid(),
    ], JSON_THROW_ON_ERROR));
}

public function verify(string $token, string $code, CarbonImmutable $at): VisitorTokenPayload
{
    try {
        $payload = json_decode($this->encrypter()->decryptString($token), true, 512, JSON_THROW_ON_ERROR);
        if (($payload['code'] ?? null) !== $code || ! is_string($payload['visitor_id'] ?? null) || (int) ($payload['exp'] ?? 0) < $at->timestamp) {
            throw new InvalidVisitorToken(LinkError::VISITOR_TOKEN_INVALID);
        }
        return new VisitorTokenPayload($payload['visitor_id'], $payload['code'], (int) $payload['exp'], $payload['jti']);
    } catch (Throwable) {
        throw new InvalidVisitorToken(LinkError::VISITOR_TOKEN_INVALID);
    }
}
```

`VisitorIdentityService` uses an existing valid `visitor_id` cookie only after format validation; otherwise it generates a random UUID and returns a cookie with one-year max age, `Secure`, `HttpOnly`, `SameSite=Lax`, and no broad domain. It derives a separate HMAC hash for logs, but passes the raw server-issued `$identity->visitorId` to foundation `UsageMeter`, which performs the quota hash exactly once. Hash IP/User-Agent only with independent `APP_VISITOR_HASH_KEY`, and never put raw values in a model. `VisitorTokenService` must use Laravel `Illuminate\Encryption\Encrypter` with `base64_decode(APP_VISITOR_TOKEN_KEY, true)` and `aes-256-gcm`; `issue` encrypts JSON `{code, visitor_id, exp, jti}` with `encryptString`, and `verify` decrypts it, checks code/expiry/shape and throws `InvalidVisitorToken` on authentication failure. Do not add a separate HMAC or expose a decodable payload; token text and its base64 encoding must not contain the raw visitor ID. `LandingSelectionStore` stores only a short-lived sanitized QR selection keyed by a digest of the encrypted token, TTL 600 seconds; no raw visitor ID appears in a URL. Before a successful target response, the coordinator calls `UsageMeter::consume($user, $identity->visitorId, $at)` exactly once; an already-known visitor is allowed at a full quota and a new visitor receives `QUOTA_EXCEEDED`.

- [ ] **Step 4: Run tests to verify they pass**

Run: `docker compose -f compose.test.yaml up -d --wait && serve/bin/test-env php artisan test tests/Unit/Services/VisitorTokenServiceTest.php tests/Feature/Security/VisitorIdentityTest.php tests/Feature/LinkResolutionTest.php --filter=account_uv -v`

Expected: PASS; forged/device-supplied identities are ignored, authenticated decryption rejects token tampering/code/expiry mismatch, identity-to-context conversion is explicit, and the foundation meter preserves atomic quota semantics.

- [ ] **Step 5: Commit**

```bash
cd serve && git add app/Services/VisitorIdentityService.php app/Services/VisitorTokenService.php app/Services/LandingSelectionStore.php app/DTO/VisitorTokenPayload.php app/DTO/VisitorIdentity.php app/DTO/VisitorContext.php app/Exceptions/InvalidVisitorToken.php tests/Unit/Services/VisitorTokenServiceTest.php tests/Feature/Security/VisitorIdentityTest.php tests/Feature/LinkResolutionTest.php && git commit -m "feat: add encrypted visitor landing token"
```

### Task 4: 用 Redis Lua 完成个人微信二维码过滤与原子轮换

**Files:**
- Create: `serve/app/Services/QrRotationService.php`
- Create: `serve/app/DTO/QrSelection.php`
- Create: `serve/app/Exceptions/QrUnavailable.php`
- Modify: `serve/app/Http/Controllers/Api/LinkController.php`
- Test: `serve/tests/Unit/Services/QrRotationServiceTest.php`, `serve/tests/Feature/LinkResolutionTest.php`

**Interfaces:**
- Consumes: `Link::config.wx.qr[]`（`sort/name/path/uv_limit_num/expired_at`）、`SwitchType`、`UVLimitType`、Redis。
- Produces: `QrRotationService::reserve(Link $link, CarbonImmutable $at): QrSelection`；无可用项抛出 `QrUnavailable`（错误码 `QR_UNAVAILABLE`）。

- [ ] **Step 1: Write failing rotation tests**

```php
public function test_sequence_skips_expired_and_full_codes_and_reads_each_expired_at(): void
{
    $link = $this->landingLinkWithQrs([
        ['sort' => 1, 'path' => 'old.png', 'expired_at' => now()->subMinute(), 'uv_limit_num' => 10],
        ['sort' => 2, 'path' => 'full.png', 'expired_at' => now()->addHour(), 'uv_limit_num' => 1],
        ['sort' => 3, 'path' => 'next.png', 'expired_at' => now()->addHour(), 'uv_limit_num' => 10],
    ]);
    Redis::hset('qr:link:'.$link->id.':accumulate', 2, 1);
    $this->assertSame(3, app(QrRotationService::class)->reserve($link, now())->sort);
}

public function test_atomic_reservation_never_returns_more_than_single_qr_limit(): void
{
    $link = $this->landingLinkWithQrs([['sort' => 1, 'path' => 'only.png', 'expired_at' => now()->addHour(), 'uv_limit_num' => 2]]);
    $results = collect(range(1, 5))->map(fn () => rescue(fn () => app(QrRotationService::class)->reserve($link, now())->sort, null));
    $this->assertCount(2, $results->filter());
    $this->expectException(QrUnavailable::class);
    app(QrRotationService::class)->reserve($link, now());
}

public function test_random_mode_uses_every_zero_based_candidate_slot_once_per_attempt(): void
{
    $link = $this->landingLinkWithQrs([
        ['sort' => 10, 'path' => 'a.png', 'expired_at' => now()->addHour(), 'uv_limit_num' => 1],
        ['sort' => 20, 'path' => 'b.png', 'expired_at' => now()->addHour(), 'uv_limit_num' => 1],
    ]);
    $config = $link->config;
    data_set($config, 'wx.switch_type', 2);
    $link->config = $config;
    $link->save();
    $this->assertContains(app(QrRotationService::class)->reserve($link, now())->sort, [10, 20]);
    $this->assertContains(app(QrRotationService::class)->reserve($link, now())->sort, [10, 20]);
}
```

- [ ] **Step 2: Run tests to verify they fail**

Run: `docker compose -f compose.test.yaml up -d --wait && serve/bin/test-env php artisan test tests/Unit/Services/QrRotationServiceTest.php tests/Feature/LinkResolutionTest.php --filter=rotation -v`

Expected: FAIL because the old `JumpController::getWechatNextQr` has an uninitialized `$expiredAt`, non-atomic `hgetall/hincrby`, and a lock/index race.

- [ ] **Step 3: Implement the Lua reservation and retention rules**

```php
final readonly class QrSelection
{
    public function __construct(public int $sort, public string $path, public ?string $name = null) {}
}

final class QrUnavailable extends LinkResolutionException {}

$script = <<<'LUA'
local mode, now, n = ARGV[1], tonumber(ARGV[2]), tonumber(ARGV[3])
if n < 1 then return false end
local last = tonumber(redis.call('GET', KEYS[2]) or '-1')
local random_start = (mode == 'random') and (math.random(n) - 1) or nil
for offset = 1, n do
  local i = (mode == 'sequence') and ((last + offset) % n) or ((random_start + offset - 1) % n)
  local p = 4 + (i * 3)
  local sort, limit, expires = ARGV[p], tonumber(ARGV[p + 1]), tonumber(ARGV[p + 2])
  local used = tonumber(redis.call('HGET', KEYS[1], sort) or '0')
  if expires > now and (limit < 0 or used < limit) then
    redis.call('HINCRBY', KEYS[1], sort, 1)
    redis.call('SET', KEYS[2], i)
    return sort
  end
end
return false
LUA;
$argv = [$mode, (string) $at->timestamp, (string) count($candidates)];
foreach ($candidates as $candidate) {
    $argv[] = (string) $candidate['sort'];
    $argv[] = (string) ($candidate['uv_limit_num'] ?? -1);
    $expires = empty($candidate['expired_at']) ? PHP_INT_MAX : CarbonImmutable::parse($candidate['expired_at'])->timestamp;
    $argv[] = (string) $expires;
}
$sort = Redis::eval($script, 2, $counterKey, $indexKey, ...$argv);
```

Build candidates from the stored QR array, parse each item’s own `expired_at` (a missing date is treated as `null`/no expiry, never as a guessed `$expiredAt`), use `-1` for unlimited `uv_limit_num`, and pass `Asia/Shanghai` date in the daily key. Set daily keys with `EXPIRE 172800`; cumulative keys have no expiry and are deleted only by the explicit link-delete path. `LinkController` initializes `visit_uv` only for display compatibility and never trusts it for quota.

- [ ] **Step 4: Run tests to verify they pass**

Run: `docker compose -f compose.test.yaml up -d --wait && serve/bin/test-env php artisan test tests/Unit/Services/QrRotationServiceTest.php tests/Feature/LinkResolutionTest.php --filter=rotation -v`

Expected: PASS; expired and full rows are skipped, sequence advances over skipped rows, random chooses only eligible rows, and no concurrent reservations exceed `uv_limit_num`.

- [ ] **Step 5: Commit**

```bash
cd serve && git add app/Services/QrRotationService.php app/Http/Controllers/Api/LinkController.php tests/Unit/Services/QrRotationServiceTest.php tests/Feature/LinkResolutionTest.php && git commit -m "feat: atomically rotate landing qr codes"
```

### Task 5: 建立 SafeHttp/SSRF 边界并实现六个 TargetResolver

**Files:**
- Create: `serve/app/Services/Http/UrlPolicy.php`
- Create: `serve/app/Services/Http/SafeHttpClient.php`
- Create: `serve/app/Services/Resolvers/TargetResolver.php`
- Create: `serve/app/Services/Resolvers/MiniProgramTargetResolver.php`
- Create: `serve/app/Services/Resolvers/KingDocTargetResolver.php`
- Create: `serve/app/Services/Resolvers/CliQrTargetResolver.php`
- Create: `serve/app/Services/Resolvers/WorkWechatTargetResolver.php`
- Create: `serve/app/Services/Resolvers/LandingMiniTargetResolver.php`
- Create: `serve/app/Services/Resolvers/QqQrTargetResolver.php`
- Create: `serve/app/Exceptions/UnsafeUrl.php`
- Test: `serve/tests/Unit/Services/Http/SafeHttpClientTest.php`, `serve/tests/Unit/Services/Resolvers/TargetResolverTest.php`

**Interfaces:**
- Consumes: 已通过 `LinkAccessPolicy` 的 `Link`、`MiniProgram`、由 `VisitorContext::fromIdentity()` 得到的 `VisitorContext`、`QrRotationService`、`VisitorTokenService`；测试注入 `Http::fake()`。
- Produces: `TargetResolver::resolve(Link $link, VisitorContext $visitor): TargetResult`；`SafeHttpClient::getText/getJson` 和 `UrlPolicy::assertExternal(string $url, array $allowedHosts): UriInterface`。

- [ ] **Step 1: Write failing allowlist/resolver tests**

```php
public function test_safe_http_rejects_private_address_and_rechecks_redirects(): void
{
    $this->expectException(UnsafeUrl::class);
    app(UrlPolicy::class)->assertExternal('https://127.0.0.1/admin', ['qr61.cn']);
}

public function test_resolver_mapping_returns_type_specific_target_without_secret(): void
{
    Http::fake(['https://api.example.test/*' => Http::response(['openlink' => 'weixin://dl/example'], 200)]);
    $link = $this->linkForType(LinkType::WORK_WECHAT, ['url' => 'https://work.weixin.qq.com/ca/example']);
    $result = app(WorkWechatTargetResolver::class)->resolve($link, VisitorContext::anonymous());
    $this->assertSame('https://work.weixin.qq.com/ca/example', $result->target);
    $this->assertStringNotContainsString('secret', json_encode($result));
}
```

- [ ] **Step 2: Run tests to verify they fail**

Run: `docker compose -f compose.test.yaml up -d --wait && serve/bin/test-env php artisan test tests/Unit/Services/Http/SafeHttpClientTest.php tests/Unit/Services/Resolvers/TargetResolverTest.php -v`

Expected: FAIL because `file_get_contents()` and the monolithic controller still perform unbounded/unchecked requests and no resolver interface exists.

- [ ] **Step 3: Implement SafeHttp and all six exact mappings**

```php
interface TargetResolver
{
    public function resolve(Link $link, VisitorContext $visitor): TargetResult;
}

final class UnsafeUrl extends LinkResolutionException {}

final class SafeHttpClient
{
    public function getJson(string $url, array $allowlist): array
    {
        $uri = $this->policy->assertExternal($url, $allowlist);
        return Http::connectTimeout(3)->timeout(8)->retry(2, 100)
            ->acceptJson()->get((string) $uri)->throw()->json();
    }
}
```

Implement the fixed resolver map:

| `LinkType` | Resolver behavior and output rule |
| --- | --- |
| `MINI_PROGRAM` | Validate relative `pages/[A-Za-z0-9_/-]+` path; call the configured mini-program API with encrypted Secret internally; return only the generated `weixin://` target. Never append visitor identity. |
| `KING_DOC` | Accept only `https://kdocs.cn/l/{id}`; call the two Kdocs endpoints through `SafeHttpClient`; validate returned `url_scheme` as an allowed WeChat Scheme; malformed/timeout response is `KING_DOC_EXTERNAL_ERROR`. |
| `CLI_QR` | Accept only `https://qr61.cn/{user}/{id}`; call ticket and fetch URLs through `SafeHttpClient`, revalidate fetch redirect each time; require a `data.urlScheme` WeChat Scheme or return `CLI_QR_EXTERNAL_ERROR`. |
| `WORK_WECHAT` | Accept only an administrator-configured `https://work.weixin.qq.com/...` path; return that HTTPS URL after `UrlPolicy` validation. |
| `LANDING_MINI` | Reserve a QR, generate a Scheme to official pool path `pages/views/tools/news` with `code` and opaque ten-minute `visitor_token`, and put the selected sanitized QR in `LandingSelectionStore`. |
| `QR_QQ` | Accept only `https://ym.link/...`; fetch HTML with `SafeHttpClient`, extract exactly one `weixin://dl/business/?t=...`, validate it, and return `QQ_QR_EXTERNAL_ERROR` for malformed content. |

`SafeHttpClient` sets connection/response timeouts and at most two retries, rejects all non-HTTPS external URLs except resolver-generated `weixin://`, disables arbitrary redirects or follows at most three with a fresh `UrlPolicy` check, rejects DNS results in private/reserved/loopback/link-local ranges, caps body size, and never logs request headers/query strings containing credentials. Every resolver returns a `TargetResult`; none returns `appid`, `secret`, access tokens, raw HTML, or request metadata.

- [ ] **Step 4: Run tests to verify they pass**

Run: `docker compose -f compose.test.yaml up -d --wait && serve/bin/test-env php artisan test tests/Unit/Services/Http/SafeHttpClientTest.php tests/Unit/Services/Resolvers/TargetResolverTest.php -v`

Expected: PASS; private targets, illegal schemes, malformed payloads and unsafe redirects fail with type-specific codes, while all six resolver fixtures return the expected target shape without secrets.

- [ ] **Step 5: Commit**

```bash
cd serve && git add app/Services/Http/UrlPolicy.php app/Services/Http/SafeHttpClient.php app/Services/Resolvers tests/Unit/Services/Http/SafeHttpClientTest.php tests/Unit/Services/Resolvers/TargetResolverTest.php && git commit -m "feat: add safe http and six target resolvers"
```

### Task 6: 用 LinkResolutionCoordinator 收敛两个公开 API 的访问、缓存和日志

**Files:**
- Create: `serve/app/Services/LinkResolutionCoordinator.php`
- Create: `serve/app/DTO/CoordinatorResponse.php`
- Modify: `serve/app/Http/Controllers/Api/JumpController.php`
- Delete: `serve/app/Http/Controllers/HomeController.php`（该文件错误地重复声明 `JumpController`，且无引用）
- Modify: `serve/routes/api.php`
- Modify: `serve/app/Models/LinkVisitLog.php`
- Test: `serve/tests/Feature/LinkResolutionTest.php`, `serve/tests/Feature/Security/LinkSecretTest.php`

**Interfaces:**
- Consumes: `LinkAccessPolicy`、`VisitorIdentityService`、基础计划 `UsageMeter::consume(User, string, CarbonImmutable)`、`QrRotationService`、六个 resolver、`LandingSelectionStore`、`VisitorTokenService`、`LinkShareUrl`。
- Produces: `LinkResolutionCoordinator::target(Request $request, string $code): CoordinatorResponse`、`LinkResolutionCoordinator::showQr(Request $request, string $code): CoordinatorResponse`，其中 `CoordinatorResponse` 包含 JSON payload、HTTP status 和待附加 Cookie。

- [ ] **Step 1: Write failing endpoint tests for all six types and refusal behavior**

```php
public function test_link_target_sets_visitor_cookie_and_landing_only_returns_visitor_token(): void
{
    $link = $this->landingLinkWithQrs([['sort' => 1, 'path' => 'qr.png', 'expired_at' => now()->addHour(), 'uv_limit_num' => 5]]);
    $response = $this->getJson('/api/link-target/'.$link->code);
    $response->assertOk()->assertCookie('visitor_id')->assertJsonPath('data.visitorToken', fn ($v) => is_string($v));
    $this->assertArrayNotHasKey('secret', $response->json('data'));

    $mini = $this->miniProgramLink();
    $response = $this->getJson('/api/link-target/'.$mini->code);
    $response->assertOk()->assertJsonMissingPath('data.visitorToken')->assertJsonMissingPath('data.params');
}

public function test_refused_link_does_not_create_visit_log(): void
{
    $link = $this->landingLinkWithQrs([]);
    $link->update(['manual_status' => 0]);
    $this->getJson('/api/link-target/'.$link->code)->assertJsonPath('code', LinkError::LINK_DISABLED);
    $this->assertDatabaseMissing('link_visit_logs', ['link_id' => $link->id]);
}

public function test_show_qr_reads_encrypted_landing_selection_without_second_uv(): void
{
    $link = $this->landingLinkWithQrs([['sort' => 1, 'path' => 'qr.png', 'expired_at' => now()->addHour(), 'uv_limit_num' => 5]]);
    $token = $this->getJson('/api/link-target/'.$link->code)->json('data.visitorToken');
    $before = UsagePeriod::query()->where('user_id', $link->user_id)->value('used_uv');
    $this->getJson('/api/link-show-qr/'.$link->code.'?visitor_token='.urlencode($token))->assertOk()->assertJsonPath('data.qr', fn ($v) => $v !== '');
    $after = UsagePeriod::query()->where('user_id', $link->user_id)->value('used_uv');
    $this->assertSame($before, $after);
}
```

- [ ] **Step 2: Run tests to verify they fail**

Run: `docker compose -f compose.test.yaml up -d --wait && cd serve && composer dump-autoload --optimize --strict-psr && cd .. && serve/bin/test-env php artisan test tests/Feature/LinkResolutionTest.php tests/Feature/Security/LinkSecretTest.php -v`

Expected: FAIL because `JumpController` currently trusts `device_uid`, checks `expired_at`, writes raw IP/device/cache including Secret, has no account-level atomic UV, and `getShowQr` accepts no signed token.

- [ ] **Step 3: Implement the coordinator and controller delegation**

```php
final readonly class CoordinatorResponse
{
    public function __construct(public array $payload, public int $status = 200, public ?Cookie $cookie = null) {}
    public static function success(array $payload): self { return new self($payload); }
    public static function error(string $code, int $status): self { return new self(['code' => $code], $status); }
    public function withCookie(Cookie $cookie): self { return new self($this->payload, $this->status, $cookie); }
}

public function target(Request $request, string $code): CoordinatorResponse
{
    $link = Link::query()->where('code', $code)->first();
    if (! $link) {
        return CoordinatorResponse::error(LinkError::LINK_NOT_FOUND, 404);
    }
    $decision = $this->policy->check($link, CarbonImmutable::now('Asia/Shanghai'));
    if (! $decision->allowed) {
        return CoordinatorResponse::error($decision->errorCode, 403); // no log
    }
    $visitor = $this->identity->resolve($request);
    try {
        $this->usageMeter->consume($link->user, $visitor->visitorId, CarbonImmutable::now('Asia/Shanghai'));
    } catch (\App\Exceptions\BusinessRuleException $exception) {
        if ($exception->errorCode === 'QUOTA_EXCEEDED') {
            return CoordinatorResponse::error(LinkError::QUOTA_EXCEEDED, 429);
        }
        throw $exception;
    }
    $context = VisitorContext::fromIdentity($visitor);
    $result = $this->resolvers->for($link->type)->resolve($link, $context);
    $payload = $this->sanitizer->publicTarget($result);
    $this->visits->createSanitized($link, $visitor, $request, $payload);
    return CoordinatorResponse::success($payload)->withCookie($this->identity->cookie($visitor));
}
```

For `LANDING_MINI`, the resolver issues the token only after QR reservation and the coordinator stores the sanitized selection before logging; for the other five types, no token is issued. `showQr` verifies `visitor_token` against code and expiration, reads the selection cache, re-runs `LinkAccessPolicy` without calling `UsageMeter::consume`, and returns only `id/avatar/title/sub_title/qr`. Invalid token, missing selection or unavailable QR returns `VISITOR_TOKEN_INVALID`/`QR_UNAVAILABLE` and no log. `JumpController::target/getShowQr` become thin JSON adapters; routes remain exactly the two public `GET` routes and must not gain a client-supplied device identity parameter. Cache keys contain only link ID, target type, public title/description/icon and sanitized target; logs contain `visitor_hash`, `ip_hash`, `user_agent_hash`, link/user IDs and no `params`, Secret or raw input.

- [ ] **Step 4: Run tests to verify they pass**

Run: `docker compose -f compose.test.yaml up -d --wait && serve/bin/test-env php artisan test tests/Feature/LinkResolutionTest.php tests/Feature/Security/LinkSecretTest.php -v`

Expected: Composer reports no duplicate/PSR-4 class violation；PASS for six resolver paths, cookie/token behavior, canonical public payload, atomic UV, stable refusal codes, no refusal logs, and no Secret in response/cache/database/log output.

- [ ] **Step 5: Commit**

```bash
cd serve && git add app/Services/LinkResolutionCoordinator.php app/Http/Controllers/Api/JumpController.php app/Http/Controllers/HomeController.php routes/api.php app/Models/LinkVisitLog.php tests/Feature/LinkResolutionTest.php tests/Feature/Security/LinkSecretTest.php && git commit -m "feat: coordinate secure public link resolution"
```

### Task 7: 完成链接健康检查并保持人工停用不可逆覆盖

**Files:**
- Create: `serve/database/migrations/2026_09_01_000102_link_health_check.php`
- Create: `serve/app/Services/Health/HealthChecker.php`
- Create: `serve/app/Services/Health/HealthCheckResult.php`
- Create: `serve/app/Services/Health/LinkHealthCheckService.php`
- Create: `serve/app/Services/Health/MiniProgramHealthChecker.php`, `KingDocHealthChecker.php`, `CliQrHealthChecker.php`, `WorkWechatHealthChecker.php`, `LandingMiniHealthChecker.php`, `QqQrHealthChecker.php`
- Modify: `serve/app/Models/Link.php`
- Modify: `serve/app/Console/Commands/ChkLinkStatus.php`
- Modify: `serve/app/Console/Kernel.php`
- Test: `serve/tests/Unit/Services/Health/LinkHealthCheckServiceTest.php`, `serve/tests/Feature/LinkHealthCheckTest.php`

**Interfaces:**
- Consumes: `Link`, `LinkType`, `SafeHttpClient`, six resolver-specific lightweight checkers and existing `manual_status`。
- Produces: `links.health_checked_at/health_error_code`、`HealthChecker::check(Link $link, CarbonImmutable $at): HealthCheckResult`、`LinkHealthCheckService::check(Link $link, CarbonImmutable $at): void`、`php artisan app:links-health-check`；only `health_status`, `health_checked_at` and `health_error_code` are writable by this path.

- [ ] **Step 1: Write failing health-schema and manual-status protection tests**

```php
public function test_health_check_records_result_without_touching_manual_status(): void
{
    $link = $this->linkForType(LinkType::WORK_WECHAT, ['url' => 'https://work.weixin.qq.com/ca/example']);
    $link->update(['manual_status' => 0, 'health_status' => 0]);
    Http::fake(['https://work.weixin.qq.com/*' => Http::response('', 200)]);
    $this->artisan('app:links-health-check')->assertSuccessful();
    $link->refresh();
    $this->assertSame(0, $link->manual_status);
    $this->assertNotNull($link->health_checked_at);
    $this->assertContains($link->health_error_code, [null, 'WORK_WECHAT_HEALTH_FAILED']);
}
```

- [ ] **Step 2: Run the test to verify it fails**

Run: `docker compose -f compose.test.yaml up -d --wait && serve/bin/test-env php artisan test tests/Unit/Services/Health/LinkHealthCheckServiceTest.php tests/Feature/LinkHealthCheckTest.php -v`

Expected: FAIL because health columns, the command and the type checker dispatch do not exist.

- [ ] **Step 3: Implement migration, six lightweight checkers and scheduler**

```php
Schema::table('links', function (Blueprint $table): void {
    $table->timestamp('health_checked_at')->nullable()->after('health_status');
    $table->string('health_error_code', 64)->nullable()->after('health_checked_at');
    $table->index(['health_status', 'health_checked_at']);
});

final readonly class HealthCheckResult
{
    public function __construct(public bool $healthy, public ?string $errorCode = null) {}
}

public function check(Link $link, CarbonImmutable $at): void
{
    $result = $this->checkers->for($link->type)->check($link, $at);
    $link->forceFill([
        'health_status' => $result->healthy ? 1 : 0,
        'health_error_code' => $result->errorCode,
        'health_checked_at' => $at,
    ])->save();
}
```

Implement six checkers with no writes to `manual_status`: `MiniProgramHealthChecker` validates the relative page path and configured mini-program enablement; `KingDocHealthChecker`, `CliQrHealthChecker`, `WorkWechatHealthChecker`, and `QqQrHealthChecker` call `SafeHttpClient` with their existing allowlists and map timeout/invalid structure to `KING_DOC_HEALTH_FAILED`, `CLI_QR_HEALTH_FAILED`, `WORK_WECHAT_HEALTH_FAILED`, and `QQ_QR_HEALTH_FAILED`; `LandingMiniHealthChecker` validates the official landing mini path and QR configuration without reserving a QR or consuming UV. An exception is caught per link and translated to its type-specific stable error code. `ChkLinkStatus` delegates to `LinkHealthCheckService`; `app:links-health-check` is scheduled in `serve/app/Console/Kernel.php` every ten minutes with `withoutOverlapping(9)`. `effective_status` continues to require both manual and health status, so a health pass never re-enables a manually stopped link.

- [ ] **Step 4: Run tests to verify they pass**

Run: `docker compose -f compose.test.yaml up -d --wait && serve/bin/test-env php artisan test tests/Unit/Services/Health/LinkHealthCheckServiceTest.php tests/Feature/LinkHealthCheckTest.php -v && serve/bin/test-env php artisan schedule:list | rg 'app:links-health-check'`

Expected: PASS; each type receives a stable health result, `health_checked_at` updates, the scheduler runs at ten-minute intervals without overlap, and a manually stopped link remains stopped after a successful health check.

- [ ] **Step 5: Commit**

```bash
cd serve && git add database/migrations/2026_09_01_000102_link_health_check.php app/Services/Health app/Models/Link.php app/Console/Commands/ChkLinkStatus.php app/Console/Kernel.php tests/Unit/Services/Health/LinkHealthCheckServiceTest.php tests/Feature/LinkHealthCheckTest.php && git commit -m "feat: add scheduled link health checks"
```

### Task 8: 完成并发、安全和六类型回归门

**Files:**
- Create: `serve/tests/Feature/Security/LinkResolutionConcurrencyTest.php`
- Create: `serve/tests/Feature/Security/LinkSsrfTest.php`
- Create: `serve/tests/Feature/Security/LinkSecretTest.php`
- Create: `docs/superpowers/evidence/2026-09-01-link-resolution-matrix.md`

**Interfaces:**
- Consumes: Tasks 1–7 的全部服务/API；使用独立测试数据库、array cache、Redis 测试库和 `Http::fake()`。
- Produces: 可复现的六类型测试矩阵、并发 UV/QR 证据、Secret/SSRF 扫描证据；不把真实外部平台凭据通过聊天或 Git 传入。

- [ ] **Step 1: Write the failing integration/security tests**

```php
public function test_new_visitors_do_not_exceed_account_limit(): void
{
    $link = $this->miniProgramLink();
    $link->user->vipPackage->update(['config' => ['uv_limit' => 2, 'allow_type' => array_fill_keys(LinkType::getAllType(), true)]]);
    $responses = collect(range(1, 4))->map(function () use ($link) {
        $visitor = (string) Str::uuid();
        return $this->withCookie('visitor_id', $visitor)->getJson('/api/link-target/'.$link->code);
    });
    $this->assertSame(2, UsagePeriod::query()->where('user_id', $link->user_id)->value('used_uv'));
    $this->assertCount(2, collect($responses)->filter(fn ($r) => $r->json('code') === 0));
    $this->assertTrue(collect($responses)->contains(fn ($r) => $r->json('code') === LinkError::QUOTA_EXCEEDED));
}

public function test_no_secret_or_raw_network_identity_is_exposed(): void
{
    $link = $this->miniProgramLink();
    MiniProgram::query()->where('user_id', $link->user_id)->update(['secret' => 'must-never-leak']);
    $response = $this->getJson('/api/link-target/'.$link->code);
    $this->assertStringNotContainsString('must-never-leak', $response->getContent());
    $this->assertDatabaseMissing('link_visit_logs', ['ip' => '203.0.113.10', 'device_uid' => 'client-id']);
}
```

These integration tests import `App\Models\UsagePeriod` and `Illuminate\Support\Str`; they inspect `usage_periods.used_uv` directly and never call `UsageMeter::consume()` a second time merely to read usage. Every call to `consume()` receives the raw server-issued UUID exactly once, leaving the foundation service as the sole hashing boundary.

- [ ] **Step 2: Run tests to verify they fail**

Run: `docker compose -f compose.test.yaml up -d --wait && serve/bin/test-env php artisan test tests/Feature/Security/LinkResolutionConcurrencyTest.php tests/Feature/Security/LinkSsrfTest.php tests/Feature/Security/LinkSecretTest.php -v`

Expected: FAIL until all six paths use the coordinator, test environment is isolated, and raw fields/unsafe URLs are removed.

- [ ] **Step 3: Verify the foundation test isolation and write the evidence matrix**

Before running this task, assert that the foundation plan’s `compose.test.yaml`, `phpunit.xml` and test bootstrap already enforce isolated MySQL 8 on host port `33067` with database `link_saas_test`, Redis 7 on host port `6390` with a dedicated DB/prefix, `CACHE_STORE=array`, `MAIL_MAILER=array`, `QUEUE_CONNECTION=sync`, `APP_ENV=testing`, non-secret visitor keys, and an immediate failure for non-test databases/environments. Do not modify `serve/tests/TestCase.php` or `serve/phpunit.xml` here. Use `Http::fake()` fixtures for each resolver and the isolated Redis namespace for Lua atomicity; run `docker compose -f compose.test.yaml up -d --wait` before the security/concurrency suite.

Create the matrix with one row per `MINI_PROGRAM`, `KING_DOC`, `CLI_QR`, `WORK_WECHAT`, `LANDING_MINI`, `QR_QQ`, recording input fixture, expected `TargetResult` shape, Android/iOS external result, and evidence path. Mark absent credentials as `外部阻塞`, not as code failure. Add commands for `rg -n "火星快链|mayilink|secret|device_uid|file_get_contents\(" serve admin mini_programs jump --glob '!**/dist/**'` and a JSON/cache/log assertion that only masked Secret metadata appears.

- [ ] **Step 4: Run the complete verification gate**

Run:

```bash
docker compose -f compose.test.yaml up -d --wait
serve/bin/test-env php artisan migrate:fresh
serve/bin/test-env php artisan test tests/Unit/Services tests/Feature/LinkResolutionTest.php tests/Feature/LinkCrudTest.php tests/Feature/Security -v
serve/bin/test-env php artisan route:list --path=api/link
git diff --check
```

Expected: PASS for the foundation schema/rolling-period tests, repeated visitors, multi-link account totals through `UsageMeter`, QR limits, six resolver fixtures, status/permanent semantics, `min_id` tenant checks, SSRF rejection and Secret non-disclosure. `route:list` shows only the two public APIs for resolution; no payment mutation route is added. Any unavailable external platform is reported separately as `外部阻塞`.

- [ ] **Step 5: Commit**

```bash
git add tests/Feature/Security/LinkResolutionConcurrencyTest.php tests/Feature/Security/LinkSsrfTest.php tests/Feature/Security/LinkSecretTest.php docs/superpowers/evidence/2026-09-01-link-resolution-matrix.md && git commit -m "test: close link resolution security and concurrency gates"
```

## Self-Review Checklist

- [ ] Spec coverage: status split/permanent semantics, boolean status API, canonical share URL validation, `min_id` tenancy, visitor Cookie/AES-GCM token, account atomic UV through foundation `UsageMeter`, QR Lua rotation, six resolver map, coordinator, SafeHttp/SSRF, Secret non-disclosure, both public APIs, link health checks and regression evidence each have a named task; uni-app ownership remains in the frontend plan.
- [ ] Placeholder scan: no unfinished marker, duplicated-step shortcut, or unspecified error-handling instruction appears; every code action has a concrete file, interface, snippet and command.
- [ ] Type consistency: `TargetResolver::resolve(Link, VisitorContext): TargetResult`, `VisitorTokenService::issue/verify`, foundation `UsageMeter::consume(User, string, CarbonImmutable)`, `QrRotationService::reserve`, and coordinator methods are reused with the same names/signatures across tasks.
- [ ] Safety: this plan does not authorize real credentials, production mutation, deletion of legacy data, commits or pushes by the plan author; external platform/device/deployment proof remains a separate user-controlled gate.
