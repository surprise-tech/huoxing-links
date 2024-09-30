<template>
<view class="tabs">
    <view v-for="(item, index) in tabList" :key="index" class="tabs_list" @tap="setTabs(item,index)" :style="'color:' + (active == index ?colors :'') + ';font-weight:' + (active == index ?'bold' : '500')">
      {{item.name}}
      <view class="active" :style="'background:' + colors" v-if="active == index"></view>
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
    tabList: {
      type: Array
    },
    active: {
      type: Number,
      default: 0
    }
  },
  methods: {
    setTabs(item,index) {
      this.$emit('setTabs',item,index);
    }
  }
};
</script>
<style scoped lang="scss">
.tabs{
  height: 80upx;
  line-height: 80upx;
  display: flex;
  justify-content: flex-start;
  align-items: center;
  background-color: #fff;
}
.tabs_list{
  flex: 1;
  text-align: center;
  font-size: 28upx;
  position: relative;
  color: #333;
}
.active{
  font-weight: bold;
  color: #DD4F42;
  width: 50upx;
  height: 5upx;
  border-radius: 20upx;
  background-color: #DD4F42;
  position: absolute;
  bottom: 10upx;
  left: 50%;
  transform: translateX(-50%);
  transition: all 0.3s;
}
</style>