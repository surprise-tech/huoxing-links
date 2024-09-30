<template>
 <view class="category-list">
		<view v-for="(item, index) in categoryList" :key="index" class="category" @tap="jumpList(item)">
			<view class="class_img"><image :src="item.img" class="imgs"></image></view>
			<view class="text">{{ item.name }}</view>
		</view>
</view>
</template>
<script>

export default {
  data() {
    return {
      
    };
  },
  props: {
	  categoryList:{
		  type:Array
	  }
  },
  methods: {
    jumpList(item) {
      uni.navigateTo({
        url: item.link// + item.id
      });
    }
  }
};
</script>
<style scoped lang="scss">
.category-list {
  width: 94%;
  margin: 0 3%;
  padding: 0 0 30upx 0;
  border-bottom: solid 2upx #f6f6f6;
  display: flex;
  flex-wrap: wrap;
  box-shadow: 0upx 0upx 10upx #eee;
  margin-top: 20upx;
  border-radius: 10upx;
}
.category {
  width: 25%;
  margin-top: 50upx;
  display: flex;
  flex-wrap: wrap;
}
.category:active{
  opacity: 0.8;
}
.category .class_img {
  width: 100%;
  display: flex;
  justify-content: center;
  .imgs {
    width: 9vw;
    height: 9vw;
  }
}
.text {
  margin-top: 16upx;
  width: 100%;
  display: flex;
  justify-content: center;
  font-size: 24upx;
  color: #3c3c3c;
  font-weight: 500;
}
</style>