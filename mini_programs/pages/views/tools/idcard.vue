<template>
	<view class="tools">
		<view class="jilu">请输入您要查询的身份证号</view>
		<view class="t_input">
			<view class="title">身份证号</view>
			<view class="inputs">
				<input v-model="idcard" placeholder="例如: 430421187609233453" placeholder-style="font-size: 28upx; color: #999"></input>
			</view>
			<view class="all_btn"></view>
		</view>
		<view class="result" v-show="showDetail">
			<view class="title">详细信息</view>
			<u-form ref="uForm" :label-width="120">
				<u-form-item label="地区:">{{info.area}}</u-form-item>
				<u-form-item label="性别:">{{info.gender}}</u-form-item>
				<u-form-item label="生日:">{{info.birthday}}</u-form-item>
				<u-form-item label="星座:">{{info.constellation}}</u-form-item>
				<u-form-item label="生肖:">{{info.zodiac}}</u-form-item>
			</u-form>
		</view>
		<u-empty style="margin-top: 80upx;" v-show="empty" text="暂无手机信息" mode="list"></u-empty>
		<view @tap="query" class="addclerk" :style="'background:' + colors">
			查询
		</view>
		<u-toast ref="uToast" />
	</view>
</template>

<script>
	var app = getApp();
	import itemCell from "../../commponent/setting/item-cell";
	export default {
		data() {
			return {
				colors: '',
				idcard: '',
				info: {},
				showDetail: false,
				empty: false
			};
		},
		components: {
			itemCell
		},
		props: {},

		/**
		 * 生命周期函数--监听页面加载
		 */
		onLoad: function(options) {
			this.setData({
				colors: app.globalData.newColor
			});
		},

		/**
		 * 生命周期函数--监听页面初次渲染完成
		 */
		onReady: function() {},

		/**
		 * 生命周期函数--监听页面显示
		 */
		onShow: function() {},

		/**
		 * 生命周期函数--监听页面隐藏
		 */
		onHide: function() {},

		/**
		 * 生命周期函数--监听页面卸载
		 */
		onUnload: function() {},

		/**
		 * 页面相关事件处理函数--监听用户下拉动作
		 */
		onPullDownRefresh: function() {},

		/**
		 * 页面上拉触底事件的处理函数
		 */
		onReachBottom: function() {},

		/**
		 * 用户点击右上角分享
		 */
		onShareAppMessage: function() {},
		methods: {
			query(){
				if(!this.idcard){
					this.$refs.uToast.show({
						title: '身份证号不能为空',
						type: 'error'
					})
					return;
				}
				if(!this.$u.test.idCard(this.idcard)){
					this.$refs.uToast.show({
						title: '身份证号不正确',
						type: 'error'
					})
					return;
				}
				this.request('/v1/api/idcard/' + this.idcard, {}, 'GET').then(res=>{
					if(res.code == 200){
						this.setData({
							empty: false,
							showDetail: true,
							info: res.data
						});
					} else {
						this.setData({
						  empty: true,
						  showDetail: false,
						});
					}
				})
			}
		}
	};
</script>
<style lang="scss" scoped>
	page {
		background-color: #F5F5FA;
	}
	.result{
		padding: 0 4%;
		margin-top: 20upx;
		background-color: #ffffff;
	}
	.result .title{
		padding: 30upx 0 20upx 0;
		font-size: 30upx;
		font-weight: bold;
	}
	.t_input {
		padding: 20upx 4%;
		background-color: #fff;
		display: flex;
		align-items: center;
		justify-content: space-between;
	}

	.t_input .title {
		height: 80upx;
		line-height: 80upx;
		color: #333;
		font-weight: bold;
		width: 120upx;
	}

	.t_input .inputs {
		color: #333;
		font-size: 32upx;
	}

	.inputs input {
		height: 80upx;
		line-height: 80upx;
		width: 100%;
		display: block;
	}

	.all_btn {
		font-size: 24upx;
		font-family: Source Han Sans CN;
		font-weight: 400;
		width: 150upx;
	}

	.jilu {
		height: 60upx;
		line-height: 60upx;
		padding: 0 4%;
		font-size: 28upx;
		color: #999;
		font-family: Source Han Sans CN;
		font-weight: 400;
		margin-top: 10upx;
	}

	.addclerk {
		width: 80%;
		height: 82upx;
		line-height: 82upx;
		border-radius: 15upx;
		font-size: 32upx;
		font-family: Source Han Sans CN;
		font-weight: 500;
		color: rgba(255, 255, 255, 1);
		text-align: center;
		position: fixed;
		bottom: 30upx;
		left: 50%;
		transform: translateX(-50%);
		background-color: pink;
	}

	.addclerk:active {
		opacity: 0.8;
	}
</style>
<style>
	page {
		background-color: #F5F5FA;
	}
</style>
