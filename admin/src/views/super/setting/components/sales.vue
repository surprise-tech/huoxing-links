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
  recommend_commission_1: 0,
  recommend_commission_2: 0,
  member_pay_commission_1: 0,
  member_pay_commission_2: 0
})

watch(
  () => props.data,
  () => {
    formData.value.is_give_vip = props.data.is_give_vip
    formData.value.give_vip_id = props.data.give_vip_id
    formData.value.give_vip_days = props.data.give_vip_days
    formData.value.recommend_commission_1 = props.data.recommend_commission_1
    formData.value.recommend_commission_2 = props.data.recommend_commission_2
    formData.value.member_pay_commission_1 = props.data.member_pay_commission_1
    formData.value.member_pay_commission_2 = props.data.member_pay_commission_2
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
      <div>
        <div class="margin-b-10" style="margin-top: 20px">一级邀请注册佣金</div>
        <el-input-number style="width: 100%" v-model="formData.recommend_commission_1" :min="0" />
      </div>
      <div>
        <div class="margin-b-10" style="margin-top: 20px">二级邀请注册佣金</div>
        <el-input-number style="width: 100%" v-model="formData.recommend_commission_2" :min="0" />
      </div>
      <div>
        <div class="margin-b-10" style="margin-top: 20px">一级分销（%）</div>
        <el-input-number style="width: 100%" v-model="formData.member_pay_commission_1" />
      </div>
      <div>
        <div class="margin-b-10" style="margin-top: 20px">二级分销（%）</div>
        <el-input-number style="width: 100%" v-model="formData.member_pay_commission_2" />
      </div>
      <div class="m-b-20 text-center" style="margin-top: 60px">
        <el-button type="primary" size="large" @click="clickSubmit">保存</el-button>
      </div>
    </div>
  </el-card>
</template>

<style scoped lang="scss"></style>
