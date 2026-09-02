# 非支付全功能部署与验收 Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** 将通过代码验收的非支付全功能版本安全部署到独立测试域名，并形成可回滚、可监控、可审计的浏览器与真机验收证据。

**Architecture:** 管理端和小程序产物在受控构建环境生成，ECS 只运行 Nginx、PHP-FPM、MySQL、Redis、队列和 Scheduler。每次发布写入独立 release 目录，通过 `current` 软链接切换；迁移前备份，健康检查失败立即回切代码和 Nginx，数据库恢复必须显式执行。

**Tech Stack:** Alibaba Cloud Linux 3、Nginx、PHP 8.3、Laravel 13、MySQL 8、Redis、systemd、cron、Bash、Certbot、curl、jq、Playwright。

**Spec:** `docs/superpowers/specs/2026-09-01-full-function-stabilization-design.md`

## Global Constraints

- 第一阶段不得创建、更新或回调任何支付状态。
- 唯一 canonical origin 由 `PUBLIC_ORIGIN` 注入；管理端 `/web`、API `/api`、分享页 `/?code=`、内部页 `/j/{code}` 均在同一 SaaS 实例。
- 生产环境不得直接编辑 `admin/dist/config.js`、Laravel 分享壳 Blade 或仓库内 `.env`；运行时 config 只能由发布脚本生成。
- 服务器为共享 ECS；不得覆盖现有站点、数据库、端口、Nginx 配置或 DNS 记录。
- PHP 8.3 必须以独立 FPM 服务/socket 运行本应用；不得替换共享 ECS 的系统默认 PHP 或改变其他站点的 FPM 版本。
- PHP 8.3 运行时必须启用 `bcmath,curl,dom,fileinfo,filter,gd,hash,mbstring,openssl,pdo_mysql,session,simplexml,sodium,tokenizer,xml,xmlwriter,zip`；队列进程使用 pcntl 时一并启用。
- DNS、HTTPS、外部凭据和真机结果必须与代码/构建证据分开报告。
- 所有脚本使用 `set -Eeuo pipefail`，拒绝空的路径、主机名、release SHA 和数据库备份参数。

---

### Task 1: 固化只读服务器审计与部署配置契约

**Files:**
- Create: `deploy/scripts/audit-server.sh`
- Create: `deploy/scripts/init-app-env.sh`
- Create: `deploy/scripts/provision-database.sh`
- Create: `deploy/config/deploy.env.example`
- Create: `deploy/config/serve.env.example`
- Create: `deploy/tests/audit-server.test.sh`
- Create: `deploy/tests/bootstrap-config.test.sh`
- Create: `docs/deployment.md`

**Interfaces:**
- Consumes: 已批准规格中的单一 `PUBLIC_ORIGIN`、独立测试子域名和本机 MySQL 8 管理员 login-path。
- Produces: `/etc/link-saas/deploy.env` 非敏感部署契约、`/etc/link-saas/serve.env` 生产敏感配置契约、幂等最小范围数据库建立和 `audit-server.sh` 的 Markdown/JSON 审计输出。

- [ ] **Step 1: 写审计与 bootstrap 失败测试**

```bash
#!/usr/bin/env bash
set -Eeuo pipefail

repo_root="$(cd "$(dirname "$0")/../.." && pwd)"
audit_output="$(mktemp)"
trap 'rm -f "$audit_output"' EXIT
if PUBLIC_HOST='' bash "$repo_root/deploy/scripts/audit-server.sh" >"$audit_output" 2>&1; then
  echo 'expected empty PUBLIC_HOST to fail' >&2
  exit 1
fi
grep -q 'PUBLIC_HOST is required' "$audit_output"

test_root="$(mktemp -d)"
trap 'rm -rf "$test_root"' EXIT
if APP_ENV_FILE="$test_root/serve.env" APP_USER="$(id -gn)" \
  bash "$repo_root/deploy/scripts/provision-database.sh" >"$test_root/output" 2>&1; then
  echo 'expected missing app env to fail' >&2
  exit 1
fi
grep -q 'APP_ENV_FILE does not exist' "$test_root/output"
```

- [ ] **Step 2: 运行测试并确认红灯**

Run: `bash deploy/tests/audit-server.test.sh && bash deploy/tests/bootstrap-config.test.sh`

Expected: FAIL，提示审计或 bootstrap 脚本不存在。

- [ ] **Step 3: 实现只读审计脚本与配置示例**

`deploy/config/deploy.env.example` 必须包含且不赋生产密钥：

```bash
PUBLIC_HOST=link.example.invalid
ALLOWED_SHARE_HOSTS=link.example.invalid
PUBLIC_ORIGIN=https://link.example.invalid
APP_ROOT=/opt/link-saas
BACKUP_ROOT=/var/backups/link-saas
APP_USER=link-saas
APP_ENV_FILE=/etc/link-saas/serve.env
ACME_EMAIL=ops@example.invalid
NGINX_BIN=/usr/sbin/nginx
NGINX_CONF_DIR=/etc/nginx/conf.d
PHP_BIN=/usr/bin/php83
PHP_FPM_BIN=/usr/sbin/php-fpm83
COMPOSER_BIN=/usr/local/bin/composer
NODE_BIN=/usr/bin/node
PHP_FPM_SERVICE=php-fpm83
PHP_FPM_POOL_DIR=/etc/php-fpm.d
PHP_FPM_SOCKET=/run/php-fpm83/www.sock
WEB_GROUP=www
MYSQL_SERVICE=mysqld
MYSQL_BIN=/usr/bin/mysql
MYSQLDUMP_BIN=/usr/bin/mysqldump
DB_ADMIN_LOGIN_PATH=link-saas-admin
REDIS_SERVICE=redis
CRON_SERVICE=crond
QUEUE_SERVICE=link-saas-queue
DEPLOY_REPO=https://github.com/dangbac421514-crypto/huoxing-links.git
DEPLOY_BRANCH=codex/full-function-stabilization
```

`deploy/config/serve.env.example` 是唯一生产 Laravel 环境模板，不含真实敏感值，至少包含：

```dotenv
APP_NAME="Link SaaS"
APP_ENV=production
APP_KEY=
APP_PREVIOUS_KEYS=
APP_DEBUG=false
APP_URL=
PUBLIC_ORIGIN=
ALLOWED_SHARE_HOSTS=
APP_TIMEZONE=Asia/Shanghai
APP_VISITOR_HASH_KEY=
APP_VISITOR_TOKEN_KEY=
LOG_CHANNEL=stack
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=link_saas
DB_USERNAME=link_saas
DB_PASSWORD=
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_PASSWORD=null
CACHE_STORE=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=file
MAIL_MAILER=runtime_smtp
```

`init-app-env.sh` 只允许在 `APP_ENV_FILE` 不存在时从该模板生成：把已验证的 `PUBLIC_ORIGIN` 同时写入 `APP_URL` 和 `PUBLIC_ORIGIN`，写入同一份 `ALLOWED_SHARE_HOSTS`，用 CSPRNG 分别生成 `base64:<32-byte-base64>` 格式的 `APP_KEY`、至少 32 随机字节的 `APP_VISITOR_HASH_KEY`、不带 `base64:` 前缀且严格解码为 32 字节的 `APP_VISITOR_TOKEN_KEY` 和数据库密码，设为 `root:${APP_USER}`/`0640`，日志只输出路径和成功状态；既有文件时必须拒绝覆盖。`bootstrap-config.test.sh` 断言生成文件中 `APP_URL=PUBLIC_ORIGIN`、两者均为 HTTPS 且不含 `.invalid`，并对三个密钥做长度/格式检查；这保证 Laravel `Storage::url()` 不会把素材/Logo/二维码指向模板域名。`APP_NAME` 只是未公开内部值，对外名称仍以数据库 `BrandConfig` 为准，不使用旧品牌。

`audit-server.sh` 只运行读取命令并输出：

```bash
: "${PUBLIC_HOST:?PUBLIC_HOST is required}"
uname -a
cat /etc/os-release
df -h
free -h
ss -lntp
systemctl --no-pager --full status nginx "${PHP_FPM_SERVICE}" "${MYSQL_SERVICE}" "${REDIS_SERVICE}" || true
"${NGINX_BIN}" -T
"${PHP_BIN}" -v
"${PHP_BIN}" -m
mysql --version
redis-cli ping
getent ahostsv4 "${PUBLIC_HOST}"
```

- [ ] **Step 4: 实现不泄密的数据库初始化**

`provision-database.sh` 必须先验证 `APP_ENV_FILE` 为普通文件、不对 group 可写/不对 other 可读，再导入配置。它只允许 `DB_HOST=127.0.0.1`、`DB_PORT=3306`，`DB_DATABASE/DB_USERNAME` 只允许 `[A-Za-z0-9_]+`，拒绝空密码。管理员认证只使用预先由运营方在 ECS 上建立的 `mysql_config_editor` login-path，不接受命令行 root 密码。

脚本用 base64 把应用数据库密码传入 MySQL stdin，在 MySQL 会话内用 `FROM_BASE64` + `QUOTE` 生成 `CREATE USER IF NOT EXISTS`/`ALTER USER` 的 prepared statement；原密码不得出现在 argv、stdout/stderr 或 shell trace。它创建 utf8mb4 数据库，只向 `'${DB_USERNAME}'@'127.0.0.1'` 授予该库的 DDL/DML 权限，不授予全局权限；重复执行后库、用户和权限不重复。`bootstrap-config.test.sh` 用一个捕获 argv/stdin 的 fake mysql 可执行文件验证成功/非法标识符/无效权限/重复执行路径，并断言原密码不出现在命令参数或日志。

- [ ] **Step 5: 运行 shell 语法、失败保护和本机兼容测试**

Run: `bash -n deploy/scripts/audit-server.sh deploy/scripts/init-app-env.sh deploy/scripts/provision-database.sh deploy/tests/audit-server.test.sh deploy/tests/bootstrap-config.test.sh && bash deploy/tests/audit-server.test.sh && bash deploy/tests/bootstrap-config.test.sh`

Expected: PASS；审计测试没有写服务器或 DNS，bootstrap 测试只写 `mktemp` 目录，不连接真实 MySQL。

- [ ] **Step 6: 记录远程审计证据**

Run on ECS after the first-deploy bootstrap checkout exists: `sudo bash -lc 'set -a; source /etc/link-saas/deploy.env; set +a; bash "${APP_ROOT}/bootstrap/deploy/scripts/audit-server.sh" | tee /var/log/link-saas/server-audit.txt'`

Expected: 明确列出既有监听端口、Nginx vhost、PHP/MySQL/Redis 服务名、磁盘和内存；`${PHP_BIN}` 必须报告 PHP 8.3.x，FPM socket 只供本应用使用。缺少独立 PHP 8.3 时状态为环境阻塞，先通过宝塔多版本 PHP 或等价并行包安装，不替换共享默认 PHP；任何冲突在部署前停止。

- [ ] **Step 7: Commit**

```bash
git add deploy/scripts/audit-server.sh deploy/scripts/init-app-env.sh deploy/scripts/provision-database.sh deploy/config/deploy.env.example deploy/config/serve.env.example deploy/tests/audit-server.test.sh deploy/tests/bootstrap-config.test.sh docs/deployment.md
git commit -m "ops: add audited application bootstrap"
```

### Task 2: 建立 Nginx、HTTPS、队列和 Scheduler 运行配置

**Files:**
- Create: `deploy/nginx/link-saas-http.conf.template`
- Create: `deploy/nginx/link-saas-https.conf.template`
- Create: `deploy/php-fpm/link-saas.conf.template`
- Create: `deploy/scripts/render-nginx-config.sh`
- Create: `deploy/scripts/install-runtime.sh`
- Create: `deploy/systemd/link-saas-queue.service.template`
- Create: `deploy/cron/link-saas.template`
- Create: `deploy/tests/runtime-config.test.sh`

**Interfaces:**
- Consumes: `/etc/link-saas/deploy.env` 的 `PUBLIC_HOST`、逗号分隔 `ALLOWED_SHARE_HOSTS`、`APP_ROOT`、`APP_USER`、`PHP_FPM_BIN/PHP_FPM_POOL_DIR/PHP_FPM_SOCKET`、`WEB_GROUP`、`QUEUE_SERVICE/CRON_SERVICE`。
- Produces: 同源 `/web`、`/api`、`/j` 与分享页；独立 PHP 8.3 FPM pool/socket；可启用的 Redis 队列 worker 与每分钟 Laravel Scheduler。

- [ ] **Step 1: 写配置渲染失败测试**

```bash
rendered="$(mktemp)"
trap 'rm -f "$rendered"' EXIT
PUBLIC_HOST=link.example.invalid ALLOWED_SHARE_HOSTS=link.example.invalid,share.example.invalid \
  APP_ROOT=/opt/link-saas PHP_FPM_SOCKET=/run/php-fpm/www.sock \
  bash deploy/scripts/render-nginx-config.sh deploy/nginx/link-saas-http.conf.template "$rendered"
grep -q 'server_name link.example.invalid share.example.invalid;' "$rendered"
grep -q 'root /opt/link-saas/current/serve/public;' "$rendered"
grep -q 'location /web/' "$rendered"
grep -q 'location /api/' "$rendered"
grep -q 'fastcgi_pass unix:/run/php-fpm/www.sock;' "$rendered"
```

- [ ] **Step 2: 运行测试并确认红灯**

Run: `bash deploy/tests/runtime-config.test.sh`

Expected: FAIL，提示 Nginx 模板不存在。

- [ ] **Step 3: 实现 Nginx 同源路由模板**

HTTP-only 模板必须包含：

```nginx
server {
    listen 80;
    server_name ${SERVER_NAMES};
    root ${APP_ROOT}/current/serve/public;
    client_max_body_size 50m;

    location ^~ /.well-known/acme-challenge/ { root /var/www/acme; }
    location = /web { return 301 /web/; }
    location ^~ /web/ { try_files $uri $uri/ /web/index.html; }
    location ^~ /api/ { try_files $uri /index.php?$query_string; }
    location ^~ /j/ { try_files $uri /index.php?$query_string; }
    location / { try_files $uri $uri/ /index.php?$query_string; }
    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_pass unix:${PHP_FPM_SOCKET};
    }
}
```

`render-nginx-config.sh` 把 `ALLOWED_SHARE_HOSTS` 按逗号拆分，逐个验证 RFC 风格主机名、禁止端口/路径/userinfo，并要求列表包含 `PUBLIC_HOST`；验证后生成空格分隔 `SERVER_NAMES` 再执行 envsubst。发布脚本在每个 release 中创建 `serve/public/web -> ../../admin/dist` 软链接，因此 Nginx 只使用同一 `serve/public` root，避免 `alias + try_files` 路径歧义。HTTPS 模板引用 Certbot 安装到 `/etc/letsencrypt/live/${PUBLIC_HOST}/fullchain.pem` 和 `privkey.pem`，并把 HTTP 非 ACME 请求 301 到 HTTPS。申请证书前只安装 HTTP-only 模板，因此不存在“证书尚未创建导致 nginx -t 失败”的循环依赖。

- [ ] **Step 4: 实现 PHP-FPM pool、systemd、cron 与两阶段安装脚本**

PHP-FPM pool 模板固定应用用户和独立 socket：

```ini
[link-saas]
user = ${APP_USER}
group = ${APP_USER}
listen = ${PHP_FPM_SOCKET}
listen.owner = ${APP_USER}
listen.group = ${WEB_GROUP}
listen.mode = 0660
pm = ondemand
pm.max_children = 8
pm.process_idle_timeout = 10s
pm.max_requests = 500
catch_workers_output = yes
php_admin_flag[log_errors] = on
php_admin_value[error_log] = /var/log/link-saas/php-fpm-error.log
```

Queue ExecStart 固定为：

```ini
User=${APP_USER}
Group=${APP_USER}
WorkingDirectory=${APP_ROOT}/current/serve
ExecStart=${PHP_BIN} ${APP_ROOT}/current/serve/artisan queue:work redis --sleep=1 --tries=3 --timeout=90 --max-time=3600
Restart=always
RestartSec=5
```

Cron 固定为：

```cron
* * * * * ${APP_USER} cd ${APP_ROOT}/current/serve && ${PHP_BIN} artisan schedule:run >> /var/log/link-saas/scheduler.log 2>&1
```

`install-runtime.sh --prepare` 先校验 `${PHP_BIN}` 为 8.3.x，且必需扩展、`${PHP_FPM_BIN}`、`${PHP_FPM_SERVICE}`、`${WEB_GROUP}` 存在；任一缺失则返回稳定环境阻塞，不自动替换共享服务器的系统 PHP。验证通过后它原子渲染 FPM pool 和 queue unit，将 cron 渲染到只有 root 可读的 staging 路径，创建 `/var/log/link-saas`、执行 `${PHP_FPM_BIN} -tt`、`systemd-analyze verify`、`systemctl daemon-reload`和 `systemctl enable "${QUEUE_SERVICE}"`，但在 `${APP_ROOT}/current` 不存在时不启动 queue/不安装 cron。它只 reload 已存在的 PHP 8.3 FPM 服务，生成的 socket 必须与 Nginx 模板一致。

`install-runtime.sh --activate` 只能在 `readlink -f "${APP_ROOT}/current"` 为受控 release、`artisan about` 成功后执行：安装 `/etc/cron.d/link-saas` 为 root/root `0644`，`systemctl enable --now "${CRON_SERVICE}"`，并 `systemctl enable --now "${QUEUE_SERVICE}"`。重复 prepare/activate 幂等，不生成重复 cron 行或 unit。`runtime-config.test.sh` 使用临时目录和 fake `systemctl/php-fpm` 验证 prepare 不早启队列、activate 需要 current、两次 activate 仍只有一条 cron。

`serve/app/Console/Kernel.php` 已由基础/链接计划拥有；本任务只通过 `artisan schedule:list` 验证它同时包含 `$schedule->command('app:vip-expired')->hourly()->withoutOverlapping()` 和 `app:links-health-check` 十分钟任务，不再修改 Kernel，也不引入静态 `Schedule::command()`。

- [ ] **Step 5: 验证配置**

Run locally: `bash -n deploy/scripts/install-runtime.sh && bash deploy/tests/runtime-config.test.sh && php -l serve/app/Console/Kernel.php && docker compose -f compose.test.yaml up -d --wait && serve/bin/test-env php artisan schedule:list | rg 'app:vip-expired|app:links-health-check'`

Run on ECS after rendering: `sudo "$NGINX_BIN" -t && sudo systemd-analyze verify /etc/systemd/system/link-saas-queue.service`

Expected: 本地 fake-root 所有检查 PASS，未改动真实 systemd/cron/FPM；ECS 预检中独立 pool、socket、queue unit 和 cron staging 均可验证。

- [ ] **Step 6: Commit**

```bash
git add deploy/nginx/link-saas-http.conf.template deploy/nginx/link-saas-https.conf.template deploy/php-fpm/link-saas.conf.template deploy/scripts/render-nginx-config.sh deploy/scripts/install-runtime.sh deploy/systemd/link-saas-queue.service.template deploy/cron/link-saas.template deploy/tests/runtime-config.test.sh
git commit -m "ops: define web php queue and scheduler runtime"
```

### Task 3: 实现备份、原子发布、健康检查和回滚

**Files:**
- Create: `deploy/scripts/backup.sh`
- Create: `deploy/scripts/healthcheck.sh`
- Create: `deploy/scripts/release.sh`
- Create: `deploy/scripts/rollback.sh`
- Create: `deploy/scripts/render-runtime-config.mjs`
- Create: `deploy/tests/release-flow.test.sh`
- Modify: `deploy.sh`

**Interfaces:**
- Consumes: `RELEASE_SHA`、`PUBLIC_ORIGIN`、`APP_ROOT`、`BACKUP_ROOT`、`APP_ENV_FILE` 和 `DB_ADMIN_LOGIN_PATH`。
- Produces: `${APP_ROOT}/releases/${RELEASE_SHA}`、`${APP_ROOT}/current` 原子软链接、可核验备份目录与 route-specific 健康结果。

- [ ] **Step 1: 写临时目录发布流失败测试**

```bash
test_root="$(mktemp -d)"
APP_ROOT="$test_root/app" BACKUP_ROOT="$test_root/backups" RELEASE_SHA=abc123 \
  bash deploy/scripts/release.sh --dry-run
test -d "$test_root/app/releases/abc123"
test -L "$test_root/app/current"
```

- [ ] **Step 2: 运行并确认红灯**

Run: `bash deploy/tests/release-flow.test.sh`

Expected: FAIL，提示发布脚本不存在。

- [ ] **Step 3: 实现备份脚本**

`backup.sh` 必须验证目录不为空且不为 `/`，然后生成：

```text
${BACKUP_ROOT}/${timestamp}/manifest.sha256
${BACKUP_ROOT}/${timestamp}/database.sql.gz
${BACKUP_ROOT}/${timestamp}/nginx.tar.gz
${BACKUP_ROOT}/${timestamp}/serve.env.enc-or-restricted-copy
${BACKUP_ROOT}/${timestamp}/current-release.txt
```

数据库命令使用 `"${MYSQLDUMP_BIN}" --login-path="${DB_ADMIN_LOGIN_PATH}" --single-transaction --routines --triggers "${DB_DATABASE}"`，数据库名先通过与 bootstrap 相同的白名单验证；不从 argv 传递密码。备份目录权限 `0700`、文件 `0600`，日志不得打印密码。`APP_ENV_FILE` 仅以 `0600` 受限副本进入本机备份，manifest 覆盖每个文件；异地备份在没有 age/KMS 加密前禁止上传。

首次发布时 `${APP_ROOT}/current` 可以不存在：`backup.sh` 此时必须将 `current-release.txt` 原子写为单行 `NONE`，跳过应用 release 文件归档，但仍完成数据库、Nginx、受限 env 和 manifest 备份。`current` 一旦存在，必须是解析到 `${APP_ROOT}/releases/<sha>` 的软链接；断链、普通文件或越出 releases 目录时拒绝备份/发布。`release-flow.test.sh` 分别覆盖 `NONE`、有效 current 和断链三种路径。

- [ ] **Step 4: 实现原子 release 流程**

顺序固定为：验证 SHA/磁盘/安全路径和 `APP_ENV_FILE` → 备份 → 从经确认的 remote fetch 并将精确 commit archive 到新 release → 创建共享 storage 并在 release 内链接 `serve/.env -> ${APP_ENV_FILE}`、`serve/storage -> ${APP_ROOT}/shared/storage`、`serve/public/web -> ../../admin/dist`，将 shared storage 和 `serve/bootstrap/cache` 设为 `${APP_USER}` 可写且其他 release 文件只读 → 在 `release/serve` 执行 `"${PHP_BIN}" "${COMPOSER_BIN}" install --no-dev --no-interaction --prefer-dist --classmap-authoritative`、`artisan storage:link`并验证 `public/storage` 只指向本 release 的共享 storage → 检查 PHP 8.3 平台需求 → 用 `"${NODE_BIN}"` 生成无密钥 `admin/dist/config.js` → 在未切换的 release 中执行 `migrate --force`、`app:secret-storage-status --json` 和 `config:cache`（通用 release 流程不改写历史 secret） → 原子切换 `current` → 显式调用 `bash "${APP_ROOT}/bootstrap/deploy/scripts/install-runtime.sh" --activate` 启用首次 queue/cron 或重载已有单元 → restart PHP-FPM/queue 且 reload Nginx → route-specific healthcheck。

`release.sh` 不使用工作区中的未提交文件，必须验证 archive 的 `git rev-parse HEAD` 等于完整 `RELEASE_SHA`。候选 release 必须提供 `app:secret-storage-status`；如当前数据库已有 encrypted secret，切换前还必须验证旧 `current` 也提供该命令，否则在任何 secret 改写/软链接切换前停止。首次发布没有旧 `current` 时也可执行；任何切换后失败自动回切旧软链接并 reload，首次发布则移除失败的 `current` 并恢复 HTTP-only 安全页。数据库迁移不自动倒退；只有经过实测的显式恢复流程才可恢复数据。

`release-flow.test.sh` 把 `install-runtime.sh` 替换为记录参数的 fake：首次发布必须在 `current` 切换之后精确调用一次 `--activate`，该 fake 才生成 cron/queue active 标记；发布脚本必须在这两个标记存在后才运行 healthcheck。测试同时断言 `--activate` 失败会进入切换后回滚路径，不把队列/cron 留在伪成功状态。

`render-runtime-config.mjs` 只接受 `PUBLIC_ORIGIN` 和目标文件路径，使用 `JSON.stringify` 生成：

```js
window.__LINK_RUNTIME__ = {
  publicOrigin: 'https://validated-host.example',
  apiOrigin: 'https://validated-host.example/api'
}
```

脚本先用 `new URL()` 验证 HTTPS 且无用户名/密码/查询串，再原子写入 release 内的 `admin/dist/config.js`；品牌字段继续从 `/api/config` 获取，任何 Secret/数据库配置不得进入该文件。

- [ ] **Step 5: 实现健康检查**

```bash
config_json="$(curl --fail --silent --show-error "${PUBLIC_ORIGIN}/api/config")"
jq -e '.code == 0 and .data.brand.productName != null' <<<"$config_json"
curl --fail --silent --show-error "${PUBLIC_ORIGIN}/web/" | grep -q '<div id="app"'
redis-cli ping | grep -qx PONG
"${PHP_BIN}" "${APP_ROOT}/current/serve/artisan" about --only=environment
systemctl is-active --quiet "${QUEUE_SERVICE}"
```

`healthcheck.sh` 还必须验证 `readlink -f "${APP_ROOT}/current"` 末尾为目标 `RELEASE_SHA`、`/api/config` 的 `productName` 不是旧品牌、`/?code=definitely-missing` 返回可预期的业务错误而不是 Nginx 404/500，并执行 `artisan queue:monitor redis:default --max=100` 与 `artisan schedule:list`。这些检查分别记录 HTTP、Laravel、Redis、队列和 Scheduler 结果，不用单个 200 代替。

- [ ] **Step 6: 实现显式回滚**

`rollback.sh RELEASE_SHA` 只切换到已验证 release 并 reload 服务。它先用当前 release 读取 secret storage status；存在 encrypted secret 时，目标 release 必须能在同一 `APP_ENV_FILE` 下成功运行 `app:secret-storage-status --json`且返回 `compatible=true`，否则在切换前拒绝并提示只能使用显式 secret 备份恢复流程。数据库恢复必须要求 `--restore-database BACKUP_DIR`，校验备份 manifest、数据库名和显式确认变量 `CONFIRM_DATABASE_RESTORE=YES`，否则拒绝执行。

- [ ] **Step 7: 运行脚本测试**

Run: `bash -n deploy/scripts/*.sh deploy/tests/*.sh && bash deploy/tests/release-flow.test.sh`

Expected: PASS；dry-run 不接触真实 `/opt`、systemd、Nginx 或数据库。

- [ ] **Step 8: Commit**

```bash
git add deploy.sh deploy/scripts deploy/tests/release-flow.test.sh
git commit -m "ops: add atomic release backup and rollback"
```

### Task 4: 建立 CI 与发布前质量门

**Files:**
- Create: `.github/workflows/ci.yml`
- Create: `scripts/check-api-inventory.mjs`
- Create: `scripts/scan-brand-residue.sh`
- Create: `scripts/check-release.sh`
- Test: `serve/tests/Feature/ApiRouteContractTest.php`

**Interfaces:**
- Consumes: 四份实施计划产生的后端、管理端、小程序和部署文件。
- Produces: 单一 `scripts/check-release.sh` 发布阻断命令与 GitHub Actions 证据。

- [ ] **Step 1: 写发布检查失败测试**

```bash
bash scripts/check-release.sh
```

Expected: FAIL，因为检查脚本不存在。

- [ ] **Step 2: 实现发布检查聚合**

```bash
#!/usr/bin/env bash
set -Eeuo pipefail
composer install --working-dir=serve --no-interaction --prefer-dist
docker compose -f compose.test.yaml up -d --wait
serve/bin/test-env php artisan migrate:fresh --seed
(cd serve && composer validate --no-check-publish && composer audit --locked --ignore-severity=low --ignore-severity=medium --abandoned=fail --no-interaction)
serve/bin/test-env php artisan test
(cd admin && pnpm install --frozen-lockfile && pnpm audit --prod --audit-level high && pnpm type-check && pnpm test:unit && pnpm build && pnpm test:e2e)
(cd mini_programs && pnpm install --frozen-lockfile && pnpm audit --prod --audit-level high && pnpm test && pnpm build:mp-weixin)
node scripts/check-api-inventory.mjs
bash scripts/scan-brand-residue.sh
bash -n deploy/scripts/*.sh
git diff --check
git diff --exit-code -- admin/dist
```

`scripts/scan-brand-residue.sh` 必须显式覆盖 `admin/index.html`、`admin/src`、`admin/public`、`admin/dist`、`serve/resources`、`jump`和 `mini_programs`，并同时检查旧品牌/联系方式/演示凭据与 `_AMapSecurityConfig`、`securityJsCode`、非空 AppID/Secret 模式。扫描只跳过根 `LICENSE` 和明确的第三方 NOTICE；不因“是 HTML”而跳过 `admin/index.html`。

- [ ] **Step 3: 实现 CI 服务容器与缓存**

Workflow 后端使用 PHP 8.3，管理端构建/单测使用 Node 24，uni-app 构建使用 Node 20.19，Playwright 使用与 `@playwright/test 1.62.1` 一致的 Chromium/系统依赖，服务容器为 MySQL 8 与 Redis 7；安装 `bcmath,curl,dom,fileinfo,gd,mbstring,pdo_mysql,simplexml,sodium,xml,xmlwriter,zip`，通过 `serve/bin/test-env` 运行 `migrate:fresh --seed`、全量测试和所有构建。CI 不读取仓库 `.env`，只使用测试环境变量/固定容器测试密码。不同 Node 版本在独立 job 中执行，最终 `release-gate` job 只在后端、管理端、Playwright、小程序和脚本五个 job 全部成功时通过；不用当前机器的一个 Node 版本假冒两个构建环境。

- [ ] **Step 4: 验证 CI YAML 与本地聚合命令**

Run: `ruby -e 'require "yaml"; YAML.load_file(".github/workflows/ci.yml")' && bash scripts/check-release.sh`

Expected: YAML 可解析；所有发布检查 PASS。

- [ ] **Step 5: Commit**

```bash
git add .github/workflows/ci.yml scripts serve/tests/Feature/ApiRouteContractTest.php
git commit -m "ci: enforce non-payment release gates"
```

### Task 5: 执行测试域名部署与环境证据验收

**Files:**
- Create: `docs/acceptance/full-function-evidence.md`
- Create: `docs/acceptance/device-matrix.md`
- Modify: `docs/deployment.md`

**Interfaces:**
- Consumes: 全部自动化测试通过的 release SHA、测试域名和用户控制的外部凭据。
- Produces: 代码、运行、API、浏览器、手机和外部平台六条独立证据结论。

- [ ] **Step 1: 建立一次性 bootstrap checkout，执行审计、密钥初始化、数据库建立与备份**

先把已审阅的非敏感 deploy.env 安装到 `/etc/link-saas/deploy.env`，其中本次测试部署固定 `PUBLIC_HOST=link.bjaajsdad.xyz`、`PUBLIC_ORIGIN=https://link.bjaajsdad.xyz`、`ALLOWED_SHARE_HOSTS=link.bjaajsdad.xyz`；`ACME_EMAIL` 使用运营方实际可接收的邮箱。MySQL 管理员凭据由运营方在 ECS 终端交互式写入 `DB_ADMIN_LOGIN_PATH`，不通过 Git、命令行参数或聊天传递。然后运行：

```bash
sudo bash -lc '
set -Eeuo pipefail
set -a; source /etc/link-saas/deploy.env; set +a
test "${PUBLIC_HOST}" = "link.bjaajsdad.xyz"
test "${PUBLIC_ORIGIN}" = "https://link.bjaajsdad.xyz"
test "${ALLOWED_SHARE_HOSTS}" = "link.bjaajsdad.xyz"
getent passwd "${APP_USER}" >/dev/null || useradd --system --home-dir "${APP_ROOT}" --shell /sbin/nologin "${APP_USER}"
install -d -o "${APP_USER}" -g "${APP_USER}" -m 0750 "${APP_ROOT}" "${APP_ROOT}/shared" "${APP_ROOT}/shared/storage"
test ! -e "${APP_ROOT}/bootstrap"
sudo -u "${APP_USER}" git clone --filter=blob:none --branch "${DEPLOY_BRANCH}" "${DEPLOY_REPO}" "${APP_ROOT}/bootstrap"
bash "${APP_ROOT}/bootstrap/deploy/scripts/audit-server.sh"
bash "${APP_ROOT}/bootstrap/deploy/scripts/install-runtime.sh" --prepare
bash "${APP_ROOT}/bootstrap/deploy/scripts/init-app-env.sh"
bash "${APP_ROOT}/bootstrap/deploy/scripts/provision-database.sh"
bash "${APP_ROOT}/bootstrap/deploy/scripts/backup.sh"
'
```

Expected: 无端口/vhost/目录冲突；PHP 8.3 pool/socket 和 queue unit/cron staging 完成预备但 queue/cron 未提前启动；`APP_ENV_FILE` 为 `root:${APP_USER}`/`0640` 且三个加密密钥与数据库密码均非空；MySQL 只创建本应用数据库/用户；备份 manifest 校验通过。任何审计冲突必须在安装 vhost 前停止。

- [ ] **Step 2: 先启用 HTTP-only vhost，再申请证书并切换 HTTPS**

Run on ECS:

```bash
sudo bash -lc '
set -Eeuo pipefail
set -a; source /etc/link-saas/deploy.env; set +a
install -d -o root -g root -m 0755 /var/www/acme
bash "${APP_ROOT}/bootstrap/deploy/scripts/render-nginx-config.sh" \
  "${APP_ROOT}/bootstrap/deploy/nginx/link-saas-http.conf.template" \
  "${NGINX_CONF_DIR}/link-saas.conf"
"${NGINX_BIN}" -t
systemctl reload nginx
IFS=, read -r -a domains <<<"${ALLOWED_SHARE_HOSTS}"
domain_args=()
for domain in "${domains[@]}"; do domain_args+=(-d "${domain}"); done
certbot certonly --webroot -w /var/www/acme "${domain_args[@]}" \
  --cert-name "${PUBLIC_HOST}" --non-interactive --agree-tos -m "${ACME_EMAIL}"
bash "${APP_ROOT}/bootstrap/deploy/scripts/render-nginx-config.sh" \
  "${APP_ROOT}/bootstrap/deploy/nginx/link-saas-https.conf.template" \
  "${NGINX_CONF_DIR}/link-saas.conf"
"${NGINX_BIN}" -t
systemctl reload nginx
certbot renew --dry-run
'
```

Expected: 证书文件存在；`certbot renew --dry-run` PASS。若安装 Certbot 需要系统包变更，先记录包源、版本与回滚命令。

- [ ] **Step 3: 发布目标 SHA**

Run: `release_sha="$(git rev-parse HEAD)"; test "$(git ls-remote origin refs/heads/codex/full-function-stabilization | awk '{print $1}')" = "$release_sha" && sudo env RELEASE_SHA="$release_sha" bash -lc 'set -Eeuo pipefail; set -a; source /etc/link-saas/deploy.env; set +a; bash "${APP_ROOT}/bootstrap/deploy/scripts/release.sh"'`

Expected: current 软链接指向目标 SHA；迁移、服务 reload、route-specific health 全部通过。

- [ ] **Step 4: 在新代码健康后建立 encrypted-secret 回滚兼容下限**

Run on ECS:

```bash
sudo bash -lc '
set -Eeuo pipefail
set -a; source /etc/link-saas/deploy.env; set +a
status_json="$("${PHP_BIN}" "${APP_ROOT}/current/serve/artisan" app:secret-storage-status --json)"
jq -e ".compatible == true" <<<"${status_json}" >/dev/null
plaintext_count="$(jq -r ".plaintext_count" <<<"${status_json}")"
if (( plaintext_count > 0 )); then
  bash "${APP_ROOT}/bootstrap/deploy/scripts/backup.sh"
  "${PHP_BIN}" "${APP_ROOT}/current/serve/artisan" app:encrypt-legacy-secrets
fi
status_json="$("${PHP_BIN}" "${APP_ROOT}/current/serve/artisan" app:secret-storage-status --json)"
jq -e ".compatible == true and .plaintext_count == 0" <<<"${status_json}" >/dev/null
current_sha="$(basename "$(readlink -f "${APP_ROOT}/current")")"
marker_tmp="$(mktemp "${APP_ROOT}/shared/.secret-compatible.XXXXXX")"
printf "%s\n" "${current_sha}" >"${marker_tmp}"
chmod 0600 "${marker_tmp}"
mv -f "${marker_tmp}" "${APP_ROOT}/shared/secret-compatible-floor"
bash "${APP_ROOT}/bootstrap/deploy/scripts/healthcheck.sh"
'
```

Expected: 新库直接得到 `plaintext_count=0`；存在历史明文时先额外备份、再事务化加密，最终状态为 `compatible=true/plaintext_count=0`，并记录当前兼容 SHA。从此回滚脚本必须拒绝不支持 encrypted-secret 的旧 release；不能用普通软链接回切伪装 secret 兼容。

- [ ] **Step 5: 交互式建立首个管理员并完成强制改密**

Run in the operator-controlled ECS terminal with a TTY: `sudo bash -lc 'set -a; source /etc/link-saas/deploy.env; set +a; exec sudo -u "${APP_USER}" "${PHP_BIN}" "${APP_ROOT}/current/serve/artisan" app:admin-provision'`

Expected: 命令交互式询问管理员用户名和至少 12 位密码，密码不出现在 argv/日志/聊天；数据库中只有显式创建的管理员且 `must_change_password=true`。运营方随后用浏览器首次登录并完成强制改密；该步骤属于身份/凭据门，不由非交互脚本伪造默认管理员。如需运行外部 E2E，再单独建立专用测试管理员/会员，不使用 owner 账号做可破坏 fixture。

- [ ] **Step 6: 验证服务运行证据**

Run:

```bash
sudo bash -lc '
set -a; source /etc/link-saas/deploy.env; set +a
"${NGINX_BIN}" -t
systemctl is-active nginx "${PHP_FPM_SERVICE}" "${MYSQL_SERVICE}" "${REDIS_SERVICE}" "${QUEUE_SERVICE}" "${CRON_SERVICE}"
systemctl is-enabled "${QUEUE_SERVICE}" "${CRON_SERVICE}"
test -f /etc/cron.d/link-saas
grep -q 'artisan schedule:run' /etc/cron.d/link-saas
curl -fsS "${PUBLIC_ORIGIN}/api/config" | jq -e ".code == 0 and .data.brand.productName != null"
curl -fsS "${PUBLIC_ORIGIN}/web/" | grep -q "<div id=\"app\""
"${PHP_BIN}" "${APP_ROOT}/current/serve/artisan" schedule:run
journalctl -u "${QUEUE_SERVICE}" --since "15 minutes ago" --no-pager
'
```

Expected: 每条命令有明确成功输出；不能只记录 HTTP 200。

- [ ] **Step 7: 执行 Playwright 浏览器验收**

Run: `cd admin && E2E_ORIGIN="$PUBLIC_ORIGIN" E2E_ADMIN_USER="$E2E_ADMIN_USER" E2E_ADMIN_PASSWORD="$E2E_ADMIN_PASSWORD" E2E_MEMBER_USERNAME="$E2E_MEMBER_USERNAME" pnpm test:e2e`

Expected: 凭据来自本机不入 Git 的环境；外部模式不运行清库/Seeder。登录、公告、素材、域名、小程序池、人工会员、六类链接 CRUD/status/share、退出和未预期 404 检查全部 PASS；fixture 只删除本次唯一前缀产生的 ID。

- [ ] **Step 8: 填写真机与外部平台矩阵**

`device-matrix.md` 每类必须记录配置输入、API 目标、Android、iOS、外部凭据门和证据时间。缺少凭据写 `外部阻塞`，不得写 PASS。

- [ ] **Step 9: 执行备份恢复演练**

在测试数据库副本运行显式数据库恢复；生产/共享数据库只演练代码软链接回滚。记录恢复耗时、数据行数校验和健康检查结果。

- [ ] **Step 10: Commit**

```bash
git add docs/acceptance docs/deployment.md
git commit -m "docs: record full-function release evidence"
```
