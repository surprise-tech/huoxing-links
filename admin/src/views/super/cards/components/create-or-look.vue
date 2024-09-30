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
      number: detail?.number, // 数量
      month: detail?.month, // 月
      expiry_time: detail?.expiry_time, // 有效期
      vip_id: detail?.vip_id // 会员套餐
    }
  }
})

const vipList = ref<any>()
const getVipList = async () => {
  vipList.value = await ApiVipList()
}

// 表单校验
const rules: FormRules = {
  number: [{ required: true, message: '请输入数量', trigger: 'blur' }],
  month: [{ required: true, message: '请输入时长', trigger: 'blur' }],
  vip_id: [{ required: true, message: '请选择类型', trigger: 'blur' }],
  expiry_time: [{ required: true, message: '请选择类型', trigger: 'blur' }]
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
    <el-form-item prop="number" label="生成数量">
      <el-input v-model="formData.number" style="width: 350px"></el-input>
    </el-form-item>
    <el-form-item prop="vip_id" label="会员类型">
      <el-select v-model="formData.vip_id" style="width: 350px">
        <el-option
          v-for="item in vipList"
          :key="item.id"
          :label="item.name"
          :value="item.id"
        ></el-option>
      </el-select>
    </el-form-item>
    <el-form-item prop="month" label="会员时长">
      <el-input v-model="formData.month" style="width: 350px">
        <template #suffix>个月</template>
      </el-input>
    </el-form-item>
    <el-form-item prop="expiry_time" label="有效期">
      <el-date-picker
        v-model="formData.expiry_time"
        type="datetime"
        value-format="YYYY-MM-DD HH:mm:ss"
      ></el-date-picker>
    </el-form-item>
  </el-form>
</template>

<style scoped lang="scss"></style>
