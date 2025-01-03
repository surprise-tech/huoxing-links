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
        <form method="post" action="?step=2">
            <div class="mounted-title">安装步骤</div>
            <div class="mounted-container" id="tab">
                <ul class="mounted-nav" id="nav">
                    <li @if($step == "1") class="active" @endif>许可协议</li>
                    <li @if($step == "2") class="active" @endif>环境监测</li>
                    <li @if($step == "3") class="active" @endif>参数配置</li>
                    <li @if($step >= "4") class="active" @endif>安装</li>
                </ul>
                <!-- 阅读许可 -->
                <div class="mounted-content-item show">
                    <div class="content-header">
                        开源系统授权协议
                    </div>
                    <div class="content">
                        <h2>版权所有(c)2019-<?=date('Y')?>，火星快链团队保留所有权利。</h2>
                        <p class="mt16">
                        <p class="mt6">为了正确并合法的使用本软件，请你在使用前务必阅读清楚下面的协议条款：</p>
                        <h3 class="mt16">一、本授权协议适用且仅适用于火星快链开源软件产品任何版本。</h3>
                        <h3 class="mt16">二、协议许可的权利</h3>
                        <p class="mt6">
                            1．免费版本可免费商用，不能私自去除官方版权。付费版本需要取得商业授权方可使用，可以去除官方版权，否则会被视为盗版行为并承担相应法律责任。
                        </p>
                        <p class="mt6">
                            2．请尊重火星快链团队劳动成果，严禁使用本系统转手、转卖、倒卖或二次开发后转手、转卖、倒卖等商业行为。
                        </p>
                        <p class="mt6">
                            3．任何企业和个人不允许对程序代码以任何形式任何目的再发布。
                        </p>
                        <p class="mt6">
                            4．你可以在协议规定的约束和限制范围内修改系列开源软件产品或界面风格以适应你的网站要求。
                        </p>
                        <p class="mt6">
                            5．你拥有使用本软件构建的系统全部内容所有权，并独立承担与这些内容的相关法律义务。
                        </p>
                        <p class="mt6">
                            6．获得商业授权之后，你可以将本软件应用于商业用途，同时依据所购买的授权类型中确定的技术支持内容，自购买时刻起，在技术支持期限内拥有通过指定的方式获得指定范围内的技术支持服务。商业授权用户享有反映和提出意见的权力，相关意见将被作为首要考虑，但没有一定被采纳的承诺或保证。
                        </p>
                        <h3 class="mt6">三、协议规定的约束和限制</h3>
                        <p class="mt6">
                            1．免费版本可免费商用，不能私自去除官方版权。付费版本需要取得商业授权方可使用，可以去除官方版权，否则会被视为盗版行为并承担相应法律责任。
                        </p>
                        <p class="mt6">
                            2．未经许可，不得对本软件或与之关联的商业授权进行出租、出售、抵押或发放子许可证。
                        </p>
                        <p class="mt6">
                            3．未经许可，禁止在开源系统的整体或任何部分基础上以发展任何派生版本、修改版本或第三方版本用于重新分发。
                        </p>
                        <p class="mt6">
                            4．如果你未能遵守本协议的条款，你的授权将被终止，所被许可的权利将被收回，并承担相应法律责任。
                        </p>
                        <h3 class="mt6">四、有限担保和免责声明</h3>
                        <p class="mt6">
                            1．本软件及所附带的文件是作为不提供任何明确的或隐含的赔偿或担保的形式提供的。
                        </p>
                        <p class="mt6">
                            2．用户出于自愿而使用本软件，你必须了解使用本软件的风险，在尚未购买产品技术服务之前，我们不承诺对免费用户提供任何形式的技术支持、使用担保，也不承担任何因使用本软件而产生问题的相关责任。
                        </p>
                        <p class="mt6">
                            3．电子文本形式的授权协议如同双方书面签署的协议一样，具有完全的和等同的法律效力。你一旦开始确认本协议并安装本软件，即被视为完全理解并接受本协议的各项条款，在享有上述条款授予的权力的同时，受到相关的约束和限制。协议许可范围以外的行为，将直接违反本授权协议并构成侵权，我们有权随时终止授权，责令停止损害，并保留追究相关责任的权力。
                        </p>
                        <p class="mt6">
                            4．如果本软件带有其它软件的整合API示范例子包，这些文件版权不属于本软件官方，并且这些文件是没经过授权发布的，请参考相关软件的使用许可合法的使用。
                        </p>
                        <br><br>
                        <p class="mt6">
                            -----------------------------------------------------
                        </p>
                    </div>
                </div>
            </div>
            <div class="form-btn">
                <div class="item-btn-group show">
                    <button class="accept-btn">我已阅读并同意</button>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
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
