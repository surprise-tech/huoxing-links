<template>
<view class="search">
  <view class="input-box" @tap="jumpSearch">
		<input placeholder="搜索关键词" class="inputs" disabled placeholder-style="color:#c0c0c0;font-size:24upx;"></input>
		<view class="icon search">
      <image src="/static/images/home/search.png" class="search_img"></image>
    </view>
	</view>
</view>
</template>

<script>

export default {
  data() {
    return {};
  },

  components: {},
  props: {},
  methods: {
    jumpSearch() {
      uni.navigateTo({
        url: '/pages/views/home/search'
      });
    }

  }
};
</script>
<style lang="scss" scoped>
.search{
  z-index: 900;
}
.input-box {
  width: 100%;
  height: 60upx;
  background-color: #f5f5f5;
  position: relative;
  display: flex;
  align-items: center;
  border-radius: 30upx;
  margin-bottom: 20upx;
  z-index: 900;
}

.input-box .icon {
  display: flex;
  align-items: center;
  position: absolute;
  top: 10upx;
  right: 20upx;
  width: 40upx;
  height: 40upx;
  font-size: 32upx;
  color: #c0c0c0;
}
.search .search_img{
  width: 40upx;
  height: 40upx;
  display: block;
}
.input-box .inputs {
  padding-left: 28upx;
  height: 28upx;
  font-size: 28upx;
}
</style>