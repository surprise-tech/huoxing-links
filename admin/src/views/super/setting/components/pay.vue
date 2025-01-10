<script setup lang="ts">
import { ref, watch } from 'vue'
import { ApiSaveSet } from '@/api/comment'
import { ElMessage } from 'element-plus'
import { configStore } from '@/stores'

const props = defineProps<{
  data: any
}>()
const webFormData = ref({
  wechat_pay_app_id: '',
  wechat_pay_mch_id: '',
  wechat_pay_secret_key: '',
  wechat_pay_private_cert: '',
  wechat_pay_certificate: ''
})
watch(
  () => props.data,
  () => {
    webFormData.value.wechat_pay_app_id = props.data.wechat_pay_app_id
    webFormData.value.wechat_pay_mch_id = props.data.wechat_pay_mch_id
    webFormData.value.wechat_pay_secret_key = props.data.wechat_pay_secret_key
    webFormData.value.wechat_pay_private_cert = props.data.wechat_pay_private_cert
    webFormData.value.wechat_pay_certificate = props.data.wechat_pay_certificate
  },
  {
    deep: true,
    immediate: true
  }
)
const clickSubmit = () => {
  ApiSaveSet(webFormData.value).then(() => {
    ElMessage.success('保存成功')
    configStore.refreshConfig()
  })
}
</script>

<template>
  <el-card>
    <div style="width: 500px">
      <div>
        <div class="margin-b-10" style="margin-top: 20px">微信商户appId</div>
        <el-input v-model="webFormData.wechat_pay_app_id" placeholder="请输入微信商户appId"></el-input>
      </div>
      <div>
        <div class="margin-b-10" style="margin-top: 20px">微信商户号</div>
        <el-input v-model="webFormData.wechat_pay_mch_id" placeholder="请输入微信商户号"></el-input>
      </div>
      <div>
        <div class="margin-b-10" style="margin-top: 20px">微信支付密钥</div>
        <el-input v-model="webFormData.wechat_pay_secret_key" placeholder="请输入微信支付密钥"></el-input>
      </div>
      <div>
        <div class="margin-b-10" style="margin-top: 20px">微信支付私钥证书</div>
        <el-input v-model="webFormData.wechat_pay_private_cert" placeholder="请输入微信支付私钥证书"></el-input>
      </div>
      <div>
        <div class="margin-b-10" style="margin-top: 20px">微信支付公钥证书</div>
        <el-input v-model="webFormData.wechat_pay_certificate" placeholder="请输入微信支付公钥证书"></el-input>
      </div>
      <div class="m-b-20 text-center" style="margin-top: 60px">
        <el-button type="primary" size="large" @click="clickSubmit">保存</el-button>
      </div>
    </div>
  </el-card>
</template>

<style scoped lang="scss"></style>
