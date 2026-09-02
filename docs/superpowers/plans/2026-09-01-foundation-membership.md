# Foundation Membership Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** 为 `serve/` 建立可从空数据库安装的认证、推荐关系、管理员初始化、会员生命周期、滚动周期和账号级 UV 额度基础。

**Architecture:** Laravel API 继续以 Sanctum token 认证，`RegistrationService` 负责注册事务和不可变推荐关系，`MembershipService` 负责所有会员状态变更并写审计流水，`EntitlementService` 是控制器读取会员权益的唯一入口。`RollingPeriod` 计算以 `start_at` 为锚点的半开滚动月份，`UsageMeter` 在数据库事务中锁定账号周期并用唯一访客表完成原子 UV 计量；`VipExpired` 只负责调用会员服务推进投影，不自行复制业务规则。

**Tech Stack:** PHP 8.3、Laravel 13、Laravel Sanctum 4.3、Eloquent、MySQL 8 测试数据库、Redis 7、Carbon、PHPUnit 12。

**Spec:** `docs/superpowers/specs/2026-09-01-full-function-stabilization-design.md`

## Global Constraints

- PHP 版本保持 `^8.3` 且 Composer `config.platform.php` 固定 `8.3.0`，避免本机 PHP 8.5 解析出服务器不可运行的依赖；Laravel 目标版本为 `^13.0`；Sanctum 保持 `^4.3`；Tinker 保持 `^3.0`；Guzzle 保持 `^7.15.2`；不新增未审查生产依赖。
- `serve/` 是会员和权益事实来源；支付购买、支付回调、退款、佣金结算不进入本计划的业务接口。
- 所有额度时间使用 `Asia/Shanghai`；额度窗口为半开区间 `[period_start, period_end)`；数据库时间字段仍按 Laravel 连接配置保存并在边界计算时显式转换时区。
- 新安装唯一通过 migrations 和 seeders；运行时不得读取 `serve/database/base.sql` 或 `serve/database/packages.sql`。
- 测试必须使用根目录 `compose.test.yaml` 提供的隔离 MySQL 8（宿主 `33067`、数据库名后缀 `_test`）和 Redis 7（宿主 `6390`、独立 DB/prefix），邮件为 array、队列为 sync；测试启动时发现数据库不是测试白名单立即失败，不得连接开发或生产数据库、发送真实邮件或输出 token。
- 生产配置从环境变量或受控配置存储读取；Git 中不得保留真实 `.env`、`APP_KEY`、第三方凭据、默认管理员密码或演示账号密码。
- 首次管理员只能通过受控 `app:admin-provision` 命令创建，并在首次登录后被要求修改密码；Seeder 不创建默认管理员。
- 推荐关系只保存不可变的 `parent_id`、唯一 `referral_code` 和直属邀请记录，不创建代理等级、支付佣金或可更换推荐人入口。
- 商业上线硬门：PHP 8.1 与 Laravel 10 已结束官方支持；基线 `composer audit --locked` 已记录 47 条公告，Laravel 13 任务必须处置所有未解决的 high/critical，不能仅以忽略参数通过。
- 每个任务都先写失败测试，再实现最小代码；每个任务结束执行指定测试、`git diff --check`，并形成一个范围清晰的 commit。执行者只暂存该任务列出的文件。

---

### Task 1: 建立测试隔离和无凭据环境基线

**Files:**
- Modify: `serve/phpunit.xml:20-31`
- Modify: `serve/tests/TestCase.php:5-11`
- Modify: `serve/tests/CreatesApplication.php:8-20`
- Create: `serve/tests/Support/TestEnvironmentGuard.php`
- Create: `serve/tests/Feature/TestingIsolationTest.php`
- Create: `serve/app/Exceptions/BusinessRuleException.php`
- Create: `compose.test.yaml`
- Create: `serve/bin/test-env`
- Modify: `serve/.gitignore:1-17`
- Untrack without deleting local files: `serve/vendor/**`（依赖只由 Composer lock 安装）
- Modify: `serve/.env.example:1-45`
- Modify: `admin/.gitignore:1-3`
- Create: `admin/.env.example`
- Create: `admin/.env.development.example`

**Interfaces:**
- Consumes: Laravel application bootstrap and `config/database.php` 的 `DB_CONNECTION`、`DB_DATABASE`、`DB_HOST`、`DB_PORT` 配置。
- Produces: `Tests\Support\TestEnvironmentGuard::assertSafe(): void`；`serve/bin/test-env <command-and-arguments>` 为 PHPUnit 之外的 Artisan 测试命令显式注入同一 MySQL/Redis 白名单和测试专用 visitor 密钥；所有 Feature 测试在 `setUp()` 前验证 MySQL 测试库、隔离 Redis/array 缓存、array 邮件、sync 队列、visitor token 32 字节密钥和 `testing` 环境。

- [ ] **Step 1: Write the failing isolation tests**

```php
// serve/tests/Feature/TestingIsolationTest.php
namespace Tests\Feature;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

final class TestingIsolationTest extends TestCase
{
    public function test_test_runtime_uses_the_isolated_allowlist(): void
    {
        $this->assertSame('testing', app()->environment());
        $this->assertNotSame('', (string) env('APP_KEY'));
        $this->assertNotSame('', (string) env('APP_VISITOR_HASH_KEY'));
        $this->assertSame(32, strlen((string) base64_decode((string) env('APP_VISITOR_TOKEN_KEY'), true)));
        $this->assertNotSame('', (string) config('app.key'));
        $this->assertSame('mysql', config('database.default'));
        $this->assertStringEndsWith('_test', config('database.connections.mysql.database'));
        $this->assertSame('array', config('cache.default'));
        $this->assertSame('array', config('mail.default'));
        $this->assertSame('sync', config('queue.default'));
        $this->assertSame('14', (string) config('database.redis.default.database'));
        $this->assertSame('15', (string) config('database.redis.cache.database'));
        $this->assertSame('link_saas_test_', (string) config('database.redis.options.prefix'));
    }

    public function test_runtime_does_not_expose_a_default_application_key(): void
    {
        $this->assertNotSame('', (string) config('app.key'));
    }
}
```

- [ ] **Step 2: Run the tests to verify they fail for the current project**

Run: `docker compose -f compose.test.yaml up -d --wait && serve/bin/test-env php artisan test tests/Feature/TestingIsolationTest.php -v`

Expected: FAIL because `phpunit.xml` does not select the isolated MySQL test connection, `config('cache.default')` is not guaranteed to be `array`, and the checked-in example key is fixed.

- [ ] **Step 3: Add the fail-fast guard and explicit PHPUnit environment**

```php
// serve/tests/Support/TestEnvironmentGuard.php
namespace Tests\Support;

use PHPUnit\Framework\AssertionFailedError;

final class TestEnvironmentGuard
{
    public static function assertSafe(): void
    {
        $allowed = [
            'APP_ENV' => 'testing',
            'DB_CONNECTION' => 'mysql',
            'DB_DATABASE' => null,
            'CACHE_STORE' => 'array',
            'CACHE_DRIVER' => 'array',
            'MAIL_MAILER' => 'array',
            'QUEUE_CONNECTION' => 'sync',
        ];

        foreach ($allowed as $key => $expected) {
            if ($key === 'DB_DATABASE') {
                if (! str_ends_with((string) env($key), '_test')) {
                    throw new AssertionFailedError('Unsafe test environment: DB_DATABASE');
                }
                continue;
            }
            if ((string) env($key) !== $expected) {
                throw new AssertionFailedError("Unsafe test environment: {$key}");
            }
        }
        if ((string) env('REDIS_PREFIX') !== 'link_saas_test_' || (string) env('REDIS_DB') !== '14' || (string) env('REDIS_CACHE_DB') !== '15') {
            throw new AssertionFailedError('Unsafe test environment: Redis database or prefix');
        }
        if (trim((string) env('APP_KEY')) === '') {
            throw new AssertionFailedError('Unsafe test environment: APP_KEY');
        }
        if (trim((string) env('APP_VISITOR_HASH_KEY')) === '') {
            throw new AssertionFailedError('Unsafe test environment: APP_VISITOR_HASH_KEY');
        }
        $visitorTokenKey = base64_decode((string) env('APP_VISITOR_TOKEN_KEY'), true);
        if ($visitorTokenKey === false || strlen($visitorTokenKey) !== 32) {
            throw new AssertionFailedError('Unsafe test environment: APP_VISITOR_TOKEN_KEY');
        }
    }
}
```

```php
// serve/tests/TestCase.php
namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\Support\TestEnvironmentGuard;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase;

    protected function setUp(): void
    {
        TestEnvironmentGuard::assertSafe();
        parent::setUp();
    }
}
```

```php
// serve/tests/CreatesApplication.php
public function createApplication(): Application
{
    TestEnvironmentGuard::assertSafe();
    $app = require __DIR__.'/../bootstrap/app.php';
    $app->make(Kernel::class)->bootstrap();
    return $app;
}
```

在文件顶部加入 `use Tests\Support\TestEnvironmentGuard;`，这样应用读取数据库配置之前就会阻断错误测试环境；`TestCase::setUp()` 的再次检查用于捕获测试过程中被改写的环境。

```php
// serve/app/Exceptions/BusinessRuleException.php
namespace App\Exceptions;

use RuntimeException;

final class BusinessRuleException extends RuntimeException
{
    public function __construct(public readonly string $errorCode, string $message, public readonly int $status = 422)
    {
        parent::__construct($message);
    }
}
```

在 `serve/phpunit.xml` 的 `<php>` 节点加入明确标记为测试专用的非空 `APP_KEY=base64:dGVzdC1rZXl0ZXN0LWtleXRlc3Qta2V5dGVzdC1rZXk=`、`APP_VISITOR_HASH_KEY=test-visitor-hash-key-not-secret`和可解码为 32 字节的 `APP_VISITOR_TOKEN_KEY=MDEyMzQ1Njc4OWFiY2RlZjAxMjM0NTY3ODlhYmNkZWY=`，以及 `DB_CONNECTION=mysql`、`DB_HOST=127.0.0.1`、`DB_PORT=33067`、`DB_DATABASE=link_saas_test`、`DB_USERNAME=link_test`、`DB_PASSWORD=link_test_password`、`REDIS_HOST=127.0.0.1`、`REDIS_PORT=6390`、`REDIS_DB=14`、`REDIS_CACHE_DB=15`、`REDIS_PREFIX=link_saas_test_`、`CACHE_STORE=array`、`CACHE_DRIVER=array`，保留 `MAIL_MAILER=array`、`QUEUE_CONNECTION=sync`，并把 `APP_ENV` 固定为 `testing`。CI 可以显式覆盖这三个密钥、DB host/port/database、Redis host/port，但三个密钥不得为空，visitor token key 必须严格 base64 解码为 32 字节，`DB_DATABASE` 必须以 `_test` 结尾，Redis DB 必须为 14/15 且 prefix 必须命中白名单。测试命令统一先执行 `docker compose -f compose.test.yaml up -d --wait`。在 `serve/.env.example` 删除固定 `APP_KEY`，改为 `APP_KEY=`，增加 `PUBLIC_ORIGIN`、`ALLOWED_SHARE_HOSTS`、`APP_VISITOR_HASH_KEY=`、`APP_VISITOR_TOKEN_KEY=` 和 `APP_TIMEZONE=Asia/Shanghai`；生产示例中的 `QUEUE_CONNECTION` 改成 `redis`，但 PHPUnit 的 `sync` 只存在于测试配置。将 `serve/.env`、`serve/.env.development`、`admin/.env`、`admin/.env.development` 及其备份模式加入各自忽略文件，并为 `admin/.env.example`、`admin/.env.development.example` 提供空值模板。

创建根目录测试编排文件，凭据只用于本地测试容器：

```yaml
# compose.test.yaml
services:
  mysql:
    image: mysql:8.0
    ports: ["33067:3306"]
    environment:
      MYSQL_DATABASE: link_saas_test
      MYSQL_USER: link_test
      MYSQL_PASSWORD: link_test_password
      MYSQL_ROOT_PASSWORD: link_test_root_password
    healthcheck:
      test: ["CMD", "mysqladmin", "ping", "-h", "127.0.0.1", "-ulink_test", "-plink_test_password"]
      interval: 2s
      timeout: 3s
      retries: 30
  redis:
    image: redis:7-alpine
    ports: ["6390:6379"]
    command: ["redis-server", "--save", "", "--appendonly", "no"]
    healthcheck:
      test: ["CMD", "redis-cli", "ping"]
      interval: 2s
      timeout: 3s
      retries: 30
```

创建可执行的 `serve/bin/test-env`，所有具有清库/迁移副作用的测试命令必须经它启动，禁止仅依赖 `--env=testing`。每个变量先读取 CI 显式值，缺失时才采用隔离测试默认值；因此本机 Compose、CI 容器网络和后续 Playwright 可以复用同一入口：

```bash
#!/usr/bin/env bash
set -Eeuo pipefail
cd "$(dirname "$0")/.."
: "${APP_ENV:=testing}"
: "${DB_CONNECTION:=mysql}"
: "${DB_HOST:=127.0.0.1}"
: "${DB_PORT:=33067}"
: "${APP_KEY:=base64:dGVzdC1rZXl0ZXN0LWtleXRlc3Qta2V5dGVzdC1rZXk=}"
: "${APP_VISITOR_HASH_KEY:=test-visitor-hash-key-not-secret}"
: "${APP_VISITOR_TOKEN_KEY:=MDEyMzQ1Njc4OWFiY2RlZjAxMjM0NTY3ODlhYmNkZWY=}"
: "${DB_DATABASE:=link_saas_test}"
: "${DB_USERNAME:=link_test}"
: "${DB_PASSWORD:=link_test_password}"
: "${REDIS_HOST:=127.0.0.1}"
: "${REDIS_PORT:=6390}"
: "${REDIS_DB:=14}"
: "${REDIS_CACHE_DB:=15}"
: "${REDIS_PREFIX:=link_saas_test_}"
: "${CACHE_STORE:=array}"
: "${CACHE_DRIVER:=array}"
: "${MAIL_MAILER:=array}"
: "${QUEUE_CONNECTION:=sync}"
export APP_ENV DB_CONNECTION DB_HOST DB_PORT APP_KEY APP_VISITOR_HASH_KEY APP_VISITOR_TOKEN_KEY DB_DATABASE DB_USERNAME DB_PASSWORD
export REDIS_HOST REDIS_PORT REDIS_DB REDIS_CACHE_DB REDIS_PREFIX
export CACHE_STORE CACHE_DRIVER MAIL_MAILER QUEUE_CONNECTION
exec "$@"
```

- [ ] **Step 4: Run the tests to verify the isolation setup passes**

Run: `docker compose -f compose.test.yaml up -d --wait && serve/bin/test-env php artisan test tests/Feature/TestingIsolationTest.php -v`

Expected: PASS；测试进程连接 MySQL `link_saas_test` 和 Redis 7 测试端口，不会读取开发数据库，缓存/邮件/队列分别为 array/array/sync。

- [ ] **Step 5: Verify the environment boundary has no tracked files or fixed application key**

Run: `git ls-files | rg '(^|/)\.env($|\.)' | rg -v '\.env(\.[^.]+)*\.example$'`; `git ls-files 'serve/vendor/**'`; and `rg -n 'base64:[A-Za-z0-9+/=]{20,}' serve/.env.example admin/*.example`

Expected: 三条扫描均无输出。`serve/.gitignore` 包含 `/vendor/`；实施时使用 `git rm -r --cached -- serve/vendor` 只从 Git 索引移除依赖，不删除本机文件，干净 clone 必须先执行 `composer install`。发现历史中已存在的密钥时记录轮换清单，不把新值写回仓库；默认管理员密码和 SQL dump 读取将在后续初始化任务中移除，并由最终验收再次扫描。

- [ ] **Step 6: Validate composer manifest and lock-file consistency**

Run: `docker compose -f compose.test.yaml up -d --wait && cd serve && composer validate --strict --check-lock`

Expected: PASS；`serve/composer.json` 与 `serve/composer.lock` 的包约束、PHP 平台约束和 content-hash 一致。若命令报告锁文件不一致，先运行 `composer update --lock --no-install --no-interaction`，再重新执行同一 `composer validate` 命令；不得借此升级依赖版本或改动生产依赖。

- [ ] **Step 7: Commit the isolated test and environment baseline**

```bash
git rm -r --cached -- serve/vendor
git add compose.test.yaml serve/bin/test-env serve/phpunit.xml serve/tests/TestCase.php serve/tests/CreatesApplication.php serve/tests/Support/TestEnvironmentGuard.php serve/tests/Feature/TestingIsolationTest.php serve/.gitignore serve/.env.example admin/.gitignore admin/.env.example admin/.env.development.example serve/composer.lock
git diff --cached --check
git commit -m "test: isolate backend test environment"
```

### Task 2: Laravel 10→11 和图片验证码替换

**Files:**
- Modify: serve/composer.json:7-30
- Modify: serve/composer.lock
- Modify: serve/app/Http/Controllers/Api/CaptchaController.php:19-23
- Modify: serve/app/Http/Requests/SMSCaptchaRequest.php:24-47
- Create: serve/app/Services/ImageCaptchaService.php
- Create: serve/tests/Unit/ImageCaptchaServiceTest.php
- Create: serve/tests/Feature/ImageCaptchaEndpointTest.php

**Interfaces:**
- Consumes: Laravel cache、Gregwar CaptchaBuilder 和现有 POST /api/captcha/sms 的 key/captcha 字段。
- Produces: 目标依赖 PHP ^8.3、Laravel ^11.0、Sanctum ^4.3、Tinker ^3；移除 laravel/sail、spatie/laravel-ignition、mews/captcha；增加 gregwar/captcha ^2.1.1；ImageCaptchaService::issue(): array 返回 {key,img}，ImageCaptchaService::verify(string $key, string $answer): bool 一次消费答案哈希，测试专用 ImageCaptchaService::issueForTesting(string $answer): array 仅供隔离测试生成已知答案。图片接口保持 GET /api/captcha/image 返回非空 key 和 data:image/png;base64 图片。

- [ ] **Step 1: Record the EOL/security baseline and write red tests**

商业上线门先记录当前基线：PHP 8.1 和 Laravel 10 已结束官方支持，现有锁文件的 composer audit 报告记录为 47 条公告；该记录只说明基线风险，不作为通过证据。先加入以下测试：

```php
// serve/tests/Unit/ImageCaptchaServiceTest.php
namespace Tests\Unit;

use App\Services\ImageCaptchaService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

final class ImageCaptchaServiceTest extends TestCase
{
    public function test_issue_returns_key_and_png_and_answer_is_hashed_with_one_time_ttl(): void
    {
        $issued = app(ImageCaptchaService::class)->issueForTesting('123456');
        $this->assertMatchesRegularExpression('/^[A-Za-z0-9_-]{32}$/', $issued['key']);
        $this->assertStringStartsWith('data:image/png;base64,', $issued['img']);
        $payload = Cache::store('redis')->get('image-captcha:'.$issued['key']);
        $this->assertIsString($payload['hash']);
        $this->assertFalse(isset($payload['answer']));
        $this->assertGreaterThanOrEqual(295, $payload['expires_at'] - now()->timestamp);
        $this->assertLessThanOrEqual(300, $payload['expires_at'] - now()->timestamp);
        $this->assertTrue(Hash::check('123456', $payload['hash']));
        $this->assertTrue(app(ImageCaptchaService::class)->verify($issued['key'], '123456'));
        $this->assertFalse(app(ImageCaptchaService::class)->verify($issued['key'], '123456'));
    }
}
```

测试只读 service 提供的测试结构字段或 Fake builder，生产响应不能返回 answer；将测试改为让 service 的 test-only builder 注入明文答案，断言 Redis/array cache 只保存哈希和 expires_at，TTL 为 300 秒，第二次 verify 返回 false。

```php
// serve/tests/Feature/ImageCaptchaEndpointTest.php
namespace Tests\Feature;

use Tests\TestCase;

final class ImageCaptchaEndpointTest extends TestCase
{
    public function test_image_endpoint_preserves_key_img_contract(): void
    {
        $json = $this->getJson('/api/captcha/image')->assertOk()->json();
        $this->assertMatchesRegularExpression('/^[A-Za-z0-9_-]{32}$/', $json['data']['key']);
        $this->assertStringStartsWith('data:image/png;base64,', $json['data']['img']);
        $this->assertArrayNotHasKey('answer', $json['data']);
    }
}
```

- [ ] **Step 2: Run red tests against the old dependency stack**

Run: docker compose -f compose.test.yaml up -d --wait && serve/bin/test-env php artisan test tests/Unit/ImageCaptchaServiceTest.php tests/Feature/ImageCaptchaEndpointTest.php -v

Expected: FAIL because ImageCaptchaService is absent and composer.json still targets PHP ^8.1/Laravel ^10 with mews/captcha.

- [ ] **Step 3: Upgrade the dependency manifest and implement the image contract**

将 serve/composer.json 的关键段改为：

```json
{
  "require": {
    "php": "^8.3",
    "guzzlehttp/guzzle": "^7.2",
    "gregwar/captcha": "^2.1.1",
    "laravel/framework": "^11.0",
    "laravel/sanctum": "^4.3",
    "laravel/tinker": "^3.0",
    "overtrue/easy-sms": "^3.2.1"
  },
  "require-dev": {
    "fakerphp/faker": "^1.9.1",
    "laravel/pint": "^1.0",
    "mockery/mockery": "^1.4.4",
    "nunomaduro/collision": "^8.1",
    "phpunit/phpunit": "^10.5"
  },
  "config": {
    "platform": { "php": "8.3.0" }
  }
}
```

实际 manifest 中删除 laravel/sail、spatie/laravel-ignition 和 mews/captcha；保留当前其它包，执行 composer update -W 让 composer.lock 与 manifest 同步。由于 Laravel 11 的默认 bootstrap/app.php 结构可继续运行现有 providers，保留 serve/ 目录结构，只按官方 Laravel 11 upgrade guide 修复实际报错，不改变 API 路径。

```php
// serve/app/Services/ImageCaptchaService.php
namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Gregwar\Captcha\CaptchaBuilder;

final class ImageCaptchaService
{
    public function issue(): array
    {
        $key = str_replace('-', '', (string) Str::uuid());
        $builder = new CaptchaBuilder();
        $builder->setImageType('png');
        $builder->build();
        $phrase = strtolower($builder->getPhrase());
        Cache::store('redis')->put('image-captcha:'.$key, [
            'hash' => Hash::make($phrase),
            'expires_at' => now()->addSeconds(300)->timestamp,
        ], now()->addSeconds(300));

        return ['key' => $key, 'img' => $builder->inline()];
    }

    public function issueForTesting(string $answer): array
    {
        $key = str_replace('-', '', (string) Str::uuid());
        $builder = new CaptchaBuilder($answer);
        $builder->setImageType('png');
        $builder->build();
        Cache::store('redis')->put('image-captcha:'.$key, ['hash' => Hash::make(strtolower($answer)), 'expires_at' => now()->addSeconds(300)->timestamp], now()->addSeconds(300));
        return ['key' => $key, 'img' => $builder->inline()];
    }

    public function verify(string $key, string $answer): bool
    {
        $cacheKey = 'image-captcha:'.$key;
        $payload = Cache::store('redis')->pull($cacheKey);
        if (! $payload || now()->timestamp > $payload['expires_at']) {
            return false;
        }
        return Hash::check(strtolower($answer), $payload['hash']);
    }
}
```

CaptchaController::image 注入 ImageCaptchaService 并返回 success($captchas->issue())。SMSCaptchaRequest 去掉旧图片验证码扩展规则，只保留 captcha/key 的 required|string 形状，是否必填由 verify_code_is_open 控制；Task 11 的 CaptchaController 会在发送短信前调用同一 verify 合约。answer 不得进入 API 响应、日志或长期缓存。

- [ ] **Step 4: Run green dependency, image-contract and application checks**

Run: docker compose -f compose.test.yaml up -d --wait && cd serve && composer update -W && composer validate --strict --check-lock && cd .. && serve/bin/test-env php artisan test tests/Unit/ImageCaptchaServiceTest.php tests/Feature/ImageCaptchaEndpointTest.php -v

Expected: PASS；composer.json/lock 同步，Laravel 11 启动，/captcha/image 返回 {key,img}，验证码答案只保存哈希并且只能消费一次。

- [ ] **Step 5: Commit Laravel 11 and image captcha replacement**

```bash
git add serve/composer.json serve/composer.lock serve/app/Http/Controllers/Api/CaptchaController.php serve/app/Http/Requests/SMSCaptchaRequest.php serve/app/Services/ImageCaptchaService.php serve/tests/Unit/ImageCaptchaServiceTest.php serve/tests/Feature/ImageCaptchaEndpointTest.php
git diff --cached --check
git commit -m "build: upgrade backend to laravel 11 and replace image captcha"
```

### Task 3: Laravel 11→12 依赖升级与官方兼容修复

**Files:**
- Modify: serve/composer.json:7-30
- Modify: serve/composer.lock
- Modify: serve/bootstrap/app.php:14-52
- Modify: serve/config/app.php:158-171
- Create: serve/tests/Feature/Laravel12CompatibilityTest.php

**Interfaces:**
- Consumes: Task 2 的 Laravel 11、Sanctum 4.3、PHP ^8.3 和已替换的 ImageCaptchaService。
- Produces: Laravel framework ^12.0，保留 PHP ^8.3、Sanctum ^4.3、Tinker ^3、Gregwar Captcha；官方 upgrade guide 规定的兼容代码已落地，配置缓存、路由、队列和现有 providers 可启动。

- [ ] **Step 1: Write the failing version/boot smoke test**

```php
// serve/tests/Feature/Laravel12CompatibilityTest.php
namespace Tests\Feature;

use Illuminate\Foundation\Application;
use Tests\TestCase;

final class Laravel12CompatibilityTest extends TestCase
{
    public function test_laravel_twelve_or_newer_boots_with_routes_and_config_cache(): void
    {
        $this->assertGreaterThanOrEqual(12, (int) explode('.', Application::VERSION)[0]);
        $this->artisan('route:list')->assertExitCode(0);
        $this->artisan('config:cache')->assertExitCode(0);
        $this->artisan('config:clear')->assertExitCode(0);
    }
}
```

- [ ] **Step 2: Run red test against Laravel 11**

Run: docker compose -f compose.test.yaml up -d --wait && serve/bin/test-env php artisan test tests/Feature/Laravel12CompatibilityTest.php -v

Expected: FAIL because the installed framework major version is 11; after Task 3 it remains valid under Laravel 13 because it asserts the supported minimum major.

- [ ] **Step 3: Follow the Laravel 12 official upgrade guide and update with all dependencies**

先对照 https://laravel.com/docs/12.x/upgrade，按 guide 中命中的 breaking changes 修改 serve/bootstrap/app.php、serve/config/app.php 和实际报错文件；不预先改写无报错的应用目录结构。将 composer.json framework 约束改为 ^12.0，执行：

```bash
docker compose -f compose.test.yaml up -d --wait
cd serve
composer update -W
composer validate --strict --check-lock
```

保留其它已通过 Task 2 的约束，不重新引入 laravel/sail、spatie/laravel-ignition 或 mews/captcha。

- [ ] **Step 4: Run green compatibility and full backend regression tests**

Run: docker compose -f compose.test.yaml up -d --wait && serve/bin/test-env php artisan test tests/Feature/Laravel12CompatibilityTest.php && serve/bin/test-env php artisan test

Expected: PASS；Laravel 12 启动、route:list、config cache/clear 和现有 Unit/Feature 测试全部通过。

- [ ] **Step 5: Commit Laravel 12 upgrade**

```bash
git add serve/composer.json serve/composer.lock serve/bootstrap/app.php serve/config/app.php serve/tests/Feature/Laravel12CompatibilityTest.php
git diff --cached --check
git commit -m "build: upgrade backend to laravel 12"
```

### Task 4: Laravel 12→13、依赖安全门和 FrameworkCompatibilityTest

**Files:**
- Modify: serve/composer.json:7-30
- Modify: serve/composer.lock
- Modify: serve/bootstrap/app.php:14-52
- Modify: serve/config/app.php:158-171
- Create: serve/tests/Feature/FrameworkCompatibilityTest.php

**Interfaces:**
- Consumes: Task 3 的 Laravel 12 应用和官方升级指南要求；Task 1 的 MySQL/Redis 测试 harness。
- Produces: PHP ^8.3、Laravel framework ^13.0、Sanctum ^4.3、Tinker ^3、Guzzle ^7.15.2、PHPUnit ^12.5.34、Collision ^8.9、Pint ^1.30；FrameworkCompatibilityTest 覆盖 app version、route:list、Sanctum token、config cache、queue/scheduler 和 ugly-base 实际使用面。

- [ ] **Step 1: Write the failing Laravel 13 compatibility test**

```php
// serve/tests/Feature/FrameworkCompatibilityTest.php
namespace Tests\Feature;

use App\Http\Controllers\Api\AuthController;
use App\Models\User;
use Illuminate\Foundation\Application;
use Tests\TestCase;
use Ugly\Base\Traits\ApiResource;

final class FrameworkCompatibilityTest extends TestCase
{
    public function test_framework_and_app_boot_contracts_are_supported(): void
    {
        $this->assertSame(13, (int) explode('.', Application::VERSION)[0]);
        $this->artisan('route:list')->assertExitCode(0);
        $user = User::factory()->create(['status' => true]);
        $plain = $user->createToken('compatibility')->plainTextToken;
        $this->assertNotSame('', $plain);
        $this->assertSame('testing', app()->environment());
        $this->assertSame('sync', config('queue.default'));
        $this->assertContains(ApiResource::class, class_uses_recursive(AuthController::class));
        $this->artisan('schedule:list')->assertExitCode(0);
        $this->artisan('config:cache')->assertExitCode(0);
        $this->assertNotNull(config('app.key'));
        $this->artisan('config:clear')->assertExitCode(0);
    }
}
```

- [ ] **Step 2: Run red compatibility and audit checks**

Run: docker compose -f compose.test.yaml up -d --wait && serve/bin/test-env php artisan test tests/Feature/FrameworkCompatibilityTest.php -v

Expected: FAIL because the framework major version is 12 and the Laravel 13 compatibility test is not yet satisfied. Independently run docker compose -f compose.test.yaml up -d --wait && cd serve && composer audit --locked --format=summary; record the current 47-advisory baseline without treating it as a pass.

- [ ] **Step 3: Apply the Laravel 13 manifest and official guide changes**

将 composer.json 关键版本改为：

```json
{
  "require": {
    "php": "^8.3",
    "guzzlehttp/guzzle": "^7.15.2",
    "laravel/framework": "^13.0",
    "laravel/sanctum": "^4.3",
    "laravel/tinker": "^3.0",
    "overtrue/easy-sms": "^3.2.1"
  },
  "require-dev": {
    "fakerphp/faker": "^1.9.1",
    "laravel/pint": "^1.30",
    "mockery/mockery": "^1.4.4",
    "nunomaduro/collision": "^8.9",
    "phpunit/phpunit": "^12.5.34"
  }
}
```

保留 gregwar/captcha ^2.1.1 和其它已有可用包；确认 laravel/sail、spatie/laravel-ignition、mews/captcha 未出现。先对照 https://laravel.com/docs/13.x/upgrade，按实际命中项修复 bootstrap/config/测试兼容性，保留当前 serve/admin/jump/mini_programs 目录结构。执行 composer update -W 和 composer validate --strict --check-lock，不使用宽松安装绕过锁文件。

- [ ] **Step 4: Run the Laravel 13 security and full regression gates**

Run: docker compose -f compose.test.yaml up -d --wait && cd serve && composer update -W && composer validate --strict --check-lock && composer check-platform-reqs && (composer audit --locked --format=summary || true) && composer audit --locked --ignore-severity=low --ignore-severity=medium --abandoned=fail && cd .. && serve/bin/test-env php artisan test

Expected: PASS；依赖锁文件一致，composer audit 不存在未处置的 high/critical 公告，Laravel 13 全量 Unit/Feature 测试通过。任何 high/critical 必须先升级受影响依赖或提交明确的风险处置记录，不能以忽略参数隐藏。

- [ ] **Step 5: Commit Laravel 13 and compatibility gate**

```bash
git add serve/composer.json serve/composer.lock serve/bootstrap/app.php serve/config/app.php serve/tests/Feature/FrameworkCompatibilityTest.php
git diff --cached --check
git commit -m "build: upgrade backend to laravel 13 and close compatibility gate"
```

### Task 5: 补齐安装所需 migrations、会员审计和额度表

**Files:**
- Create: `serve/database/migrations/2019_12_14_000001_create_personal_access_tokens_table.php`
- Create: `serve/database/migrations/2026_09_01_000001_add_membership_audit_fields.php`
- Create: `serve/database/migrations/2026_09_01_000002_create_membership_changes_table.php`
- Create: `serve/database/migrations/2026_09_01_000003_create_usage_tables.php`
- Create: `serve/database/migrations/2026_09_01_000004_add_user_auth_and_indexes.php`
- Create: `serve/app/Models/MembershipChange.php`
- Create: `serve/app/Models/UsagePeriod.php`
- Create: `serve/app/Models/UsageVisitor.php`
- Modify: `serve/database/factories/UserFactory.php:12-43`
- Modify: `serve/app/Models/VipLogs.php:10-29`
- Modify: `serve/app/Models/User.php:19-29`
- Create: `serve/tests/Feature/DatabaseSchemaTest.php`

**Interfaces:**
- Consumes: Existing `users`、`vip_packages`、`vip_logs` 表和 Sanctum `HasApiTokens`。
- Produces: `personal_access_tokens`、`membership_changes`、`usage_periods`、`usage_visitors` 表；`VipLogs` 可持久化 `actor_user_id`、`action`、`reason`、`before_snapshot`、`after_snapshot`、`effective_at`、`idempotency_key`；`UsagePeriod::visitors()` 和 `UsageVisitor::period()` 关系。

- [ ] **Step 1: Write schema assertions before adding migrations**

```php
// serve/tests/Feature/DatabaseSchemaTest.php
namespace Tests\Feature;

use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

final class DatabaseSchemaTest extends TestCase
{
    public function test_foundation_tables_and_constraints_exist(): void
    {
        $this->assertTrue(Schema::hasTable('personal_access_tokens'));
        $this->assertTrue(Schema::hasTable('membership_changes'));
        $this->assertTrue(Schema::hasTable('usage_periods'));
        $this->assertTrue(Schema::hasTable('usage_visitors'));
        $this->assertTrue(Schema::hasColumns('vip_logs', [
            'actor_user_id', 'action', 'reason', 'before_snapshot',
            'after_snapshot', 'effective_at', 'idempotency_key',
        ]));
        $this->assertTrue(Schema::hasColumn('users', 'must_change_password'));
    }
}
```

- [ ] **Step 2: Run the schema test to verify it fails**

Run: `docker compose -f compose.test.yaml up -d --wait && serve/bin/test-env php artisan test tests/Feature/DatabaseSchemaTest.php -v`

Expected: FAIL because Sanctum and the membership/usage tables are absent.

- [ ] **Step 3: Add the Sanctum migration and membership audit columns**

```php
// serve/database/migrations/2019_12_14_000001_create_personal_access_tokens_table.php
return new class extends Migration {
    public function up(): void
    {
        Schema::create('personal_access_tokens', function (Blueprint $table): void {
            $table->id();
            $table->morphs('tokenable');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personal_access_tokens');
    }
};
```

```php
// serve/database/migrations/2026_09_01_000001_add_membership_audit_fields.php
return new class extends Migration {
    public function up(): void
    {
        Schema::table('vip_logs', function (Blueprint $table): void {
            $table->foreignId('actor_user_id')->nullable()->after('user_id')->constrained('users')->nullOnDelete();
            $table->string('action', 32)->default('legacy')->after('status');
            $table->text('reason')->nullable();
            $table->json('before_snapshot')->nullable();
            $table->json('after_snapshot')->nullable();
            $table->timestamp('effective_at')->nullable();
            $table->string('idempotency_key', 128)->nullable()->unique();
        });
    }

    public function down(): void
    {
        Schema::table('vip_logs', function (Blueprint $table): void {
            $table->dropForeign(['actor_user_id']);
            $table->dropUnique('vip_logs_idempotency_key_unique');
            $table->dropColumn(['actor_user_id', 'action', 'reason', 'before_snapshot', 'after_snapshot', 'effective_at', 'idempotency_key']);
        });
    }
};
```

- [ ] **Step 4: Add pending membership changes and atomic usage schema**

```php
// serve/database/migrations/2026_09_01_000002_create_membership_changes_table.php
return new class extends Migration {
    public function up(): void
    {
        Schema::create('membership_changes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('from_vip_id')->nullable()->constrained('vip_packages')->nullOnDelete();
            $table->foreignId('to_vip_id')->constrained('vip_packages')->restrictOnDelete();
            $table->string('action', 32);
            $table->string('status', 16)->default('pending');
            $table->timestamp('effective_at');
            $table->foreignId('actor_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('reason')->nullable();
            $table->uuid('idempotency_key')->unique();
            $table->timestamps();
            $table->index(['user_id', 'status', 'effective_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('membership_changes');
    }
};
```

```php
// serve/database/migrations/2026_09_01_000003_create_usage_tables.php
return new class extends Migration {
    public function up(): void
    {
        Schema::create('usage_periods', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('period_start');
            $table->timestamp('period_end');
            $table->unsignedBigInteger('used_uv')->default(0);
            $table->timestamps();
            $table->unique(['user_id', 'period_start']);
            $table->index(['user_id', 'period_start', 'period_end']);
        });

        Schema::create('usage_visitors', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('usage_period_id')->constrained('usage_periods')->cascadeOnDelete();
            $table->char('visitor_hash', 64);
            $table->timestamp('first_seen_at');
            $table->timestamps();
            $table->unique(['usage_period_id', 'visitor_hash']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usage_visitors');
        Schema::dropIfExists('usage_periods');
    }
};
```

```php
// serve/database/migrations/2026_09_01_000004_add_user_auth_and_indexes.php
return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->boolean('must_change_password')->default(false)->after('status');
            $table->index(['type', 'status']);
            $table->index(['parent_id']);
            $table->index(['vip_id', 'end_at']);
        });
        Schema::table('links', function (Blueprint $table): void {
            $table->index(['user_id', 'status']);
        });
        Schema::table('link_visit_logs', function (Blueprint $table): void {
            $table->index(['user_id', 'created_at']);
            $table->index(['link_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropIndex(['type', 'status']);
            $table->dropIndex(['parent_id']);
            $table->dropIndex(['vip_id', 'end_at']);
            $table->dropColumn('must_change_password');
        });
        Schema::table('links', fn (Blueprint $table) => $table->dropIndex(['user_id', 'status']));
        Schema::table('link_visit_logs', function (Blueprint $table): void {
            $table->dropIndex(['user_id', 'created_at']);
            $table->dropIndex(['link_id', 'created_at']);
        });
    }
};
```

模型使用 `$guarded = []` 的现有约定，同时将日期和 JSON 字段显式 cast：`MembershipChange` 的 `effective_at`、`UsagePeriod` 的 `period_start/period_end`、`UsageVisitor` 的 `first_seen_at`、`VipLogs` 的 `start_at/end_at/effective_at` 和 `User` 的 `start_at/end_at` cast 为 `datetime`，快照 cast 为 `array`。`VipLogs` 加入 `actor()`、`user()`、`vipPackage()` 关系；`MembershipChange` 加入 `actor()`、`user()`、`fromPackage()`、`toPackage()` 关系；`UsagePeriod` 加入 `visitors()`，`UsageVisitor` 加入 `period()`。`User` 使用 `HasFactory`、将 `must_change_password` cast 为 `boolean`，并将 `password`、`remember_token`、`tokens` 放入 `$hidden`。`UserFactory` 必须生成现有表真正需要的 `username`、`password`、`status`、`type` 和唯一 `referral_code`，不能继续生成不存在的 `name`、`email` 列。

```php
// serve/database/factories/UserFactory.php
public function definition(): array
{
    return [
        'username' => fake()->unique()->numerify('138########'),
        'password' => static::$password ??= Hash::make('password'),
        'status' => true,
        'type' => UserType::MEMBER,
        'referral_code' => fake()->unique()->regexify('[A-Z0-9]{8}'),
    ];
}
```

- [ ] **Step 5: Run fresh migration and schema tests**

Run: `docker compose -f compose.test.yaml up -d --wait && serve/bin/test-env php artisan migrate:fresh --seed && serve/bin/test-env php artisan test tests/Feature/DatabaseSchemaTest.php -v`

Expected: PASS；隔离 MySQL 8 测试数据库创建四张基础新表、全部旧业务表和 Sanctum token 表，重复执行 migration 不报错。

- [ ] **Step 6: Verify migration rollback and commit**

Run: `docker compose -f compose.test.yaml up -d --wait && serve/bin/test-env sh -lc 'php artisan migrate:rollback --step=5 && php artisan migrate --force && php artisan test tests/Feature/DatabaseSchemaTest.php -v'`

Expected: rollback/re-run 成功，schema assertions 仍 PASS。

```bash
git add serve/database/migrations/2019_12_14_000001_create_personal_access_tokens_table.php serve/database/migrations/2026_09_01_000001_add_membership_audit_fields.php serve/database/migrations/2026_09_01_000002_create_membership_changes_table.php serve/database/migrations/2026_09_01_000003_create_usage_tables.php serve/database/migrations/2026_09_01_000004_add_user_auth_and_indexes.php serve/app/Models/MembershipChange.php serve/app/Models/UsagePeriod.php serve/app/Models/UsageVisitor.php serve/app/Models/VipLogs.php serve/app/Models/User.php serve/tests/Feature/DatabaseSchemaTest.php
git diff --cached --check
git commit -m "feat: add membership audit and usage schema"
```

### Task 6: Secret-at-rest 加密、轮换与 JSON 确定性

**Files:**
- Create: serve/app/Services/SecretConfigService.php
- Create: serve/app/Console/Commands/EncryptLegacySecrets.php
- Create: serve/app/Console/Commands/SecretStorageStatus.php
- Create: serve/database/migrations/2026_09_01_000005_expand_secret_columns.php
- Modify: serve/app/Models/SysConfig.php:8-41
- Modify: serve/app/Services/SystemConfig.php:8-54
- Modify: serve/app/Models/MiniProgram.php:11-20
- Modify: serve/config/app.php:119-127
- Modify: serve/app/Forms/BaseConfig.php:1-90
- Modify: serve/.env.example:1-60
- Create: serve/tests/Feature/SecretAtRestTest.php
- Create: serve/tests/Feature/SecretRotationTest.php

**Interfaces:**
- Consumes: Laravel Crypt/Encrypter、APP_KEY、APP_PREVIOUS_KEYS、固定 secret slugs 和现有 sys_configs/mini_programs 数据。
- Produces: SecretConfigService::get(string $slug, mixed $default = null): mixed；set(string $slug, string $value): void；mask(string $slug): ?string；configured(string $slug): bool；EncryptLegacySecrets::handle(): int；`app:secret-storage-status --json`。SysConfig secret value 统一存 enc:v1: 加密密文；status 命令只输出 `plaintext_count/encrypted_count/compatible` 和退出码，绝不输出 slug 值或密文；解密失败只返回 SECRET_DECRYPTION_FAILED，不回显原文、密钥或异常堆栈。

固定 secret slugs 为 ali_sms_key、ali_sms_secret、mail_password、wechat_pay_secret_key、wechat_pay_private_cert、wechat_pay_certificate；非 secret 的 sign_name、mail_host、mail_username、mail_from_* 继续使用普通 SystemConfig。短信/邮箱 gateway 的 secret 读取必须经 SecretConfigService；历史微信支付 slugs 只做存量加密迁移和 configured/mask 管理，第一阶段不启用支付消费者、支付路由或支付状态变更，前端计划不重新暴露支付入口。

- [ ] **Step 1: Write failing at-rest, rotation and JSON tests**

~~~php
// serve/tests/Feature/SecretAtRestTest.php
namespace Tests\Feature;

use App\Services\SecretConfigService;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

final class SecretAtRestTest extends TestCase
{
    public function test_legacy_value_is_backed_up_then_encrypted_and_never_returned_raw(): void
    {
        DB::table('sys_configs')->updateOrInsert(['slug' => 'ali_sms_secret'], ['value' => 'legacy-secret', 'desc' => 'secret']);
        $this->artisan('app:encrypt-legacy-secrets')->assertExitCode(0);
        $stored = DB::table('sys_configs')->where('slug', 'ali_sms_secret')->value('value');
        $this->assertStringStartsWith('enc:v1:', $stored);
        $this->assertStringNotContainsString('legacy-secret', $stored);
        $this->assertSame('legacy-secret', app(SecretConfigService::class)->get('ali_sms_secret'));
        $this->assertSame('********', substr(app(SecretConfigService::class)->mask('ali_sms_secret'), 0, 8));
    }

    public function test_empty_update_does_not_overwrite_existing_secret(): void
    {
        app(SecretConfigService::class)->set('ali_sms_key', 'keep-me');
        app(SecretConfigService::class)->set('ali_sms_key', '');
        $this->assertSame('keep-me', app(SecretConfigService::class)->get('ali_sms_key'));
    }
}
~~~

~~~php
// serve/tests/Feature/SecretRotationTest.php
namespace Tests\Feature;

use App\Exceptions\BusinessRuleException;
use App\Services\SecretConfigService;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

final class SecretRotationTest extends TestCase
{
    public function test_previous_key_can_read_and_reencrypt_with_current_key(): void
    {
        app(SecretConfigService::class)->set('mail_password', 'old-password');
        $cipher = DB::table('sys_configs')->where('slug', 'mail_password')->value('value');
        $oldKey = config('app.key');
        config(['app.previous_keys' => [$oldKey], 'app.key' => 'base64:'.base64_encode(random_bytes(32))]);
        app()->forgetInstance('encrypter');
        $this->assertContains($oldKey, \Illuminate\Support\Facades\Crypt::getPreviousKeys());
        $this->assertSame('old-password', app(SecretConfigService::class)->get('mail_password'));
        app(SecretConfigService::class)->set('mail_password', 'new-password');
        $this->assertNotSame($cipher, DB::table('sys_configs')->where('slug', 'mail_password')->value('value'));
    }

    public function test_wrong_key_has_stable_error_without_secret(): void
    {
        app(SecretConfigService::class)->set('ali_sms_secret', 'do-not-leak');
        config(['app.key' => 'base64:'.base64_encode(random_bytes(32)), 'app.previous_keys' => []]);
        app()->forgetInstance('encrypter');
        try {
            app(SecretConfigService::class)->get('ali_sms_secret');
            $this->fail('错误密钥必须失败');
        } catch (BusinessRuleException $exception) {
            $this->assertSame('SECRET_DECRYPTION_FAILED', $exception->errorCode);
            $this->assertStringNotContainsString('do-not-leak', $exception->getMessage());
        }
    }
}
~~~

测试另外断言数据库、API JSON、Redis/cache、访问日志和模型序列化结果均不包含原 secret；迁移前备份文件只允许出现在 storage/app/private/secret-backups，内容必须是当前 APP_KEY 加密的 JSON 且权限 0600，不能进入 Git。
测试还要先插入一条历史明文和一条密文，断言 `app:secret-storage-status --json` 只返回数量与 `compatible=true`；执行加密后 `plaintext_count=0`，不得包含任何原值。

- [ ] **Step 2: Run red tests before SecretConfigService exists**

Run: docker compose -f compose.test.yaml up -d --wait && serve/bin/test-env php artisan test tests/Feature/SecretAtRestTest.php tests/Feature/SecretRotationTest.php -v

Expected: FAIL because SecretConfigService, the legacy-encryption command and the at-rest migration do not exist.

- [ ] **Step 3: Add encrypted column migration and deterministic SysConfig JSON**

~~~php
// serve/database/migrations/2026_09_01_000005_expand_secret_columns.php
return new class extends Migration {
    public function up(): void
    {
        Schema::table('mini_programs', fn (Blueprint $table) => $table->text('secret')->change());
        Schema::table('sys_configs', fn (Blueprint $table) => $table->longText('value')->change());
    }

    public function down(): void
    {
        Schema::table('mini_programs', fn (Blueprint $table) => $table->string('secret', 255)->change());
        Schema::table('sys_configs', fn (Blueprint $table) => $table->longText('value')->change());
    }
};
~~~

SysConfig::value 的 getter 用 json_decode($value, true, 512, JSON_THROW_ON_ERROR) 处理 JSON；setter 对数组递归按 key 排序，再用 JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PRESERVE_ZERO_FRACTION|JSON_THROW_ON_ERROR 编码。解析失败返回原字符串并记录不含敏感上下文的诊断；SystemConfig::set 只调用该 deterministic accessor，不再直接 json_encode 数组。

- [ ] **Step 4: Implement SecretConfigService, legacy command and encrypted model cast**

~~~php
// serve/app/Services/SecretConfigService.php
namespace App\Services;

use App\Exceptions\BusinessRuleException;
use App\Models\SysConfig;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

final class SecretConfigService
{
    private const SLUGS = ['ali_sms_key', 'ali_sms_secret', 'mail_password', 'wechat_pay_secret_key', 'wechat_pay_private_cert', 'wechat_pay_certificate'];

    public function get(string $slug, mixed $default = null): mixed
    {
        $this->assertSlug($slug);
        $raw = SysConfig::query()->whereKey($slug)->value('value');
        if ($raw === null || $raw === '') return $default;
        if (! Str::startsWith($raw, 'enc:v1:')) return $raw;
        try {
            return Crypt::decryptString(Str::after($raw, 'enc:v1:'));
        } catch (\Throwable $exception) {
            throw new BusinessRuleException('SECRET_DECRYPTION_FAILED', '受保护配置无法解密', 500);
        }
    }

    public function set(string $slug, string $value): void
    {
        $this->assertSlug($slug);
        if ($value === '') return;
        SysConfig::query()->updateOrCreate(['slug' => $slug], ['value' => 'enc:v1:'.Crypt::encryptString($value)]);
    }

    public function configured(string $slug): bool { return filled($this->get($slug)); }
    public function mask(string $slug): ?string
    {
        $value = $this->get($slug);
        return filled($value) ? '********'.substr((string) $value, -4) : null;
    }
    public function secretSlugs(): array { return self::SLUGS; }
    private function assertSlug(string $slug): void
    {
        if (! in_array($slug, self::SLUGS, true)) throw new \InvalidArgumentException('不是受保护配置');
    }
}
~~~

~~~php
// serve/app/Console/Commands/EncryptLegacySecrets.php
final class EncryptLegacySecrets extends Command
{
    protected $signature = 'app:encrypt-legacy-secrets';
    protected $description = '将历史明文受保护配置加密并生成受保护备份';

    public function handle(SecretConfigService $secrets): int
    {
        $legacy = [];
        foreach ($secrets->secretSlugs() as $slug) {
            $raw = DB::table('sys_configs')->where('slug', $slug)->value('value');
            if (filled($raw) && ! str_starts_with($raw, 'enc:v1:')) {
                $legacy['sys_config:'.$slug] = $raw;
            }
        }
        foreach (DB::table('mini_programs')->select(['id', 'secret'])->whereNotNull('secret')->get() as $row) {
            try { Crypt::decryptString($row->secret); } catch (\Throwable $exception) { if (filled($row->secret)) $legacy['mini_program:'.$row->id] = $row->secret; }
        }
        if ($legacy !== []) {
            $dir = storage_path('app/private/secret-backups');
            File::ensureDirectoryExists($dir, 0700);
            $path = $dir.'/'.now()->format('YmdHis').'-'.Str::uuid().'.json.enc';
            File::put($path, Crypt::encryptString(json_encode($legacy, JSON_THROW_ON_ERROR)));
            chmod($path, 0600);
        }
        DB::transaction(function () use ($secrets): void {
            foreach ($secrets->secretSlugs() as $slug) {
                $raw = DB::table('sys_configs')->where('slug', $slug)->value('value');
                if (filled($raw) && ! str_starts_with($raw, 'enc:v1:')) {
                    DB::table('sys_configs')->where('slug', $slug)->update(['value' => 'enc:v1:'.Crypt::encryptString($raw)]);
                }
            }
            foreach (DB::table('mini_programs')->select(['id', 'secret'])->whereNotNull('secret')->get() as $row) {
                try { Crypt::decryptString($row->secret); continue; } catch (\Throwable $exception) { }
                if (filled($row->secret)) {
                    DB::table('mini_programs')->where('id', $row->id)->update(['secret' => Crypt::encryptString($row->secret)]);
                }
            }
        });
        return self::SUCCESS;
    }
}
~~~

命令用 DB::transaction，写入前在受保护目录生成当前 APP_KEY 加密备份；重复运行跳过 enc:v1: 和已加密 MiniProgram 值。MiniProgram 的 secret cast 改为 encrypted；迁移与命令全部使用原始 DB 查询，避免旧明文在 cast 切换时提前解密。
`SecretStorageStatus` 复用同一 secret slug 清单，对 MiniProgram 原始值只做受控的解密可读性检查并计数；命令存在本身就是 release 支持 encrypted-secret 格式的机器可读标记，供发布/回滚脚本判定兼容下限。

- [ ] **Step 5: Route every secret consumer through SecretConfigService**

BaseConfig、AliyunSmsGateway、LaravelMailGateway 只从 SecretConfigService 读取固定 secret slugs；历史微信支付 slugs 不接入活动支付代码或路由。控制器保存 secret 调用 set，空字符串不覆盖已有值，读取接口只输出 configured/mask。config/app 增加：

~~~php
'previous_keys' => array_values(array_filter(explode(',', (string) env('APP_PREVIOUS_KEYS', '')))),
~~~

serve/.env.example 增加 APP_PREVIOUS_KEYS=；前端计划只能消费 configured/mask 字段。Laravel 13 的 EncryptionServiceProvider 会把 config/app.php 的 key 作为当前密钥，再把 previous_keys 传给 Encrypter::previousKeys(array $keys)；加密始终使用当前 key，解密按当前 key 后依次尝试 previous_keys。SecretConfigService 只调用 Crypt facade，不自行实现多密钥算法。

轮换测试必须断言 `Crypt::getPreviousKeys()` 包含旧 key，再用新 key 加密一条新值并确认新密文不能用旧 key 读取；错 key 且 previous_keys 为空时只返回 SECRET_DECRYPTION_FAILED。

- [ ] **Step 6: Run migration, at-rest, rotation and consumer tests**

Run: docker compose -f compose.test.yaml up -d --wait && serve/bin/test-env php artisan migrate:fresh --seed && serve/bin/test-env php artisan test tests/Feature/SecretAtRestTest.php tests/Feature/SecretRotationTest.php -v && serve/bin/test-env php artisan app:secret-storage-status --json

Expected: PASS；legacy secret 先备份后加密，空更新不覆盖，APP_PREVIOUS_KEYS 可读并可重加密，错误密钥只返回 SECRET_DECRYPTION_FAILED；数据库/API/cache/log/序列化无明文；status JSON 为 `compatible=true`且不包含敏感值。

- [ ] **Step 7: Commit Secret-at-rest boundary**

~~~bash
git add serve/app/Services/SecretConfigService.php serve/app/Console/Commands/EncryptLegacySecrets.php serve/app/Console/Commands/SecretStorageStatus.php serve/database/migrations/2026_09_01_000005_expand_secret_columns.php serve/app/Models/SysConfig.php serve/app/Services/SystemConfig.php serve/app/Models/MiniProgram.php serve/config/app.php serve/app/Forms/BaseConfig.php serve/.env.example serve/tests/Feature/SecretAtRestTest.php serve/tests/Feature/SecretRotationTest.php
git diff --cached --check
git commit -m "security: encrypt persisted service secrets"
~~~

### Task 7: 将初始化数据迁移到幂等 Seeder，并建立受控管理员 Provisioner

**Files:**
- Create: `serve/database/seeders/VipPackageSeeder.php`
- Create: `serve/database/seeders/SystemConfigSeeder.php`
- Create: `serve/database/seeders/MaterialCategorySeeder.php`
- Create: `serve/app/Services/AdminProvisioner.php`
- Create: `serve/app/Console/Commands/ProvisionAdmin.php`
- Modify: `serve/database/seeders/DatabaseSeeder.php:8-21`
- Modify: `serve/app/Console/Commands/SystemInit.php:28-49`
- Create: `serve/tests/Feature/SeederIdempotencyTest.php`
- Create: `serve/tests/Feature/AdminProvisionerTest.php`

**Interfaces:**
- Consumes: `vip_packages`、`sys_configs`、`material_categories` 表和 `UserType::Admin`。
- Produces: `VipPackageSeeder::run(): void`、`SystemConfigSeeder::run(): void`、`MaterialCategorySeeder::run(): void`、`AdminProvisioner::provision(string $username, string $plainPassword): User`、`app:admin-provision {username?}`。

- [ ] **Step 1: Write failing seeder and provisioner tests**

```php
// serve/tests/Feature/SeederIdempotencyTest.php
namespace Tests\Feature;

use App\Models\MaterialCategory;
use App\Models\SysConfig;
use App\Models\VipPackage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class SeederIdempotencyTest extends TestCase
{
    public function test_database_seeder_is_idempotent_and_has_all_allow_type_keys(): void
    {
        $this->seed();
        $this->seed();

        $this->assertSame(3, VipPackage::query()->count());
        $this->assertSame(2, MaterialCategory::query()->count());
        $this->assertSame(1, SysConfig::query()->where('slug', 'give_vip_days')->count());
        $this->assertSame(['CLI_QR', 'KING_DOC', 'LANDING_MINI', 'MINI_PROGRAM', 'QQ_QR', 'WORK_WECHAT'], array_keys(VipPackage::query()->firstOrFail()->config['allow_type']));
        $this->assertDatabaseCount('users', 0);
    }
}
```

```php
// serve/tests/Feature/AdminProvisionerTest.php
namespace Tests\Feature;

use App\Enums\UserType;
use App\Services\AdminProvisioner;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

final class AdminProvisionerTest extends TestCase
{
    public function test_provisioner_creates_an_admin_without_a_default_password(): void
    {
        $admin = app(AdminProvisioner::class)->provision('owner', 'correct-horse-battery-staple');

        $this->assertSame(UserType::Admin, $admin->type);
        $this->assertTrue(Hash::check('correct-horse-battery-staple', $admin->password));
        $this->assertTrue($admin->must_change_password);
        $this->assertDatabaseMissing('users', ['username' => 'admin']);
    }
}
```

- [ ] **Step 2: Run the tests to verify the current SQL-based initialization fails**

Run: `docker compose -f compose.test.yaml up -d --wait && serve/bin/test-env php artisan test tests/Feature/SeederIdempotencyTest.php tests/Feature/AdminProvisionerTest.php -v`

Expected: FAIL because `DatabaseSeeder` is empty, `SystemInit` reads `packages.sql`, and `AdminProvisioner` does not exist.

- [ ] **Step 3: Implement deterministic, idempotent seeders**

```php
// serve/database/seeders/VipPackageSeeder.php
namespace Database\Seeders;

use App\Models\VipPackage;
use Illuminate\Database\Seeder;

final class VipPackageSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['id' => 1, 'name' => '体验套餐', 'price' => 0, 'level' => 0, 'config' => ['pre_min' => false, 'support' => true, 'uv_limit' => 9, 'cur_index' => false, 'count_limit' => 1, 'min_count_limit' => 1, 'allow_type' => ['CLI_QR' => false, 'KING_DOC' => false, 'LANDING_MINI' => false, 'MINI_PROGRAM' => true, 'QQ_QR' => false, 'WORK_WECHAT' => false]]],
            ['id' => 2, 'name' => '初级会员', 'price' => 4300, 'level' => 1, 'config' => ['pre_min' => true, 'support' => true, 'uv_limit' => 50, 'cur_index' => true, 'count_limit' => 5, 'min_count_limit' => 5, 'allow_type' => ['CLI_QR' => true, 'KING_DOC' => true, 'LANDING_MINI' => true, 'MINI_PROGRAM' => true, 'QQ_QR' => true, 'WORK_WECHAT' => true]]],
            ['id' => 3, 'name' => '高级会员', 'price' => 29800, 'level' => 2, 'config' => ['pre_min' => true, 'support' => true, 'uv_limit' => 100000, 'cur_index' => true, 'count_limit' => 50, 'min_count_limit' => 10, 'allow_type' => ['CLI_QR' => true, 'KING_DOC' => true, 'LANDING_MINI' => true, 'MINI_PROGRAM' => true, 'QQ_QR' => true, 'WORK_WECHAT' => true]]],
        ];

        foreach ($rows as $row) {
            VipPackage::query()->updateOrCreate(['id' => $row['id']], $row);
        }
    }
}
```

`SystemConfigSeeder` 用固定 `slug`、`value`、`desc` 键调用 `updateOrCreate` 写入 `give_vip_days=3`、`give_vip_id=1`、`is_give_vip=1` 和非敏感品牌/验证码开关；短信、邮件、支付密钥值固定为空并由环境/后台安全配置注入。具体实现如下：

```php
// serve/database/seeders/SystemConfigSeeder.php
namespace Database\Seeders;

use App\Models\SysConfig;
use Illuminate\Database\Seeder;

final class SystemConfigSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['slug' => 'give_vip_days', 'value' => '3', 'desc' => '注册赠送会员有效期/天'],
            ['slug' => 'give_vip_id', 'value' => '1', 'desc' => '赠送套餐'],
            ['slug' => 'is_give_vip', 'value' => '1', 'desc' => '是否开启注册赠送会员'],
            ['slug' => 'verify_code_is_open', 'value' => '0', 'desc' => '是否开启验证码'],
            ['slug' => 'send_code_mode', 'value' => '2', 'desc' => '发送验证码类型'],
            ['slug' => 'ali_sms_key', 'value' => '', 'desc' => '阿里短信 key 从受控配置注入'],
            ['slug' => 'ali_sms_secret', 'value' => '', 'desc' => '阿里短信 secret 从受控配置注入'],
            ['slug' => 'ali_sms_sign_name', 'value' => '', 'desc' => '阿里短信签名从受控配置注入'],
            ['slug' => 'mail_password', 'value' => '', 'desc' => '邮件密码从受控配置注入'],
        ];
        foreach ($rows as $row) {
            SysConfig::query()->updateOrCreate(['slug' => $row['slug']], ['value' => $row['value'], 'desc' => $row['desc']]);
        }
    }
}
```

`MaterialCategorySeeder` 用固定 ID 1/2 写入“系统”和“其他”。`DatabaseSeeder::run()` 按 `VipPackageSeeder`、`SystemConfigSeeder`、`MaterialCategorySeeder` 顺序调用，不创建 User。

```php
// serve/database/seeders/MaterialCategorySeeder.php
namespace Database\Seeders;

use App\Models\MaterialCategory;
use Illuminate\Database\Seeder;

final class MaterialCategorySeeder extends Seeder
{
    public function run(): void
    {
        foreach ([[1, '系统', 1, 1], [2, '其他', 0, 0]] as [$id, $name, $sort, $type]) {
            MaterialCategory::query()->updateOrCreate(['id' => $id], compact('name', 'sort', 'type'));
        }
    }
}
```

```php
// serve/database/seeders/DatabaseSeeder.php
public function run(): void
{
    $this->call([VipPackageSeeder::class, SystemConfigSeeder::class, MaterialCategorySeeder::class]);
}
```

- [ ] **Step 4: Implement `AdminProvisioner` and controlled command**

```php
// serve/app/Services/AdminProvisioner.php
namespace App\Services;

use App\Enums\UserType;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use InvalidArgumentException;
use LogicException;

final class AdminProvisioner
{
    public function provision(string $username, string $plainPassword): User
    {
        if (mb_strlen($plainPassword) < 12) {
            throw new InvalidArgumentException('管理员密码至少 12 位');
        }

        return DB::transaction(function () use ($username, $plainPassword): User {
            $user = User::query()->lockForUpdate()->where('username', $username)->first();
            if ($user && $user->type !== UserType::Admin) {
                throw new LogicException('该账号已存在且不是管理员');
            }
            $user ??= new User(['username' => $username]);
            $user->forceFill([
                'password' => Hash::make($plainPassword),
                'type' => UserType::Admin,
                'status' => true,
                'must_change_password' => true,
                'referral_code' => $user->referral_code ?: Str::upper(Str::random(8)),
            ])->save();

            return $user->refresh();
        });
    }
}
```

```php
// serve/app/Console/Commands/ProvisionAdmin.php
namespace App\Console\Commands;

use App\Services\AdminProvisioner;
use Illuminate\Console\Command;

final class ProvisionAdmin extends Command
{
    protected $signature = 'app:admin-provision {username?}';
    protected $description = '通过受控交互创建或更新首个管理员';

    public function handle(AdminProvisioner $provisioner): int
    {
        $username = (string) ($this->argument('username') ?: $this->ask('管理员用户名'));
        $password = (string) $this->secret('管理员密码（至少 12 位）');
        try {
            $admin = $provisioner->provision($username, $password);
        } catch (\Throwable $exception) {
            $this->error($exception->getMessage());
            return self::FAILURE;
        }
        $this->info('管理员已创建：'.$admin->username.'；首次登录必须修改密码。');
        return self::SUCCESS;
    }
}
```

`ProvisionAdmin` 用 `$this->ask()` 获取用户名、`$this->secret()` 获取密码，不接受密码命令行参数；密码低于 12 位直接返回 `Command::FAILURE`。命令输出只报告用户名和成功/失败，不打印密码、token 或数据库快照。`SystemInit` 改为提示使用 `php artisan migrate --seed` 与 `php artisan app:admin-provision`，移除 `file_get_contents(database_path('packages.sql'))`、固定 `admin` 和固定密码逻辑。

- [ ] **Step 5: Run fresh install, duplicate seed and provisioner tests**

Run: `docker compose -f compose.test.yaml up -d --wait && serve/bin/test-env php artisan migrate:fresh --seed && serve/bin/test-env php artisan test tests/Feature/SeederIdempotencyTest.php tests/Feature/AdminProvisionerTest.php -v`

Expected: PASS；连续两次 `--seed` 不重复套餐/分类/配置，数据库中没有隐式管理员；Provisioner 只创建受控管理员并设置 `must_change_password=true`。

- [ ] **Step 6: Commit seeders and controlled admin provisioning**

```bash
git add serve/database/seeders/VipPackageSeeder.php serve/database/seeders/SystemConfigSeeder.php serve/database/seeders/MaterialCategorySeeder.php serve/database/seeders/DatabaseSeeder.php serve/app/Services/AdminProvisioner.php serve/app/Console/Commands/ProvisionAdmin.php serve/app/Console/Commands/SystemInit.php serve/tests/Feature/SeederIdempotencyTest.php serve/tests/Feature/AdminProvisionerTest.php
git diff --cached --check
git commit -m "feat: make installation seeders idempotent"
```

### Task 8: 实现 RollingPeriod 并锁定滚动月份边界

**Files:**
- Create: `serve/app/ValueObjects/RollingPeriod.php`
- Create: `serve/tests/Unit/RollingPeriodTest.php`

**Interfaces:**
- Consumes: `users.start_at` 锚点、显式的 `CarbonImmutable $at` 和固定 `Asia/Shanghai` 时区。
- Produces: `RollingPeriod::forAnchor(CarbonImmutable $anchor, CarbonImmutable $at): RollingPeriod`、`start(): CarbonImmutable`、`end(): CarbonImmutable`、`contains(CarbonImmutable $instant): bool`、`anchorDay(): int`。

- [ ] **Step 1: Write failing boundary tests**

```php
// serve/tests/Unit/RollingPeriodTest.php
namespace Tests\Unit;

use App\ValueObjects\RollingPeriod;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;

final class RollingPeriodTest extends TestCase
{
    public function test_fifteenth_anchor_is_half_open(): void
    {
        $anchor = CarbonImmutable::parse('2026-09-15 10:00:00', 'Asia/Shanghai');
        $period = RollingPeriod::forAnchor($anchor, CarbonImmutable::parse('2026-10-15 09:59:59', 'Asia/Shanghai'));
        $this->assertSame('2026-09-15 10:00:00', $period->start()->format('Y-m-d H:i:s'));
        $this->assertSame('2026-10-15 10:00:00', $period->end()->format('Y-m-d H:i:s'));
        $this->assertTrue($period->contains($period->start()));
        $this->assertFalse($period->contains($period->end()));
    }

    public function test_thirty_first_anchor_clamps_only_the_missing_month(): void
    {
        $anchor = CarbonImmutable::parse('2024-01-31 08:00:00', 'Asia/Shanghai');
        $february = RollingPeriod::forAnchor($anchor, CarbonImmutable::parse('2024-02-29 08:00:00', 'Asia/Shanghai'));
        $march = RollingPeriod::forAnchor($anchor, CarbonImmutable::parse('2024-03-31 08:00:00', 'Asia/Shanghai'));
        $this->assertSame('2024-01-31 08:00:00', $february->start()->format('Y-m-d H:i:s'));
        $this->assertSame('2024-02-29 08:00:00', $february->end()->format('Y-m-d H:i:s'));
        $this->assertSame('2024-03-31 08:00:00', $march->start()->format('Y-m-d H:i:s'));
        $this->assertSame(31, $march->anchorDay());
    }
}
```

- [ ] **Step 2: Run the tests to verify the value object is missing**

Run: `docker compose -f compose.test.yaml up -d --wait && serve/bin/test-env vendor/bin/phpunit tests/Unit/RollingPeriodTest.php -v`

Expected: FAIL with `Class "App\ValueObjects\RollingPeriod" not found`。

- [ ] **Step 3: Implement the value object with explicit month-end clamping**

```php
// serve/app/ValueObjects/RollingPeriod.php
namespace App\ValueObjects;

use Carbon\CarbonImmutable;
use InvalidArgumentException;

final readonly class RollingPeriod
{
    private function __construct(private CarbonImmutable $start, private CarbonImmutable $end, private int $anchorDay) {}

    public static function forAnchor(CarbonImmutable $anchor, CarbonImmutable $at): self
    {
        $anchor = $anchor->setTimezone('Asia/Shanghai');
        $at = $at->setTimezone('Asia/Shanghai');
        if ($anchor->isFuture()) {
            throw new InvalidArgumentException('会员锚点不能晚于当前时间');
        }

        $anchorDay = $anchor->day;
        $candidate = self::monthDate($anchor, $at->year, $at->month, $anchorDay);
        if ($candidate->gt($at)) {
            $previous = $at->subMonthNoOverflow();
            $candidate = self::monthDate($anchor, $previous->year, $previous->month, $anchorDay);
        }
        return new self($candidate, self::monthDate($anchor, $candidate->addMonthNoOverflow()->year, $candidate->addMonthNoOverflow()->month, $anchorDay), $anchorDay);
    }

    private static function monthDate(CarbonImmutable $anchor, int $year, int $month, int $day): CarbonImmutable
    {
        $first = CarbonImmutable::create($year, $month, 1, $anchor->hour, $anchor->minute, $anchor->second, 'Asia/Shanghai');
        return $first->setDay(min($day, $first->daysInMonth));
    }

    public function start(): CarbonImmutable { return $this->start; }
    public function end(): CarbonImmutable { return $this->end; }
    public function anchorDay(): int { return $this->anchorDay; }
    public function contains(CarbonImmutable $instant): bool
    {
        $instant = $instant->setTimezone('Asia/Shanghai');
        return $instant->gte($this->start) && $instant->lt($this->end);
    }
}
```

- [ ] **Step 4: Run leap-year, 29/30/31 and boundary tests**

Run: `docker compose -f compose.test.yaml up -d --wait && serve/bin/test-env vendor/bin/phpunit tests/Unit/RollingPeriodTest.php -v`

Expected: PASS；补充 2025-01-31→2025-02-28、2025-04-30→2025-05-30、2024-02-29→2024-03-29 测试后仍 PASS。

- [ ] **Step 5: Commit the rolling-period contract**

```bash
git add serve/app/ValueObjects/RollingPeriod.php serve/tests/Unit/RollingPeriodTest.php
git diff --cached --check
git commit -m "feat: add anchored rolling membership periods"
```

### Task 9: 以事务和审计流水实现 MembershipService

**Files:**
- Create: `serve/app/Enums/MembershipState.php`
- Create: `serve/app/Services/MembershipService.php`
- Modify: `serve/app/Models/VipLogs.php:10-29`
- Modify: `serve/app/Models/MembershipChange.php:1-80`
- Modify: `serve/app/Http/Controllers/Api/UserController.php:30-79`
- Create: `serve/tests/Unit/MembershipServiceTest.php`
- Create: `serve/tests/Feature/AdminMembershipTest.php`

**Interfaces:**
- Consumes: `User`、`VipPackage`、`VipLogs`、`MembershipChange` 和数据库事务；Task 7 的 `give_vip_days/give_vip_id/is_give_vip` 配置。
- Produces: `grantConfiguredTrial(User $user): ?VipLogs`、`open(User $user, VipPackage $package, User $actor, string $reason, string $idempotencyKey): VipLogs`、`renew(User $user, VipPackage $package, User $actor, string $reason, string $idempotencyKey): VipLogs`、`upgrade(User $user, VipPackage $package, User $actor, string $reason, string $idempotencyKey): VipLogs`、`scheduleDowngrade(User $user, VipPackage $package, User $actor, string $reason, string $idempotencyKey): MembershipChange`、`revoke(User $user, User $actor, string $reason, string $idempotencyKey): VipLogs`、`expireDue(CarbonImmutable $at): int`、`applyDueChanges(CarbonImmutable $at): int`。每个变更锁定用户、写 `before_snapshot/after_snapshot` 和唯一 `idempotency_key`。

- [ ] **Step 1: Write failing lifecycle tests**

```php
// serve/tests/Unit/MembershipServiceTest.php
namespace Tests\Unit;

use App\Enums\MembershipState;
use App\Models\User;
use App\Models\VipPackage;
use App\Services\MembershipService;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Date;
use Tests\TestCase;

final class MembershipServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_first_open_sets_new_anchor_and_audit_snapshot(): void
    {
        Date::setTestNow('2026-09-15 10:00:00');
        $admin = User::factory()->create(['type' => 3, 'status' => true]);
        $member = User::factory()->create(['type' => 1, 'status' => true]);
        $package = VipPackage::query()->findOrFail(2);

        $log = app(MembershipService::class)->open($member, $package, $admin, '人工开通', '00000000-0000-0000-0000-000000000001');

        $this->assertSame($package->id, $member->refresh()->vip_id);
        $this->assertSame('2026-09-15 10:00:00', $member->start_at->format('Y-m-d H:i:s'));
        $this->assertSame('2026-10-15 10:00:00', $member->end_at->format('Y-m-d H:i:s'));
        $this->assertSame('open', $log->action);
        $this->assertNotEmpty($log->after_snapshot);
    }

    public function test_same_package_renews_from_existing_end_and_reuses_anchor(): void
    {
        Date::setTestNow('2026-09-15 10:00:00');
        $member = User::factory()->create(['type' => 1, 'status' => true, 'vip_id' => 2, 'start_at' => '2026-08-15 10:00:00', 'end_at' => '2026-10-15 10:00:00']);
        $actor = User::factory()->create(['type' => 3]);
        $log = app(MembershipService::class)->renew($member, VipPackage::query()->findOrFail(2), $actor, '续期', '00000000-0000-0000-0000-000000000002');
        $this->assertSame('2026-11-15 10:00:00', $member->refresh()->end_at->format('Y-m-d H:i:s'));
        $this->assertSame('renew', $log->action);
    }

    public function test_downgrade_is_pending_until_period_end_and_revoke_is_immediate(): void
    {
        Date::setTestNow('2026-09-20 10:00:00');
        $member = User::factory()->create(['type' => 1, 'status' => true, 'vip_id' => 3, 'start_at' => '2026-09-15 10:00:00', 'end_at' => '2026-10-15 10:00:00']);
        $actor = User::factory()->create(['type' => 3]);
        app(MembershipService::class)->scheduleDowngrade($member, VipPackage::query()->findOrFail(2), $actor, '降级', '00000000-0000-0000-0000-000000000003');
        $this->assertDatabaseHas('membership_changes', ['user_id' => $member->id, 'status' => 'pending']);
        app(MembershipService::class)->revoke($member, $actor, '撤销', '00000000-0000-0000-0000-000000000004');
        $this->assertNull($member->refresh()->vip_id);
        $this->assertNull($member->start_at);
    }

    public function test_upgrade_changes_package_without_resetting_anchor_or_used_period(): void
    {
        Date::setTestNow('2026-09-20 10:00:00');
        $member = User::factory()->create(['type' => 1, 'status' => true, 'vip_id' => 2, 'start_at' => '2026-09-15 10:00:00', 'end_at' => '2026-10-15 10:00:00']);
        $actor = User::factory()->create(['type' => 3]);
        app(MembershipService::class)->upgrade($member, VipPackage::query()->findOrFail(3), $actor, '升级', '00000000-0000-0000-0000-000000000005');
        $member = $member->refresh();
        $this->assertSame(3, $member->vip_id);
        $this->assertSame('2026-09-15 10:00:00', $member->start_at->format('Y-m-d H:i:s'));
        $this->assertSame('2026-10-15 10:00:00', $member->end_at->format('Y-m-d H:i:s'));
    }
}
```

```php
// serve/tests/Feature/AdminMembershipTest.php
namespace Tests\Feature;

use App\Enums\UserType;
use App\Models\User;
use App\Models\VipPackage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

final class AdminMembershipTest extends TestCase
{
    public function test_admin_api_delegates_manual_open_to_membership_service(): void
    {
        $this->seed();
        $admin = User::factory()->create(['type' => UserType::Admin, 'status' => true, 'must_change_password' => false]);
        $member = User::factory()->create(['type' => UserType::MEMBER, 'status' => true]);
        Sanctum::actingAs($admin, ['*'], 'api');
        $key = '00000000-0000-0000-0000-000000000009';

        $this->putJson('/api/users/'.$member->id, [
            'vip_id' => VipPackage::query()->findOrFail(2)->id,
            'action' => 'open', 'reason' => '人工开通', 'idempotency_key' => $key,
        ])->assertOk();

        $this->assertDatabaseHas('users', ['id' => $member->id, 'vip_id' => 2]);
        $this->assertDatabaseHas('vip_logs', ['user_id' => $member->id, 'action' => 'open', 'idempotency_key' => $key]);
    }
}
```

- [ ] **Step 2: Run lifecycle tests to verify direct-controller behavior is insufficient**

Run: `docker compose -f compose.test.yaml up -d --wait && serve/bin/test-env php artisan test tests/Unit/MembershipServiceTest.php tests/Feature/AdminMembershipTest.php -v`

Expected: FAIL because no service exists and `UserController` currently sets `now()->addMonth()` directly without audit or pending downgrade.

- [ ] **Step 3: Implement the membership state and transactional service**

```php
// serve/app/Enums/MembershipState.php
namespace App\Enums;

enum MembershipState: string
{
    case ADMIN = 'admin';
    case TRIAL = 'trial';
    case ACTIVE = 'active';
    case EXPIRED = 'expired';
    case NONE = 'none';
}
```

```php
// serve/app/Services/MembershipService.php
namespace App\Services;

use App\Enums\MembershipState;
use App\Enums\UserType;
use App\Enums\VipStatus;
use App\Models\MembershipChange;
use App\Models\User;
use App\Models\VipLogs;
use App\Models\VipPackage;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Str;
use RuntimeException;

final class MembershipService
{
    public function grantConfiguredTrial(User $user): ?VipLogs
    {
        if (! filter_var(SystemConfig::get('is_give_vip'), FILTER_VALIDATE_BOOL) || (int) SystemConfig::get('give_vip_days') < 1) {
            return null;
        }
        $package = VipPackage::query()->findOrFail((int) SystemConfig::get('give_vip_id'));
        $start = CarbonImmutable::now();
        return $this->openForDuration($user, $package, $start, $start->addDays((int) SystemConfig::get('give_vip_days')), null, '注册体验', Str::uuid()->toString(), 'trial');
    }

    public function open(User $user, VipPackage $package, User $actor, string $reason, string $idempotencyKey): VipLogs
    {
        $start = CarbonImmutable::now();
        return $this->openForDuration($user, $package, $start, $start->addMonth(), $actor, $reason, $idempotencyKey, 'open');
    }

    public function renew(User $user, VipPackage $package, User $actor, string $reason, string $idempotencyKey): VipLogs
    {
        $start = CarbonImmutable::parse($user->start_at);
        $end = CarbonImmutable::parse($user->end_at)->addMonth();
        return $this->openForDuration($user, $package, $start, $end, $actor, $reason, $idempotencyKey, 'renew');
    }

    public function upgrade(User $user, VipPackage $package, User $actor, string $reason, string $idempotencyKey): VipLogs
    {
        return $this->replaceActivePackage($user, $package, $actor, $reason, $idempotencyKey, 'upgrade');
    }

    public function scheduleDowngrade(User $user, VipPackage $package, User $actor, string $reason, string $idempotencyKey): MembershipChange
    {
        return DB::transaction(function () use ($user, $package, $actor, $reason, $idempotencyKey): MembershipChange {
            $existing = MembershipChange::query()->where('idempotency_key', $idempotencyKey)->first();
            if ($existing) { return $existing; }
            $locked = User::query()->lockForUpdate()->findOrFail($user->id);
            if (! $locked->vip_id || ! $locked->start_at || ! $locked->end_at) {
                throw new RuntimeException('没有可降级的有效会员');
            }
            return MembershipChange::query()->create([
                'user_id' => $locked->id, 'from_vip_id' => $locked->vip_id, 'to_vip_id' => $package->id,
                'action' => 'downgrade', 'status' => 'pending', 'effective_at' => $locked->end_at,
                'actor_user_id' => $actor->id, 'reason' => $reason, 'idempotency_key' => $idempotencyKey,
            ]);
        });
    }

    public function revoke(User $user, User $actor, string $reason, string $idempotencyKey): VipLogs
    {
        return DB::transaction(function () use ($user, $actor, $reason, $idempotencyKey): VipLogs {
            $existing = VipLogs::query()->where('idempotency_key', $idempotencyKey)->first();
            if ($existing) { return $existing; }
            $locked = User::query()->lockForUpdate()->findOrFail($user->id);
            $before = $this->snapshot($locked);
            $locked->forceFill(['vip_id' => null, 'start_at' => null, 'end_at' => null])->save();
            return $this->writeLog($locked, $actor, 'revoke', $reason, $before, $this->snapshot($locked), CarbonImmutable::now(), $idempotencyKey);
        });
    }

    public function applyDueChanges(CarbonImmutable $at): int
    {
        $ids = MembershipChange::query()->where('status', 'pending')->where('effective_at', '<=', $at)->pluck('id');
        $processed = 0;
        foreach ($ids as $id) {
            $processed += DB::transaction(function () use ($id, $at): int {
                $change = MembershipChange::query()->lockForUpdate()->find($id);
                if (! $change || $change->status !== 'pending') { return 0; }
                $user = User::query()->lockForUpdate()->findOrFail($change->user_id);
                $before = $this->snapshot($user);
                $user->forceFill(['vip_id' => $change->to_vip_id])->save();
                $change->forceFill(['status' => 'applied'])->save();
                $this->writeLog($user, $change->actor, 'downgrade_applied', $change->reason ?? '到期降级', $before, $this->snapshot($user), $at, 'membership-change:'.$change->id);
                return 1;
            });
        }
        return $processed;
    }

    private function openForDuration(User $user, VipPackage $package, CarbonImmutable $start, CarbonImmutable $end, ?User $actor, string $reason, string $idempotencyKey, string $action): VipLogs
    {
        return DB::transaction(function () use ($user, $package, $start, $end, $actor, $reason, $idempotencyKey, $action): VipLogs {
            $existing = VipLogs::query()->where('idempotency_key', $idempotencyKey)->first();
            if ($existing) { return $existing; }
            $locked = User::query()->lockForUpdate()->findOrFail($user->id);
            $before = $this->snapshot($locked);
            $locked->forceFill(['vip_id' => $package->id, 'start_at' => $start, 'end_at' => $end])->save();
            return $this->writeLog($locked, $actor, $action, $reason, $before, $this->snapshot($locked), $start, $idempotencyKey);
        });
    }
    private function replaceActivePackage(User $user, VipPackage $package, User $actor, string $reason, string $idempotencyKey, string $action): VipLogs
    {
        return DB::transaction(function () use ($user, $package, $actor, $reason, $idempotencyKey, $action): VipLogs {
            $existing = VipLogs::query()->where('idempotency_key', $idempotencyKey)->first();
            if ($existing) { return $existing; }
            $locked = User::query()->lockForUpdate()->findOrFail($user->id);
            if (! $locked->vip_id || ! $locked->end_at || CarbonImmutable::parse($locked->end_at)->lte(CarbonImmutable::now())) {
                throw new RuntimeException('会员已过期，不能升级');
            }
            $before = $this->snapshot($locked);
            $locked->forceFill(['vip_id' => $package->id])->save();
            return $this->writeLog($locked, $actor, $action, $reason, $before, $this->snapshot($locked), CarbonImmutable::now(), $idempotencyKey);
        });
    }

    private function writeLog(User $user, ?User $actor, string $action, string $reason, array $before, array $after, CarbonImmutable $effectiveAt, string $idempotencyKey): VipLogs
    {
        return VipLogs::query()->create([
            'user_id' => $user->id, 'actor_user_id' => $actor?->id, 'vip_id' => $user->vip_id,
            'status' => $user->vip_id ? VipStatus::ACTIVE : VipStatus::EXPIRED,
            'start_at' => $user->start_at ?? $effectiveAt, 'end_at' => $user->end_at ?? $effectiveAt,
            'action' => $action, 'reason' => $reason, 'before_snapshot' => $before,
            'after_snapshot' => $after, 'effective_at' => $effectiveAt, 'idempotency_key' => $idempotencyKey,
        ]);
    }

    public function expireDue(CarbonImmutable $at): int
    {
        $ids = VipLogs::query()->where('status', VipStatus::ACTIVE)->where('end_at', '<=', $at)->pluck('id');
        $processed = 0;
        foreach ($ids as $id) {
            $changed = DB::transaction(function () use ($id, $at): bool {
                $log = VipLogs::query()->lockForUpdate()->find($id);
                if (! $log || $log->status !== VipStatus::ACTIVE) { return false; }
                $user = User::query()->lockForUpdate()->find($log->user_id);
                if (! $user || $user->vip_id !== $log->vip_id || ! $user->end_at || CarbonImmutable::parse($user->end_at)->gt($at)) { return false; }
                $before = $this->snapshot($user);
                $user->forceFill(['vip_id' => null, 'start_at' => null, 'end_at' => null])->save();
                $log->forceFill(['status' => VipStatus::EXPIRED])->save();
                $this->writeLog($user, null, 'expired', '会员到期', $before, $this->snapshot($user), $at, 'vip-expired:'.$user->id.':'.$log->end_at);
                return true;
            });
            $processed += $changed ? 1 : 0;
        }
        return $processed;
    }
    private function snapshot(User $user): array { return ['vip_id' => $user->vip_id, 'start_at' => optional($user->start_at)?->toISOString(), 'end_at' => optional($user->end_at)?->toISOString()]; }
}
```

实现校验：`openForDuration` 执行 `DB::transaction`、`User::lockForUpdate()`、重复幂等键查询、`vip_id/start_at/end_at` 更新和 `VipLogs` 创建；`replaceActivePackage` 拒绝过期会员并保留原锚点/剩余时长；`writeLog` 把 `actor_user_id`、`action`、`reason`、JSON 快照、`effective_at`、`idempotency_key` 和会员状态写入同一事务。降级申请不修改用户，`applyDueChanges` 只把待生效记录改为 applied，并沿用原 `start_at`。

将 `UserController::store/update` 和注册体验逻辑改为调用服务，禁止控制器写 `vip_id/start_at/end_at`。人工更新需传 `action=open|renew|upgrade|downgrade|revoke`、`reason` 和客户端生成的 UUID 幂等键；服务端重新确认操作者为管理员，不信任前端的 `end_at`。

- [ ] **Step 4: Run lifecycle and admin API tests**

Run: `docker compose -f compose.test.yaml up -d --wait && serve/bin/test-env php artisan test tests/Unit/MembershipServiceTest.php tests/Feature/AdminMembershipTest.php tests/Feature/AuthRegistrationTest.php -v`

Expected: PASS；首次开通/体验建立新锚点，同套餐续期从旧 `end_at` 顺延，升级保留剩余时长，降级进入 `membership_changes.pending`，撤销立即清空用户会员状态，重复幂等键不产生第二条流水。

- [ ] **Step 5: Commit the membership lifecycle service**

```bash
git add serve/app/Enums/MembershipState.php serve/app/Services/MembershipService.php serve/app/Models/VipLogs.php serve/app/Models/MembershipChange.php serve/app/Http/Controllers/Api/UserController.php serve/tests/Unit/MembershipServiceTest.php serve/tests/Feature/AdminMembershipTest.php
git diff --cached --check
git commit -m "feat: centralize membership lifecycle changes"
```

### Task 10: 统一 Sanctum 认证、密码变更门和注册推荐事务

**Files:**
- Create: `serve/app/Services/RegistrationService.php`
- Modify: `serve/app/Http/Controllers/Api/AuthController.php:25-121`
- Modify: `serve/app/Http/Requests/RegisterRequest.php:25-61`
- Modify: `serve/app/Http/Requests/LoginRequest.php:24-48`
- Modify: `serve/app/Http/Middleware/ApiAuth.php:19-32`
- Modify: `serve/app/Models/User.php:19-71`
- Modify: `serve/config/auth.php:38-76`
- Modify: `serve/tests/Feature/UserTest.php:11-89`
- Create: `serve/tests/Feature/AuthRegistrationTest.php`

**Interfaces:**
- Consumes: `AdminProvisioner`、`MembershipService`（Task 9 提供）和 `User` 的 `parent_id/referral_code` 唯一约束。
- Produces: `RegistrationService::register(string $username, string $plainPassword, ?string $referralCode): User`；登录继续返回 Sanctum `plainTextToken`；注册推荐关系只在同一事务内绑定且不可更新；密码首次变更后清除 `must_change_password`。

- [ ] **Step 1: Write failing authentication and referral tests**

```php
// serve/tests/Feature/AuthRegistrationTest.php
namespace Tests\Feature;

use App\Enums\UserType;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

final class AuthRegistrationTest extends TestCase
{
    public function test_registration_binds_a_valid_referral_code_without_creating_commission_rows(): void
    {
        $parent = User::factory()->create(['type' => UserType::MEMBER, 'status' => true]);
        $response = $this->postJson('/api/register', [
            'username' => '13800000001', 'password' => 'password',
            'password_confirmation' => 'password', 'referral_code' => $parent->referral_code,
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('users', ['username' => '13800000001', 'parent_id' => $parent->id]);
        $this->assertSame(2, User::query()->count());
    }

    public function test_invalid_self_or_unknown_referral_code_is_rejected(): void
    {
        $parent = User::factory()->create(['username' => '13800000002', 'type' => UserType::MEMBER]);
        $this->postJson('/api/register', ['username' => '13800000003', 'password' => 'password', 'password_confirmation' => 'password', 'referral_code' => 'missing'])->assertStatus(422);
        $this->postJson('/api/register', ['username' => '13800000002', 'password' => 'password', 'password_confirmation' => 'password', 'referral_code' => $parent->referral_code])->assertStatus(422);
    }

    public function test_provisioned_admin_must_change_password_and_sanctum_auth_is_used(): void
    {
        $admin = app(\App\Services\AdminProvisioner::class)->provision('owner', 'correct-horse-battery-staple');
        $tokenResponse = $this->postJson('/api/login', ['username' => 'owner', 'password' => 'correct-horse-battery-staple']);
        $tokenResponse->assertOk()->assertJsonPath('data.token', fn ($token) => is_string($token) && $token !== '');
        $this->withHeader('Authorization', 'Bearer '.$tokenResponse->json('data.token'))->getJson('/api/userinfo')->assertStatus(403)->assertJsonPath('code', 'PASSWORD_CHANGE_REQUIRED');
    }
}
```

- [ ] **Step 2: Run the tests to verify the existing controller fails**

Run: `docker compose -f compose.test.yaml up -d --wait && serve/bin/test-env php artisan test tests/Feature/AuthRegistrationTest.php -v`

Expected: FAIL because an unknown referral code dereferences `null`, referral changes are not transactional, commission code is still reachable, and `must_change_password` is ignored.

- [ ] **Step 3: Implement the registration transaction and Sanctum guard recommendation**

```php
// serve/app/Services/RegistrationService.php
namespace App\Services;

use App\Enums\UserType;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

final class RegistrationService
{
    public function __construct(private readonly MembershipService $membership) {}

    public function register(string $username, string $plainPassword, ?string $referralCode): User
    {
        return DB::transaction(function () use ($username, $plainPassword, $referralCode): User {
            $parent = null;
            if ($referralCode !== null && $referralCode !== '') {
                $parent = User::query()->lockForUpdate()->where('referral_code', $referralCode)->first();
                if (! $parent || $parent->username === $username || ! $parent->status) {
                    throw ValidationException::withMessages(['referral_code' => '推荐码无效']);
                }
            }

            $user = User::query()->create([
                'username' => $username,
                'password' => Hash::make($plainPassword),
                'type' => UserType::MEMBER,
                'status' => true,
                'parent_id' => $parent?->id,
            ]);

            $this->membership->grantConfiguredTrial($user);
            return $user->refresh();
        });
    }
}
```

`AuthController::login` 使用 `Hash::check`，保留 `auth.php` 的 `api => sanctum` 配置，不引入第二套 JWT/自定义 token；`AuthController::register` 只调用 `RegistrationService`。`RegisterRequest` 将 `referral_code` 改为 `nullable|string|exists:users,referral_code`，自推荐和禁用推荐人由事务服务返回 422。删除注册流程中的 `CommissionType`、`commission_logs` 和支付关联；`LoginRequest` 只对验证码开关做条件验证，不以固定用户名绕过密码验证。

`ApiAuth` 先读取 `auth('api')->user()`，未登录返回 401，禁用返回 403；当 `must_change_password=true` 且当前路由不是 `change-password` 时返回 JSON `{'code':'PASSWORD_CHANGE_REQUIRED'}`。`changePassword` 用 `Hash::make` 保存并将该标志设为 false。`User` 增加 `tokens()`（由 Sanctum trait 提供）且不序列化密码、remember token、会员快照。

- [ ] **Step 4: Run focused auth tests and verify no commission side effect**

Run: `docker compose -f compose.test.yaml up -d --wait && serve/bin/test-env php artisan test tests/Feature/AuthRegistrationTest.php -v`

Expected: PASS；有效推荐码只设置 `parent_id`，无效/自推荐返回 422；登录返回非空 Sanctum token，未改密码的管理员访问普通接口返回 `PASSWORD_CHANGE_REQUIRED`。

- [ ] **Step 5: Commit authentication and recommendation boundary**

```bash
git add serve/app/Services/RegistrationService.php serve/app/Http/Controllers/Api/AuthController.php serve/app/Http/Requests/RegisterRequest.php serve/app/Http/Requests/LoginRequest.php serve/app/Http/Middleware/ApiAuth.php serve/app/Models/User.php serve/config/auth.php serve/tests/Feature/UserTest.php serve/tests/Feature/AuthRegistrationTest.php
git diff --cached --check
git commit -m "feat: secure registration and sanctum authentication"
```

### Task 11: 短信/邮箱验证码、频控与找回密码

**Files:**
- Create: serve/app/Contracts/SmsGateway.php
- Create: serve/app/Contracts/EmailGateway.php
- Create: serve/app/Services/AliyunSmsGateway.php
- Create: serve/app/Services/LaravelMailGateway.php
- Create: serve/app/Services/VerificationCodeService.php
- Create: serve/tests/Support/FakeSmsGateway.php
- Create: serve/tests/Support/FakeEmailGateway.php
- Delete: serve/app/Services/AliDySms.php
- Modify: serve/app/Jobs/SendEmailJobs.php:17-63
- Modify: serve/app/Providers/AppServiceProvider.php:1-80
- Modify: serve/app/Http/Controllers/Api/CaptchaController.php:15-47
- Modify: serve/app/Http/Requests/SMSCaptchaRequest.php:9-48
- Modify: serve/app/Http/Requests/RegisterRequest.php:10-84
- Modify: serve/app/Http/Requests/ResetPasswordRequest.php:8-52
- Modify: serve/app/Http/Controllers/Api/AuthController.php:6-102
- Modify: serve/tests/Feature/AuthRegistrationTest.php:11-90
- Modify: serve/app/Exceptions/Handler.php:1-120
- Modify: serve/config/services.php:49-55
- Modify: serve/config/mail.php:1-120
- Modify: serve/.env.example:1-55
- Create: serve/tests/Feature/SmsVerificationTest.php
- Create: serve/tests/Feature/EmailVerificationTest.php
- Create: serve/tests/Feature/PasswordResetTest.php

**Interfaces:**
- Consumes: Task 2 的 ImageCaptchaService、CodeMode::SMS/Email、overtrue/easy-sms 3.2.1、SendEmail Mailable、`SystemConfig` 的 SMTP 非敏感字段、`SecretConfigService::get('mail_password')` 和 Laravel Redis cache。
- Produces: SmsGateway::send(string $recipient, string $code, string $template): void；EmailGateway::send(string $recipient, string $code, string $template): void；VerificationCodeService::send(CodeMode $mode, string $recipient, string $ip, string $purpose, string $template): void；VerificationCodeService::consume(CodeMode $mode, string $recipient, string $ip, string $purpose, string $code): void。手机号使用 regex /^1[3-9]\d{9}$/，邮箱使用 email 规则；稳定错误码包括 SMS_NOT_CONFIGURED、SMS_RATE_LIMITED、SMS_SEND_FAILED、SMS_CODE_INVALID、SMS_CODE_EXPIRED、EMAIL_NOT_CONFIGURED、EMAIL_RATE_LIMITED、EMAIL_SEND_FAILED、EMAIL_CODE_INVALID、EMAIL_CODE_EXPIRED、IMAGE_CAPTCHA_INVALID。

- [ ] **Step 1: Write failing fake-gateway and endpoint tests**

```php
// serve/tests/Support/FakeSmsGateway.php
namespace Tests\Support;

use App\Contracts\SmsGateway;

final class FakeSmsGateway implements SmsGateway
{
    public array $messages = [];

    public function send(string $recipient, string $code, string $template): void
    {
        $this->messages[] = compact('recipient', 'code', 'template');
    }

    public function lastCode(string $recipient): string
    {
        return collect($this->messages)->last(fn (array $message) => $message['recipient'] === $recipient)['code'];
    }
}
```

```php
// serve/tests/Support/FakeEmailGateway.php
namespace Tests\Support;

use App\Contracts\EmailGateway;

final class FakeEmailGateway implements EmailGateway
{
    public array $messages = [];

    public function send(string $recipient, string $code, string $template): void
    {
        $this->messages[] = compact('recipient', 'code', 'template');
    }

    public function lastCode(string $recipient): string
    {
        return collect($this->messages)->last(fn (array $message) => $message['recipient'] === $recipient)['code'];
    }
}
```

```php
// serve/tests/Feature/SmsVerificationTest.php
namespace Tests\Feature;

use App\Contracts\SmsGateway;
use App\Enums\CodeMode;
use App\Exceptions\BusinessRuleException;
use App\Services\SystemConfig;
use App\Services\VerificationCodeService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Tests\Support\FakeSmsGateway;
use Tests\TestCase;

final class SmsVerificationTest extends TestCase
{
    private FakeSmsGateway $fake;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fake = new FakeSmsGateway();
        $this->app->instance(SmsGateway::class, $this->fake);
        Redis::connection('default')->flushdb();
        Redis::connection('cache')->flushdb();
    }

    public function test_success_is_hashed_in_redis_and_consumed_once(): void
    {
        app(VerificationCodeService::class)->send(CodeMode::SMS, '13800000001', '203.0.113.10', 'reset_password', 'RESET_TEMPLATE');
        $code = $this->fake->lastCode('13800000001');
        $identity = hash_hmac('sha256', '13800000001', (string) config('app.key'));
        $payload = Cache::store('redis')->get('verification:SMS:reset_password:'.$identity);
        $this->assertIsArray($payload);
        $this->assertFalse(isset($payload['code']));
        app(VerificationCodeService::class)->consume(CodeMode::SMS, '13800000001', '203.0.113.10', 'reset_password', $code);
        try {
            app(VerificationCodeService::class)->consume(CodeMode::SMS, '13800000001', '203.0.113.10', 'reset_password', $code);
            $this->fail('验证码必须一次消费');
        } catch (BusinessRuleException $exception) {
            $this->assertSame('SMS_CODE_EXPIRED', $exception->errorCode);
        }
    }

    public function test_same_recipient_and_ip_is_rate_limited_without_real_http(): void
    {
        $service = app(VerificationCodeService::class);
        $service->send(CodeMode::SMS, '13800000002', '203.0.113.11', 'register', 'REGISTER_TEMPLATE');
        try {
            $service->send(CodeMode::SMS, '13800000002', '203.0.113.11', 'register', 'REGISTER_TEMPLATE');
            $this->fail('同手机号和 IP 的频控必须拒绝第二次发送');
        } catch (BusinessRuleException $exception) {
            $this->assertSame('SMS_RATE_LIMITED', $exception->errorCode);
        }
        $this->assertCount(1, $this->fake->messages);
    }

    public function test_image_captcha_switch_controls_required_fields(): void
    {
        \App\Services\SystemConfig::set(['send_code_mode' => (string) \App\Enums\CodeMode::SMS->value, 'verify_code_is_open' => '0']);
        $this->postJson('/api/captcha/sms', ['tel' => '13800000004', 'purpose' => 'register'])->assertOk();
        \App\Services\SystemConfig::set(['verify_code_is_open' => '1']);
        $this->postJson('/api/captcha/sms', ['tel' => '13800000005', 'purpose' => 'register'])->assertStatus(422);
    }

    public function test_sms_endpoint_consumes_image_captcha_key_once(): void
    {
        \App\Services\SystemConfig::set(['send_code_mode' => (string) \App\Enums\CodeMode::SMS->value, 'verify_code_is_open' => '1']);
        $image = app(\App\Services\ImageCaptchaService::class)->issueForTesting('123456');
        $payload = ['tel' => '13800000007', 'purpose' => 'register', 'key' => $image['key'], 'captcha' => '123456'];
        $this->postJson('/api/captcha/sms', $payload)->assertOk();
        $this->postJson('/api/captcha/sms', $payload)->assertStatus(422)->assertJsonPath('code', 'IMAGE_CAPTCHA_INVALID');
    }

    public function test_missing_provider_configuration_returns_stable_error_without_network(): void
    {
        \App\Services\SystemConfig::set(['ali_sms_key' => '', 'ali_sms_secret' => '', 'ali_sms_sign_name' => '']);
        try {
            app(\App\Services\AliyunSmsGateway::class)->send('13800000006', '123456', 'REGISTER_TEMPLATE');
            $this->fail('缺少短信配置必须阻断真实发送');
        } catch (BusinessRuleException $exception) {
            $this->assertSame('SMS_NOT_CONFIGURED', $exception->errorCode);
        }
    }
}
```

```php
// serve/tests/Feature/EmailVerificationTest.php
namespace Tests\Feature;

use App\Contracts\EmailGateway;
use App\Enums\CodeMode;
use App\Exceptions\BusinessRuleException;
use App\Services\SystemConfig;
use App\Services\VerificationCodeService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;
use Tests\Support\FakeEmailGateway;
use Tests\TestCase;

final class EmailVerificationTest extends TestCase
{
    public function test_email_mode_uses_fake_gateway_and_consumes_once(): void
    {
        Mail::fake();
        $fake = new FakeEmailGateway();
        $this->app->instance(EmailGateway::class, $fake);
        Redis::connection('default')->flushdb();
        Redis::connection('cache')->flushdb();
        SystemConfig::set(['send_code_mode' => (string) CodeMode::Email->value, 'mail_host' => 'smtp.test', 'mail_from_address' => 'test@example.com']);
        $service = app(VerificationCodeService::class);
        $service->send(CodeMode::Email, 'owner@example.com', '203.0.113.30', 'reset_password', 'RESET_EMAIL');
        $code = $fake->lastCode('owner@example.com');
        try {
            $service->send(CodeMode::Email, 'owner@example.com', '203.0.113.30', 'reset_password', 'RESET_EMAIL');
            $this->fail('同邮箱和 IP 必须频控');
        } catch (BusinessRuleException $exception) {
            $this->assertSame('EMAIL_RATE_LIMITED', $exception->errorCode);
        }
        $service->consume(CodeMode::Email, 'owner@example.com', '203.0.113.30', 'reset_password', $code);
        try {
            $service->consume(CodeMode::Email, 'owner@example.com', '203.0.113.30', 'reset_password', $code);
            $this->fail('邮箱验证码必须一次消费');
        } catch (BusinessRuleException $exception) {
            $this->assertSame('EMAIL_CODE_EXPIRED', $exception->errorCode);
        }
        Mail::assertNothingSent();
    }

    public function test_missing_smtp_returns_email_not_configured(): void
    {
        SystemConfig::set(['mail_host' => '', 'mail_from_address' => '']);
        try {
            app(\App\Services\LaravelMailGateway::class)->send('owner@example.com', '123456', 'RESET_EMAIL');
            $this->fail('缺少 SMTP 必须返回稳定错误');
        } catch (BusinessRuleException $exception) {
            $this->assertSame('EMAIL_NOT_CONFIGURED', $exception->errorCode);
        }
    }

    public function test_email_gateway_failure_maps_to_stable_error(): void
    {
        $this->app->instance(EmailGateway::class, new class implements EmailGateway {
            public function send(string $recipient, string $code, string $template): void
            {
                throw new \RuntimeException('smtp unavailable');
            }
        });
        SystemConfig::set(['mail_host' => 'smtp.test', 'mail_from_address' => 'test@example.com']);
        try {
            app(VerificationCodeService::class)->send(CodeMode::Email, 'owner@example.com', '203.0.113.31', 'register', 'REGISTER_EMAIL');
            $this->fail('SMTP 发送失败必须映射稳定错误');
        } catch (BusinessRuleException $exception) {
            $this->assertSame('EMAIL_SEND_FAILED', $exception->errorCode);
        }
    }
}
```

```php
// serve/tests/Feature/PasswordResetTest.php
namespace Tests\Feature;

use App\Models\User;
use App\Enums\CodeMode;
use App\Services\VerificationCodeService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

final class PasswordResetTest extends TestCase
{
    public function test_reset_consumes_sms_code_once_and_never_sends_real_mail(): void
    {
        Mail::fake();
        $user = User::factory()->create(['username' => '13800000003', 'password' => Hash::make('old-password'), 'status' => true]);
        $fake = new \Tests\Support\FakeSmsGateway();
        $this->app->instance(\App\Contracts\SmsGateway::class, $fake);
        app(VerificationCodeService::class)->send(CodeMode::SMS, $user->username, '203.0.113.12', 'reset_password', 'RESET_TEMPLATE');
        $code = $fake->lastCode($user->username);
        $this->postJson('/api/reset-password', ['username' => $user->username, 'password' => 'new-password', 'password_confirmation' => 'new-password', 'code' => $code])->assertOk();
        $this->assertTrue(Hash::check('new-password', $user->refresh()->password));
        Mail::assertNothingSent();
        $this->postJson('/api/reset-password', ['username' => $user->username, 'password' => 'another-password', 'password_confirmation' => 'another-password', 'code' => $code])->assertStatus(422)->assertJsonPath('code', 'SMS_CODE_EXPIRED');
    }

    public function test_reset_uses_email_recipient_when_email_mode_is_enabled(): void
    {
        \App\Services\SystemConfig::set(['send_code_mode' => (string) CodeMode::Email->value, 'mail_host' => 'smtp.test', 'mail_from_address' => 'test@example.com']);
        $user = User::factory()->create(['username' => 'owner@example.com', 'password' => Hash::make('old-password'), 'status' => true]);
        $fake = new \Tests\Support\FakeEmailGateway();
        $this->app->instance(\App\Contracts\EmailGateway::class, $fake);
        app(VerificationCodeService::class)->send(CodeMode::Email, $user->username, '203.0.113.13', 'reset_password', 'RESET_EMAIL');
        $code = $fake->lastCode($user->username);
        $this->postJson('/api/reset-password', ['username' => $user->username, 'password' => 'new-password', 'password_confirmation' => 'new-password', 'code' => $code])->assertOk();
        $this->assertTrue(Hash::check('new-password', $user->refresh()->password));
    }
}
```

- [ ] **Step 2: Run focused tests to verify the channel gateway and service are absent**

Run: `docker compose -f compose.test.yaml up -d --wait && serve/bin/test-env php artisan test tests/Feature/SmsVerificationTest.php tests/Feature/EmailVerificationTest.php tests/Feature/PasswordResetTest.php -v`

Expected: FAIL because the EmailGateway, mode-aware channel selection, HMAC-keyed code record and atomic reset endpoint integration do not exist.

- [ ] **Step 3: Implement gateway contracts and safe production adapters**

```php
// serve/app/Contracts/SmsGateway.php
namespace App\Contracts;

interface SmsGateway
{
    public function send(string $recipient, string $code, string $template): void;
}
```

```php
// serve/app/Contracts/EmailGateway.php
namespace App\Contracts;

interface EmailGateway
{
    public function send(string $recipient, string $code, string $template): void;
}
```

```php
// serve/app/Services/AliyunSmsGateway.php
namespace App\Services;

use App\Contracts\SmsGateway;

final class AliyunSmsGateway implements SmsGateway
{
    public function __construct(private readonly \App\Services\SecretConfigService $secrets) {}

    public function send(string $recipient, string $code, string $template): void
    {
        $key = (string) $this->secrets->get('ali_sms_key', '');
        $secret = (string) $this->secrets->get('ali_sms_secret', '');
        $signName = (string) SystemConfig::get('ali_sms_sign_name');
        if ($key === '' || $secret === '' || $signName === '') {
            throw new BusinessRuleException('SMS_NOT_CONFIGURED', '短信服务尚未配置');
        }
        try {
            $client = new \Overtrue\EasySms\EasySms([
                'timeout' => 5,
                'default' => ['gateways' => ['aliyun']],
                'gateways' => ['aliyun' => ['access_key_id' => $key, 'access_key_secret' => $secret, 'sign_name' => $signName]],
            ]);
            $client->send($recipient, ['template' => $template, 'data' => ['code' => $code]], ['aliyun']);
        } catch (\Throwable $exception) {
            throw new BusinessRuleException('SMS_SEND_FAILED', '短信发送失败', 502);
        }
    }
}
```

```php
// serve/app/Services/LaravelMailGateway.php
namespace App\Services;

use App\Contracts\EmailGateway;
use App\Exceptions\BusinessRuleException;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;

final class LaravelMailGateway implements EmailGateway
{
    public function __construct(private readonly \App\Services\SecretConfigService $secrets) {}

    public function send(string $recipient, string $code, string $template): void
    {
        $host = (string) SystemConfig::get('mail_host');
        $port = (int) SystemConfig::get('mail_port', 587);
        $username = (string) SystemConfig::get('mail_username');
        $fromAddress = (string) SystemConfig::get('mail_from_address');
        $fromName = (string) SystemConfig::get('mail_from_name', config('app.name'));
        $encryption = strtolower((string) SystemConfig::get('mail_encryption', 'tls'));
        $password = (string) $this->secrets->get('mail_password', '');
        if ($host === '' || $username === '' || $fromAddress === '' || $password === '' || ! in_array($encryption, ['tls', 'ssl'], true)) {
            throw new BusinessRuleException('EMAIL_NOT_CONFIGURED', 'SMTP 服务尚未配置');
        }
        try {
            config([
                'mail.mailers.runtime_smtp' => [
                    'transport' => 'smtp',
                    'scheme' => $encryption === 'ssl' ? 'smtps' : 'smtp',
                    'host' => $host,
                    'port' => $port,
                    'username' => $username,
                    'password' => $password,
                    'timeout' => 8,
                ],
                'mail.from.address' => $fromAddress,
                'mail.from.name' => $fromName,
            ]);
            Mail::purge('runtime_smtp');
            Mail::mailer('runtime_smtp')->to($recipient)->send(new SendEmail($code, $template));
        } catch (\Throwable $exception) {
            throw new BusinessRuleException('EMAIL_SEND_FAILED', '邮件发送失败', 502);
        }
    }
}
```

复用结论：生产短信通道使用仓库已有的 [overtrue/easy-sms](https://github.com/overtrue/easy-sms) 3.2.1（MIT 许可证，PHP >=8.0；目标 PHP 8.3 兼容），不再扩展自签 AliDySms。AliyunSmsGateway 只把受控配置中的 access_key_id、access_key_secret、sign_name、模板和 code 传给 EasySms；缺配置映射 SMS_NOT_CONFIGURED，EasySms 异常映射 SMS_SEND_FAILED，日志不记录签名参数、手机号、验证码或密钥。模板默认值从 config('services.ali_sms.template_code') 读取，真实模板由受控环境提供。

在 `config/mail.php` 声明名为 `runtime_smtp` 的空骨架 mailer，但不放入仓库密码；`LaravelMailGateway` 每次发送前从受控配置填入完整参数并 `Mail::purge('runtime_smtp')`，然后显式调用命名 mailer，不依赖可能为 `array` 的默认 driver。在 AppServiceProvider::register 绑定两个生产实现，在测试时由 SmsVerificationTest/EmailVerificationTest 的 setUp 分别以 FakeSmsGateway/FakeEmailGateway 覆盖；Fake 只写内存消息数组，Mail::fake 验证不会触发 SMTP，禁止调用除 Http::fake 之外的真实网络。`EmailVerificationTest` 额外断言配置完整时实际选中 `runtime_smtp`，host/port/username/from 与 DB 一致，缺失任一必需字段返回 `EMAIL_NOT_CONFIGURED`，不得在 `array` mailer 上静默成功。

```php
// serve/app/Jobs/SendEmailJobs.php
public function __construct(string $to, string $code, ?string $title = null)
{
    $this->to = $to;
    $this->code = $code;
    $this->title = $title ?? '邮箱验证码';
}

public function handle(\App\Contracts\EmailGateway $gateway): void
{
    $gateway->send($this->to, $this->code, $this->title);
}

public function failed(\Throwable $exception): void
{
    report($exception);
}
```

```php
// serve/app/Providers/AppServiceProvider.php
public function register(): void
{
    $this->app->bind(\App\Contracts\SmsGateway::class, \App\Services\AliyunSmsGateway::class);
    $this->app->bind(\App\Contracts\EmailGateway::class, \App\Services\LaravelMailGateway::class);
}
```

- [ ] **Step 4: Implement hashed codes, TTL, recipient/IP throttling and one-time consumption**

```php
// serve/app/Services/VerificationCodeService.php
namespace App\Services;

use App\Contracts\SmsGateway;
use App\Exceptions\BusinessRuleException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;

final class VerificationCodeService
{
    public function __construct(private readonly SmsGateway $sms, private readonly EmailGateway $email) {}

    public function send(CodeMode $mode, string $recipient, string $ip, string $purpose, string $template): void
    {
        $identity = hash_hmac('sha256', $recipient, (string) config('app.key'));
        $ipHash = hash_hmac('sha256', $ip, (string) config('app.key'));
        $prefix = $mode === CodeMode::SMS ? 'SMS' : 'EMAIL';
        $throttleKey = "verification:rate:{$prefix}:{$purpose}:{$identity}:{$ipHash}";
        if (! Redis::set($throttleKey, '1', 'EX', 60, 'NX')) {
            throw new BusinessRuleException($prefix.'_RATE_LIMITED', '验证码发送过于频繁');
        }
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        try {
            ($mode === CodeMode::SMS ? $this->sms : $this->email)->send($recipient, $code, $template);
        } catch (BusinessRuleException $exception) {
            Redis::del($throttleKey);
            throw $exception;
        } catch (\Throwable $exception) {
            Redis::del($throttleKey);
            throw new BusinessRuleException($prefix.'_SEND_FAILED', '验证码发送失败', 502);
        }
        Cache::store('redis')->put("verification:{$prefix}:{$purpose}:{$identity}", ['hash' => Hash::make($code), 'expires_at' => now()->addMinutes(5)->timestamp, 'attempts' => 0], now()->addMinutes(5));
    }

    public function consume(CodeMode $mode, string $recipient, string $ip, string $purpose, string $code): void
    {
        $identity = hash_hmac('sha256', $recipient, (string) config('app.key'));
        $prefix = $mode === CodeMode::SMS ? 'SMS' : 'EMAIL';
        $key = "verification:{$prefix}:{$purpose}:{$identity}";
        Cache::store('redis')->lock('verification:lock:'.$key, 5)->block(1, function () use ($key, $prefix, $code): void {
            $payload = Cache::store('redis')->get($key);
            if (! $payload) { throw new BusinessRuleException($prefix.'_CODE_EXPIRED', '验证码已过期或已使用'); }
            $remaining = (int) $payload['expires_at'] - now()->timestamp;
            if ($remaining <= 0) { Cache::store('redis')->forget($key); throw new BusinessRuleException($prefix.'_CODE_EXPIRED', '验证码已过期'); }
            if (! Hash::check($code, $payload['hash'])) {
                $payload['attempts'] = (int) $payload['attempts'] + 1;
                if ($payload['attempts'] >= 5) { Cache::store('redis')->forget($key); throw new BusinessRuleException($prefix.'_CODE_EXPIRED', '验证码尝试次数已用尽'); }
                Cache::store('redis')->put($key, $payload, $remaining);
                throw new BusinessRuleException($prefix.'_CODE_INVALID', '验证码错误');
            }
            Cache::store('redis')->forget($key);
        });
    }
}
```

VerificationCodeService 按 CodeMode 选择短信或邮箱 gateway；验证码只保存哈希、原始 expires_at 和 attempts，recipient/IP 均先用应用密钥 HMAC 后再组成 key。Redis lock 内成功消费原子 forget，错误尝试最多 5 次并按原 expires_at 保留剩余 TTL，绝不通过 pull 后重新 put 延长有效期。测试 Fake gateway 与 Mail::fake 保证单元/功能测试绝不真实发信。

- [ ] **Step 5: Connect image-captcha switch, SMS endpoint, registration and reset password**

```php
// serve/app/Http/Requests/SMSCaptchaRequest.php
public function rules(): array
{
    $mode = CodeMode::from((int) SystemConfig::get('send_code_mode'));
    $rules = [
        'tel' => ['required', $mode === CodeMode::SMS ? 'regex:/^1[3-9]\\d{9}$/' : 'email'],
        'purpose' => ['required', 'in:register,reset_password'],
    ];
    if ((bool) SystemConfig::get('verify_code_is_open')) {
        $rules['captcha'] = ['required', 'string'];
        $rules['key'] = ['required', 'string'];
    }
    return $rules;
}
```

```php
// serve/app/Http/Controllers/Api/CaptchaController.php
use App\Exceptions\BusinessRuleException;
use App\Services\ImageCaptchaService;

public function sms(SMSCaptchaRequest $request, VerificationCodeService $codes, ImageCaptchaService $images): JsonResponse
{
    $mode = CodeMode::from((int) SystemConfig::get('send_code_mode'));
    if ((bool) SystemConfig::get('verify_code_is_open') && ! $images->verify($request->string('key')->toString(), $request->string('captcha')->toString())) {
        throw new BusinessRuleException('IMAGE_CAPTCHA_INVALID', '图片验证码错误');
    }
    $template = (string) config($mode === CodeMode::SMS ? 'services.ali_sms.template_code' : 'services.mail.template_code');
    $codes->send($mode, $request->string('tel')->toString(), $request->ip(), $request->string('purpose')->toString(), $template);
    return $this->success([]);
}
```

```php
// serve/app/Http/Requests/RegisterRequest.php
public function rules(): array
{
    $mode = CodeMode::from((int) SystemConfig::get('send_code_mode'));
    return [
        'username' => ['required', $mode === CodeMode::SMS ? 'regex:/^1[3-9]\d{9}$/' : 'email', 'unique:users,username'],
        'password' => ['required', 'min:6', 'confirmed'],
        'code' => ['required', 'digits:6'],
        'referral_code' => ['nullable', 'string'],
    ];
}
```

```php
// serve/app/Http/Requests/ResetPasswordRequest.php
public function rules(): array
{
    $mode = CodeMode::from((int) SystemConfig::get('send_code_mode'));
    return [
        'username' => ['required', $mode === CodeMode::SMS ? 'regex:/^1[3-9]\d{9}$/' : 'email', 'exists:users,username'],
        'password' => ['required', 'min:6', 'confirmed'],
        'code' => ['required', 'digits:6'],
    ];
}
```

RegisterRequest 和 ResetPasswordRequest 根据 send_code_mode 对 username 使用手机号 regex 或 email 规则，并要求 code 为 digits:6；AuthController::register/resetPassword 将 CodeMode::from(...)、username、request->ip()、用途和 code 传给 VerificationCodeService::consume，再在同一事务用 Hash::make 更新密码。验证码开关关闭时注册/重置可不提交图片 captcha，但仍必须提交当前通道验证码；开关开启时沿用 ImageCaptchaService 规则。重置密码请求第二次使用相同 code 必须返回对应通道的 *_CODE_EXPIRED。

在 AuthRegistrationTest 增加邮箱模式回归，证明注册也走同一通道选择：

```php
public function test_registration_uses_email_recipient_in_email_mode(): void
{
    \App\Services\SystemConfig::set(['send_code_mode' => (string) \App\Enums\CodeMode::Email->value]);
    $fake = new \Tests\Support\FakeEmailGateway();
    $this->app->instance(\App\Contracts\EmailGateway::class, $fake);
    app(\App\Services\VerificationCodeService::class)->send(\App\Enums\CodeMode::Email, 'new@example.com', '203.0.113.14', 'register', 'REGISTER_EMAIL');
    $response = $this->postJson('/api/register', ['username' => 'new@example.com', 'password' => 'password', 'password_confirmation' => 'password', 'code' => $fake->lastCode('new@example.com')]);
    $response->assertOk();
    $this->assertDatabaseHas('users', ['username' => 'new@example.com']);
}
```

在 Handler.php 的 render 方法中将 BusinessRuleException 转换为稳定 JSON，保留 HTTP 状态码，不回显上游异常：

```php
public function render($request, Throwable $exception): \Symfony\Component\HttpFoundation\Response
{
    if ($exception instanceof BusinessRuleException && $request->expectsJson()) {
        return response()->json(['code' => $exception->errorCode, 'message' => $exception->getMessage()], $exception->status);
    }
    return parent::render($request, $exception);
}
```

为 services.php 增加 ali_sms.template_code 的环境值，并在 serve/.env.example 增加 `ALI_SMS_TEMPLATE_CODE=`；不把手机号、验证码、模板密钥或第三方响应写日志。

```php
// serve/config/services.php
'ali_sms' => [
    'key' => env('ALI_SMS_KEY'),
    'secret' => env('ALI_SMS_SECRET'),
    'sign_name' => env('ALI_SMS_SIGN_NAME'),
    'template_code' => env('ALI_SMS_TEMPLATE_CODE'),
],
```

- [ ] **Step 6: Run SMS/email switch, success, missing-config, rate-limit and one-time-reset tests**

Run: `docker compose -f compose.test.yaml up -d --wait && serve/bin/test-env php artisan test tests/Feature/SmsVerificationTest.php tests/Feature/PasswordResetTest.php tests/Feature/AuthRegistrationTest.php -v`

Expected: PASS；SMS 和 Email 按 CodeMode 选择正确 gateway，手机号/邮箱规则、图片验证码一次消费、HMAC key、原始 TTL、最多 5 次错误和原子 forget 均有断言；Fake gateway 与 Mail::fake 证明不真实发信；短信配置/频控/发送错误使用 SMS_*，SMTP 缺失/发送错误使用 EMAIL_NOT_CONFIGURED/EMAIL_SEND_FAILED，找回密码 code 只能消费一次。真实阿里短信和 SMTP 凭据、签名、模板为独立外部阻塞门。

- [ ] **Step 7: Commit SMS verification and reset-password integration**

```bash
git add serve/app/Contracts/SmsGateway.php serve/app/Contracts/EmailGateway.php serve/app/Services/AliyunSmsGateway.php serve/app/Services/LaravelMailGateway.php serve/app/Services/VerificationCodeService.php serve/tests/Support/FakeSmsGateway.php serve/tests/Support/FakeEmailGateway.php serve/app/Jobs/SendEmailJobs.php serve/app/Providers/AppServiceProvider.php serve/app/Http/Controllers/Api/CaptchaController.php serve/app/Http/Requests/SMSCaptchaRequest.php serve/app/Http/Requests/RegisterRequest.php serve/app/Http/Requests/ResetPasswordRequest.php serve/app/Http/Controllers/Api/AuthController.php serve/tests/Feature/AuthRegistrationTest.php serve/app/Exceptions/Handler.php serve/config/services.php serve/config/mail.php serve/.env.example serve/tests/Feature/SmsVerificationTest.php serve/tests/Feature/EmailVerificationTest.php serve/tests/Feature/PasswordResetTest.php
git add -u serve/app/Services/AliDySms.php
git diff --cached --check
git commit -m "feat: support sms and email verification safely"
```

### Task 12: 建立唯一权益入口和数据库原子 UsageMeter

**Files:**
- Create: `serve/app/ValueObjects/EntitlementSnapshot.php`
- Create: `serve/app/Services/EntitlementService.php`
- Create: `serve/app/Services/UsageMeter.php`
- Modify: `serve/app/Models/UsagePeriod.php:1-100`
- Modify: `serve/app/Models/UsageVisitor.php:1-100`
- Modify: `serve/app/Http/Controllers/Api/AuthController.php:104-121`
- Create: `serve/tests/Unit/EntitlementServiceTest.php`
- Create: `serve/tests/Feature/UsageMeterTest.php`

**Interfaces:**
- Consumes: `MembershipService` 生命周期、`RollingPeriod`、`VipPackage.config` 和 `usage_periods/usage_visitors` 唯一约束。
- Produces: `EntitlementService::resolve(User $user, ?CarbonImmutable $at = null): EntitlementSnapshot`、`EntitlementService::assertActive(User $user, ?CarbonImmutable $at = null): EntitlementSnapshot`、`UsageMeter::consume(User $user, string $visitorId, CarbonImmutable $at): void`。稳定错误码包括 `MEMBERSHIP_EXPIRED`、`QUOTA_EXCEEDED` 和 `NO_ENTITLEMENT`。

- [ ] **Step 1: Write failing entitlement and atomic-usage tests**

```php
// serve/tests/Unit/EntitlementServiceTest.php
namespace Tests\Unit;

use App\Enums\MembershipState;
use App\Models\User;
use App\Models\VipPackage;
use App\Services\EntitlementService;
use Carbon\CarbonImmutable;
use Tests\TestCase;

final class EntitlementServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_admin_is_unlimited_and_member_gets_package_limits(): void
    {
        $admin = User::factory()->create(['type' => 3]);
        $this->assertSame(MembershipState::ADMIN, app(EntitlementService::class)->resolve($admin)->state);
        $member = User::factory()->create(['type' => 1, 'vip_id' => 2, 'start_at' => '2026-09-15 10:00:00', 'end_at' => '2026-10-15 10:00:00']);
        $snapshot = app(EntitlementService::class)->resolve($member, CarbonImmutable::parse('2026-09-20 10:00:00', 'Asia/Shanghai'));
        $this->assertSame(MembershipState::ACTIVE, $snapshot->state);
        $this->assertSame(50, $snapshot->uvLimit);
        $this->assertSame('2026-09-15 10:00:00', $snapshot->period->start()->format('Y-m-d H:i:s'));
    }
}
```

```php
// serve/tests/Feature/UsageMeterTest.php
namespace Tests\Feature;

use App\Exceptions\BusinessRuleException;
use App\Models\User;
use App\Services\UsageMeter;
use Carbon\CarbonImmutable;
use Tests\TestCase;

final class UsageMeterTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_same_visitor_across_two_links_counts_once_and_new_visitor_is_rejected_at_limit(): void
    {
        $member = User::factory()->create(['type' => 1, 'vip_id' => 1, 'start_at' => '2026-09-15 10:00:00', 'end_at' => '2026-10-15 10:00:00']);
        $at = CarbonImmutable::parse('2026-09-20 10:00:00', 'Asia/Shanghai');
        $meter = app(UsageMeter::class);
        $meter->consume($member, 'visitor-a', $at);
        $meter->consume($member, 'visitor-a', $at);
        foreach (range(1, 8) as $number) { $meter->consume($member, 'visitor-'.$number, $at); }
        try {
            $meter->consume($member, 'visitor-new', $at);
            $this->fail('满额的新访客必须被拒绝');
        } catch (BusinessRuleException $exception) {
            $this->assertSame('QUOTA_EXCEEDED', $exception->errorCode);
        }
        $this->assertDatabaseHas('usage_periods', ['user_id' => $member->id, 'used_uv' => 9]);
    }
}
```

- [ ] **Step 2: Run the tests to verify the service and exception are missing**

Run: `docker compose -f compose.test.yaml up -d --wait && serve/bin/test-env php artisan test tests/Unit/EntitlementServiceTest.php tests/Feature/UsageMeterTest.php -v`

Expected: FAIL because no entitlement snapshot/service or atomic meter exists.

- [ ] **Step 3: Implement the snapshot, stable business exception and entitlement resolver**

```php
// serve/app/ValueObjects/EntitlementSnapshot.php
namespace App\ValueObjects;

use App\Enums\MembershipState;

final readonly class EntitlementSnapshot
{
    public function __construct(
        public MembershipState $state,
        public ?int $packageId,
        public ?RollingPeriod $period,
        public int $linkLimit,
        public int $miniProgramLimit,
        public int $uvLimit,
        public array $allowTypes,
    ) {}
}
```

```php
// serve/app/Services/EntitlementService.php
namespace App\Services;

use App\Enums\MembershipState;
use App\Enums\UserType;
use App\Models\User;
use App\ValueObjects\EntitlementSnapshot;
use App\ValueObjects\RollingPeriod;
use Carbon\CarbonImmutable;

final class EntitlementService
{
    public function resolve(User $user, ?CarbonImmutable $at = null): EntitlementSnapshot
    {
        $at ??= CarbonImmutable::now('Asia/Shanghai');
        if ($user->type === UserType::Admin) {
            return new EntitlementSnapshot(MembershipState::ADMIN, null, null, PHP_INT_MAX, PHP_INT_MAX, PHP_INT_MAX, ['*']);
        }
        if (! $user->vip_id || ! $user->start_at || ! $user->end_at || CarbonImmutable::parse($user->end_at)->setTimezone('Asia/Shanghai')->lte($at)) {
            return new EntitlementSnapshot(MembershipState::EXPIRED, null, null, 0, 0, 0, []);
        }
        $package = $user->vipPackage;
        $config = $package->config ?? [];
        $state = (int) $package->level === 0 ? MembershipState::TRIAL : MembershipState::ACTIVE;
        return new EntitlementSnapshot($state, $package->id, RollingPeriod::forAnchor(CarbonImmutable::parse($user->start_at), $at), (int) ($config['count_limit'] ?? 0), (int) ($config['min_count_limit'] ?? 0), (int) ($config['uv_limit'] ?? 0), array_keys(array_filter($config['allow_type'] ?? [])));
    }

    public function assertActive(User $user, ?CarbonImmutable $at = null): EntitlementSnapshot
    {
        $snapshot = $this->resolve($user, $at);
        if (in_array($snapshot->state, [MembershipState::EXPIRED, MembershipState::NONE], true)) {
            throw new \App\Exceptions\BusinessRuleException('MEMBERSHIP_EXPIRED', '会员已过期');
        }
        return $snapshot;
    }
}
```

- [ ] **Step 4: Implement the lock-then-insert usage meter**

```php
// serve/app/Services/UsageMeter.php
namespace App\Services;

use App\Exceptions\BusinessRuleException;
use App\Models\UsagePeriod;
use App\Models\UsageVisitor;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

final class UsageMeter
{
    public function __construct(private readonly EntitlementService $entitlements) {}

    public function consume(User $user, string $visitorId, CarbonImmutable $at): void
    {
        $snapshot = $this->entitlements->assertActive($user, $at);
        if ($snapshot->state === \App\Enums\MembershipState::ADMIN) {
            return;
        }
        if ($visitorId === '') {
            throw new BusinessRuleException('INVALID_VISITOR', '访客标识不能为空');
        }
        $period = $snapshot->period;
        DB::transaction(function () use ($user, $visitorId, $at, $period, $snapshot): void {
            DB::table('usage_periods')->insertOrIgnore([
                'user_id' => $user->id, 'period_start' => $period->start(), 'period_end' => $period->end(),
                'used_uv' => 0, 'created_at' => $at, 'updated_at' => $at,
            ]);
            $row = UsagePeriod::query()->where('user_id', $user->id)->where('period_start', $period->start())->lockForUpdate()->firstOrFail();
            $visitorHash = hash('sha256', $visitorId);
            $seen = UsageVisitor::query()->where('usage_period_id', $row->id)->where('visitor_hash', $visitorHash)->exists();
            if ($seen) {
                return;
            }
            if ($row->used_uv >= $snapshot->uvLimit) {
                throw new BusinessRuleException('QUOTA_EXCEEDED', '本周期 UV 额度已用尽');
            }
            UsageVisitor::query()->create(['usage_period_id' => $row->id, 'visitor_hash' => $visitorHash, 'first_seen_at' => $at]);
            $row->increment('used_uv');
        });
    }
}
```

`UsagePeriod` 创建时必须在 `user_id + period_start` 唯一约束冲突后重新读取并锁定；MySQL 并发测试使用 `insertOrIgnore` 后 `lockForUpdate` 重读，确保唯一约束和行锁在真实目标数据库上得到证明。哈希输入使用服务端访客 cookie 值，客户端传入的 `device_uid` 不直接作为额度凭据。`/home`、链接拦截和后续跳转协调器只能读取 `UsagePeriod.used_uv`，不再按访问日志自行计数。

- [ ] **Step 5: Run entitlement and quota boundary tests**

Run: `docker compose -f compose.test.yaml up -d --wait && serve/bin/test-env php artisan test tests/Unit/EntitlementServiceTest.php tests/Feature/UsageMeterTest.php -v`

Expected: PASS；管理员无限制，会员返回套餐权益，周期起点计入且终点不计入，同一账号跨链接同一访客只占一个 UV，新访客在满额时得到 `QUOTA_EXCEEDED` 且不新增访客行。

- [ ] **Step 6: Commit entitlement and atomic usage accounting**

```bash
git add serve/app/ValueObjects/EntitlementSnapshot.php serve/app/Services/EntitlementService.php serve/app/Services/UsageMeter.php serve/app/Models/UsagePeriod.php serve/app/Models/UsageVisitor.php serve/app/Http/Controllers/Api/AuthController.php serve/tests/Unit/EntitlementServiceTest.php serve/tests/Feature/UsageMeterTest.php
git diff --cached --check
git commit -m "feat: enforce membership entitlements and uv quotas"
```

### Task 13: 让 VipExpired 只推进可重试的状态投影

**Files:**
- Modify: `serve/app/Console/Commands/VipExpired.php:10-65`
- Modify: `serve/app/Console/Kernel.php:13-19`
- Create: `serve/tests/Feature/VipExpiredCommandTest.php`

**Interfaces:**
- Consumes: `MembershipService::applyDueChanges(CarbonImmutable $at): int` 和用户实时会员字段。
- Produces: `app:vip-expired` 每小时执行、幂等处理到期会员和待生效降级；命令重复执行不重复生成流水，不依赖定时任务决定请求是否允许访问。

- [ ] **Step 1: Write a regression test for the current command bugs**

```php
// serve/tests/Feature/VipExpiredCommandTest.php
namespace Tests\Feature;

use App\Enums\VipStatus;
use App\Models\User;
use App\Models\VipLogs;
use Carbon\CarbonImmutable;
use Tests\TestCase;

final class VipExpiredCommandTest extends TestCase
{
    public function test_due_membership_is_projected_once_and_wrong_user_column_is_not_used(): void
    {
        $user = User::factory()->create(['type' => 1, 'status' => true, 'vip_id' => 2, 'start_at' => '2026-08-15 10:00:00', 'end_at' => '2026-09-15 10:00:00']);
        VipLogs::query()->create(['user_id' => $user->id, 'vip_id' => 2, 'status' => VipStatus::ACTIVE, 'start_at' => '2026-08-15 10:00:00', 'end_at' => '2026-09-15 10:00:00', 'action' => 'open', 'idempotency_key' => '00000000-0000-0000-0000-000000000010']);
        $this->artisan('app:vip-expired', ['--at' => '2026-09-15 10:00:00'])->assertExitCode(0);
        $this->assertNull($user->refresh()->vip_id);
        $this->artisan('app:vip-expired', ['--at' => '2026-09-15 10:00:00'])->assertExitCode(0);
        $this->assertSame(1, VipLogs::query()->where('user_id', $user->id)->where('action', 'expired')->count());
    }
}
```

- [ ] **Step 2: Run the regression test to verify the existing command fails**

Run: `docker compose -f compose.test.yaml up -d --wait && serve/bin/test-env php artisan test tests/Feature/VipExpiredCommandTest.php -v`

Expected: FAIL because the current command queries `users.user_id` instead of `users.id`, reads `$log->ent_at`, and mutates logs without an idempotency guard.

- [ ] **Step 3: Delegate due changes to `MembershipService` and keep runtime checks real-time**

```php
// serve/app/Console/Commands/VipExpired.php
namespace App\Console\Commands;

use App\Services\MembershipService;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;

final class VipExpired extends Command
{
    protected $signature = 'app:vip-expired {--at= : Asia/Shanghai evaluation time for deterministic tests}';
    protected $description = '处理会员到期、待生效变更和状态投影';

    public function handle(MembershipService $memberships): int
    {
        $at = $this->option('at')
            ? CarbonImmutable::parse($this->option('at'), 'Asia/Shanghai')
            : CarbonImmutable::now('Asia/Shanghai');
        $processed = $memberships->expireDue($at) + $memberships->applyDueChanges($at);
        $this->info("processed={$processed}");
        return self::SUCCESS;
    }
}
```

在 `MembershipService::expireDue` 中按 `users.end_at <= $at` 锁定用户和当前有效流水，写一条 `action=expired`、唯一幂等键为 `vip-expired:{user_id}:{end_at}` 的审计记录，再清空 `vip_id/start_at/end_at`；已清空用户和已存在幂等键直接跳过。`applyDueChanges` 对待生效降级使用 `membership_changes.status`，不依赖历史流水推断。`Kernel` 保留 `$schedule->command('app:vip-expired')->hourly()`；所有 HTTP 权益判断仍调用 `EntitlementService` 实时比较 `end_at`。

- [ ] **Step 4: Run command and complete backend regression suite**

Run: `docker compose -f compose.test.yaml up -d --wait && serve/bin/test-env php artisan test tests/Feature/VipExpiredCommandTest.php tests/Unit/MembershipServiceTest.php tests/Unit/EntitlementServiceTest.php tests/Feature/UsageMeterTest.php -v`

Expected: PASS；到期/待生效投影可安全重试，会员服务与权益服务的边界测试仍通过。

- [ ] **Step 5: Commit the idempotent expiry projection**

```bash
git add serve/app/Console/Commands/VipExpired.php serve/app/Console/Kernel.php serve/app/Services/MembershipService.php serve/tests/Feature/VipExpiredCommandTest.php
git diff --cached --check
git commit -m "fix: make membership expiry projection idempotent"
```

### Task 14: 基础验收、旧初始化路径封堵和交付证据

**Files:**
- Modify: `serve/README.md`
- Modify: `README.md`
- Create: `serve/tests/Feature/FoundationAcceptanceTest.php`

**Interfaces:**
- Consumes: Tasks 1–13 的 migrations、seeders、认证、会员和额度服务。
- Produces: 可复现的空库安装命令、重复 Seeder 证据、认证/会员/滚动周期/额度测试证据，以及明确的外部部署门状态；不把代码通过冒充 DNS、Nginx、真实微信或手机验收通过。

- [ ] **Step 1: Write the foundation acceptance assertions**

```php
// serve/tests/Feature/FoundationAcceptanceTest.php
namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

final class FoundationAcceptanceTest extends TestCase
{
    public function test_runtime_initialization_does_not_read_sql_dumps_or_expose_default_credentials(): void
    {
        $source = File::get(base_path('app/Console/Commands/SystemInit.php'));
        $this->assertStringNotContainsString("file_get_contents(database_path('packages.sql'))", $source);
        $this->assertStringNotContainsString("bcrypt('admin123')", $source);
        $this->assertStringContainsString('app:admin-provision', $source);
    }
}
```

- [ ] **Step 2: Run the full foundation suite before documentation changes**

Run: `docker compose -f compose.test.yaml up -d --wait && serve/bin/test-env php artisan migrate:fresh --seed && serve/bin/test-env php artisan test`

Expected: PASS；所有测试使用隔离数据库，认证、推荐、Seeder、会员状态、滚动月份、额度原子性和到期命令均有绿灯。

- [ ] **Step 3: Add reproducible operator commands and evidence boundaries**

在 `serve/README.md` 把测试与生产命令拆成两个不可混用的代码块，不写任何密码或 token。本地/持续集成只能从仓库根目录运行隔离 wrapper：

```bash
docker compose -f compose.test.yaml up -d --wait
serve/bin/test-env php artisan migrate:fresh --seed
serve/bin/test-env php artisan test
```

生产只在部署脚本已建立受限 `APP_ENV_FILE`、备份完成且操作员明确位于目标 release 时运行非破坏命令：

```bash
php artisan migrate --force
php artisan db:seed --force
php artisan app:admin-provision
php artisan schedule:list
```

生产文档明确禁止 `migrate:fresh`、禁止用 `php artisan test` 代替 `serve/bin/test-env`，也不建议把 `.env.example` 直接复制成生产配置。`app:admin-provision` 的密码通过隐藏交互输入。文档将“代码/数据库测试通过”和“DNS、Nginx、HTTPS、PHP-FPM、MySQL、Redis、队列、Scheduler、真实外部平台、Android/iOS 设备”分成独立证据栏，缺少外部凭据时记为外部阻塞，不以伪造客户端替代真实验收。

- [ ] **Step 4: Run final version, security, static and diff checks**

Run: `docker compose -f compose.test.yaml up -d --wait && cd serve && composer validate --strict --check-lock && (composer audit --locked --format=summary || true) && composer audit --locked --ignore-severity=low --ignore-severity=medium --abandoned=fail && composer show laravel/framework && composer show laravel/sanctum && composer show laravel/tinker && composer show guzzlehttp/guzzle && composer show phpunit/phpunit && composer show nunomaduro/collision && composer show laravel/pint && composer show gregwar/captcha && composer show overtrue/easy-sms && cd .. && serve/bin/test-env php artisan route:list --path=api && serve/bin/test-env php artisan test && git diff --check`; then from repository root run `rg -n 'admin123|base64:y5ide|packages\.sql|base\.sql' serve/app serve/database/seeders serve/tests serve/.env.example admin/src admin/public`

Expected: route listing succeeds；composer manifest/lock 一致；目标版本为 PHP 8.3、Laravel 13、Sanctum 4.3、Tinker 3、Guzzle 7.15.2、PHPUnit 12.5.34、Collision 8.9、Pint 1.30、Gregwar Captcha 2.1.1、overtrue/easy-sms 3.2.1；`composer audit --locked` 无未处置 high/critical；全部测试 PASS；`git diff --check` 无输出；业务代码、Seeder、测试和示例环境中无固定管理员密码、固定应用密钥或运行时 SQL dump 读取。许可证和合规文件可保留依法必须的归属内容。

- [ ] **Step 5: Commit the foundation acceptance record**

```bash
git add serve/README.md README.md serve/tests/Feature/FoundationAcceptanceTest.php
git diff --cached --check
git commit -m "docs: document foundation installation and acceptance"
```

## Self-review checklist

- [ ] 已覆盖测试隔离、Redis DB14/15 与 prefix 白名单、array 缓存/邮件、队列、数据库白名单和真实环境凭据隔离。
- [ ] 已覆盖 serve/bin/test-env 的逐项安全默认值与 CI 覆盖语义，APP_KEY 非空、DB 名称 `_test`、Redis DB14/15 和 prefix 白名单由 Guard 强制验证。
- [ ] 已覆盖 Laravel 13 之后、Seeder 之前的 Secret-at-rest 边界：Crypt/APP_KEY/APP_PREVIOUS_KEYS、固定 slugs、enc:v1、MiniProgram encrypted cast、幂等迁移命令、加密备份和错误密钥错误码。
- [ ] 已覆盖 Sanctum migration、会员审计字段、待生效变更、滚动周期和账号级 UV 唯一约束。
- [ ] 已覆盖 `AdminProvisioner`、交互式首次管理员、强制首次改密、无默认管理员和幂等 Seeder。
- [ ] 已覆盖认证推荐、不可变 `parent_id/referral_code`、无效/自推荐/禁用推荐人和无佣金副作用。
- [ ] 已覆盖 `MembershipService` 的首次开通、体验、续期、升级、待生效降级、撤销、审计和幂等。
- [ ] 已覆盖 `EntitlementService`、`RollingPeriod`、`UsageMeter`、29/30/31 日与半开边界、重复访客和满额拒绝。
- [ ] 已覆盖 `VipExpired` 的实时权益边界、到期投影、待生效变更和重复执行安全。
- [ ] 已覆盖 PHP 8.1/Laravel 10 EOL 商业硬门、Laravel 10→11→12→13 顺序升级、Composer 47 条基线公告和最终 high/critical 审计门。
- [ ] 已覆盖 Gregwar ImageCaptchaService 的 `{key,img}` 契约、答案哈希/TTL/一次消费，以及短信/邮箱任务对该服务的实际消费。
- [ ] 已覆盖 CodeMode 的 SMS/Email 分流、SmsGateway/EmailGateway、阿里短信与 Laravel Mail 适配、Fake 发信隔离、SMTP 错误码、HMAC key、原子消费和 SendEmailJobs 二次发送防护。
- [ ] 已为每个任务提供精确文件路径、Interfaces、TDD red/green 命令与预期、PHP/迁移/测试片段和独立 commit。
- [ ] 已扫描本计划全文，没有占位步骤或未定义接口；实现时不得把代码测试、部署状态、真实外部平台和设备结果合并为一个结论。
