<template>
 <view class="notice">
    <view class="left_icon">
      <text class="iconfont icon-xinbaniconshangchuan-" :style="'color: ' + colors + ';'"></text>
    </view>
    <view class="right_notice">
      <swiper class="notice_swiper" vertical easing-function="easeInOutCubic" autoplay interval="8000">
        <swiper-item v-for="(item,index) in noticeList" :key="index" class="sw_item">
		   <text class="sw_text">{{item.noticeTitle}}</text>
          <image class="sw_image" src="/static/images/home/right.png"></image>
        </swiper-item>
      </swiper>
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
    colors: {
      type: String
    },
	noticeList:{
	  type:Array
	}
  },
  methods: {}
};
</script>
<style lang="scss" scoped>
.notice{
  height: 80upx;
  line-height: 80upx;
  margin: 0 3%;
  margin-top: 15upx;
  padding: 0 10upx;
  box-shadow: 0upx 0upx 10upx #eee;
  border-radius: 8upx;
}
.left_icon{
  height: 100%;
  width: 10%;
  float: left;
  padding-top: 3upx;
}
.left_icon .iconfont{
  display: inline-block;
  font-size: 44upx;
}
.right_notice{
  float: left;
  width: 90%;
}
.right_notice .notice_swiper{
  height: 80upx;
}
.notice_swiper .sw_item{
  height: 80upx;
}
.notice_swiper .sw_item .sw_text{
  font-size: 24upx;
  color: #333;
  display: inline-block;
}
.notice_swiper .sw_image{
  width: 40upx;
  height: 40upx;
  float: right;
  margin-top: 20upx;
}
</style>