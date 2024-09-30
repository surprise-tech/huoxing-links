<template>
	<view class="tools">
		<u-card title="每度电的电价" :show-foot="false" :show-head="false">
			<view class="" slot="body">
				<u-form ref="uForm" label-width="150">
					<u-form-item label="每度电价"><u-input v-model="form.dj" placeholder="请输入电价"/><view slot="right">元</view></u-form-item>
					<u-form-item label="设备功率"><u-input v-model="form.gl" placeholder="请输入功率"/><view slot="right">瓦</view></u-form-item>
					<u-form-item label="每天使用"><u-input v-model="form.dayTxt" type="select" @click="showDay = true"/></u-form-item>
					<u-form-item label="每月使用"><u-input v-model="form.monthTxt" type="select" @click="showMonth = true"/></u-form-item>
				</u-form>
				<u-button @click="submit">计算电费</u-button>
			</view>
		</u-card>
		<u-card title="每度电的电价" :show-foot="false" :show-head="false">
			<view class="" slot="body">
				<u-form ref="uForm" label-width="200">
					<u-form-item label="每月的用电量"><u-input placeholder=" " :disabled="true" v-model="result.vol"/><view slot="right">瓦</view></u-form-item>
					<u-form-item label="每周电费大约"><u-input placeholder=" " :disabled="true" v-model="result.week"/><view slot="right">元</view></u-form-item>
					<u-form-item label="每月电费大约"><u-input placeholder=" " :disabled="true" v-model="result.month"/><view slot="right">元</view></u-form-item>
					<u-form-item label="每年电费大约"><u-input placeholder=" " :disabled="true" v-model="result.year"/><view slot="right">元</view></u-form-item>
				</u-form>
			</view>
		</u-card>
		<u-select v-model="showDay" :list="dayList" @confirm="dayConfirm"></u-select>
		<u-select v-model="showMonth" :list="monthList" @confirm="monthConfirm"></u-select>
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
				form: {
					dj: null,
					gl: null,
					day: 24,
					month: 30,
					dayTxt: '24小时',
					monthTxt: '30天'
				},
				result: {},
				dayList: [],
				monthList: [],
				showDay: false,
				showMonth: false
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
			let dayList = [];
			for(let i = 1; i <= 24; i++){
				dayList.push({value: i, label: i + '小时'})
			}
			let monthList = [];
			for(let i = 1; i <= 30; i++){
				monthList.push({value: i, label: i + '天'})
			}
			this.setData({
				dayList: dayList,
				monthList: monthList
			})
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
			dayConfirm(e) {
				let form = this.form;
				form.day = e[0].value;
				form.dayTxt = e[0].label;
				this.setData({
					form: form
				})
			},
			monthConfirm(e) {
				let form = this.form;
				form.month = e[0].value;
				form.monthTxt = e[0].label;
				this.setData({
					form: form
				})
			},
			submit() {
				let x;let h;let f;let t;let v;let q;let u;let r;let w;
				if(!this.form.dj){
					this.$refs.uToast.show({
						title: '请输入电价',
						type: 'error'
					})
					return;
				}
				if(!this.form.gl){
					this.$refs.uToast.show({
						title: '请输入功率',
						type: 'error'
					})
					return;
				}
				t = this.form.dj; //每度电价
				t = parseFloat(t);
				x = this.form.gl; //设备功率
				x = parseFloat(x);
				h = this.form.day //每天使用
				f = this.form.month //每月使用
				let kwhperday= x / 1e3 * h;
				v = kwhperday * f;
				if(v>20){
					v=v
				}else{
					v=v*10/10
				}
				let costperday = kwhperday * t;
				u = kwhperday * f * t;
				w = u * 12;
				r = costperday * 7;
				this.setData({
					result: {
						vol: v.toFixed(),
						week: r.toFixed(2),
						month: u.toFixed(2),
						year: w.toFixed(2)
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
		width: 100upx;
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
