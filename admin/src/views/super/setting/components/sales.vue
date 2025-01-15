<script setup lang="ts">
import { ApiVipList } from '@/api/super'
import { onMounted, ref, watch } from 'vue'
import { ApiSaveSet } from '@/api/comment'
import { ElMessage } from 'element-plus'
import { configStore } from '@/stores'

const props = defineProps<{
  data: any
}>()
const formData = ref({
  is_give_vip: false,
  give_vip_id: undefined,
  give_vip_days: 0,
})

watch(
  () => props.data,
  () => {
    formData.value.is_give_vip = props.data.is_give_vip
    formData.value.give_vip_id = props.data.give_vip_id
    formData.value.give_vip_days = props.data.give_vip_days
  },
  {
    deep: true,
    immediate: true
  }
)

const vipPackages = ref<any>([])
const clickSubmit = () => {
  ApiSaveSet(formData.value).then(() => {
    ElMessage.success('保存成功')
    configStore.refreshConfig()
  })
}
onMounted(() => {
  ApiVipList().then((res: any) => {
    vipPackages.value = vipPackages.value.concat(res)
  })
})
</script>

<template>
  <el-card>
    <div style="width: 500px">
      <div>
        <div class="margin-b-10" style="margin-top: 20px">是否开启赠送会员</div>
        <el-switch style="width: 100%" v-model="formData.is_give_vip"></el-switch>
      </div>
      <div>
        <div class="margin-b-10" style="margin-top: 20px">注册赠送会员</div>

        <el-select v-model="formData.give_vip_id" placeholder="注册赠送会员" style="width: 100%">
          <el-option v-for="vip in vipPackages" :key="vip.id" :label="vip.name" :value="vip.id" />
        </el-select>
      </div>
      <div>
        <div class="margin-b-10" style="margin-top: 20px">注册赠送天数</div>
        <el-input-number style="width: 100%" v-model="formData.give_vip_days" :min="1" :max="365" />
      </div>
      <div class="m-b-20 text-center" style="margin-top: 60px">
        <el-button type="primary" size="large" @click="clickSubmit">保存</el-button>
      </div>
    </div>
  </el-card>
</template>

<style scoped lang="scss"></style>
