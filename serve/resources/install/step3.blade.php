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
               <!-- 数据库设置 -->
                <div class="mounted-content-item show">
                    <div class="mounted-item">
                        <div class="content-header">
                            数据库选项
                        </div>
                        <div class="content-form">

                            <div class="form-box-item">
                                <div class="form-desc">
                                    数据库主机
                                </div>
                                <div>
                                    <input type="text" name="host" value="{!! $post['host'] !!}"/>
                                </div>
                            </div>
                            <div class="form-box-item">
                                <div class="form-desc">
                                    端口号
                                </div>
                                <div>
                                    <input type="text" name="port" value="{!! $post['port'] !!}"/>
                                </div>
                            </div>
                            <div class="form-box-item">
                                <div class="form-desc">
                                    数据库用户
                                </div>
                                <div>
                                    <input type="text" name="user" value="{!! $post['user'] !!}"/>
                                </div>
                            </div>
                            <div class="form-box-item">
                                <div class="form-desc">
                                    数据库密码
                                </div>
                                <div>
                                    <input type="text" name="password" value="{!! $post['password'] !!}"/>
                                </div>
                            </div>
                            <div class="form-box-item">
                                <div class="form-desc">
                                    数据库名称
                                </div>
                                <div>
                                    <input type="text" name="name" value="{!! $post['name'] !!}"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mounted-item">
                        <div class="content-header mt16">
                            管理选项
                        </div>
                        <div class="content-form">

                            <div class="form-box-item">
                                <div class="form-desc">
                                    管理员账号
                                </div>
                                <div>
                                    <input type="text" name="admin_user" value="{!! $post['admin_user'] !!}"/>
                                </div>
                            </div>
                            <div class="form-box-item">
                                <div class="form-desc">
                                    管理员密码
                                </div>
                                <div>
                                    <input type="password" name="admin_password" value="{!! $post['admin_password'] !!}"/>
                                </div>
                            </div>
                            <div class="form-box-item">
                                <div class="form-desc">
                                    确认密码
                                </div>
                                <div>
                                    <input type="password" name="admin_password_confirmation" value="{!! $post['admin_confirm_password'] !!}"/>
                                </div>
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
