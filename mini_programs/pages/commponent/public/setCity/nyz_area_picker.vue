<template>
<view>
<!--自定义地址选择器-->
<view class="nyz_area_mask" v-if="show == true"></view>
<view :class="'nyz_area_view ' + (show ? 'show':'hide')">
  <view class="nyz_area_view_btns">
    <text class="nyz_area_view_btn_cancle" @tap="handleNYZAreaCancle">取消</text>
    <text class="nyz_area_view_btn_sure" @tap="handleNYZAreaSelect" :data-province="province" :data-city="city" :data-area="area">确定</text>
  </view>
  <picker-view class="nyz_area_pick_view" indicator-style="height: 35px;" @change="handleNYZAreaChange" :value="value">
    <picker-view-column>
      <view v-for="(item, index) in provinces" :key="index" class="nyz_area_colum_view">{{item}}</view>
    </picker-view-column>
    <picker-view-column>
      <view v-for="(item, index) in citys" :key="index" class="nyz_area_colum_view">{{item}}</view>
    </picker-view-column>
    <picker-view-column>
      <view v-for="(item, index) in areas" :key="index" class="nyz_area_colum_view">{{item}}</view>
    </picker-view-column>
  </picker-view>
</view>
</view>
</template>

<script>
var areaTool = require("@/utils/area.js");
var index = [0, 0, 0];
var provinces = areaTool.getProvinces();
var citys = areaTool.getCitys(index[0]);
var areas = areaTool.getAreas(index[0], index[1]);

export default {
  data() {
    return {
      provinces: provinces,
      citys: areaTool.getCitys(index[0]),
      areas: areaTool.getAreas(index[0], index[1]),
      value: [0, 0, 0],
      province: '北京市',
      city: '北京市',
      area: '东城区'
    };
  },

  components: {},
  props: {
    show: {
      //控制area_select显示隐藏
      type: Boolean,
      default: false
    },
    maskShow: {
      //是否显示蒙层
      type: Boolean,
      default: true
    }
  },
  methods: {
    handleNYZAreaChange: function (e) {
      var that = this;
      console.log("e:" + JSON.stringify(e));
      var value = e.detail.value;
      /**
       * 滚动的是省
       * 省改变 市、区都不变
       */

      if (index[0] != value[0]) {
        index = [value[0], 0, 0];
        let selectCitys = areaTool.getCitys(index[0]);
        let selectAreas = areaTool.getAreas(index[0], 0);
        that.setData({
          citys: selectCitys,
          areas: selectAreas,
          value: [index[0], 0, 0],
          province: provinces[index[0]],
          city: selectCitys[0],
          area: selectAreas[0]
        });
      } else if (index[1] != value[1]) {
        /**
         * 市改变了 省不变 区变
         */
        index = [value[0], value[1], 0];
        let selectCitys = areaTool.getCitys(index[0]);
        let selectAreas = areaTool.getAreas(index[0], value[1]);
        that.setData({
          citys: selectCitys,
          areas: selectAreas,
          value: [index[0], index[1], 0],
          province: provinces[index[0]],
          city: selectCitys[index[1]],
          area: selectAreas[0]
        });
      } else if (index[2] != value[2]) {
        /**
         * 区改变了
         */
        index = [value[0], value[1], value[2]];
        let selectCitys = areaTool.getCitys(index[0]);
        let selectAreas = areaTool.getAreas(index[0], value[1]);
        that.setData({
          citys: selectCitys,
          areas: selectAreas,
          value: [index[0], index[1], index[2]],
          province: provinces[index[0]],
          city: selectCitys[index[1]],
          area: selectAreas[index[2]]
        });
      }
    },

    /**
     * 确定按钮的点击事件
     */
    handleNYZAreaSelect: function (e) {
      var myEventDetail = e; // detail对象，提供给事件监听函数
      var myEventOption = {}; // 触发事件的选项
      this.$emit('sureSelectArea', {
        detail: myEventDetail
      }, myEventOption);
    },

    /**
     * 取消按钮的点击事件
     */
    handleNYZAreaCancle: function (e) {
      var that = this;
      console.log("e:" + JSON.stringify(e));
      this.$emit('hideShow', {
        detail: false
      });
    }
  }
};
</script>
<style scoped lang="scss">
.nyz_area_view{
  width: 100%;
  position: fixed;
  bottom:-1000px;
  left: 0px;
  background-color: #fff;
  z-index: 21;
  transition: all 0.3s;
}

.nyz_area_pick_view{
  height: 200px;
  width: 100%;
  
}
.nyz_area_colum_view{
  line-height: 35px;
  text-align: center;
  font-size: 28upx;
}

.hide{
  bottom: -1000upx;
  transition: all 0.3s;
}

.show{
  bottom: 0upx;
  transition: all 0.3s;
}

.nyz_area_view_btns{
  background-color: #fff;
  border-bottom: 1px solid #eeeeee;
  font-size: 30upx;
  padding: 18upx 0upx;
}

.nyz_area_view_btns>text{
  display: inline-block;
  word-spacing: 4upx;
  letter-spacing: 4upx;
}

.nyz_area_view_btn_cancle{
  color: #939393;
  padding-right: 20upx;
  padding-left: 25upx;
}

.nyz_area_view_btn_sure{
  float: right;
  padding-left: 20upx;
  padding-right: 25upx;
}

.nyz_area_mask{
  width: 100%;
  height: 100vh;
  background-color: rgba(8, 8, 8, 0.8);
  position: absolute;
  top: 0upx;
  left: 0upx;
  z-index: 20;
}
</style>