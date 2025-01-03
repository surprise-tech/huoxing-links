<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>火星快链安装</title>
    <link rel="stylesheet" type="text/css" href="https://www.layuicdn.com/layui/css/layui.css"/>
    <link rel="stylesheet" type="text/css" href="/css/install.css"/>
    <link rel="shortcut icon" href="favicon.ico"/>
</head>
<body>
<div class="mounted" id="mounted">
    <div class="mounted-box">
        <form method="post" action="">
            <div class="mounted-title">安装步骤</div>
            <div class="mounted-container" id="tab">
                <ul class="mounted-nav" id="nav">
                    <li @if($step == "1") class="active" @endif>许可协议</li>
                    <li @if($step == "2") class="active" @endif>环境监测</li>
                    <li @if($step == "3") class="active" @endif>参数配置</li>
                    <li @if($step >= "4") class="active" @endif>安装</li>
                </ul>

                <!-- 安装中 -->
                <div class="mounted-content-item show">
                    <div id="mounting">
                        <div class="content-header">
                            正在安装中
                        </div>
                    </div>

                    <div class="show" id="mounting-success">
                        <div class="content-header">
                            安装成功
                        </div>
                        <div class="success-content">
                            <div class="mt16 result">安装完成，进入管理后台</div>
                            <div class="tips">
                                为了您站点的安全，安装完成后即可将网站根目录下的“install”文件夹删除，或者config/install.lock/目录下创建install.lock文件防止重复安装。
                            </div>
                            <div class="btn-group">
                                <a class="btn" href="/" style="margin-left: 20px;">进入首页</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<footer>
    Copyright © 2019-<?=date('Y')?>
</footer>
<script src="https://www.layuicdn.com/layui/layui.js"></script>
</body>
</html>
