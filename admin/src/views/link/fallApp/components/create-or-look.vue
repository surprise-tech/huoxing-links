<script setup lang="ts">
import type { FormInstance, FormRules } from 'element-plus'
import { userStore } from '@/stores'
import useForm from '@/hooks/form'
import { ref } from 'vue'

const formRef = ref<FormInstance>()
const { formData, formLoading } = useForm({
  formRef,
  defVal(detail) {
    return {
      name: detail?.name, // 名称
      app_id: detail?.app_id, // appid
      secret: detail?.secret, // secret
      url: detail?.url, // secret
      is_pre_min: detail?.is_pre_min ? detail.is_pre_min : 0,
      type: detail?.type, // 类型
      is_enable: detail?.is_enable ? detail.is_enable : 0
    }
  }
})
const store = userStore
const isAdmin = ref(false)
if (store.$state.userInfo.type == 3) {
  isAdmin.value = true
}
const appType = [
  { name: '落地小程序', value: 1 },
  { name: '自有小程序', value: 2 }
]

// 表单校验
const rules: FormRules = {
  name: [{ required: true, message: '请输入小程序名称', trigger: 'blur' }],
  app_id: [{ required: true, message: '请输入appid', trigger: 'blur' }],
  secret: [{ required: true, message: '请输入secret', trigger: 'blur' }],
  url: [{ required: true, message: '请输入页面路径', trigger: 'blur' }],
  type: [{ required: true, message: '请选择类型', trigger: 'blur' }]
}
</script>

<template>
  <el-form :model="formData" :rules="rules" ref="formRef" v-loading="formLoading" label-position="top">
    <el-form-item prop="name" label="小程序名称">
      <el-input v-model="formData.name"></el-input>
    </el-form-item>
    <el-form-item prop="app_id" label="小程序APPID">
      <el-input v-model="formData.app_id" placeholder="请输入小程序APPID，以wx开头"></el-input>
    </el-form-item>
    <el-form-item prop="secret" label="小程序AppSecret">
      <el-input v-model="formData.secret"></el-input>
    </el-form-item>
    <el-form-item prop="type" label="类型">
      <el-select v-model="formData.type" placeholder="选择类型">
        <el-option v-for="item in appType" :key="item.value" :label="item.name" :value="item.value"></el-option>
      </el-select>
    </el-form-item>
    <el-form-item prop="url" label="页面路径">
      <el-input v-model="formData.url"></el-input>
    </el-form-item>
    <el-form-item v-if="isAdmin" prop="is_pre_min" label="是否平台小程序池">
      <el-radio-group v-model="formData.is_pre_min">
        <el-radio :value="1" :label="1">是</el-radio>
        <el-radio :value="0" :label="0">否</el-radio>
      </el-radio-group>
    </el-form-item>
    <el-form-item prop="is_pre_min" label="是否禁用小程序">
      <el-radio-group v-model="formData.is_enable">
        <el-radio :value="0" :label="0">是</el-radio>
        <el-radio :value="1" :label="1">否</el-radio>
      </el-radio-group>
    </el-form-item>
  </el-form>
</template>

<style scoped lang="scss"></style>
