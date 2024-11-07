<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <script>
        // window.location = '/web/#/login'
    </script>
    <title>火星快链-智慧引流工具</title>
    <link rel="stylesheet" href="/css/css.css">
    <link rel="stylesheet" href="/css/boot.css">
    <style>
        html, body {
            height: 100%;
            margin: 0;
        }
    </style>
</head>
<body>

<div class="app" id="home">
    <!--    导航-->
    <div class="sticky-top bg-white  pl-2 pr-2">
        <div class="main ">
            <div class=" navigation bg-white  d-flex justify-content-between">
                <div class="d-flex flex-row align-items-center" style="position: relative">
                    <img src="/image/toplogo.png"/>
                    <div class="d-flex flex-row hide-below-1000">
                        <div class="navigation-item home" onclick="scrollAndHighlight('home')">首页</div>
                        <div class="navigation-item features" onclick="scrollAndHighlight('features')">产品功能</div>
                        <div class="navigation-item pricing" onclick="scrollAndHighlight('pricing')">服务定价</div>
                        {{--<a class="navigation-item text" href="https://alidocs.dingtalk.com/i/p/preview/l2Amo47OL4bgEGdb"
                           _target="_blank">帮助文档</a>--}}
                    </div>
                    <div id="highlight" class="hide-below-1000" style="position: absolute"></div>
                </div>
                <div class="d-flex flex-row">
                    <div>
                        <a class="btn btn-outline-primary btn-sm" href="/web/#/register" role="button">免费注册</a>
                    </div>
                    <div class="ml-2">
                        <a class="btn btn-outline-primary btn-sm" href="/web/#/login" role="button">立即登录</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--    banner图-->
    <div class="banner d-flex   align-items-center">
        <div class="bannerCard mainCard ">
            <div class="font-weight-bold title">
                <span>链接一键添加好友</span>
                <span class="text-primary">·全平台私域获客工具</span>
            </div>
            <div class="mt-3 text-secondary desc—text">
                支持抖音，快手，知乎等一键跳转到微信、微信群、公众号、小程序
            </div>
            <div class="mt-2 text-secondary desc—text">
                支持广告落地页添加好友同步回传
            </div>
            <div class="mt-3 d-flex ">
                <div>
                    <button class="btn btn-primary btn-sm pl-4 pr-4">方案演示</button>
                </div>
                <div class="ml-2">
                    <button class="btn btn-outline-primary btn-sm">立即免费创建</button>
                </div>
            </div>
        </div>
    </div>
    <!--    主要功能-->
    <div class="feature bg-white pt-5 pb-5" id="features">
        <div class="mainCard p-4">
            <div class="text-center">
                <div class="font-weight-bold font-28"> 主要功能</div>
                <div class="text-secondary font-14 mt-2">兼容大部分手机APP，一键跳转微信/QQ，实现超高流量转化</div>
            </div>

            <div class=" mt-5">
                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-2 row-cols-lg-4 no-gutters">
                    <div class="itemCard col">
                        <img src="/image/gongzhonghao.png">
                        <div class="font-18 mt-2">跳转到公众号</div>
                        <div class="font-16 text-secondary mt-2">实现跳转关注公众号</div>
                        <button class="btn btn-link mt-2">免费创建</button>
                    </div>
                    <div class="itemCard col">
                        <img src="/image/weixin.png">
                        <div class="font-18 mt-2">跳转到个人微信</div>
                        <div class="font-16 text-secondary mt-2">实现跳转添加个人微信</div>
                        <button class="btn btn-link mt-2">免费创建</button>
                    </div>
                    <div class="itemCard col">
                        <img src="/image/weixinqun.png">
                        <div class="font-18 mt-2">跳转到微信群</div>
                        <div class="font-16 text-secondary mt-2">实现跳转加入微信群</div>
                        <button class="btn btn-link mt-2">免费创建</button>
                    </div>
                    <div class="itemCard col">
                        <img src="/image/xiaochengxu.png">
                        <div class="font-18 mt-2">跳转到小程序</div>
                        <div class="font-16 text-secondary mt-2">实现跳转打开小程序</div>
                        <button class="btn btn-link mt-2">免费创建</button>
                    </div>
                </div>
            </div>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-2 row-cols-lg-4">
                <div class="itemCard col">
                    <img src="/image/qiyeweixin.png">
                    <div class="font-18 mt-2">跳转到企业微信</div>
                    <div class="font-16 text-secondary mt-2">实现跳转添加企业微信</div>
                    <button class="btn btn-link mt-2">免费创建</button>
                </div>
                <div class="itemCard col">
                    <img src="/image/jianshan.png">
                    <div class="font-18 mt-2">跳转到金山文档</div>
                    <div class="font-16 text-secondary mt-2">实现跳转到金山文档</div>
                    <button class="btn btn-link mt-2">免费创建</button>
                </div>
                <div class="itemCard col">
                    <img src="/image/caoliao.png">
                    <div class="font-18 mt-2">跳转到草料二维码</div>
                    <div class="font-16 text-secondary mt-2">实现跳转到草料二维码</div>
                    <button class="btn btn-link mt-2">免费创建</button>
                </div>
                <div class="itemCard col" style="box-shadow:none;display: none">

                </div>
            </div>

        </div>
    </div>
    <!--    服务定价-->
    <div class="price pt-5 pb-5" id="pricing">
        <div style="min-width:1000px ">
            <div class="mainCard ">
                <div class="text-center">
                    <div class="font-weight-bold font-28"> 服务定价</div>
                    <div class="text-secondary font-14 mt-2">
                        不同版本的服务定价通常基于所提供的服务范围、功能、质量以及目标市场的不同需求来确定
                    </div>
                </div>
                <div class="mt-4 d-flex flex-row">
                    <div class=" bg-white head    flex-1" style="line-height: 190px;">
                        <div class="font-weight-bold font-20 pl-4">权益/版本</div>
                    </div>
                    <div class="head basicsCard flex-1">
                        <div class="pt-2 pl-3">
                            <div class="font-weight-bold font-20">基础版</div>
                            <div class="font-14 text-secondary mt-2">基础功能版本，具备平台所有功能链
                                <br>
                                接创建，适用于私域加粉用户
                            </div>
                            <div class="font-weight-bold mt-4">
                                <span class="font-28">68</span><span class="font-20">/元/月</span>
                            </div>
                        </div>
                    </div>
                    <div class="head advanceCard flex-1">
                        <div class="pt-2 pl-3">
                            <div class="font-weight-bold font-20">高级版</div>
                            <div class="font-14 text-secondary mt-2">在基础版上具备数据回传、小程序检<br>测等功能，适用投注推广用户
                            </div>
                            <div class="font-weight-bold mt-4">
                                <span class="font-28">298</span><span class="font-20">/元/月</span>
                            </div>
                        </div>
                    </div>
                    <div class="head enterpriseCard flex-1">
                        <div class="pt-2 pl-3">
                            <div class="font-weight-bold font-20">专业版</div>
                            <div class="font-14 text-secondary mt-2">支持所有功能使用，用于有开发能力<br>的用户使用</div>
                            <div class="font-weight-bold mt-4">
                                <span class="font-28">598</span><span class="font-20">/元/月</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-row">
                    <div class=" bg-white head  flex-1">
                        <div class="td border-top bg-white pl-4 font-14">
                            每月访问数量（UV）上限
                        </div>
                        <div class="td border-top bg-white pl-4 font-14">
                            链接创建数量上限（同时）
                        </div>
                        <div class="td border-top bg-white pl-4 font-14">
                            自建小程序池可创建数量
                        </div>
                        <div class="td border-top bg-white pl-4 font-14">
                            链接保留时长
                        </div>
                        <div class="td border-top bg-white pl-4 font-14">
                            可创建链接的类型
                        </div>
                        <div class="td border-top bg-white pl-4 font-14">
                            官方小程序池使用
                        </div>
                        <div class="td border-top bg-white pl-4 font-14">
                            数据统计/分析
                        </div>
                        <div class="td border-top bg-white pl-4 font-14">
                            自定义落地页
                        </div>

                        <div class="td border-top bg-white pl-4 font-14">
                            自建小程序池下架自动检测
                        </div>
                        <div class="td border-top bg-white pl-4 font-14">
                            免费技术协助
                        </div>
                    </div>
                    <div class=" flex-1">
                        <div class="td border-top basics pl-4 font-14 justify-content-center">
                            3万
                        </div>
                        <div class="td border-top basics pl-4 font-14 justify-content-center">
                            10条
                        </div>
                        <div class="td border-top basics pl-4 font-14 justify-content-center">
                            3个
                        </div>
                        <div class="td border-top basics pl-4 font-14 justify-content-center">
                            永久
                        </div>
                        <div class="td border-top basics pl-4 font-14 justify-content-center">
                            支持
                        </div>
                        <div class="td border-top basics pl-4 font-14 justify-content-center">
                            支持
                        </div>
                        <div class="td border-top basics pl-4 font-14 justify-content-center">
                            支持
                        </div>

                        <div class="td border-top basics pl-4 font-14 justify-content-center">
                            支持
                        </div>
                        <div class="td border-top basics pl-4 font-14 justify-content-center text-danger">
                            不支持
                        </div>
                        <div class="td border-top basics pl-4 font-14 justify-content-center text-danger">
                            不支持
                        </div>
                    </div>
                    <div class="   flex-1">
                        <div class="td border-top advance pl-4 font-14 justify-content-center">
                            10万
                        </div>
                        <div class="td border-top advance pl-4 font-14 justify-content-center">
                            50条
                        </div>
                        <div class="td border-top advance pl-4 font-14 justify-content-center">
                            10个
                        </div>
                        <div class="td border-top advance pl-4 font-14 justify-content-center">
                            永久
                        </div>
                        <div class="td border-top advance pl-4 font-14 justify-content-center">
                            支持
                        </div>
                        <div class="td border-top advance pl-4 font-14 justify-content-center">
                            支持
                        </div>
                        <div class="td border-top advance pl-4 font-14 justify-content-center">
                            支持
                        </div>
                        <div class="td border-top advance pl-4 font-14 justify-content-center ">
                            支持
                        </div>
                        <div class="td border-top advance pl-4 font-14 justify-content-center ">
                            支持
                        </div>
                        <div class="td border-top advance pl-4 font-14 justify-content-center text-danger">
                            不支持
                        </div>
                    </div>
                    <div class="   flex-1">
                        <div class="td border-top enterprise pl-4 font-14 justify-content-center">
                            30万
                        </div>
                        <div class="td border-top enterprise pl-4 font-14 justify-content-center">
                            100条
                        </div>
                        <div class="td border-top enterprise pl-4 font-14 justify-content-center">
                            50个
                        </div>
                        <div class="td border-top enterprise pl-4 font-14 justify-content-center">
                            永久
                        </div>
                        <div class="td border-top enterprise pl-4 font-14 justify-content-center">
                            支持
                        </div>
                        <div class="td border-top enterprise pl-4 font-14 justify-content-center">
                            支持
                        </div>
                        <div class="td border-top enterprise pl-4 font-14 justify-content-center">
                            支持
                        </div>
                        <div class="td border-top enterprise pl-4 font-14 justify-content-center">
                            支持
                        </div>
                        <div class="td border-top enterprise pl-4 font-14 justify-content-center">
                            支持
                        </div>
                        <div class="td border-top enterprise pl-4 font-14 justify-content-center">
                            支持
                        </div>

                    </div>
                </div>

            </div>
        </div>

    </div>
    <!--    评价-->
    <div class="evaluate pt-5 pb-5 ">
        <div class="mainCard">
            <div class="text-center p-2">
                <div class="font-weight-bold font-28"> 主要功能</div>
                <div class="text-secondary font-14 mt-2">兼容大部分手机APP，一键跳转微信/QQ，实现超高流量转化</div>
            </div>
        </div>
        <div class="evaluateCard p-1">
            <div class="d-flex  mt-4" style="flex-wrap: nowrap;">
                <div class="itemCard p-3">
                    <div class="d-flex justify-content-between ">
                        <div class="userInfo d-flex">
                            <img src="/image/photo/lin.png">
                            <div class="d-flex flex-column ml-3">
                                <div class="font-weight-bold font-16">林女士</div>
                                <div class="text-secondary font-14 mt-1 ">运营主管</div>
                            </div>

                        </div>
                        <div class="d-flex mt-2">
                            <div class="star"></div>
                            <div class="star"></div>
                            <div class="star"></div>
                            <div class="star"></div>
                            <div class="star"></div>
                        </div>
                    </div>
                    <div class="mt-3 font-14">短信链接直连微信，加好友转化翻倍，好产品不容错过！
                    </div>
                </div>
                <div class="itemCard p-3">
                    <div class="d-flex justify-content-between ">
                        <div class="userInfo d-flex">
                            <img src="/image/photo/li.png">
                            <div class="d-flex flex-column ml-3">
                                <div class="font-weight-bold font-16">李先生</div>
                                <div class="text-secondary font-14 mt-1 ">自由工作者</div>
                            </div>

                        </div>
                        <div class="d-flex mt-2">
                            <div class="star"></div>
                            <div class="star"></div>
                            <div class="star"></div>
                            <div class="star"></div>
                            <div class="star"></div>
                        </div>
                    </div>
                    <div class="mt-3 font-14">微信营销新利器，四款外链方案轻松上手，技术小白也能变大神！

                    </div>
                </div>
                <div class="itemCard p-3">
                    <div class="d-flex justify-content-between ">
                        <div class="userInfo d-flex">
                            <img src="/image/photo/meng.png">
                            <div class="d-flex flex-column ml-3">
                                <div class="font-weight-bold font-16">孟女士</div>
                                <div class="text-secondary font-14 mt-1 ">产品运营</div>
                            </div>

                        </div>
                        <div class="d-flex mt-2">
                            <div class="star"></div>
                            <div class="star"></div>
                            <div class="star"></div>
                            <div class="star"></div>
                            <div class="star"></div>
                        </div>
                    </div>
                    <div class="mt-3 font-14">在抖音私信发给粉丝卡片，一键跳转到微信添加好友，私域流量大大提升！

                    </div>
                </div>
                <div class="itemCard p-3">
                    <div class="d-flex justify-content-between ">
                        <div class="userInfo d-flex">
                            <img src="/image/photo/zhang.png">
                            <div class="d-flex flex-column ml-3">
                                <div class="font-weight-bold font-16">张先生</div>
                                <div class="text-secondary font-14 mt-1 ">内容创作者</div>
                            </div>

                        </div>
                        <div class="d-flex mt-2">
                            <div class="star"></div>
                            <div class="star"></div>
                            <div class="star"></div>
                            <div class="star"></div>
                            <div class="star"></div>
                        </div>
                    </div>
                    <div class="mt-3 font-14">对在外部公众号文章传播有很大的帮助，公众号关注人数提高了很多，阅读量也多了起来！
                    </div>
                </div>
                <div class="itemCard p-3">
                    <div class="d-flex justify-content-between ">
                        <div class="userInfo d-flex">
                            <img src="/image/photo/li2.png">
                            <div class="d-flex flex-column ml-3">
                                <div class="font-weight-bold font-16">李女士</div>
                                <div class="text-secondary font-14 mt-1 ">教培销售</div>
                            </div>

                        </div>
                        <div class="d-flex mt-2">
                            <div class="star"></div>
                            <div class="star"></div>
                            <div class="star"></div>
                            <div class="star"></div>
                            <div class="star"></div>
                        </div>
                    </div>
                    <div class="mt-3 font-14">在搜索广告中使用外链，转化率提高很多，业绩直线上升

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="yanshi d-flex align-items-center justify-content-center flex-column">
        <div class="font-weight-bold yanshiTitle">全平台跨域跳转微信工具，打造流量闭环</div>
        <div class="mt-3 d-flex ">
            <div>
                <button class="btn btn-primary btn-sm pl-4 pr-4">方案演示</button>
            </div>
            <div class="ml-2">
                <button class="btn btn-outline-primary btn-sm">立即免费创建</button>
            </div>
        </div>
    </div>
    <!--    页脚-->

    <div class="footer hide-below-1000">
        <div class="mainCard d-flex justify-content-between p-4">
            <div>
                <img class="logo" src="/image/logo.png">
            </div>
            <div class="d-flex justify-content-between flex-1">
                <div class="line" style="margin-left: 100px"></div>
                <div>
                    <div class="font-20 text-white">快捷入口</div>
                    <div class="mt-2 text-secondary font-14 cursor" onclick="scrollAndHighlight('home')">首页</div>
                    <div class="mt-2 text-secondary font-14 cursor" onclick="scrollAndHighlight('features')">产品功能</div>
                    <div class="mt-2 mb-2 text-secondary font-14 cursor" onclick="scrollAndHighlight('pricing')">服务定价</div>
                    {{--<a class="text-secondary font-14 text"
                       href="https://alidocs.dingtalk.com/i/p/preview/l2Amo47OL4bgEGdb">帮助文档</a>--}}
                </div>
                <div style="margin-left: 40px">
                    <div class="font-20 text-white">联系我们</div>
                    <div class="mt-2 text-secondary font-14">公司地址：××××××××××</div>
                </div>
                <div class="line" style="margin-right: 100px"></div>
            </div>
            <div>
                <img class="weixin" src="/image/web-qr.png">
            </div>
        </div>
        <div class="xlin"></div>
        <div class="text-center text-secondary font-14 mt-3">Copyright © ××××××××××有限公司</div>
    </div>
    <div class="show-below-1000 phoneFooter" style="display: none">
        <div class="row row-cols-sm-2 p-4">
            <div>
                <img class="weixin" src="/image/web-qr.png">
                <div class="mt-3">
                    <div class="font-20 text-white">联系我们</div>
                    <div class="mt-2 text-secondary font-14">公司地址：××××××××××</div>
                </div>
            </div>

        </div>
    </div>
    <script>
        window.onload = function () {
            if (window.innerWidth >= 1000) {
                scrollAndHighlight('home')
            }
        }

        function scrollAndHighlight(anchorId) {
            // 获取锚点元素
            var anchor = document.getElementById(anchorId);

            // 将窗口滚动到锚点元素位置
            anchor.scrollIntoView({behavior: 'smooth', block: 'start'});

            // 获取导航栏项的宽度和偏移量
            var element = document.querySelector('.' + anchorId);
            var left = element.offsetLeft;

            // 设置高亮线条的宽度和偏移量
            var highlight = document.getElementById('highlight');
            highlight.style.width = 40 + 'px';
            highlight.style.height = 4 + 'px';
            highlight.style.left = left + 20 + 'px';
            highlight.style.bottom = '2px';
            // highlight.style.transform = 'translateX(' + left + 'px)';
            highlight.style.background = '#316AFF'
        }
    </script>
</div>
</body>

</html>
