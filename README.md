## 火星快链


> 火星快链全开源，项目基于laravel10，前端采用TypeScript，可进行二次开发，基础版永久免费使用，基础版包含链接功能及分销。如需其他应用，请联系我们购买商业授权，开源不易，请大家珍惜我们劳动成果。
> 
> 作为一款私域引流工具，凭借其强大的功能和灵活的应用场景，成为了推广营销领域的热门选择，它支持从抖音直接跳转到个人微信、微信群、小程序以及企业微信客服，有效打通了不同平台之间的壁垒，实现了流量的高效转化和利用，推广营销必备！


![image](https://github.com/user-attachments/assets/76f93840-4453-4aae-ac50-7b01a4010152)



### 系统演示
```
演示地址：https://open.mayilink.cn/
管理员账号：admin
管理员密码：admin123
用户账号：13999999999
用户密码：123456
```


### 目录结构
```
- admin  后台管理
- serve  api接口
- jump   跳转页面
- mini-programs 小程序
```


### 安装说明

- 配置环境（推荐nginx、php8.1、pnpm、mysql）




### 1.Api serve目录下复制 .env.example .env
### 2.配置nginx 
```angular2html
# 后台地址
location /web {
    alias /home/link/huoxing-links/admin/dist;
    index index.html;
}
#api配置
location / {
    try_files $uri $uri/ /index.php?$query_string;
    client_max_body_size 50m;
}

```

### 3.后台 admin/dist 目录下config.js 修改
```
url: 'https://您的域名/api',
```

### 4.跳转文件 jump目录下a_dev.html修改
```
var host = "你的域名(不加机构)"
var domain = "机构"
```

### 5.小程序文件 mini_programs/utils/request.js
```
let baseUrl = "https://xxx.xxxxx.xxx"; // 后台请求域名改为你的域名
```
### 6.访问你的域名，填写数据库连接账号完成安装

## 重要信息

欢迎进群获取最新开发计划或交流技术

如需进群请添加微信号

微信号：tok4mi

添加请备注：交流群

项目合作洽谈，请联系客服微信（使用微信扫码添加好友，请注明来意）。

<img src="https://huoxing.tos-cn-shanghai.volces.com/2024/11/05/HFiFy0AaqSLxmlFb0RScBKgLtGVzC9xXb7M9HCJ5.jpg" width="400">
