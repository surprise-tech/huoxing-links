<script setup lang="ts">
import useUserStore from '@/stores/modules/user'

// 获取弹窗
const store = useUserStore()
</script>

<template>
  <div>
    <el-dialog v-model="store.dialogVisible" width="800" :show-close="false" align-center>
      <div class="dialog_style">
        <div class="right_box">
          <div class="top_img">
            <img class="init_img" src="@/assets/initImg/wechat_qr.jpg" alt="" />
          </div>
          <div class="bottom_name">查看小程序演示</div>
        </div>
        <div class="right_box">
          <div class="top_img">
            <img class="init_img" src="@/assets/initImg/wechat_qr.jpg" alt="" />
          </div>
          <div class="bottom_name">扫码关注公众号</div>
        </div>
        <div class="right_box">
          <div class="top_img">
            <img class="init_img" src="@/assets/initImg/wechat_qr.jpg" alt="" />
          </div>
          <div class="bottom_name">扫码添加客服</div>
        </div>
      </div>
    </el-dialog>
  </div>
</template>

<style scoped lang="scss">
:deep(.el-dialog) {
  border-radius: 10px;
}
:deep(.el-dialog__header) {
  display: none;
}
:deep(.el-dialog__body) {
  background: url('@/assets/initImg/dialog_bc.png') no-repeat;
  background-size: 100% 100%;
}
.dialog_style {
  display: flex;
  justify-content: space-evenly;
  align-items: center;
  width: 100%;
  height: 300px;
  .right_box {
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 200px;
    height: 250px;
    background-color: #fff;
    border-radius: 10px;
    .top_img {
      width: 200px;
      height: 200px;
      border-radius: 10px;
    }
    .bottom_name {
      display: flex;
      justify-content: center;
      align-items: center;
      width: 200px;
      height: 50px;
      font-size: 16px;
      font-weight: bold;
    }
  }
}
.init_img {
  width: 100%;
}
</style>
