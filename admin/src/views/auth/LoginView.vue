<script lang="ts" setup>
import { ref, onMounted } from 'vue'
import { userStore } from '@/stores'
import { configStore } from '@/stores'
import { useRouter } from 'vue-router'
import type { ILoginRequest } from '@/models/user'
import useLoading from '@/hooks/loading'
import type { FormInstance } from 'element-plus'
import { Back } from '@element-plus/icons-vue'
import { validateForm } from '@/utils'
import Register from '@/views/auth/register.vue'
import ResetPassword from '@/views/auth/resetPassword.vue'

const router = useRouter()
// 登录数据
const loginData = ref({} as ILoginRequest)

// 是否登录/注册
const loginFlag = ref(false)

// 找回密码
const resetPsd = ref(false)

// 表单实例
const loginFormRef = ref<FormInstance>()

// 登录菊花转
const loading = useLoading()
// 点击登录
const submit = (formEl?: FormInstance) => {
  validateForm(formEl).then(() => {
    loading.start()
    userStore
      .login(loginData.value, true)
      .then(() => {
        router.push('/home')
      })
      .finally(loading.end)
  })
}

const regJump = () => {
  loginFlag.value = true
  var url = '/register'
  if (router.currentRoute.value.query.code) {
    url = url + '?code=' + router.currentRoute.value.query.code
  }
  router.push(url)
}

onMounted(() => {
  if (router.currentRoute.value.query.code) {
    setTimeout(() => {
      loginFlag.value = true
    }, 100)
  }
  // 已经登录了吗？
  if (userStore.tokenInfo) {
    router.push('/home')
  }
})
</script>
<template>
  <div class="login-warp">
    <div class="login-right">
      <!--    登录页    -->
      <div class="login-main">
        <div class="right">
          <div class="return-icon">
            <el-icon><Back /></el-icon>
          </div>
          <h2 style="color: #000">欢迎登录</h2>
          <el-form ref="loginFormRef" :disabled="loading.status" :model="loginData" @keyup.enter="submit(loginFormRef)">
            <el-form-item prop="username" :rules="[{ required: true, message: '请输入账号', trigger: 'blur' }]">
              <el-input v-model="loginData.username" placeholder="请输入账号" prefix-icon="el-icon-user" />
            </el-form-item>
            <el-form-item prop="password" :rules="[{ required: true, message: '请输入密码', trigger: 'blur' }]">
              <el-input
                show-password
                v-model="loginData.password"
                placeholder="请输入密码"
                prefix-icon="el-icon-lock"
              />
            </el-form-item>
            <el-form-item>
              <el-button class="login-btn" type="primary" @click="submit(loginFormRef)" :loading="loading.status"
                >立即登录</el-button
              >
            </el-form-item>
          </el-form>
          <div class="login-sign">
            <el-button type="primary" link @click="resetPsd = true">找回密码</el-button>
            <el-button type="primary" link @click="regJump">注册账号</el-button>
          </div>
        </div>
      </div>
      <!--    注册页    -->
      <register v-model:visible="loginFlag" :close_btn="false"></register>
      <!--   找回密码   -->
      <reset-password v-model:visible="resetPsd"></reset-password>
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
      padding: 0 60px;
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
