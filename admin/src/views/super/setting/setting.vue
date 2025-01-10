<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { ApiGetSetALL } from '@/api/comment'

import Web from '@/views/super/setting/components/web.vue'
import CodeSetUp from '@/views/super/setting/components/codeSetUp.vue'
import Pay from '@/views/super/setting/components/pay.vue'
import Sales from '@/views/super/setting/components/sales.vue'

const formData = ref<any>({})

onMounted(() => {
  ApiGetSetALL().then((res: any) => {
    formData.value = res
  })
})
const tab = ref('web')
</script>

<template>
  <div>
    <el-card>
      <el-tabs type="card" class="demo-tabs" v-model="tab">
        <el-tab-pane label="网站配置" name="web">
          <Web :data="formData"></Web>
        </el-tab-pane>
        <el-tab-pane label="验证码配置" name="code">
          <CodeSetUp :data="formData"></CodeSetUp>
        </el-tab-pane>
        <el-tab-pane label="支付配置" name="pay">
          <Pay :data="formData"></Pay>
        </el-tab-pane>
        <el-tab-pane label="分销配置" name="sales">
          <Sales :data="formData"></Sales>
        </el-tab-pane>
      </el-tabs>
    </el-card>
  </div>
</template>

<style scoped lang="scss"></style>
