<template>
  <div @click="requestPay">
    <slot />
  </div>
  <el-dialog v-model="payVisible" append-to-body title="" width="400px">
    <div class="pay-warp" v-loading="loading">
      <div style="font-size: 20px; padding-bottom: 15px">
        订单金额：<span class="price">￥{{ payJson?.amount }}</span>
      </div>
      <qrcode-vue :value="payJson?.code_url" :size="200" level="H" />
      <el-radio-group v-model="vip_type" v-if="props.type === 'pay-vip'">
        <el-radio label="1"><span :class="{ active: vip_type === '1' }">立即生效</span></el-radio>
        <el-radio label="2"><span :class="{ active: vip_type === '2' }">顺延生效</span></el-radio>
      </el-radio-group>
      <div class="tips">请使用微信扫码支付，支付成功后请刷新页面</div>
    </div>
  </el-dialog>
</template>
<script setup lang="ts">
import { ref, watch } from 'vue'
import { ApiPayVip, ApiRechargeCredit } from '@/api/pay'
import QrcodeVue from 'qrcode.vue'
const props = defineProps<{
  type: string
  info: any
}>()
const vip_type = ref('1')
const payVisible = ref(false)
const loading = ref(false)
const payJson = ref<any>()
const requestPay = (pay?: any) => {
  if (props.type === 'pay-vip') {
    payVisible.value = true
    loading.value = true
    ApiPayVip(props.info.id, vip_type.value)
      .then((res) => {
        payJson.value = res
      })
      .finally(() => {
        loading.value = false
      })
  } else if (props.type === 'pay-agent') {
    payVisible.value = true
    loading.value = true
    payJson.value = pay
    loading.value = false
  } else if (props.type === 'pay-credit') {
    if (props.info > 0) {
      payVisible.value = true
      loading.value = true
      ApiRechargeCredit(props.info)
        .then((res) => {
          payJson.value = res
        })
        .finally(() => {
          loading.value = false
        })
    }
  }
}

watch(
  () => vip_type.value,
  () => {
    requestPay()
  }
)

defineExpose({
  requestPay
})
</script>
<style scoped lang="scss">
.pay-warp {
  transform: translateY(-20px);
  text-align: center;
  .price {
    font-size: 38px;
    color: #ff0000;
    padding-bottom: 15px;
  }

  img {
    width: 200px;
    height: 200px;
    max-width: 100%;
  }

  .active {
    color: var(--el-color-primary-light-1);
  }
}
</style>
