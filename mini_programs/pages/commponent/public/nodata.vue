<template>
<view class="nodata">
  <text class="iconfont icon-zanwushuju" :style="'color:' + colors"></text>
  <view class="texts">
    {{title}}
  </view>
</view>
</template>

<script>

export default {
  data() {
    return {};
  },

  components: {},
  props: {
    title: {
      type: String,
      default: '暂无数据'
    },
    colors: {
      type: String
    }
  },
  methods: {}
};
</script>
<style scoped lang="scss">
.nodata{
  width: 100vw;
  height: 50vw;
}
.nodata .iconfont{
  display: inline-block;
  text-align: center;
  font-size: 160upx;
  color: pink;
  width: 100%;
  margin-top: 100upx;
}
.texts{
  margin-top: 20upx;
  height: 40upx;
  line-height: 40upx;
  text-align: center;
  color: #9c9c9c;
  font-size: 24upx;
  letter-spacing: 5upx;
}
</style>