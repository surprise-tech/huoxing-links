<script>
import { getConfig, setConfig, getCart, getToken } from "./utils/auth";
import myconfig from "./utils/config";
import request from "./utils/request";
let colors = getConfig();
let nowColor = "";
if (colors) {
	nowColor = colors.color;
}
uni.$ajax = request.request; //挂载全局请求方法
uni.$ajaxJson = request.requestJson;
uni.$ajaxForm = request.requestForm;
export default {
	onLaunch: function () {
		// #ifdef MP-WEIXIN
		uni.getSetting({
			success: (res) => {
				if (res.authSetting["scope.userInfo"]) {
					// 已经授权，可以直接调用 getUserInfo 获取头像昵称，不会弹框
					uni.getUserInfo({
						success: (res) => {
							// 可以将 res 发送给后台解码出 unionId
							this.globalData.userInfo = res.userInfo; // 由于 getUserInfo 是网络请求，可能会在 Page.onLoad 之后才返回
							// 所以此处加入 callback 以防止这种情况
							if (this.userInfoReadyCallback) {
								this.userInfoReadyCallback(res);
							}
						},
					});
				}
			},
		});
		// #endif
		uni.getSystemInfo({
			// 获取手机状态栏高度
			complete: (res) => {
				this.globalData.statusHeight = res.statusBarHeight;
				if (res.platform == "ios") {
					this.globalData.toBar = 44;
				} else if (res.platform == "android") {
					this.globalData.toBar = 48;
				} else {
					this.globalData.toBar = 44;
				}
			},
		}); // 设置默认主题颜色
		let config = getConfig();
		if (config && config.color !== "") {
			//如果存在设置的主题 使用设置的主题
			uni.setTabBarStyle({
				selectedColor: config.color,
			});
			let tabList = config.tabList;
			if (config.tabList) {
				for (var i = 0; i < tabList.length; i++) {
					let img = tabList[i];
					uni.setTabBarItem({
						//设置tabBar 首页图标
						index: i,
						selectedIconPath: img,
					});
				}
			}
		} else {
			//如果不存在主题 使用默认主题
			let fristColor = {};
			fristColor.color = "#0081ff";
			fristColor.name = "default";
			setConfig(fristColor);
			uni.setTabBarStyle({
				selectedColor: "#0081ff",
			});
		}
		/**
		 * 模拟获取购物车的数量 getCart
		 */
		let cart = getCart();
		let length = "";
		if (cart && getToken()) {
			length = cart.length;
			uni.setTabBarBadge({
				//给tabBar添加角标
				index: 2,
				text: String(length),
			});
		}
	},
	globalData: {
		userInfo: null,
		statusHeight: "20", //状态栏高度
		toBar: "44", //标题栏高度
		newColor: nowColor || "#0081ff", //小程序主题颜色
		config: myconfig.themeList, //主题图标
	},
	methods: {},
};
</script>
<style lang="scss">
@import "@/colorui/main.css";
@import "@/colorui/icon.css";
@import "@/public.scss";
@import "uview-ui/index.scss";
@import "./app.css";
</style>
