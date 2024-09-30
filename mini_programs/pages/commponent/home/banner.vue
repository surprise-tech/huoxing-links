<template>
<view class="banner">
    <image src="https://img.zcool.cn/community/01e47655efeee832f875a132e43a9c.png@1280w_1l_2o_100sh.png" lazy-load="true" mode="scaleToFill" class="imgs"></image>
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
.banner{
  height: 26vw;
  width: 94%;
  margin: 0 auto;
  margin-top: 20upx;
  overflow: hidden;
  .imgs{
    width: 100%;
    height: 100%;
    display: block;
    border-radius: 15upx;
  }
}
</style>