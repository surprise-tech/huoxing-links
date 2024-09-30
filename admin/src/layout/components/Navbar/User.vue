<template>
  <div class="avatar-wrapper">
    <el-space>
      <el-button type="text" size="small" @click="payCreditVisible = true">联系客服</el-button>
      <el-dropdown>
        <span class="el-dropdown-link">
          <div class="flex">
            <span class="username">{{ userStore.userInfo.username }}</span>
            <div style="margin-top: 3px; margin-left: -4px">
              <el-icon class="el-icon--right">
                <arrow-down />
              </el-icon>
            </div>
          </div>
        </span>
        <template #dropdown>
          <el-dropdown-menu>
            <el-dropdown-item> <span class="logout" @click="dialogVisible = true">修改密码</span></el-dropdown-item>
            <el-dropdown-item> <span class="logout" @click="logout">退出</span></el-dropdown-item>
          </el-dropdown-menu>
        </template>
      </el-dropdown>
    </el-space>
  </div>
  <el-dialog title="修改密码" append-to-body v-model="dialogVisible" width="400px">
    <el-form ref="formRef" :disabled="formLoading" :model="formData" :rules="rules" label-width="80px">
      <el-form-item label="新密码" prop="password">
        <el-input v-model="formData.password" placeholder="请输入新密码" show-password></el-input>
      </el-form-item>
      <el-form-item label="确认密码" prop="password_confirmation">
        <el-input v-model="formData.password_confirmation" placeholder="请输入确认密码" show-password></el-input>
      </el-form-item>
      <el-form-item>
        <el-button type="primary" @click="clickSubmit" :loading="formLoading">确定</el-button>
      </el-form-item>
    </el-form>
  </el-dialog>
  <el-dialog v-model="payCreditVisible" title="联系客服" width="500px">
    <div style="margin: 0 auto; width: 300px; height: 300px">
      <oss-image
        :paths="
          userStore.userInfo && userStore.userInfo.parent && userStore.userInfo.parent.wechat_qr
            ? [userStore.userInfo && userStore.userInfo.parent && userStore.userInfo.parent.wechat_qr]
            : []
        "
        :width="300"
        :height="300"
      ></oss-image>
    </div>
  </el-dialog>
</template>

<script setup lang="ts">
import { userStore } from '@/stores'
import { provide, ref } from 'vue'
import { ArrowDown } from '@element-plus/icons-vue'
import { ElMessage, type FormInstance } from 'element-plus'
import { validateForm } from '@/utils'
import { ApiChangePassword } from '@/api/user'
import OssImage from '@/components/OssImage.vue'
const dialogVisible = ref(false)
const payCreditVisible = ref(false)
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
const logout = () => {
  userStore.logout().then(() => {
    // 刷新页面
  window.location.reload()
  })
}

provide('logout', logout)
</script>

<style lang="scss" scoped>
.avatar-wrapper {
  display: flex;
  align-items: center;
  white-space: nowrap;
  cursor: pointer;
  padding: 0 8px;
  font-size: 14px;
  color: var(--theme-header-text-color);
  .username {
    margin-right: 6px;
    color: var(--el-color-primary);
  }
}
</style>
