<script setup lang="ts">
import { ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import usePropData from '@/hooks/propData'
import { configStore } from '@/stores'
import { useWindowSize } from '@vueuse/core'
import imageCaptcha from '@/utils/imageCaptcha'
import { ApiGetSmsCaptcha } from '@/api/comment'
import { ElMessage, type FormInstance } from 'element-plus'
import { validateForm } from '@/utils'
import { ApiRegister } from '@/api/user'
import PayButton from '@/views/home/components/PayButton.vue'
const emits = defineEmits(['update:visible'])
const props = defineProps<{
  visible: boolean
  close_btn: boolean
}>()
const { width: windowWidth } = useWindowSize()
const visible = usePropData(() => props.visible, false)
const closeDrawBtn = usePropData(() => props.close_btn, false)
const formRef = ref<FormInstance>()
const formLoading = ref(false)
const formData = ref({
  phone: '',
  password: '',
  confirmPassword: '',
  authCode: '',
  agent: ''
})
const regexTxt = ref(/^\w+([.-]?\w+)*@\w+([.-]?\w+)*(\.\w{2,3})+$/)
const msgTxt = ref('邮箱')
if (configStore.verify_code_is_open === 1) {
  regexTxt.value = configStore.code_mode === 1 ? /^1\d{10}$/ : /^\w+([.-]?\w+)*@\w+([.-]?\w+)*(\.\w{2,3})+$/
  msgTxt.value = configStore.code_mode === 1 ? '手机号' : '邮箱'
}

const rules = {
  phone: [
    { required: true, message: `请输入${msgTxt.value}`, trigger: 'blur' },
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
  imageCaptcha.show(function (info) {
    imageCaptcha.hide()
    ApiGetSmsCaptcha({
      tel: formData.value.phone,
      captcha: info.code,
      key: info.key
    }).then(() => {
      btnDisabled.value = true
      ElMessage.success('发送成功')
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

const route = useRoute()
const router = useRouter()
const payBtnRef = ref()

// 点击注册
const clickRegister = () => {
  validateForm(formRef.value).then(() => {
    formLoading.value = true
    ApiRegister({
      username: formData.value.phone,
      captcha: formData.value.authCode,
      password: formData.value.password,
      password_confirmation: formData.value.confirmPassword,
      agent_id: formData.value.agent,
      referral_code: route.query.code as string
    })
      .then(() => {
        ElMessage.success('注册成功，请登录')
        localStorage.setItem('register', 'register')
        visible.value = false
        router.push('/login')
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
      title="注册账号"
      :show-close="!closeDrawBtn"
      @close="emits('update:visible', false)"
    >
      <el-form
        ref="formRef"
        :model="formData"
        :rules="rules"
        label-position="left"
        label-width="80px"
        v-loading="formLoading"
      >
        <el-form-item prop="phone" :label="msgTxt">
          <el-input v-model="formData.phone" :placeholder="'请输入' + msgTxt"></el-input>
        </el-form-item>

        <el-form-item prop="password" label="密码">
          <el-input
            v-model="formData.password"
            type="password"
            placeholder="请输入密码"
            show-password
          ></el-input>
        </el-form-item>
        <el-form-item prop="confirmPassword" label="确认密码">
          <el-input
            v-model="formData.confirmPassword"
            type="password"
            placeholder="请再次输入密码"
          ></el-input>
        </el-form-item>

        <el-form-item v-if="configStore.verify_code_is_open === 1" prop="authCode" label="验证码">
          <el-input v-model="formData.authCode" placeholder="请输入验证码">
            <template v-slot:suffix>
              <el-button
                class="code-btn"
                :disabled="btnDisabled"
                type="primary"
                @click.stop="sendAuthCode($event)"
              >
                发送验证码
              </el-button>
            </template>
          </el-input>
        </el-form-item>
      </el-form>
      <el-button
        class="sign-btn"
        v-loading="formLoading"
        type="primary"
      >
        立即注册
      </el-button>
      <div class="reg-footer">
        <el-button type="primary" link @click="router.push('/login')">已有账号，去登录！</el-button>
      </div>
    </el-drawer>
    <PayButton ref="payBtnRef" type="pay-agent" :info="{}" />
  </div>
</template>

<style scoped lang="scss">
:deep(.el-form-item) {
  align-items: center;
}

.code-btn {
  width: 110px;
}

// 选中状态
.el-radio.is-bordered.is-checked {
  border-color: transparent;
  background-color: var(--el-color-primary);
}

// 注册按钮
.sign-btn {
  margin-top: 15px;
  width: 100%;
  height: 40px;
}

.reg-footer {
  display: flex;
  justify-content: space-between;
  margin-top: 15px;

  .des_text {
    text-align: right;
    font-size: 12px;
    color: #333;
  }
}
</style>
