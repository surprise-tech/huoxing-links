<template>
 <view class="top_nav" @tap="jumpNext">
      <text :class="'iconfont ' + icons + ' icon'" :style="'color:' + colors"></text>
      <text class="name"><slot></slot></text>
      <image src="/static/images/home/right.png" class="right_icon" v-if="rightshow == true"></image>
</view>
</template>

<script>

export default {
  data() {
    return {};
  },

  components: {},
  props: {
    icons: {
      type: String
    },
    linkUrl: {
      type: String //跳转的页面地址
    },
    colors: {
      type: String
    },
    rightshow: {
      type: Boolean,
      default: true
    }
  },
  methods: {
    jumpNext() {
      console.log(this.linkUrl);
      if (this.linkUrl && this.linkUrl !== '') {
        uni.navigateTo({
          url: this.linkUrl
        });
      }
    }

  }
};
</script>
<style scoped lang="scss">
.top_nav {
  line-height: 60upx;
  padding: 20upx 30upx;
  display: flex;
  align-items: baseline;
  position: relative;
}

.top_nav::after {
  position: absolute;
  left: 30upx;
  right: 0;
  height: 0;
  content: '';
  bottom: 0;
  -webkit-transform: scaleY(0.5);
  transform: scaleY(0.5);
  border-bottom: 1px solid #E4E7ED;
}

.top_nav:active {
  background-color: #fafafa;
}

.top_nav .icon {
  align-self: center;
  width: 56upx;
  max-height: 60upx;
  font-size: 38upx;
}

.top_nav .name {
  flex: 1;
  font-size: 28upx;
  color: #303133;
}

.top_nav .right_icon {
  float: right;
  width: 40upx;
  height: 40upx;
  align-self: center;
}

</style>