<script setup lang="ts">
import { userStore } from '@/stores'
import { inject, reactive, ref } from 'vue'
import { View, Hide } from '@element-plus/icons-vue'
import { ApiChangePassword } from '@/api/user'
import { ElMessage } from 'element-plus'
import type { FormRules } from 'element-plus'

const emits = defineEmits(['update:visible'])
defineProps<{
  visible: boolean
}>()

const logout = inject('logout') as () => void

const store = userStore
const isShowPassword = ref(false)
const isShowUpdate = ref(false)
const showPassword = () => {
  isShowPassword.value = !isShowPassword.value
}
const showPasswordUpdate = () => {
  isShowUpdate.value = !isShowUpdate.value
}

const rules: FormRules = {
  password: [
    { required: true, message: '请输入密码', trigger: 'blur' },
    { min: 6, message: '密码长度不能小于6位', trigger: 'blur' }
  ],
  password_confirmation: [
    { required: true, message: '请输入确认密码', trigger: 'blur' },
    { min: 6, message: '密码长度不能小于6位', trigger: 'blur' },
    {
      validator: (rule, value) => {
        if (value !== formData.password) {
          return new Error('两次输入密码不一致')
        }
      }
    }
  ]
}

// 修改密码
const formData = reactive({
  password: '',
  password_confirmation: ''
})
const save_account = async () => {
  await ApiChangePassword(formData).then(() => {
    ElMessage.success('修改成功')
    emits('update:visible', false)
    logout()
  })
}
</script>

<template>
  <el-form :model="formData" :rules="rules" label-position="right" label-width="80px">
    <el-form-item label="用户名">
      <el-input v-model="store.$state.userInfo.username" disabled style="width: 300px"></el-input>
    </el-form-item>
    <el-form-item label="密码" prop="password">
      <el-input
        v-model="formData.password"
        :type="isShowPassword ? 'text' : 'password'"
        style="width: 300px"
      >
        <template #suffix>
          <el-icon @click="showPassword" style="cursor: pointer">
            <View v-if="isShowPassword" />
            <Hide v-else />
          </el-icon>
        </template>
      </el-input>
    </el-form-item>
    <el-form-item label="确认密码" prop="password_confirmation">
      <el-input
        v-model="formData.password_confirmation"
        :type="isShowUpdate ? 'text' : 'password'"
        style="width: 300px"
      >
        <template #suffix>
          <el-icon @click="showPasswordUpdate" style="cursor: pointer">
            <View v-if="isShowUpdate" />
            <Hide v-else />
          </el-icon>
        </template>
      </el-input>
    </el-form-item>
    <el-form-item>
      <el-button @click="emits('update:visible', false)">取消</el-button>
      <el-button type="primary" @click="save_account">确定</el-button>
    </el-form-item>
  </el-form>
</template>

<style scoped lang="scss"></style>
