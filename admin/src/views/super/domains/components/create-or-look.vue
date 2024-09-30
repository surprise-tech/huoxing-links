<script setup lang="ts">
import type { FormInstance, FormRules } from 'element-plus'
import useForm from '@/hooks/form'
import { ref } from 'vue'

const formRef = ref<FormInstance>()
const { formData, formLoading } = useForm({
  formRef,
  defVal(detail) {
    return {
      title: detail?.title, // 标题名称
      url: detail?.url, // 地址
      enable: detail?.enable ? detail?.enable : false // 启用/禁用
    }
  }
})

// 表单校验
const rules: FormRules = {
  title: [{ required: true, message: '请输入套餐名称', trigger: 'blur' }],
  url: [{ required: true, message: '请输入地址', trigger: 'blur' }]
}
</script>

<template>
  <el-form
    :model="formData"
    :rules="rules"
    ref="formRef"
    v-loading="formLoading"
    label-width="120px"
  >
    <el-form-item prop="title" label="标题">
      <el-input v-model="formData.title" style="width: 700px" placeholder="请输入标题"></el-input>
    </el-form-item>
    <el-form-item prop="url" label="链接">
      <el-input v-model="formData.url" style="width: 700px" placeholder="请输入链接"></el-input>
    </el-form-item>
    <el-form-item label="是否启用">
      <el-switch
        v-model="formData.enable"
        :active-value="true"
        :inactive-value="false"
        active-text="启用"
        inactive-text="禁用"
        inline-prompt
      ></el-switch>
    </el-form-item>
  </el-form>
</template>

<style scoped lang="scss"></style>
