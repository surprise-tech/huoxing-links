<script setup lang="ts">
import App from '@/views/link/addLink/components/app/index.vue'
import WX from '@/views/link/addLink/components/wx/index.vue'
import KingDoc from '@/views/link/addLink/components/kingDoc/index.vue'
import QR from '@/views/link/addLink/components/qr/index.vue'
import FirmWx from '@/views/link/addLink/components/firmWx/index.vue'
import { onMounted, ref } from 'vue'
import xcx from '@/assets/link/xcx.png'
import jinshan from '@/assets/link/jinshan.png'
import caoliao from '@/assets/link/caoliao.png'
import qywx from '@/assets/link/qywx.png'
import wx from '@/assets/link/wx.png'
import { userStore } from '@/stores'

const tabsList = [
  { id: 5, name: '跳转到微信二维码', icon: wx },
  { id: 2, name: '跳转到金山文档', icon: jinshan },
  { id: 3, name: '跳转到草料二维码', icon: caoliao },
  { id: 4, name: '跳转到企业微信', icon: qywx },
  { id: 1, name: '跳转到小程序', icon: xcx }
]
const user = userStore
// tabs点击事件
const type = ref(5)
const tabsChange = (id: any) => {
  type.value = id
}
onMounted(() => {
  userStore.refreshUserInfo()
})
</script>

<template>
  <el-card class="margin-b-20">
    <div class="top-box">
      <div
        class="tab-box"
        :class="{ active: type === item.id }"
        @click="tabsChange(item.id)"
        v-for="item in tabsList"
        :key="item.id"
      >
        <img class="margin-right-20" style="height: 40px; width: 40px" :src="item.icon" alt="" />
        <span>{{ item.name }}</span>
      </div>
    </div>
  </el-card>
  <div class="text-center m-b-20 text-main">剩余创建个数:{{ user.userInfo.link_amount }}个</div>
  <el-card>
    <!--  链接类型 1-跳转小程序 2-跳转金山文档 3-跳转草料二维码 4-跳转企业微信 5-跳转落地小程序  -->
    <app v-if="type === 1" :type="type"></app>
    <king-doc v-if="type === 2" :type="type"></king-doc>
    <q-r v-if="type === 3" :type="type"></q-r>
    <firm-wx v-if="type === 4" :type="type"></firm-wx>
    <w-x v-if="type === 5" :type="type"></w-x>
  </el-card>
</template>

<style scoped lang="scss">
.top-box {
  display: flex;
  justify-content: space-between;
  flex-wrap: wrap;
}
.tab-box {
  padding: 20px;
  display: flex;
  flex: 1;
  margin: 5px;
  align-items: center;
  font-size: 16px;
  min-width: 230px;
  height: 76px;
  background-color: #f9faff;
  cursor: pointer;
}
.active {
  background-color: #2b6bff;
  color: #fff;
}
</style>
