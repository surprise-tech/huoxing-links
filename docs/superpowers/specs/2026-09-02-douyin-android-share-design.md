# 极风小助手 Android 抖音私信卡片闭环设计

日期：2026-09-02

状态：用户已批准方案，待复核书面规格

规格分支基线：`codex/full-function-stabilization@7a61b5e`

实施代码基线：`codex/card-jump-only@6ca16b6`

## 1. 目标

第一阶段只交付一个可真实验收且符合平台规则的闭环：用户在独立 Android 应用“极风小助手”中点击“分享到抖音好友/群”，抖音展示联系人或群选择页；用户手动选择并发送后，接收方看到链接卡片，点击后直接进入“极风小助手”静态功能介绍页。审核页不包含微信、二维码、联系方式、下载、外部按钮或自动跳转。

第一阶段完成的唯一判定不是“SDK 能编译”“抖音已拉起”或“权限已通过”，而是连接中的真实 Android 手机完成一次端到端发送与打开验收。

## 2. 已确认的外部状态

- 抖音移动应用名称为“极风小助手”，应用基础资料已完成。
- `im.share` 与 `sdk.open.share` 已通过。
- 应用仍为测试应用，控制台提示当前能力调用上限为 100 次/天，后续会逐步降低；上线转正后解除测试额度限制。
- “分享链接管理”当前为空。控制台明确要求每次提交一个精确 URL，只有审核通过的 URL 才能成功分享给抖音好友。
- Android 应用包信息当前为空。控制台要求包名和应用签名，APK 链接与应用市场下载地址在未上架阶段可以留空。
- 测试抖音号白名单当前为 `0/20`。
- 已连接验收设备：努比亚 `NX789J`，Android 16 / API 36，已安装中国大陆抖音 `40.3.0`。

## 3. 范围

### 3.1 纳入范围

- 新增独立 Android 工程 `android/`。
- 固定应用 ID：`com.jixingwangluo.jifengassistant`。
- 使用抖音 Android Open SDK `0.2.0.9`。
- 使用 `ContactHtmlObject`、`ShareToContact.Request` 和 `shareToContacts()` 分享一个审核通过的固定 H5 卡片 URL。
- 展示一张固定卡片的标题、描述、封面和落地地址预览。
- 显式按钮“分享到抖音好友/群”，点击后才拉起抖音。
- 处理 SDK 不支持、抖音未安装、URL 未审核、用户取消、发送成功和其他 SDK 错误。
- 使用正式 keystore 签名 APK，获取应用签名并填写抖音控制台 Android 包信息。
- 添加一个测试抖音号白名单。
- 创建一个直接返回 HTTP 200 的永久静态审核页，并提交该精确 URL 审核。
- 在真实手机和真实抖音私信/群中完成发送与打开验收。

### 3.2 明确排除

- 注册、登录、会员、套餐、支付、退款和订阅。
- 自动发送、后台群发、读取私信、预先指定联系人或群。
- 抖音 OAuth 登录、用户信息、access token 和 Webhooks。
- iOS、鸿蒙和应用市场上架。
- 多租户、多卡片动态 SaaS、链接批量审核和动态更换目的地。
- 为规避抖音审核、风控或安全检查而增加的域名轮换、隐藏目标或无业务价值的多跳。
- 抖音小程序卡片；它是未来多卡片 SaaS 的候选方案，不属于本阶段。

## 4. 方案选择

### 4.1 采用方案：固定 H5 卡片 + 合规静态审核页

首版只分享一个固定且经过 `im.share` 审核的 URL。这个 URL 可重复发送和打开，不设使用次数限制。Android 端不提供任意 URL 输入框，避免用户提交未审核链接导致失败或产生合规风险。

现有后端链路保持为：

```text
抖音私信卡片
  -> https://link.bjaajsdad.xyz/douyin/jifeng-assistant
  -> HTTP 200 静态功能介绍页
```

审核页只展示应用名称、图标、真实功能说明和运营主体，不读取查询参数，不执行 JavaScript 跳转，不加载第三方资源。

已创建的个人微信二维码卡片 `https://link.bjaajsdad.xyz/?code=yWi221EG` 保留在自有服务器，但不得提交抖音、不得写入 Android 分享配置。若合规审核页被抖音拒绝，不通过增加中间域名、审核后换内容或多跳绕过；按平台拒绝原因修正真实页面或改用公司主体可控域名重新审核。

### 4.2 不采用：每张 H5 卡片单独审核

抖音控制台一次只能提交一个精确 URL。每新增一张卡都人工审核，不适合未来 SaaS，也不是首阶段证明核心能力所必需。

### 4.3 后续候选：同主体抖音小程序卡片

同主体抖音小程序可以携带动态参数，更适合多客户、多卡片的 SaaS。它需要单独的小程序开发、备案、审核和落地页设计，待固定 H5 卡片闭环验收通过后再立项。

## 5. Android 架构

### 5.1 工程边界

```text
android/
  settings.gradle.kts
  build.gradle.kts
  gradle.properties
  gradle/wrapper/
  app/
    build.gradle.kts
    proguard-rules.pro
    src/main/AndroidManifest.xml
    src/main/java/com/jixingwangluo/jifengassistant/
      JifengApplication.kt
      MainActivity.kt
      douyin/
        DouyinSdkInitializer.kt
        DouyinShareGateway.kt
        DouYinEntryActivity.kt
        ShareResult.kt
      model/
        ApprovedShareCard.kt
      ui/
        MainViewModel.kt
    src/main/res/
```

Android 工程与 `admin/`、`serve/`、`mini_programs/` 平级，不把原生代码塞进微信小程序目录。抖音 SDK 只存在于 `douyin/` 适配层，UI 和业务模型不直接依赖 SDK 类型。

### 5.2 构建基线

- Android Studio：本机 Panda 2025.3。
- JDK：Android Studio 内置 JBR 21。
- Android Gradle Plugin：`8.13.0`。
- Gradle Wrapper：`8.13`。
- Kotlin：`2.2.20`。
- `compileSdk` / `targetSdk`：36。
- `minSdk`：23。
- UI：XML + ViewBinding，不引入 Compose。
- 抖音 SDK：
  - `com.bytedance.ies.ugc.aweme:opensdk-china-external:0.2.0.9`
  - `com.bytedance.ies.ugc.aweme:opensdk-common:0.2.0.9`

选择 Views + ViewBinding 是为了保持首版小而稳定，并贴近 ByteDance 官方 Demo 的接入方式。实现前必须通过 ByteDance Maven 仓库实际解析依赖；若 `0.2.0.9` 与构建基线发生可复现冲突，先记录冲突并缩小到最小复现，再调整构建版本，不能替换为非官方 SDK。

### 5.3 配置与密钥

- `Client Key` 作为构建配置注入 Android，不写死在业务代码。
- `Client Secret`、access token、服务端凭据和 keystore 密码绝不进入 APK 或 Git。
- `DOUYIN_CLIENT_KEY`、`DOUYIN_APPROVED_SHARE_URL`、卡片标题、描述和封面 URL 通过本机未跟踪的 Gradle 属性或环境变量注入；缺失时 release 构建直接失败。
- 正式 keystore 保存在仓库外，权限设为仅当前用户可读，并另做离线备份。
- `keystore.properties`、`local.properties`、keystore、APK、AAB、Gradle 缓存和 Android Studio 本机文件全部加入 `.gitignore`。
- 截图中已经出现过 Client Secret；正式上线前必须在控制台轮换，轮换后的值只保存于服务端秘密存储。本阶段分享 SDK 不需要 Client Secret。

## 6. SDK 接入与数据流

### 6.1 初始化

`JifengApplication` 不在用户同意隐私政策前启动 SDK 埋点。初始化使用 `DouYinOpenSDKConfig.Builder`，设置 `autoStartTracker(false)`；用户在首次启动确认隐私提示后，才允许调用 SDK 分享入口。

本阶段不做抖音登录，因此不请求用户资料 scope，不交换 OAuth code，不需要 Client Secret。

### 6.2 Manifest

- 声明网络权限。
- 在 `<queries>` 中声明抖音、抖音极速版和官方文档要求的兼容包名。
- 注册导出的 `DouYinEntryActivity` 作为 SDK 回调入口，`launchMode=singleTask`，`taskAffinity` 与应用包名一致。
- 不申请通讯录、短信、相册等与固定 H5 卡片无关的权限。
- 若未来分享本地图片再引入 FileProvider；固定 H5 卡片阶段不需要本地媒体权限。

### 6.3 分享请求

`ApprovedShareCard` 只保存一个已审核卡片：

- `url`：精确审核通过的 canonical URL。
- `title`：卡片标题。
- `description`：卡片描述。
- `thumbUrl`：稳定 HTTPS 远程封面；为空时由抖音使用应用图标。

点击分享按钮后：

1. 校验固定 URL、标题和隐私同意状态。
2. 用 `DouYinOpenApiFactory.create(activity)` 创建 API。
3. 检查 `isAppSupportShareToContacts`。
4. 构造 `ContactHtmlObject`。
5. 构造 `ShareToContact.Request`，设置自定义回调 Activity 和随机 state。
6. 调用 `shareToContacts(request)`。
7. 抖音展示联系人/群选择页，用户自行选择并发送。

应用不得代替用户选择联系人，不得自动点击发送，也不得将“成功拉起抖音”记为“发送成功”。

### 6.4 回调

`DouYinEntryActivity` 实现 `IApiEventHandler`，在 `onCreate` 和新 Intent 场景中交给 `DouYinOpenApi.handleIntent`。只处理当前 state 对应的 `ShareToContact.Response`，将 SDK 结果映射为：

- `SUCCESS`：SDK 返回成功；仍需接收方打开卡片作为最终验收。
- `CANCELLED`：用户取消或返回。
- `NOT_SUPPORTED`：抖音版本或环境不支持联系人分享。
- `PERMISSION_OR_PACKAGE_MISMATCH`：权限、Client Key、包名或签名不匹配。
- `URL_NOT_APPROVED`：URL 无效或未审核。
- `NETWORK_ERROR`。
- `UNKNOWN_ERROR`：保留错误码，不记录敏感数据。

回调后回到主页面显示可理解结果，同时将 SDK 原始错误码写入仅调试可见日志。

## 7. 控制台配置顺序

1. 生成并备份正式 keystore。
2. 构建 release APK。
3. 使用 `keytool -printcert -jarfile android/app/build/outputs/apk/release/app-release.apk` 获取正式 APK 签名信息。
4. 在抖音“开发设置 → 应用信息”勾选 Android，提交：
   - 应用包名：`com.jixingwangluo.jifengassistant`
   - 应用签名：正式 APK 签名
   - APK 链接：首阶段留空
   - 应用下载地址：未上架阶段留空
5. 在“白名单管理”添加本次测试用抖音号并完成授权。
6. 新增并部署 `/douyin/jifeng-assistant` 静态审核页，确认无微信、二维码、联系方式、下载、外部链接或跳转。
7. 对该精确 URL 做裸网络与手机浏览器验收，确认直接返回 HTTP 200。
8. 在 `im.share` 的“分享链接管理”提交该精确 URL，等待通过。
9. 把审核通过的 URL 注入 release 构建，重新签名并安装。
10. 真实抖音发送与打开验收通过后，再由用户确认是否点击“上线转正”。

Webhooks 不属于该链路，不配置。上线转正属于外部状态变更，执行前单独取得用户确认。

## 8. 错误处理与安全边界

- 未安装抖音或版本不支持时只展示明确提示，不回退到伪造卡片或自动复制并声称成功。
- 分享链接未通过审核时阻止 release 验收，不使用另一域名或多跳绕过。
- Android 不解析审核页内容，也不接受运行时替换 URL。
- 固定审核页不可因会员状态、链接记录或 UV 限额失效。
- 审核页不包含微信、二维码、社交账号、联系方式、下载、外部按钮、自动跳转或第三方脚本。
- 日志不得包含 Client Secret、keystore 密码、完整 token 或用户私信内容。
- 分享行为必须由用户点击触发；真机最终发送由用户在抖音界面确认。

## 9. 测试与验收

### 9.1 自动化验证

- Gradle 依赖解析与 `assembleRelease`。
- Kotlin/JVM 单元测试：固定 URL 校验、state 生成、SDK 错误码映射。
- Android 单元或 instrumentation 测试：隐私同意前不初始化、分享按钮状态、无抖音/不支持分支。
- Manifest 检查：包可见性、导出回调 Activity、无多余危险权限。
- APK 检查：包名、版本、签名、无 Client Secret、无 keystore 文件。
- 服务端相关测试：审核页直接返回 200、安全响应头完整、内容真实，并对禁止出现的引流词、二维码、外链和重定向做断言。

### 9.2 真机验收

在 `NX789J` 上使用正式签名 APK：

1. 安装或升级极风小助手。
2. 首次启动确认隐私提示。
3. 卡片预览显示正确标题、描述和封面。
4. 点击“分享到抖音好友/群”。
5. 抖音打开联系人/群选择页。
6. 用户手动选择一个测试联系人或测试群并发送。
7. 接收方看到链接卡片。
8. 点击卡片后直接进入审核 URL，页面不发生重定向。
9. 页面展示极风小助手真实功能和运营主体，且不出现微信、二维码、联系方式、下载或外部链接；重复打开仍有效。
10. 返回极风小助手后显示与 SDK 回调一致的状态。

只有第 1–10 步全部有证据，才可宣称“抖音私信卡片核心功能可用”。

## 10. 复用与许可证

采用 ByteDance 官方 `bytedance/DouyinOpenPlatformDemo` 的最小接入模式，参考固定 commit `1a6cac5525a13b694af45cb8d30ba4b31e59511a`。官方 Demo 使用 Apache-2.0，可在保留必要版权和许可证说明的前提下借鉴初始化、`ShareToContact` 和回调结构。

不采用：

- `tiktok/tiktok-opensdk-android`：面向国际 TikTok 投稿分享，不覆盖中国抖音 `im.share` 联系人分享。
- `bytedance/douyin-im-group-open-capabilities`：服务端群消息内测能力，不是用户主动选择好友/群的 Android 分享 SDK。
- 非官方博客、反编译 SDK、Intent 猜测或绕过平台验证的代码。

## 11. 官方依据

- 抖音分享至私信/群能力：<https://developer.open-douyin.com/docs/resource/zh-CN/dop/ability/opensdk/share/video-create-bind>
- Android Open SDK 接入：<https://developer.open-douyin.com/docs/resource/zh-CN/dop/develop/sdk/mobile-app/access/android>
- Android SDK 下载与版本：<https://developer.open-douyin.com/docs/resource/zh-CN/dop/develop/sdk/mobile-app/sdk-download/android/>
- ByteDance 官方 Demo：<https://github.com/bytedance/DouyinOpenPlatformDemo>
- Android Gradle Plugin 兼容性：<https://developer.android.com/build/releases/about-agp>

## 12. 实施分支与提交边界

书面规格保存在当前稳定化分支。实施阶段从已包含 H5 微信二维码链路的 `codex/card-jump-only@6ca16b6` 创建 `codex/douyin-android-share`，再带入本规格；不直接在 `main` 开发。

实施提交按可验证边界拆分：

1. Android 工程与可重复构建基线。
2. 抖音 SDK 初始化、固定卡片和回调。
3. 正式签名配置模板与安全检查。
4. 服务端固定验收链接和相关测试。
5. 控制台配置证据与真机验收记录。

每个提交只包含本任务文件；keystore、密码、Client Secret、APK 和本机配置不进入 Git。
