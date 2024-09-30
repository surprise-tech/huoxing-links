<template>
  <div>
    <div style="padding-bottom: 20px; text-align: center">
      当前可提现金额：{{ data_get(userStore.userInfo, 'commission', 0) }}
    </div>
    <el-form :model="formData" label-width="100px">
      <el-form-item label="提现金额" prop="amount">
        <el-input-number
          v-model="formData.amount"
          :min="0"
          :max="data_get(userStore.userInfo, 'commission', 0)"
          placeholder="请输入提现金额"
        />
      </el-form-item>
      <el-form-item label="支付宝姓名" prop="payee.name">
        <el-input v-model="formData.payee.name" placeholder="请输入真实的支付宝姓名" />
      </el-form-item>
      <el-form-item label="支付宝账号" prop="payee.account">
        <el-input v-model="formData.payee.account" placeholder="请输入真实的支付宝账号" />
      </el-form-item>
      <el-form-item>
        <el-button type="primary" @click="clickWithdraw">立即提现</el-button>
      </el-form-item>
    </el-form>
  </div>
</template>
<script setup lang="ts">
import { data_get } from '@/utils'
import { userStore } from '@/stores'
import { ApiApplyWithdraw } from '@/api/pay'
import { ref } from 'vue'
import { ElMessage } from 'element-plus'

const formData = ref<any>({
  amount: 0,
  payee: {
    name: '',
    account: ''
  }
})

const clickWithdraw = () => {
  ApiApplyWithdraw(formData.value).then(() => {
    ElMessage.success('提现申请成功')
    window.location.reload()
  })
}
</script>
