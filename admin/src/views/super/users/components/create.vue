<script setup lang="ts">
import type { FormInstance, FormRules } from 'element-plus'
import useForm from '@/hooks/form'
import { onMounted, ref } from 'vue'
import { ApiVipList } from '@/api/super'

const formRef = ref<FormInstance>()
const { formData, formLoading } = useForm({
  formRef,
  defVal(detail) {
    return {
      username: detail?.username,
      password: detail?.password,
      vip_id: detail?.vip_id,
      type: 1
    }
  }
})

// 获取套餐列表
const vipList = ref()
const getVipList = async () => {
  vipList.value = await ApiVipList()
}

// 表单校验
const rules: FormRules = {
  username: [{ required: true, message: '请输入手机号码', trigger: 'blur' }],
  password: [{ required: true, message: '请输入密码', trigger: 'blur' }]
}
onMounted(() => {
  getVipList()
})
</script>

<template>
  <el-form
    :model="formData"
    :rules="rules"
    ref="formRef"
    v-loading="formLoading"
    label-width="120px"
  >
    <el-form-item prop="username" label="手机号码">
      <el-input v-model="formData.username" style="width: 350px"></el-input>
    </el-form-item>
    <el-form-item prop="password" label="密码">
      <el-input v-model="formData.password" style="width: 350px"></el-input>
    </el-form-item>
    <el-form-item prop="vip_id" label="套餐">
      <el-select v-model="formData.vip_id" placeholder="请选择">
        <el-option
          v-for="item in vipList"
          :key="item.id"
          :label="item.name"
          :value="item.id"
        ></el-option>
      </el-select>
    </el-form-item>
  </el-form>
</template>

<style scoped lang="scss"></style>
