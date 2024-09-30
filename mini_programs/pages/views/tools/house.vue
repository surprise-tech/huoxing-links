<template>
	<view class="tools">
		<u-subsection :list="list" :current="current" @change="sectionChange"></u-subsection>
		<view v-show="current == 0">
			<u-cell-group>
				<u-cell-item @click="changeType" title="选择计算方式" :value="typeMap[type]"></u-cell-item>
				<u-cell-item title="商业贷款金额(万元)" :arrow="false">
					<u-input v-model="amount" input-align="right" placeholder="请输入商业贷款金额" />
				</u-cell-item>
				<u-cell-item @click="showPeriod = true" title="贷款期限" :value="periodMap[period]"></u-cell-item>
				<u-cell-item @click="showRate = true" title="商业贷款年利率" :value="(rate * 100).toFixed(3) + '%'">
				</u-cell-item>
			</u-cell-group>
		</view>
		<view v-show="current == 1">
			<u-cell-group>
				<u-cell-item @click="changeType" title="选择计算方式" :value="typeMap[type]"></u-cell-item>
				<u-cell-item title="公积金贷款金额(万元)" :arrow="false">
					<u-input v-model="gjjAmount" input-align="right" placeholder="请输入商业贷款金额" />
				</u-cell-item>
				<u-cell-item @click="showPeriod = true" title="贷款期限" :value="periodMap[period]"></u-cell-item>
				<u-cell-item @click="showGjjRate = true" title="公积金贷款年利率" :value="(gjjRate * 100).toFixed(3) + '%'">
				</u-cell-item>
			</u-cell-group>
		</view>
		<view v-show="current == 2">
			<u-cell-group>
				<u-cell-item @click="changeType" title="选择计算方式" :value="typeMap[type]"></u-cell-item>
				<u-cell-item @click="showPeriod = true" title="贷款期限" :value="periodMap[period]"></u-cell-item>
				<u-cell-item title="商业贷款金额(万元)" :arrow="false">
					<u-input v-model="amount" input-align="right" placeholder="请输入商业贷款金额" />
				</u-cell-item>
				<u-cell-item @click="showRate = true" title="商业贷款年利率" :value="(rate * 100).toFixed(3) + '%'">
				</u-cell-item>
				<u-cell-item title="公积金贷款金额(万元)" :arrow="false">
					<u-input v-model="gjjAmount" input-align="right" placeholder="请输入公积金贷款金额" />
				</u-cell-item>
				<u-cell-item @click="showGjjRate = true" title="公积金贷款年利率" :value="(gjjRate * 100).toFixed(3) + '%'">
				</u-cell-item>
			</u-cell-group>
		</view>
		<button class="calc" @tap="calc()">开始计算</button>
		<view class="reslut">
			<view class="tabs">
				<view class="tab" @tap="changeResultType(0)" :class="{active: type == '0'}">等额本息</view>
				<view class="tab" @tap="changeResultType(1)" :class="{active: type == '1'}">等额本金</view>
			</view>
			<view class="content">
				<view class="top">
					<view class="title">每月月供参考(元)</view>
					<view class="month">{{reslut.monthAmount}}</view>
				</view>
				<view class="bottom">
					<view class="col">
						<view class="title">还款总额(万元)</view>
						<view class="total">{{reslut.totalReturnAmount}}</view>
					</view>
					<view class="col">
						<view class="title">贷款总额(万元)</view>
						<view class="total">{{reslut.totalAmount}}</view>
					</view>
					<view class="col">
						<view class="title">支付利息(万元)</view>
						<view class="total">{{reslut.rateAmount}}</view>
					</view>
				</view>
				<view class="btns">
					<button class="btn" @tap="reset">重新计算</button>
					<button class="btn" @tap="resultDetail">还款数据明细</button>
				</view>
				<view class="tips">
					<text v-show="type == '0'" class="title">等额本息：每月还款额固定，其中本金部分逐月递增、利息部分逐月递减</text>
					<text v-show="type == '1'" class="title">等额本金：每月还款额递减，其中本金部分固定、利息部分逐月递减</text>
				</view>
			</view>
		</view>
		<u-select v-model="showType" :list="typeList" @confirm="typeConfirm"></u-select>
		<u-select v-model="showPeriod" :list="periodList" @confirm="periodConfirm"></u-select>
		<u-select v-model="showRate" :list="rateList" @confirm="rateConfirm"></u-select>
		<u-select v-model="showGjjRate" :list="gjjRateList" @confirm="gjjRateConfirm"></u-select>
		<u-select v-model="showResultList" :list="listAmount"></u-select>
		<u-modal v-model="showCustomeRate" @confirm="customeConfirm">
			<view class="slot-content">
				<view style="padding: 0 30upx;">
					<u-input v-model="customeRate" input-align="center" placeholder="请输入贷款利率(%)"></u-input>
				</view>
			</view>
		</u-modal>
		<u-toast ref="uToast" />
	</view>
</template>

<script>
	var app = getApp();
	export default {
		data() {
			return {
				colors: '',
				list: [{
						name: '商业贷款'
					},
					{
						name: '公积金贷款'
					},
					{
						name: '组合贷款'
					}
				],
				current: 0,
				showType: false,
				typeList: [{
						value: '0',
						label: '等额本息'
					},
					{
						value: '1',
						label: '等额本金'
					}
				],
				typeMap: {
					"0": "等额本息",
					"1": "等额本金"
				},
				showCustomeRate: false,
				showPeriod: false,
				periodMap: {},
				periodList: [],
				showRate: false,
				rateList: [{
						value: '0.04165',
						label: '基准利率8.5折(4.165%)'
					},
					{
						value: '0.0441',
						label: '基准利率9折(4.41%)'
					},
					{
						value: '0.04655',
						label: '基准利率9.5折(4.655%)'
					},
					{
						value: '0.049',
						label: '基准利率(4.9%)'
					},
					{
						value: '0.0539',
						label: '基准利率1.1倍(5.39%)'
					},
					{
						value: '0.0588',
						label: '基准利率1.2倍(5.88%)'
					},
					{
						value: '0',
						label: '自定义'
					}
				],
				showGjjRate: false,
				gjjRateList: [{
						value: '0.0325',
						label: '基准利率(3.25%)'
					},
					{
						value: '0.03575',
						label: '基准利率1.1倍(3.575%)'
					},
					{
						value: '0.039',
						label: '基准利率1.2倍(3.9%)'
					},
					{
						value: '0',
						label: '自定义'
					}
				],
				type: "0",
				amount: undefined,
				gjjAmount: undefined,
				period: 20,
				rate: "0.049",
				gjjRate: "0.0325",
				customeRate: undefined,
				customeType: undefined,
				showResultList: false,
				reslut: {
					monthAmount: 0,
					totalAmount: 0,
					totalReturnAmount: 0,
					rateAmount: 0,
				},
				groupResult: {},
				listAmount: [],
				resultType: 0
			};
		},
		components: {},
		props: {},

		/**
		 * 生命周期函数--监听页面加载
		 */
		onLoad: function(options) {

			let periodList = [];
			let periodMap = {};
			for (var i = 1; i <= 30; i++) {
				let label = i + '年' + ' (' + i * 12 + '期)';
				periodList.push({
					value: i,
					label: label
				});
				periodMap[i] = label;
			}
			let rateMap = {};
			for (var i = 0; i < this.rateList.length; i++) {
				let item = this.rateList[i];
				rateMap[item.value] = item.label;
			}
			this.setData({
				colors: app.globalData.newColor,
				periodList: periodList,
				periodMap: periodMap,
				rateMap: rateMap
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
			changeResultType(type){
				this.setData({
					type: type,
					reslut: type == 0 ? this.groupResult["bx"] : this.groupResult["bj"]
				});
			},
			resultDetail(){
				if(this.type == 0){
					this.$refs.uToast.show({
						title: '等额本息还款固定',
						type: 'warning'
					})
					return;
				}
				this.setData({
					showResultList: true
				});
			},
			calc(){
				if(this.current == 2){
					this.calcGroup();
				} else {
					this.calcSimple();
				}
			},
			calcSimple() {
				//商业贷款、公积金贷款
				let lilv = this.current == 0 ? this.rate : this.gjjRate; //得到利率
				//贷款总额
				let daikuan_total = this.current == 0 ? this.amount * 10000 : this.gjjAmount * 10000;
				//还款周期
				let month = this.period * 12;
				let groupResult = {};
				let listAmount = [];
				//1.本金还款
				if(true){
					//月还款
					let totalReturnAmount = 0;
					let firstAmount = 0;
					for (let j = 0; j < month; j++) {
						//调用函数计算: 本金月还款额
						let huankuan = this.getMonthMoney2(lilv, daikuan_total, month, j);
						totalReturnAmount += huankuan;
						huankuan = Math.round(huankuan * 100) / 100;
						if(j == 0){
							firstAmount = huankuan;
						}
						listAmount.push({"value": (j + 1) + "月", "label": '第' + (j + 1) + "月       还 " + huankuan + '元'})
					}
					//还款总额
					let total = Math.round(totalReturnAmount * 100) / 100;
					//支付利息款
					let lx = Math.round((totalReturnAmount - daikuan_total) * 100) / 100;
					let reslut = {
						totalAmount: (daikuan_total / 10000).toFixed(2),
						monthAmount: firstAmount.toFixed(2),
						totalReturnAmount: (total / 10000).toFixed(2),
						rateAmount: (lx / 10000).toFixed(2),
					}
					groupResult["bj"] = reslut;
				} 
				if(true){
					//2.本息还款
					//月均还款
					let monthAmount = this.getMonthMoney1(lilv, daikuan_total, month); //调用函数计算
					monthAmount = Math.round(monthAmount * 100) / 100;
					//还款总额
					let total = monthAmount * month ;
					//支付利息款
					let lx_txt = Math.round((total - daikuan_total) * 100) / 100;
					total = Math.round(total * 100) / 100;
					
					let reslut = {
						totalAmount: (daikuan_total / 10000).toFixed(2),
						monthAmount: monthAmount.toFixed(2),
						totalReturnAmount: (total / 10000).toFixed(2),
						rateAmount: (lx_txt / 10000).toFixed(2),
					}
					groupResult["bx"] = reslut;
				}
				this.setData({
					groupResult: groupResult,
					listAmount: listAmount,
					reslut: this.type == '0' ? groupResult["bx"]: groupResult["bj"]
				})
			},
			calcGroup(){
				//贷款总额
				let total_sy = this.amount * 10000;
				let total_gjj = this.gjjAmount * 10000;
				let daikuan_total = total_sy + total_gjj;
				//月还款
				let lilv_sd = this.rate;//得到商贷利率
				let lilv_gjj = this.gjjRate;//得到公积金利率
				let groupResult = {};
				//1.本金还款
				  //月还款
				  let all_total2 = 0;
				  let month_money2 = "";
				  let month = this.period * 12;
				  let firstAmount = 0;
				  let listAmount = [];
				  for(let j = 0; j < month; j++){
					//调用函数计算: 本金月还款额
					let huankuan = this.getMonthMoney2(lilv_sd, total_sy, month, j) + this.getMonthMoney2(lilv_gjj, total_gjj, month, j);
					all_total2 += huankuan;
					huankuan = Math.round(huankuan * 100) / 100;
					//month_money2 += (j + 1) + "月," + huankuan + "(元)\n";
					if(j == 0){
						firstAmount = huankuan;
					}
					listAmount.push({"value": (j + 1) + "月", "label": '第' + (j + 1) + "月       还 " + huankuan + '元'})
				  }
				  //还款总额
				  var all_total2_txt = Math.round(all_total2 * 100) / 100;
				  //支付利息款
				  var accrual2 = Math.round((all_total2 - daikuan_total) * 100) / 100;
				
				let reslut = {
					totalAmount: (daikuan_total / 10000).toFixed(2),
					monthAmount: firstAmount.toFixed(2),
					totalReturnAmount: (all_total2_txt / 10000).toFixed(2),
					rateAmount: (accrual2 / 10000).toFixed(2),
				}
				groupResult["bj"] = reslut;
				//2.本息还款
				  //月均还款
				  var month_money1 = this.getMonthMoney1(lilv_sd, total_sy, month) + this.getMonthMoney1(lilv_gjj, total_gjj, month);//调用函数计算
				  var month_money1_txt = Math.round(month_money1 * 100) / 100;
				  //还款总额
				  var all_total1 = month_money1 * month;
				  var all_total1_txt = Math.round(all_total1 * 100) / 100;
				  //支付利息款
				  var accrual1_txt = Math.round((all_total1 - daikuan_total) * 100) / 100;
				  
				  let reslut1 = {
				  	totalAmount: (daikuan_total / 10000).toFixed(2),
				  	monthAmount: month_money1_txt.toFixed(2),
				  	totalReturnAmount: (all_total1_txt / 10000).toFixed(2),
				  	rateAmount: (accrual1_txt / 10000).toFixed(2),
				  }
				  groupResult["bx"] = reslut1;
				  
				  this.setData({
				  	groupResult: groupResult,
				  	listAmount: listAmount,
				  	reslut: this.type == '0' ? groupResult["bx"]: groupResult["bj"]
				  })
			},
			//本息还款的月还款额(参数: 年利率/贷款总额/贷款总月份)
			getMonthMoney1(lilv, total, month){
			  var lilv_month = lilv / 12;//月利率
			  return total * lilv_month * Math.pow(1 + lilv_month, month) / ( Math.pow(1 + lilv_month, month) -1 );
			},
			//本金还款的月还款额(参数: 年利率 / 贷款总额 / 贷款总月份 / 贷款当前月0～length-1)
			getMonthMoney2(lilv, total, month, cur_month){
			  var lilv_month = lilv / 12;//月利率
			  var benjin_money = total / month;
			  return (total - benjin_money * cur_month) * lilv_month + benjin_money;
			},
			sectionChange(index) {
				this.current = index;
				this.reset();
			},
			reset(){
				this.setData({
					type: "0",
					amount: undefined,
					gjjAmount: undefined,
					period: 20,
					rate: "0.049",
					gjjRate: "0.0325",
					customeRate: undefined,
					customeType: undefined,
					reslut: {
						monthAmount: 0,
						totalAmount: 0,
						totalReturnAmount: 0,
						rateAmount: 0,
					},
					groupResult: {}
				})
			},
			changeType() {
				this.setData({
					showType: true
				})
			},
			typeConfirm(e) {
				this.setData({
					type: e[0].value
				})
			},
			periodConfirm(e) {
				this.setData({
					period: e[0].value
				})
			},
			rateConfirm(e) {
				if (e[0].value == "0") {
					this.setData({
						showCustomeRate: true,
						customeType: 0
					})
					return
				}
				this.setData({
					rate: e[0].value
				})
			},
			gjjRateConfirm(e) {
				if (e[0].value == "0") {
					this.setData({
						showCustomeRate: true,
						customeType: 1
					})
					return
				}
				this.setData({
					gjjRate: e[0].value
				})
			},
			customeConfirm() {
				if (this.customeType == 0) {
					this.setData({
						rate: Number(this.customeRate) / 100
					})
				} else {
					this.setData({
						gjjRate: Number(this.customeRate) / 100
					})
				}

			},
		}
	};
</script>
<style lang="scss" scoped>
	page {
		background-color: #F5F5FA;
	}

	.reslut {
		margin: 20upx 20upx;
		box-shadow: 0 0.07rem 0.25rem #eff0f3;
		-webkit-box-shadow: 0 0.07rem 0.25rem #eff0f3;
		-moz-box-shadow: 0 .07rem .25rem #EFF0F3;
		-o-box-shodow: 0 .07rem .25rem #EFF0F3;
		margin-bottom: .6rem;
		border-radius: .04rem;
		border: 1px solid #E6E9F0;
	}
	.reslut .tabs {
		height: 80upx;
		line-height: 80upx;
		width: 100%;
		display: flex;
	}
	.reslut .tabs .tab{
		width: 50%;
		text-align: center;
		background-color: #F7F8FA;
	}
	.reslut .tabs .active{
		background-color: #ffffff;
	}
	.reslut .content{
		padding: 30upx 30upx;
		background-color: #ffffff;
	}
	.reslut .content .title{
		color: #868E9E;
		font-size: 26upx;
		padding-bottom: 10upx;
	}
	.reslut .content .month{
		font-size: 40upx;
		font-weight: bold;
	}
	.reslut .content .bottom{
		padding: 20upx 0;
		display: flex;
		justify-content: space-between;
	}
	.reslut .content .bottom .total{
		font-size: 30upx;
	}
	.reslut .content .btns{
		padding: 20upx 0;
		display: flex;
		justify-content: space-between;
		background-color: #ffffff;
	}
	.reslut .content .btns .btn{
		width: 300upx;
		border-radius: 100upx;
		color: #394259;
		border: 1upx solid #D1D1D1;
		background-color: #fff;
		font-size: 28upx;
	}
	.reslut .content .tips{
		padding: 20upx 0;
	}
	.calc{
		background-color: #0076FF;
		color: #ffffff;
		font-size: 30upx;
		padding: 6upx 0;
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
