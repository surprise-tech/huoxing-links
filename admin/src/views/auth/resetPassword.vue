<script setup lang="ts">
import { ref } from 'vue'
import usePropData from '@/hooks/propData'
import { ElMessage } from 'element-plus'
import { ApiGetSmsCaptcha } from '@/api/comment'
import imageCaptcha from '@/utils/imageCaptcha'
import { validateForm } from '@/utils'
import { ApiResetPassword } from '@/api/user'
import { configStore } from "@/stores";

const emits = defineEmits(['update:visible'])
const props = defineProps<{
  visible: boolean
}>()
const visible = usePropData(() => props.visible, false)

const formRef = ref()
const formLoading = ref(false)
const formData = ref({
  phone: '',
  password: '',
  confirmPassword: '',
  authCode: '',
  agent: '1'
})

const regexTxt = ref()
regexTxt.value = configStore.code_mode === 1 ? /^1\d{10}$/ : /^\w+([.-]?\w+)*@\w+([.-]?\w+)*(\.\w{2,3})+$/
const msgTxt = ref()
msgTxt.value = configStore.code_mode === 1 ? '手机号' : '邮箱'

const rules = {
  phone: [
    { required: true, message: '请输入手机号', trigger: 'blur' },
    {
      pattern: regexTxt.value,
      message: `请输入正确的${msgTxt.value}`,
      trigger: 'blur'
    }
  ],
  password: [
    { required: true, message: '请输入密码', trigger: 'blur' },
    { min: 6, max: 16, message: '长度在 6 到 16 个字符', trigger: 'blur' }
  ],
  confirmPassword: [
    { required: true, message: '请再次输入密码', trigger: 'blur' },
    {
      validator: (rule: any, value: any, callback: any) => {
        if (value !== formData.value.password) {
          callback(new Error('两次输入密码不一致'))
        } else {
          callback()
        }
      },
      trigger: 'blur'
    }
  ],
  authCode: [{ required: true, message: '请输入验证码', trigger: 'blur' }],
  agent: [{ required: true, message: '请选择代理类型', trigger: 'blur' }]
}

// 点击发送验证码
const btnDisabled = ref(false)
const sendAuthCode = async (e: any) => {
  if (!regexTxt.value.test(formData.value.phone)) {
    return ElMessage.error(`请输入正确的${msgTxt.value}！`)
  }
  const btn = e.target as HTMLElement
  imageCaptcha.show((info: any) => {
    imageCaptcha.hide()
    ApiGetSmsCaptcha({
      tel: formData.value.phone,
      captcha: info.code,
      key: info.key
    }).then(() => {
      btnDisabled.value = true
      const timerFun = function () {
        let time = btn.getAttribute('data-time') as any
        if (time <= 0) {
          time = 60
        }
        time--
        btn.innerHTML = time + '秒后重新获取'
        btn.setAttribute('data-time', time)
        if (time > 0) {
          setTimeout(timerFun, 1000)
        } else {
          btnDisabled.value = false
          btn.innerHTML = '发送验证码'
        }
      }

      timerFun()
    })
  })
}
// 点击修改密码
const clickSubmit = () => {
  validateForm(formRef.value).then(() => {
    formLoading.value = true
    ApiResetPassword({
      username: formData.value.phone,
      password: formData.value.password,
      password_confirmation: formData.value.confirmPassword,
      captcha: formData.value.authCode
    })
      .then(() => {
        ElMessage.success('修改成功，请登录')
        visible.value = false
      })
      .finally(() => {
        formLoading.value = false
      })
  })
}
</script>

<template>
  <div>
    <el-drawer
      v-model="visible"
      class="register_drawer"
      :modal="false"
      title="找回密码"
      @close="emits('update:visible', false)"
    >
      <el-form
        ref="formRef"
        :model="formData"
        :rules="rules"
        label-position="left"
        label-width="100px"
        :disabled="formLoading"
      >
        <el-form-item prop="phone" :label="msgTxt">
          <el-input v-model="formData.phone" :placeholder="'请输入' + msgTxt"></el-input>
        </el-form-item>
        <el-form-item prop="authCode" label="验证码">
          <el-input v-model="formData.authCode" placeholder="请输入验证码">
            <template v-slot:suffix>
              <el-button
                class="code-btn"
                :disabled="btnDisabled"
                type="primary"
                @click="sendAuthCode($event)"
                >发送验证码</el-button
              >
            </template>
          </el-input>
        </el-form-item>
        <el-form-item prop="password" label="设置新密码">
          <el-input
            v-model="formData.password"
            show-password
            type="password"
            placeholder="请输入密码"
          ></el-input>
        </el-form-item>
        <el-form-item prop="confirmPassword" label="确认新密码">
          <el-input
            v-model="formData.confirmPassword"
            show-password
            type="password"
            placeholder="请再次输入密码"
          ></el-input>
        </el-form-item>
      </el-form>
      <el-button class="sign-btn" type="primary" @click="clickSubmit">修改密码</el-button>
    </el-drawer>
  </div>
</template>

<style scoped lang="scss">
:deep(.el-form-item) {
  align-items: center;
}

// 注册按钮
.sign-btn {
  margin-top: 15px;
  width: 100%;
  height: 40px;
}
</style>
