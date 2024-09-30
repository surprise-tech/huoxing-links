<script setup lang="ts">
import { ref } from 'vue'

const dialogVisible = ref(false)
</script>

<template>
  <div class="customer">
    <img src="@/assets/header/custom.png" alt="" />
    <el-button link plain @click="dialogVisible = true">在线客服</el-button>
    <el-dialog v-model="dialogVisible" width="360" :show-close="false" align-center>
      <div class="dialog_style">
        <div class="right_box">
          <div class="top_img"></div>
          <div class="bottom_name">扫码添加客服</div>
        </div>
      </div>
    </el-dialog>
  </div>
</template>

<style scoped lang="scss">
.customer {
  margin-right: 20px;
  display: flex;
  align-items: center;
}
:deep(.el-dialog) {
  border-radius: 10px;
}
:deep(.el-dialog__header) {
  display: none;
}
:deep(.el-dialog__body) {
  height: 360px;
  background: url('@/assets/initImg/dialog_bc.png') no-repeat;
  background-size: 100% 100%;
}
.dialog_style {
  display: flex;
  justify-content: center;
  align-items: center;
  width: 100%;
  height: 300px;

  .left_box {
    margin-left: 60px;
    border: 1px solid #000;
    width: 460px;
    height: 300px;
    background-color: #fff;
  }
  .right_box {
    display: flex;
    flex-direction: column;
    align-items: center;
    //margin-left: 40px;
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
</style>
