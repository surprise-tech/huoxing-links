<template>
 <view class="item_cell" @tap="navTo()">
    <view>
      {{cellname}}
    </view>
    <image v-if="showRight" src="/static/images/home/right.png"></image>
  </view>
</template>

<script>

export default {
  data() {
    return {};
  },

  components: {},
  props: {
    cellname: {
      type: String
    },
    linkUrl: {
      type: String
    },
	showRight: {
	  type: Boolean,
	  default: true
	}
  },
  methods: {
	  navTo(){
		  if(this.linkUrl){
			  uni.navigateTo({
			    url: this.linkUrl// + item.id
			  });
		  }
	  }
  }
};
</script>
<style lang="scss" scoped>
.item_cell{
  background-color: #fff;
  height: 80upx;
  line-height: 80upx;
  padding: 0 4%;
  display: flex;
  align-items: center;
  position: relative;
}
.item_cell:after{
  position: absolute;
  z-index: 3;
  left: 0;
  right: 0;
  height: 0;
  bottom: 0;
  left: 4%;
  content: "";
  -webkit-transform: scaleY(.5);
  transform: scaleY(.5);
  border-bottom: 1px solid #e4e7ed;
}
.item_cell:active{
  background-color: #f5f5f5;
}
.item_cell view{
  width: 93%;
  float: left;
}
.item_cell image{
  width: 40upx;
  height: 40upx;
  float: right;
}
</style>