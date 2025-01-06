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
        @if ($errors->any())
            <div style="color: red;">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form method="post" action="?step={!! $nextStep !!}" name="main_form">
            <div class="mounted-title">安装步骤</div>
            <div class="mounted-container" id="tab">
                <ul class="mounted-nav" id="nav">
                    <li @if($step == "1") class="active" @endif>许可协议</li>
                    <li @if($step == "2") class="active" @endif>环境监测</li>
                    <li @if($step == "3") class="active" @endif>参数配置</li>
                    <li @if($step >= "4") class="active" @endif>安装</li>
                </ul>

               <!-- 检查信息 -->
                <div class="mounted-content-item show">
                    <div class="mounted-env-container">
                        <div class="mounted-item">
                            <div class="content-header">
                                服务器信息
                            </div>
                            <div class="content-table">
                                <table class="layui-table" lay-skin="line">
                                    <colgroup>
                                        <col width="210">
                                        <col width="730">
                                    </colgroup>
                                    <thead>
                                    <tr>
                                        <th>参数</th>
                                        <th>值</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>服务器操作系统</td>
                                        <td>{!! PHP_OS !!}</td>
                                    </tr>
                                    <tr>
                                        <td>web服务器环境</td>
                                        <td>{!! $_SERVER['SERVER_SOFTWARE'] !!}</td>
                                    </tr>
                                    <tr>
                                        <td>PHP版本</td>
                                        <td>{!! phpversion() !!}</td>
                                    </tr>
                                    <tr>
                                        <td>程序安装目录</td>
                                        <td>{!! base_path() !!}</td>
                                    </tr>
                                    <tr>
                                        <td>磁盘空间</td>
                                        <td>{!! $chk['fred_disk_space'] !!}</td>
                                    </tr>
                                    <tr>
                                        <td>上传限制</td>
                                        <td>
                                            @if(ini_get('file_uploads'))
                                                {!! ini_get('upload_max_filesize') !!}
                                            @else
                                                禁止上传
                                            @endif
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="mounted-tips mt16">PHP环境要求必须满足下列所有条件，否则系统或系统部分功能将无法使用。</div>
                        <div class="mounted-item mt16">
                            <div class="content-header">
                                PHP环境要求
                            </div>
                            <div class="content-table">
                                <table class="layui-table" lay-skin="line">
                                    <colgroup>
                                        <col width="210">
                                        <col width="210">
                                        <col width="400">
                                    </colgroup>
                                    <thead>
                                    <tr>
                                        <th>选项</th>
                                        <th>要求</th>
                                        <th>状态</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>PHP版本</td>
                                        <td>大于7.2</td>
                                        <td>{!! $chk['php_version'] !!}</td>
                                    </tr>
                                    <tr>
                                        <td>PDO_MYSQL</td>
                                        <td>支持</td>
                                        <td>{!! $chk['pdo_mysql'] !!}</td>
                                    </tr>
                                    <tr>
                                        <td>allow_url_fopen</td>
                                        <td>支持 (建议支持cURL)</td>
                                        <td>{!! $chk['curl'] !!}</td>
                                    </tr>
                                    <tr>
                                        <td>GD2</td>
                                        <td>支持</td>
                                        <td>{!! $chk['gd2'] !!}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="mounted-tips mt16">
                            系统要求安装目录下的runtime和upload必须可写，才能使用系统的所有功能。
                        </div>
                        <div class="mounted-item mt16">
                            <div class="content-header">
                                目录权限监测
                            </div>
                            <div class="content-table">
                                <table class="layui-table" lay-skin="line">
                                    <colgroup>
                                        <col width="210">
                                        <col width="210">
                                        <col width="400">
                                    </colgroup>
                                    <thead>
                                    <tr>
                                        <th>目录</th>
                                        <th>要求</th>
                                        <th>状态</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>/server/storage/logs</td>
                                        <td>logs目录可写</td>
                                        <td>{!! $chk['log'] !!}</td>
                                    </tr>
                                    <tr>
                                        <td>/server/storage/app/public</td>
                                        <td>uploads目录可写</td>
                                        <td>{!! $chk['upload'] !!}</td>
                                    </tr>
                                    <tr>
                                        <td>/server/storage/framework</td>
                                        <td>cache目录可写</td>
                                        <td>{!! $chk['cache'] !!}</td>
                                    </tr>
                                    <tr>
                                        <td>/server/.env</td>
                                        <td>.env文件可写</td>
                                        <td>{!! $chk['env'] !!}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-btn">
                <div class="item-btn-group show">
                    <a  href="?step={!! $lastStep !!}" class="cancel-btn" style="padding: 7px 63px;margin-right: 16px">返回</a>

                    <button class="accept-btn @if(!$is_goon) action @endif" style="padding: 7px 63px;">
                        继续
                    </button>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                    &nbsp;
                    <a href="?step={!! $step !!}" class="accept-btn" style="padding: 7px 63px;">重新检查
                    </a>
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
