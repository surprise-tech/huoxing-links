<script setup lang="ts">
import type { FormInstance, FormRules } from 'element-plus'
import useForm from '@/hooks/form'
import { ref } from 'vue'

const formRef = ref<FormInstance>()
const { formData, formLoading } = useForm({
  formRef,
  defVal(detail) {
    return {
      name: detail?.name, // 名称
      sort: detail?.sort, // 排序
      type: detail?.type, // 排序
    }
  }
})
// 表单校验
const rules: FormRules = {
  name: [{ required: true, message: '请输入名称', trigger: 'blur' }],
  sort: [{ required: true, message: '请输入排序', trigger: 'blur' }],
  type: [{ required: true, message: '请输入排序', trigger: 'blur' }]
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
    <el-form-item prop="name" label="名称" style="width: 370px">
      <el-input v-model="formData.name" placeholder="输入名称"></el-input>
    </el-form-item>
    <el-form-item prop="sort" label="排序" style="width: 370px">
      <el-input v-model="formData.sort" placeholder="0"></el-input>
    </el-form-item>
    <el-form-item prop="type" label="类型" style="width: 370px">
      <el-radio-group v-model="formData.type">
        <el-radio label="1">系统</el-radio>
        <el-radio label="0">其他</el-radio>
      </el-radio-group>
    </el-form-item>
  </el-form>
</template>

<style scoped lang="scss"></style>
