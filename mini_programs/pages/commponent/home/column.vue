<template>
 <view class="column">
    <view class="column_left">
      <image src="https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1600401451540&di=570b9fa5a9140bb06b02ce12245ea637&imgtype=0&src=http%3A%2F%2Fimg.aiimg.com%2Fuploads%2Fallimg%2F181025%2F263915-1Q025104041.jpg" mode="scaleToFill" class="left_img"></image>
    </view>
    <view class="colum_right">
      <image src="https://img.zcool.cn/community/010ef3554344980000019ae9876cb6.jpg@1280w_1l_2o_100sh.jpg" class="right_img"></image>
      <image src="https://img.zcool.cn/community/01eb3a554344980000019ae91879dd.jpg@1280w_1l_2o_100sh.jpg" style="margin-top: 12upx" class="right_img"></image>
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
  methods: {}
};
</script>
<style lang="scss" scoped>
.column{
  height: 400upx;
  width: 94%;
  overflow: hidden;
  margin: 0 3%;
  margin-top: 15upx;
}
.column_left{
  height: 100%;
  width: 40%;
  float: left;
  .left_img{
    width: 100%;
    height: 100%;
    display: block;
  }
}
.colum_right{
  float: right;
  width: 58%;
  .right_img{
    width: 100%;
    height: 196upx;
    display: block;
  }
}
</style>