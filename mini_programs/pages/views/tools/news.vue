<template>
	<view
		class="homeCard flex flex-direction align-center justify-center bg-white">
		<view class="card">
			<view class="flex">
				<image class="weixin" v-if="data.avatar" :src="data.avatar"></image>
				<view class="margin-left">
					<view class="text-bold text-lg">{{ data.title || "" }}</view>
					<view class="text-bold desc-font-color margin-top-xs">
						{{ data.sub_title || "" }}
					</view>
				</view>
			</view>
			<image
				class="erweima margin-top"
				show-menu-by-longpress="true"
				:src="data.qr"></image>
			<view class="margin-top text-center desc-font-color">
				长按识别二维码添加
			</view>
		</view>
	</view>
</template>

<script>
export default {
	data() {
		return {
			data: {},
		};
	},
	methods: {},
	onLoad(opt) {
		if (!opt.code && !opt.device_uid) {
			uni.showModal({
				title: "提示",
				content: "参数错误",
				showCancel: false,
			});
			return;
		}
		this.request(`/api/link-show-qr/${opt.code}?device_uid=${opt.device_uid}`)
			.then((res) => {
				this.data = res;
				if (res.title) {
					uni.setNavigationBarTitle({
						title: res.title,
					});
				}
			})
			.catch(() => {
				uni.showModal({
					title: "参数错误",
					content: JSON.stringify(e),
					showCancel: false,
				});
			});
	},
};
</script>

<style scoped lang="scss">
.homeCard {
	width: 100%;
	height: 100vh;
	margin-top: -100rpx;
	.card {
		width: 540rpx;
		.weixin {
			width: 88rpx;
			height: 88rpx;
		}
		.erweima {
			height: 540rpx;
			width: 540rpx;
		}
	}
}
</style>
