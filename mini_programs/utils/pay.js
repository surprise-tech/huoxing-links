/**
 * @description 微信公众号支付准备
 * @data 微信支付请求成功之后返回的支付参数
 * @payParams {object} 微信支付需要的参数
 * */
function onBridgeReady(data, payParams) {
  // alert('onBridgeReady:==========')
  window.WeixinJSBridge.invoke('getBrandWCPayRequest', {
    appId: data.appId,
    // 公众号名称，由商户传入
    timeStamp: data.timeStamp,
    // 时间戳，自1970年以来的秒数
    nonceStr: data.nonceStr,
    // 随机串
    package: data.package,
    signType: data.signType,
    // 微信签名方式：
    paySign: data.paySign // 微信签名

  }, function (res) {
    console.log(res); // 使用以上方式判断前端返回,微信团队郑重提示：res.err_msg将在用户支付成功后返回    ok，但并不保证它绝对可靠。

    if (res.err_msg === 'get_brand_wcpay_request:ok') {
      // 支付成功页面
      if (payParams.type === 'SHYY') {
        toastMsg('correct', '支付成功');
        setTimeout(() => {
          router.push('/home');
        }, 500);
      } else {
        // console.log('-----测试----支付成功----')
        router.push({
          path: '/success',
          query: {
            orderId: payParams.orderId,
            totalFee: payParams.totalFee
          }
        });
      }
    } else if (res.err_msg === 'get_brand_wcpay_request:fail') {
      return alert(res.err_desc);
    } else {
      // 跳转订单详情
      toastMsg('warn', '支付取消');
    }
  });
} 

/**
 * 支付宝支付
 */
function alipay(res) {
	console.log('调起支付alipay',res );
	uni.requestPayment({
		provider: 'alipay',
		orderInfo: {
			"dealId": res.dealId,
			"appKey": res.appKey,
			"totalAmount": res.totalAmount,
			"tpOrderId": res.tpOrderId,
			"dealTitle": res.dealTitle,
			"rsaSign": res.rsaSign,
			"bizInfo": res.bizInfo
		}, //订单数据
		success: function(res) {
			console.log('success:' + JSON.stringify(res));
		},
		fail: function(err) {
			console.log('fail:' + JSON.stringify(err));
		}
	});
}

/**
 * 微信小程序支付
 */
function wxpay(ret, orderid) {
  console.log('调起支付wxpay', ret);
  let res = ret;
  let status = false;
  uni.requestPayment({
    timeStamp: res.timeStamp,
    nonceStr: res.nonceStr,
    package: res.package,
    signType: res.signType,
    paySign: res.paySign,
    appId: res.appId,
    success: function (res) {
      console.log('success:' + JSON.stringify(res), res);
      uni.showToast({
        title: '支付成功!'
      });
    },
    fail: function (err) {
      console.log('fail:' + JSON.stringify(err));
    }
  });
}
/**
 * 数组去重
 */
function arrayUnique2(arr, name) {
  var hash = {};
  return arr.reduce(function (item, next) {
    hash[next[name]] ? '' : hash[next[name]] = true && item.push(next);
    return item;
  }, []);
}

export { 
	onBridgeReady,
	alipay,
	wxpay,
	arrayUnique2
};