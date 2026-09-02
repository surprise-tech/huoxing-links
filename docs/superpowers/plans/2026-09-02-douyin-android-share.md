# 极风小助手 Android 抖音私信卡片实施计划

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** 交付正式签名的“极风小助手”Android APK，让用户主动选择抖音好友或群，发送一个审核通过、可重复打开、最终展示微信二维码的固定链接卡片。

**Architecture:** 从 `codex/card-jump-only` 创建独立实现分支，在现有 H5 二维码链路旁新增 `android/` 工程。Android UI 只依赖纯 Kotlin 卡片模型和分享网关；ByteDance SDK 被隔离在 `douyin/` 适配层，生产 URL、Client Key 和签名从未跟踪的本机配置注入。

**Tech Stack:** Kotlin 2.2.20、Android Gradle Plugin 8.13.0、Gradle 8.13、JDK 21、Android API 23–36、XML + ViewBinding、AndroidX、ByteDance Open SDK 0.2.0.9、PHPUnit 10、ADB 真机验收。

**Spec:** `docs/superpowers/specs/2026-09-02-douyin-android-share-design.md`

## Global Constraints

- 实施代码从 `codex/card-jump-only@6ca16b6` 创建 `codex/douyin-android-share`，不直接修改 `main`。
- Android 应用 ID 固定为 `com.jixingwangluo.jifengassistant`。
- 只使用 ByteDance 官方 `0.2.0.9` SDK 和官方 Demo 的联系人分享方式。
- 第一阶段只分享一个精确审核通过的固定 H5 URL，不提供任意 URL 输入。
- 第一阶段不实现注册、登录、会员、支付、退款、Webhooks、iOS、鸿蒙或抖音小程序。
- 不自动发送、不读取私信、不预选联系人、不通过多跳或换域名规避审核。
- `Client Secret`、access token、keystore、密码、个人微信二维码、APK/AAB 和本机配置不得进入 Git。
- SDK 初始化必须在用户同意隐私提示后，且 `autoStartTracker(false)`。
- 正式验收使用 release keystore，在努比亚 `NX789J`（Android 16、抖音 40.3.0）完成。
- 浏览器提交包信息、白名单、分享 URL、轮换 Secret 和上线转正前，分别取得动作时确认。
- 最终发送由用户在抖音界面手动选择测试好友或群并确认。

---

## File Structure

- Modify: `.gitignore` — 忽略 Android 本机配置、签名和构建产物。
- Create: `android/settings.gradle.kts`, `android/build.gradle.kts`, `android/gradle.properties`, `android/gradle/libs.versions.toml`, `android/gradle/wrapper/*`, `android/gradlew*` — 可重复构建。
- Create: `android/app/build.gradle.kts`, `android/app/proguard-rules.pro`, `android/app/src/main/AndroidManifest.xml` — App、签名和 SDK 配置。
- Create: `android/app/src/main/java/com/jixingwangluo/jifengassistant/model/ApprovedShareCard.kt` — 固定审核卡片验证。
- Create: `android/app/src/main/java/com/jixingwangluo/jifengassistant/douyin/*` — SDK 初始化、分享、state 和回调。
- Create: `android/app/src/main/java/com/jixingwangluo/jifengassistant/privacy/PrivacyConsentStore.kt` — 本地隐私同意。
- Create: `android/app/src/main/java/com/jixingwangluo/jifengassistant/ui/*` — 纯 UI 状态。
- Create/Modify: `android/app/src/main/java/com/jixingwangluo/jifengassistant/JifengApplication.kt`, `AppGraph.kt`, `MainActivity.kt` — 应用入口和依赖装配。
- Create: `android/app/src/main/res/layout/activity_main.xml`, `values/strings.xml`, `values/themes.xml`, `drawable/jifeng_assistant_icon.png` — 单屏 UI。
- Create: `android/scripts/create-release-keystore.sh`, `write-local-config.sh`, `verify-release-apk.sh`, `test-release-tools.sh` — 本机配置和发布门。
- Create: `android/app/src/test/**`, `android/app/src/androidTest/**` — 单元和真机 UI 测试。
- Create: `android/README.md`, `docs/acceptance/2026-09-02-douyin-android-share.md` — 运行说明和非敏感验收记录。

---

### Task 1: Create the isolated worktree and reproducible Android shell

**Files:**
- Create: `.worktrees/douyin-android-share/`
- Create: `android/settings.gradle.kts`
- Create: `android/build.gradle.kts`
- Create: `android/gradle.properties`
- Create: `android/gradle/libs.versions.toml`
- Create: `android/app/build.gradle.kts`
- Create: `android/app/src/main/AndroidManifest.xml`
- Create: `android/app/src/main/java/com/jixingwangluo/jifengassistant/MainActivity.kt`
- Create: `android/app/src/main/res/layout/activity_main.xml`
- Create: `android/app/src/main/res/values/strings.xml`
- Create: `android/app/src/main/res/values/themes.xml`
- Modify: `.gitignore`

**Interfaces:**
- Consumes: `codex/card-jump-only@6ca16b6`, design commit `eb96905`.
- Produces: `android/gradlew`, installable debug shell, resolved ByteDance SDK 0.2.0.9.

- [ ] **Step 1: Create the execution worktree with the required skill**

Invoke `superpowers:using-git-worktrees`, then run:

```bash
git worktree add .worktrees/douyin-android-share -b codex/douyin-android-share codex/card-jump-only
git -C .worktrees/douyin-android-share cherry-pick eb96905
git -C .worktrees/douyin-android-share status -sb
```

Expected: clean `codex/douyin-android-share` at the card-jump baseline plus the approved design.

- [ ] **Step 2: Verify RED before scaffolding**

```bash
cd .worktrees/douyin-android-share
test ! -e android/settings.gradle.kts
```

Expected: exit 0. If the file exists, stop and inspect the unexpected Android work.

- [ ] **Step 3: Create build settings and version catalog**

Create `android/settings.gradle.kts`:

```kotlin
pluginManagement {
    repositories { google(); mavenCentral(); gradlePluginPortal() }
}
dependencyResolutionManagement {
    repositoriesMode.set(RepositoriesMode.FAIL_ON_PROJECT_REPOS)
    repositories {
        google()
        mavenCentral()
        maven("https://artifact.bytedance.com/repository/AwemeOpenSDK")
    }
}
rootProject.name = "JifengAssistant"
include(":app")
```

Create `android/build.gradle.kts`:

```kotlin
plugins {
    id("com.android.application") version "8.13.0" apply false
    id("org.jetbrains.kotlin.android") version "2.2.20" apply false
}
```

Create `android/gradle/libs.versions.toml` with these exact stable versions from the official artifact repositories:

```toml
[versions]
coreKtx = "1.19.0"
appcompat = "1.8.0"
material = "1.14.0"
lifecycle = "2.11.0"
coroutines = "1.11.0"
douyin = "0.2.0.9"
junit4 = "4.13.2"
androidxJunit = "1.3.0"
espresso = "3.7.0"

[libraries]
androidx-core-ktx = { module = "androidx.core:core-ktx", version.ref = "coreKtx" }
androidx-appcompat = { module = "androidx.appcompat:appcompat", version.ref = "appcompat" }
material = { module = "com.google.android.material:material", version.ref = "material" }
lifecycle-runtime-ktx = { module = "androidx.lifecycle:lifecycle-runtime-ktx", version.ref = "lifecycle" }
lifecycle-viewmodel-ktx = { module = "androidx.lifecycle:lifecycle-viewmodel-ktx", version.ref = "lifecycle" }
coroutines-android = { module = "org.jetbrains.kotlinx:kotlinx-coroutines-android", version.ref = "coroutines" }
coroutines-test = { module = "org.jetbrains.kotlinx:kotlinx-coroutines-test", version.ref = "coroutines" }
douyin-external = { module = "com.bytedance.ies.ugc.aweme:opensdk-china-external", version.ref = "douyin" }
douyin-common = { module = "com.bytedance.ies.ugc.aweme:opensdk-common", version.ref = "douyin" }
junit4 = { module = "junit:junit", version.ref = "junit4" }
androidx-test-junit = { module = "androidx.test.ext:junit", version.ref = "androidxJunit" }
espresso-core = { module = "androidx.test.espresso:espresso-core", version.ref = "espresso" }
```

- [ ] **Step 4: Create the minimal app module**

Create this initial `android/app/build.gradle.kts`:

```kotlin
plugins {
    id("com.android.application")
    id("org.jetbrains.kotlin.android")
}

android {
    namespace = "com.jixingwangluo.jifengassistant"
    compileSdk = 36
    defaultConfig {
        applicationId = "com.jixingwangluo.jifengassistant"
        minSdk = 23
        targetSdk = 36
        versionCode = 1
        versionName = "1.0.0"
        testInstrumentationRunner = "androidx.test.runner.AndroidJUnitRunner"
    }
    buildFeatures { viewBinding = true; buildConfig = true }
    compileOptions {
        sourceCompatibility = JavaVersion.VERSION_17
        targetCompatibility = JavaVersion.VERSION_17
    }
    kotlinOptions { jvmTarget = "17" }
}

dependencies {
    implementation(libs.androidx.core.ktx)
    implementation(libs.androidx.appcompat)
    implementation(libs.material)
    implementation(libs.lifecycle.runtime.ktx)
    implementation(libs.lifecycle.viewmodel.ktx)
    implementation(libs.coroutines.android)
    implementation(libs.douyin.external)
    implementation(libs.douyin.common)
    testImplementation(libs.junit4)
    testImplementation(libs.coroutines.test)
    androidTestImplementation(libs.androidx.test.junit)
    androidTestImplementation(libs.espresso.core)
}
```

The initial Activity only inflates `activity_main.xml`; the layout contains the app name. The initial manifest contains INTERNET, package visibility for `com.ss.android.ugc.aweme`, `.aweme.lite`, `.ugc.live`, and one exported launcher Activity.

- [ ] **Step 5: Add ignores and Gradle Wrapper**

Add exactly:

```gitignore
android/.gradle/
android/local.properties
android/**/build/
android/*.iml
android/.idea/
android/keystore.properties
android/**/*.jks
android/**/*.keystore
android/**/*.apk
android/**/*.aab
```

Then:

```bash
export JAVA_HOME='/Applications/Android Studio.app/Contents/jbr/Contents/Home'
cd android
gradle wrapper --gradle-version 8.13
```

- [ ] **Step 6: Resolve official SDK and verify GREEN**

```bash
export JAVA_HOME='/Applications/Android Studio.app/Contents/jbr/Contents/Home'
export ANDROID_SDK_ROOT='/opt/homebrew/share/android-commandlinetools'
cd android
./gradlew --no-daemon :app:dependencies --configuration debugRuntimeClasspath
./gradlew --no-daemon :app:assembleDebug
```

Expected: dependency output contains both ByteDance `0.2.0.9` modules and `app-debug.apk` is created.

- [ ] **Step 7: Commit**

```bash
git add .gitignore android
git diff --cached --check
git commit -m "build: bootstrap Jifeng Android app"
```

---

### Task 2: Add secure local configuration and release signing

**Files:**
- Create: `android/scripts/create-release-keystore.sh`
- Create: `android/scripts/write-local-config.sh`
- Create: `android/scripts/verify-release-apk.sh`
- Create: `android/scripts/test-release-tools.sh`
- Modify: `android/app/build.gradle.kts`
- Create: `android/app/proguard-rules.pro`

**Interfaces:**
- Consumes: Task 1 Android build.
- Produces: external release keystore, mode-600 `local.properties`, signed APK verification.

- [ ] **Step 1: Write a failing tools test**

`test-release-tools.sh` creates a temporary `JIFENG_CONFIG_ROOT`, invokes the three missing helpers, asserts generated files are mode 600, verifies the keystore via `keytool -list`, and asserts local config contains Client Key and an HTTPS test URL but no `client_secret` key.

```bash
cd android
bash scripts/test-release-tools.sh
```

Expected: FAIL because helper scripts do not exist.

- [ ] **Step 2: Implement keystore generation**

`create-release-keystore.sh` uses `set -euo pipefail`, `umask 077`, default root `$HOME/.config/jifeng-assistant`, refuses overwrite, generates a 48-character random password, and runs:

```bash
keytool -genkeypair \
  -keystore "$config_root/release.jks" \
  -storetype PKCS12 \
  -storepass "$store_password" \
  -keypass "$store_password" \
  -alias jifengassistant \
  -keyalg RSA -keysize 3072 -validity 10000 \
  -dname 'CN=Jifeng Assistant, OU=Mobile, O=Hefei Jixing Network Technology Co Ltd, L=Hefei, ST=Anhui, C=CN'
```

Write absolute `storeFile`, `storePassword`, `keyAlias=jifengassistant`, `keyPassword` to external `keystore.properties`; never print the password.

- [ ] **Step 3: Implement public local config writer**

`write-local-config.sh` accepts SDK path, approved share URL and optional HTTPS thumb URL. It rejects non-HTTPS values and writes mode-600 `android/local.properties` with:

```properties
sdk.dir=/opt/homebrew/share/android-commandlinetools
DOUYIN_CLIENT_KEY=awm0sswt6y17zkhu
DOUYIN_APPROVED_SHARE_URL=https://link.example.test/?code=abc12345
DOUYIN_CARD_TITLE=添加微信
DOUYIN_CARD_DESCRIPTION=长按识别二维码，添加我为好友
DOUYIN_CARD_THUMB_URL=
```

The example URL is used only by the isolated script test; Task 3 overwrites it with the real URL.

- [ ] **Step 4: Wire BuildConfig and signing**

Load `local.properties` and external properties path from `JIFENG_KEYSTORE_PROPERTIES` (default `$HOME/.config/jifeng-assistant/keystore.properties`). Generate five string BuildConfig fields. Add `validateReleaseInputs` as a dependency of `preReleaseBuild`; it requires Client Key, approved URL, title, description and all keystore fields, while thumb may be empty. Release uses the external signing config and `minifyEnabled=false` for the first verified build.

- [ ] **Step 5: Implement APK verification**

`verify-release-apk.sh` accepts one APK and runs:

```bash
apkanalyzer manifest application-id "$apk" | grep -Fx 'com.jixingwangluo.jifengassistant'
/opt/homebrew/share/android-commandlinetools/build-tools/36.0.0/apksigner verify --verbose --print-certs "$apk"
unzip -l "$apk" | grep -E '\.(jks|keystore|properties)$' && exit 1 || true
zipgrep -i 'client_secret' "$apk" && exit 1 || true
```

- [ ] **Step 6: Verify GREEN and commit**

```bash
export JAVA_HOME='/Applications/Android Studio.app/Contents/jbr/Contents/Home'
cd android
bash scripts/test-release-tools.sh
cd ..
git add .gitignore android/app/build.gradle.kts android/app/proguard-rules.pro android/scripts
git diff --cached --check
git commit -m "build: add secure Android release signing"
```

---

### Task 3: Provision and submit one permanent production card

**Files:**
- Local only: `$HOME/.config/jifeng-assistant/wechat-qr.jpg`
- Local only: `android/local.properties`
- No tracked QR, token or secret files.

**Interfaces:**
- Consumes: user QR file, production admin, card-jump backend.
- Produces: immutable `LINK_CODE`, candidate `APPROVED_SHARE_URL`, `im.share` review row.

- [ ] **Step 1: Re-run existing backend tests**

```bash
cd .worktrees/douyin-android-share
docker compose -f compose.test.yaml up -d --wait
cd serve
vendor/bin/phpunit tests/Feature/CardJumpOnlyTest.php tests/Unit/Services/LinkShareUrlTest.php
```

Expected: PASS; stop if membership, URL or QR behavior regresses.

- [ ] **Step 2: Preserve the personal QR outside Git**

```bash
mkdir -p "$HOME/.config/jifeng-assistant"
install -m 600 /var/folders/rn/7502v_hs77g5zskts3mctn080000gn/T/codex-clipboard-1cbbe026-d986-48ad-9c25-cd298430828d.jpg \
  "$HOME/.config/jifeng-assistant/wechat-qr.jpg"
```

Verify image dimensions and scan locally; do not print QR payload.

- [ ] **Step 3: Create the fixed production card**

Immediately before uploading the personal QR, confirm the file and destination `https://link.bjaajsdad.xyz`. In the existing admin create one enabled `LANDING_MINI` card with title `添加微信`, subtitle `长按识别二维码，添加我为好友`, exactly one QR, no mini ID, `expired_at=null`, `uv_limit_num=null`, sequential mode and cumulative UV mode.

Read back the saved record and canonical URL; a success toast is not proof.

- [ ] **Step 4: Verify the chain**

With the actual `share_url` and parsed `link_code`:

```bash
curl -fsSI "$share_url" | grep -i "location: /j/$link_code"
curl -fsS "https://link.bjaajsdad.xyz/j/$link_code" | grep -F '/api/link-target/'
curl -fsS "https://link.bjaajsdad.xyz/qr/$link_code?visitor_token=invalid" | grep -F '正在加载二维码'
```

Open the real URL on the connected phone and verify it reaches the intended QR page. An invalid token must not expose QR data.

- [ ] **Step 5: Persist the exact URL locally**

```bash
cd .worktrees/douyin-android-share/android
bash scripts/write-local-config.sh /opt/homebrew/share/android-commandlinetools "$share_url" ''
```

- [ ] **Step 6: Submit exact URL for review**

Immediately before clicking “提交”, confirm the exact URL and destination application. Submit it under `im.share → 分享链接管理`, then read back `审核中`, `已通过`, or the exact rejection reason. Do not alter the URL after submission.

---

### Task 4: Implement card validation and callback result mapping with TDD

**Files:**
- Create: `android/app/src/main/java/com/jixingwangluo/jifengassistant/model/ApprovedShareCard.kt`
- Create: `android/app/src/main/java/com/jixingwangluo/jifengassistant/douyin/ShareResult.kt`
- Test: `android/app/src/test/java/com/jixingwangluo/jifengassistant/model/ApprovedShareCardTest.kt`
- Test: `android/app/src/test/java/com/jixingwangluo/jifengassistant/douyin/ShareResultMapperTest.kt`

**Interfaces:**
- Consumes: five BuildConfig strings from Task 2.
- Produces: `ApprovedShareCard.create(...)`, `ShareResultMapper.from(errorCode, isCancel, errorMessage)`.

- [ ] **Step 1: Write failing card tests**

```kotlin
class ApprovedShareCardTest {
    @Test fun acceptsApprovedHttpsCard() {
        val card = ApprovedShareCard.create(
            "https://link.example.test/?code=abc12345",
            "添加微信",
            "长按识别二维码，添加我为好友",
            null,
        )
        assertEquals("link.example.test", URI(card.url).host)
    }

    @Test fun rejectsUnsafeOrIncompleteCards() {
        assertFailsWith<IllegalArgumentException> {
            ApprovedShareCard.create("http://link.example.test", "添加微信", "描述", null)
        }
        assertFailsWith<IllegalArgumentException> {
            ApprovedShareCard.create("https://user@link.example.test/#x", "添加微信", "描述", null)
        }
        assertFailsWith<IllegalArgumentException> {
            ApprovedShareCard.create("https://link.example.test", " ", "描述", null)
        }
    }
}
```

- [ ] **Step 2: Write failing result tests**

Assert exact mappings: `20000 -> Success`, `20004/20013 -> Cancelled`, `20003 -> PermissionOrPackageMismatch`, `20006 -> NetworkError`, `20017 -> UrlNotApproved`, all others -> `Unknown` preserving code and a sanitized message.

- [ ] **Step 3: Run RED**

```bash
cd android
./gradlew --no-daemon :app:testDebugUnitTest --tests '*ApprovedShareCardTest' --tests '*ShareResultMapperTest'
```

Expected: compilation failure for missing production types.

- [ ] **Step 4: Implement minimal pure Kotlin types**

`ApprovedShareCard.create` uses `java.net.URI`; require HTTPS, nonblank host/title/description, no user info, no fragment, and optional HTTPS thumb. `ShareResult` is:

```kotlin
sealed interface ShareResult {
    data object Success : ShareResult
    data object Cancelled : ShareResult
    data class PermissionOrPackageMismatch(val code: Int, val message: String?) : ShareResult
    data class UrlNotApproved(val code: Int, val message: String?) : ShareResult
    data class NetworkError(val code: Int, val message: String?) : ShareResult
    data class Unknown(val code: Int, val message: String?) : ShareResult
}
```

Limit retained messages to 200 characters and strip control characters.

- [ ] **Step 5: Run GREEN and commit**

```bash
cd android
./gradlew --no-daemon :app:testDebugUnitTest --tests '*ApprovedShareCardTest' --tests '*ShareResultMapperTest'
cd ..
git add android/app/src/main/java/com/jixingwangluo/jifengassistant/model \
        android/app/src/main/java/com/jixingwangluo/jifengassistant/douyin/ShareResult.kt \
        android/app/src/test
git diff --cached --check
git commit -m "feat: validate approved Douyin share cards"
```

---

### Task 5: Integrate ByteDance SDK behind a testable gateway

**Files:**
- Create: `android/app/src/main/java/com/jixingwangluo/jifengassistant/douyin/ContactShareClient.kt`
- Create: `android/app/src/main/java/com/jixingwangluo/jifengassistant/douyin/DouyinContactShareClient.kt`
- Create: `android/app/src/main/java/com/jixingwangluo/jifengassistant/douyin/DouyinShareGateway.kt`
- Create: `android/app/src/main/java/com/jixingwangluo/jifengassistant/douyin/DouyinSdkInitializer.kt`
- Create: `android/app/src/main/java/com/jixingwangluo/jifengassistant/douyin/DouyinHostInfoService.kt`
- Create: `android/app/src/main/java/com/jixingwangluo/jifengassistant/douyin/DouyinLogService.kt`
- Create: `android/app/src/main/java/com/jixingwangluo/jifengassistant/douyin/PendingShareStore.kt`
- Create: `android/app/src/main/java/com/jixingwangluo/jifengassistant/douyin/DouYinEntryActivity.kt`
- Modify: `android/app/src/main/AndroidManifest.xml`
- Test: `android/app/src/test/java/com/jixingwangluo/jifengassistant/douyin/DouyinShareGatewayTest.kt`
- Test: `android/app/src/test/java/com/jixingwangluo/jifengassistant/douyin/PendingShareStoreTest.kt`

**Interfaces:**
- Consumes: `ApprovedShareCard`, `ShareResultMapper`.
- Produces: `DouyinShareGateway.launch(Activity, ApprovedShareCard): ShareLaunchResult` and callback Intent extras for MainActivity.

- [ ] **Step 1: Write failing gateway tests**

Use a fake `ContactShareClient` and cover:

```kotlin
@Test fun rejectsWhenDouyinIsNotInstalled()
@Test fun rejectsWhenContactShareIsUnsupported()
@Test fun persistsRandomStateBeforeLaunching()
@Test fun clearsPendingStateWhenSdkRejectsLaunch()
```

The fake records the exact card, state and callback class name.

Production and fake clients implement this exact boundary:

```kotlin
interface ContactShareClient {
    fun isInstalled(): Boolean
    fun supportsContactShare(): Boolean
    fun launch(card: ApprovedShareCard, state: String, callbackClassName: String): Boolean
}

sealed interface ShareLaunchResult {
    data class Launched(val state: String) : ShareLaunchResult
    data object DouyinNotInstalled : ShareLaunchResult
    data object ContactShareUnsupported : ShareLaunchResult
    data object SdkRejectedRequest : ShareLaunchResult
}
```

- [ ] **Step 2: Write failing pending-state tests**

Test `save`, `consumeIfMatches`, mismatch retention, and one-time consumption. JVM tests use an in-memory implementation; production uses private SharedPreferences.

- [ ] **Step 3: Run RED**

```bash
cd android
./gradlew --no-daemon :app:testDebugUnitTest --tests '*DouyinShareGatewayTest' --tests '*PendingShareStoreTest'
```

Expected: compilation failure for missing gateway types.

- [ ] **Step 4: Implement the SDK client with verified AAR signatures**

```kotlin
val htmlObject = ContactHtmlObject().apply {
    setHtml(card.url)
    setTitle(card.title)
    setDiscription(card.description)
    card.thumbUrl?.let(::setThumbUrl)
}
val request = ShareToContact.Request().apply {
    callerLocalEntry = DouYinEntryActivity::class.java.canonicalName
    mState = state
    this.htmlObject = htmlObject
}
val api = DouYinOpenApiFactory.create(activity)
return api.shareToContacts(request)
```

Check `api.isAppInstalled` and `api.isAppSupportShareToContacts` before launch.

- [ ] **Step 5: Implement initialization**

Build `DouYinOpenSDKConfig` with context, Client Key, `DouyinHostInfoService`, `DouyinLogService`, and `autoStartTracker(false)`, then call `DouYinOpenApiFactory.initConfig(config)`. The initializer is idempotent and rejects blank Client Key. Its separate `onPrivacyAccepted()` method first initializes, then calls `OpenTrackerManager.start()`; neither method is called before stored or just-granted consent is true.

`DouyinHostInfoService` returns app ID/name/version and empty device/install identifiers. `DouyinLogService` writes only tag and sanitized message in debug builds.

- [ ] **Step 6: Implement callback Activity**

Register exported, singleTask `.douyin.DouYinEntryActivity` with task affinity `com.jixingwangluo.jifengassistant`. In `onCreate` and `onNewIntent`, call `handleIntent`. For matching `ShareToContact.Response`, consume state, map result, and start MainActivity using `CLEAR_TOP | SINGLE_TOP`; do not log Intent extras.

- [ ] **Step 7: Add keep rules, run GREEN and commit**

```proguard
-keep class com.bytedance.sdk.open.aweme.** { *; }
-keep class com.bytedance.sdk.open.douyin.** { *; }
-keep class com.jixingwangluo.jifengassistant.douyin.DouYinEntryActivity { *; }
```

```bash
cd android
./gradlew --no-daemon :app:testDebugUnitTest :app:assembleDebug
cd ..
git add android/app/src/main android/app/src/test android/app/proguard-rules.pro
git diff --cached --check
git commit -m "feat: integrate Douyin contact sharing SDK"
```

---

### Task 6: Build the fixed-card UI and privacy gate

**Files:**
- Create: `android/app/src/main/java/com/jixingwangluo/jifengassistant/JifengApplication.kt`
- Create: `android/app/src/main/java/com/jixingwangluo/jifengassistant/AppGraph.kt`
- Modify: `android/app/src/main/java/com/jixingwangluo/jifengassistant/MainActivity.kt`
- Create: `android/app/src/main/java/com/jixingwangluo/jifengassistant/privacy/PrivacyConsentStore.kt`
- Create: `android/app/src/main/java/com/jixingwangluo/jifengassistant/ui/MainUiState.kt`
- Create: `android/app/src/main/java/com/jixingwangluo/jifengassistant/ui/MainViewModel.kt`
- Modify: `android/app/src/main/res/layout/activity_main.xml`
- Modify: `android/app/src/main/res/values/strings.xml`
- Modify: `android/app/src/main/res/values/themes.xml`
- Create: `android/app/src/main/res/drawable/jifeng_assistant_icon.png`
- Modify: `android/app/src/main/AndroidManifest.xml`
- Test: `android/app/src/test/java/com/jixingwangluo/jifengassistant/ui/MainViewModelTest.kt`
- Test: `android/app/src/androidTest/java/com/jixingwangluo/jifengassistant/MainActivityTest.kt`

**Interfaces:**
- Consumes: `ApprovedShareCard`, `DouyinShareGateway`, callback extras.
- Produces: one-screen user flow and privacy-controlled SDK initialization.

- [ ] **Step 1: Write failing ViewModel tests**

```kotlin
@Test fun shareIsDisabledBeforePrivacyConsent()
@Test fun consentEnablesShareWhenCardIsValid()
@Test fun launchedStateShowsWaitingForDouyin()
@Test fun callbackResultReplacesWaitingState()
```

- [ ] **Step 2: Write failing instrumentation smoke test**

Launch MainActivity with a test graph. Assert app name, fixed title, fixed description and share button are visible; with privacy false, assert share is disabled until `同意并继续` is tapped.

- [ ] **Step 3: Run RED**

```bash
cd android
./gradlew --no-daemon :app:testDebugUnitTest --tests '*MainViewModelTest'
```

- [ ] **Step 4: Implement UI state and privacy store**

`MainUiState` contains card, `privacyAccepted`, `shareEnabled`, `inProgress`, `statusText`. MainViewModel only transforms state. `PrivacyConsentStore` uses private key `privacy_accepted_v1`. JifengApplication initializes SDK only when stored consent is true; first acceptance stores true and invokes the same idempotent initializer.

Use this immutable state shape:

```kotlin
data class MainUiState(
    val card: ApprovedShareCard,
    val privacyAccepted: Boolean,
    val inProgress: Boolean = false,
    val statusText: String = "",
) {
    val shareEnabled: Boolean get() = privacyAccepted && !inProgress
}
```

- [ ] **Step 5: Implement the single screen**

The screen contains approved app icon, title/description, read-only approved domain, `分享到抖音好友/群` button and status text. The dialog contains `不同意` and `同意并继续`. Copy `docs/assets/branding/jifeng-assistant-icon.png` into the drawable path; never include the personal QR in the APK.

- [ ] **Step 6: Wire MainActivity**

Create the card from BuildConfig, show consent when needed, initialize SDK after acceptance, call the gateway, and process callback extras in `onCreate`/`onNewIntent`. Do not add login or app-originated network calls.

Set `android:name=".JifengApplication"` on `<application>` and `android:launchMode="singleTop"` on MainActivity so callback extras reach the existing screen.

- [ ] **Step 7: Run GREEN and commit**

```bash
export JAVA_HOME='/Applications/Android Studio.app/Contents/jbr/Contents/Home'
export ANDROID_SDK_ROOT='/opt/homebrew/share/android-commandlinetools'
cd android
./gradlew --no-daemon :app:testDebugUnitTest
./gradlew --no-daemon :app:connectedDebugAndroidTest
cd ..
git add android/app/src/main android/app/src/test android/app/src/androidTest
git diff --cached --check
git commit -m "feat: add fixed Douyin card sharing flow"
```

---

### Task 7: Build signed APK and configure package information and whitelist

**Files:**
- Local only: `$HOME/.config/jifeng-assistant/release.jks`
- Local only: `$HOME/.config/jifeng-assistant/keystore.properties`
- Local only: `android/app/build/outputs/apk/release/app-release.apk`

**Interfaces:**
- Consumes: app code, exact share URL, external keystore.
- Produces: signed release APK, package signature, configured Android package and one test whitelist account.

- [ ] **Step 1: Create and back up release key**

```bash
export JAVA_HOME='/Applications/Android Studio.app/Contents/jbr/Contents/Home'
cd android
bash scripts/create-release-keystore.sh
```

Verify both local files are mode 600. Stop and ask the user to confirm an offline backup exists before registering this signer with Douyin.

- [ ] **Step 2: Build and verify release**

```bash
export JAVA_HOME='/Applications/Android Studio.app/Contents/jbr/Contents/Home'
export ANDROID_SDK_ROOT='/opt/homebrew/share/android-commandlinetools'
export JIFENG_KEYSTORE_PROPERTIES="$HOME/.config/jifeng-assistant/keystore.properties"
cd android
./gradlew --no-daemon clean testDebugUnitTest lintRelease assembleRelease
bash scripts/verify-release-apk.sh app/build/outputs/apk/release/app-release.apk
keytool -printcert -jarfile app/build/outputs/apk/release/app-release.apk
```

Expected: exact package ID, one signer, no secret/keystore file in APK.

- [ ] **Step 3: Submit Android package information**

Immediately before `提交修改`, confirm destination, package `com.jixingwangluo.jifengassistant`, and the MD5 fingerprint from the release APK. Leave APK link and market URL empty. Submit, then read back package and signature; never enter Client Secret.

- [ ] **Step 4: Add one test Douyin account**

Ask for the exact Douyin ID used on `NX789J`. Immediately before adding it, confirm that this grants test-app authorization. Complete any user-held scan/authorization step and read back the row/status; a successful click is not proof.

---

### Task 8: Run all automated release gates and install on the real phone

**Files:**
- Modify only if an evidence-backed test/build failure needs a scoped fix.
- Local only: release APK and ADB logs.

**Interfaces:**
- Consumes: package/signature, whitelist, pending or approved URL.
- Produces: installed release build and non-sending device evidence.

- [ ] **Step 1: Run backend and Android gates**

```bash
cd .worktrees/douyin-android-share
docker compose -f compose.test.yaml up -d --wait
cd serve
vendor/bin/phpunit tests/Feature/CardJumpOnlyTest.php tests/Unit/Services/LinkShareUrlTest.php
cd ../android
./gradlew --no-daemon clean testDebugUnitTest connectedDebugAndroidTest lintRelease assembleRelease
bash scripts/verify-release-apk.sh app/build/outputs/apk/release/app-release.apk
git diff --check
```

Expected: every command passes. Commit each scoped fix separately.

- [ ] **Step 2: Install release APK**

```bash
adb -s FY25020100E6 install -r android/app/build/outputs/apk/release/app-release.apk
adb -s FY25020100E6 shell dumpsys package com.jixingwangluo.jifengassistant | rg 'versionName=|versionCode='
adb -s FY25020100E6 shell monkey -p com.jixingwangluo.jifengassistant 1
```

Expected: install succeeds, package/version match, main screen opens.

- [ ] **Step 3: Run non-sending device checks**

Verify first-launch privacy dialog, decline/accept behavior, fixed card copy, approved domain, consent persistence, and absence of registration/login/member/payment UI. Do not enter the final send flow until the share-link row reads `已通过`.

---

### Task 9: Complete human send/open acceptance and release evidence

**Files:**
- Create: `docs/acceptance/2026-09-02-douyin-android-share.md`
- Create: `android/README.md`

**Interfaces:**
- Consumes: exact URL status `已通过`, installed signed APK, authorized whitelist account.
- Produces: real send/open evidence, rotated secret, optional formal application, final PR.

- [ ] **Step 1: Verify exact URL approval**

Read “分享链接管理” and verify the exact configured URL is `已通过`. If rejected, record and fix the stated root cause; do not add redirect layers or alternate domains to bypass it.

- [ ] **Step 2: Hand final sending to the user**

Open the app and tap the share button. When Douyin displays contacts/groups, stop. The user selects a test recipient and confirms send. After return, record callback kind/code without contact names or message content.

- [ ] **Step 3: Verify recipient and landing**

With the user verify: card visible; title/description/icon correct; card opens approved URL; redirect reaches `/j/{LINK_CODE}` then `/qr/{LINK_CODE}`; QR is readable; reopening works. Sender callback alone is not completion.

- [ ] **Step 4: Rotate the exposed Client Secret**

Immediately before reset, confirm credential rotation in “极风小助手”. Android remains unaffected because it only uses Client Key. Store a new secret only in server secret storage if a future server flow needs it; never Git/chat.

- [ ] **Step 5: Ask before formalization**

Show passed device evidence and platform statuses. Only after explicit confirmation click `上线转正`, then read back `正式应用`. If declined, leave test app unchanged and report the 100/day limit.

- [ ] **Step 6: Write non-sensitive evidence**

The acceptance document records branch/SHA, SDK/build versions, APK SHA-256, signer MD5, package/device/OS/Douyin versions, package/whitelist/link statuses, sender callback, recipient card/open result, QR result, formalization decision and unresolved issues. Exclude Douyin IDs, contact names, QR payload, secrets, passwords, tokens and private screenshots.

- [ ] **Step 7: Document build and run final review**

`android/README.md` documents prerequisites, configuration keys, external keystore path, tests and manual gates. Then:

```bash
git status -sb
git diff --check
git diff --stat codex/card-jump-only...HEAD
git log --oneline codex/card-jump-only..HEAD
```

- [ ] **Step 8: Commit, push and open Draft PR**

```bash
git add android/README.md docs/acceptance/2026-09-02-douyin-android-share.md
git diff --cached --check
git commit -m "docs: record Douyin card sharing acceptance"
git push -u origin codex/douyin-android-share
```

Create a Draft PR to the intended integration branch. Separate automated, platform and human evidence; do not claim multi-card SaaS support.

---

## Final Acceptance Matrix

| Lane | Required evidence | Not sufficient |
|---|---|---|
| Build | Gradle tests, lint and signed release pass | IDE sync |
| APK | package ID, signer and no-secret checks pass | APK exists |
| Platform | package/signature saved, whitelist authorized, exact URL approved | scopes approved |
| Sender | user selects recipient and confirms send; callback recorded | app opens Douyin |
| Recipient | card visible and opens | sender toast |
| Landing | approved URL reaches readable QR twice | root HTTP 200 |
| Security | secret rotated, key outside Git, no sensitive evidence committed | `.gitignore` alone |
| Commercial | user-confirmed formalization reads `正式应用` | test app works |

The feature is complete only when every required lane passes or a clearly named external gate is reported as blocked.
