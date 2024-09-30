<template>
  <div class="bgw-box" style="min-height: 166px">
    <div class="right-h margin-b-20">
      <img class="margin-right-5" src="@/assets/home/vip-icon.png" alt="" />
      <span class="title">账号信息</span>
    </div>
    <div style="padding-left: 15px; display: flex; align-items: center">
      账号：{{ userStore.userInfo.username }}
      <el-button text type="primary" @click="dialogVisible = true">修改密码</el-button>
    </div>

    <el-dialog title="修改密码" append-to-body v-model="dialogVisible" width="400px">
      <el-form
        ref="formRef"
        :disabled="formLoading"
        :model="formData"
        :rules="rules"
        label-width="80px"
      >
        <el-form-item label="新密码" prop="password">
          <el-input v-model="formData.password" placeholder="请输入新密码" show-password></el-input>
        </el-form-item>
        <el-form-item label="确认密码" prop="password_confirmation">
          <el-input
            v-model="formData.password_confirmation"
            placeholder="请输入确认密码"
            show-password
          ></el-input>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="clickSubmit" :loading="formLoading">确定</el-button>
        </el-form-item>
      </el-form>
    </el-dialog>
  </div>

</template>

<style scoped lang="scss"></style>
<script setup lang="ts">
import { ref } from 'vue'
import { userStore } from '@/stores'
import { data_get, setClipboard, validateForm } from '@/utils'
import { ElMessage, type FormInstance } from 'element-plus'
import { ApiChangePassword } from '@/api/user'
const dialogVisible = ref(false)
const formData = ref<any>({})
const formRef = ref<FormInstance>()
const rules = {
  password: [
    { required: true, message: '请输入密码', trigger: 'blur' },
    { min: 6, max: 20, message: '长度在 6 到 20 个字符', trigger: 'blur' }
  ],
  password_confirmation: [
    { required: true, message: '请输入确认密码', trigger: 'blur' },
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
  ]
}
const formLoading = ref(false)
const clickSubmit = () => {
  console.log(formRef.value)
  validateForm(formRef.value).then(() => {
    formLoading.value = true
    ApiChangePassword(formData.value)
      .then(() => {
        ElMessage.success('修改成功,下次登录时生效!')
      })
      .finally(() => {
        formLoading.value = false
        dialogVisible.value = false
      })
  })
}
// 复制邀请链接
const copyCode = () => {
  const root = window.location.origin
  const code = data_get(userStore.userInfo, 'referral_code', '')
  var path = window.location.pathname
  setClipboard(root + path + '#/register?code=' + code)
}
</script>
