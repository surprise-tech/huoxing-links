<template>
<view class="loading">
  <view class="bg-white">
    <image src="https://image.weilanwl.com/gif/loading-white.gif" mode="aspectFit" class="gif-white response"></image>
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
<style scoped lang="scss">
.loading{
  width: 100%;
  height: 100%;
  position: fixed;
  top: 0;
  left: 0;
  z-index: 999;
  background-color: #fff;
  opacity: 0.9;
}
.bg-white{
  width: 100%;
  height: 100%;
}
.bg-white image{
  width: 100%;
  height: 60%;
  display: block;
}
</style>