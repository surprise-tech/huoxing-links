<script lang="ts" setup>
import { ref } from 'vue'
import Register from '@/views/auth/register.vue'

// 是否登录/注册
const loginFlag = ref(true)
</script>
<template>
  <div class="login-warp">
    <div class="login-right">
      <register v-model:visible="loginFlag" :close_btn="true"></register>
    </div>
  </div>
</template>

<style lang="scss" scoped>
.login-warp {
  position: relative;
  overflow: hidden;
  min-width: 370px;
  height: 100vh;
  width: 100%;
  background-image: url('@/assets/login/login-bg.png');
  .login-right {
    position: relative;
    max-width: 100%;
    min-width: 370px;
    width: 800px;
    height: 100vh;
    background-color: #fff;
    float: right;
    @media screen and (max-width: 1280px) {
      width: 100%;
    }
    .login-main {
      width: 500px;
      max-width: 100%;
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      overflow: hidden;
      box-shadow: 0 0 15px 2px rgb(14 25 80 / 10%);
      @media screen and (max-width: 600px) {
        box-shadow: none;
      }
      border-radius: 10px;
      display: flex;
      .right {
        width: 100%;
        padding: 40px;
        background-color: #ffffff;
        .return-icon {
          font-size: 25px;
        }
        h2 {
          letter-spacing: 2px;
          margin-bottom: 50px;
          display: block;
          text-align: left;
          color: #000;
          font-size: 25px;
        }
        :deep(.el-form-item:nth-child(3)) {
          margin-bottom: 90px;
        }
        .login-btn {
          height: 56px;
          line-height: 56px;
          width: 100%;
          border: none;
          background: var(--el-color-primary);
          color: #fff;
          font-size: 18px;
          letter-spacing: 2px;
          cursor: pointer;
          transition: 0.3s all linear;
          margin: 20px auto 0;
          @media screen and (max-width: 700px) {
            height: 46px;
          }
        }
        .login-sign {
          display: flex;
          justify-content: space-between;
        }
      }
    }
    :deep(.register_drawer) {
      padding: 0 50px;
      width: 830px !important;
      @media screen and (max-width: 500px) {
        padding: 0;
      }
      @media screen and (max-width: 1280px) {
        width: 100% !important;
      }
    }
  }
}
// 登录表单样式
:deep(.el-input__wrapper) {
  box-shadow: none;
  height: 48px;
  font-size: 16px;
  background-color: #f6f6f6;
  @media screen and (max-width: 700px) {
    height: 40px;
  }
}
:deep(.avatar) {
  border-radius: 50%;
}

:deep(.el-drawer__header) {
  border: 0;
}
:deep(.el-drawer__title) {
  font-size: 24px;
  font-weight: 700;
}
</style>
