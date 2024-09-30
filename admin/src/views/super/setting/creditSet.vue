<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { ApiGetSetByType, ApiSaveSetByType } from '@/api/comment'
import { ElMessage } from 'element-plus'

const formData = ref<any>({
  credit_sub_agent_yuanma: 0,
  credit_template_code: '',
  credit_notice_less: '',
  credit_notice_tel: ''
})
const formLoading = ref(false)
const clickSubmit = () => {
  formLoading.value = true
  ApiSaveSetByType('credit', formData.value)
    .then(() => {
      ElMessage.success('保存成功')
    })
    .finally(() => {
      formLoading.value = false
    })
}

onMounted(() => {
  formLoading.value = true
  ApiGetSetByType('credit')
    .then((res: any) => {
      formData.value = res
    })
    .finally(() => {
      formLoading.value = false
    })
})
</script>

<template>
  <el-card header="积分设置" v-loading="formLoading">
    <el-form label-width="200px" :disabled="formLoading">
      <el-form-item label="积分不足短信模版">
        <el-input v-model="formData.credit_template_code" style="width: 200px" />
      </el-form-item>
      <el-form-item label="最低积分不足通知">
        <el-input v-model="formData.credit_notice_less" style="width: 200px" />
      </el-form-item>
      <el-form-item label="积分不足通知手机号">
        <el-input v-model="formData.credit_notice_tel" style="width: 200px" />
      </el-form-item>
      <el-divider />
      <el-form-item>
        <el-button type="primary" @click="clickSubmit" :loading="formLoading">保存</el-button>
      </el-form-item>
    </el-form>
  </el-card>
</template>

<style scoped lang="scss"></style>
