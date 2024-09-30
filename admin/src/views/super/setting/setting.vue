<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { ApiGetSetByType, ApiSaveSetByType } from '@/api/comment'
import { ApiVipList } from '@/api/super'
import { ElMessage } from 'element-plus'

const formData = ref<any>({})

const vipPackages = ref<any>([])

const formLoading = ref(false)

const clickSubmit = () => {
  formLoading.value = true
  ApiSaveSetByType('base', formData.value)
    .then(() => {
      ElMessage.success('保存成功')
    })
    .finally(() => {
      formLoading.value = false
    })
}

onMounted(() => {
  formLoading.value = true
  ApiGetSetByType('base')
    .then((res: any) => {
      formData.value = res
    })
    .finally(() => {
      formLoading.value = false
    })
  ApiVipList().then((res: any) => {
    vipPackages.value = vipPackages.value.concat(res)
  })
})
</script>

<template>
  <el-card header="网站设置" v-loading="formLoading">
    <el-form label-width="200px" :disabled="formLoading">

      <el-form-item label="注册赠送会员">
        <el-select v-model="formData.give_vip_id" placeholder="注册赠送会员">
          <el-option style="width: 200px" v-for="vip in vipPackages" :key="vip.id" :label="vip.name" :value="vip.id" />
        </el-select>
      </el-form-item>
      <el-form-item label="是否开启赠送会员">
        <el-switch style="width: 200px" v-model="formData.is_give_vip" ></el-switch>
      </el-form-item>
      <el-form-item label="注册赠送天数">
        <el-input-number style="width: 200px" v-model="formData.give_vip_days" :min="1" :max="365" />
      </el-form-item>
      <el-form-item label="验证码发送方式">
        <el-select style="width: 200px" v-model="formData.send_code_mode" placeholder="请选择验证码发送方式">
          <el-option  label="短信" :value="1" />
          <el-option  label="邮箱" :value="2" />
        </el-select>
      </el-form-item>
      <el-form-item label="一级邀请注册佣金">
        <el-input-number style="width: 200px" v-model="formData.recommend_commission_1" :min="0"  />
      </el-form-item>
      <el-form-item label="二级邀请注册佣金">
        <el-input-number style="width: 200px" v-model="formData.recommend_commission_2" :min="0"  />
      </el-form-item>
      <el-form-item label="一级分销（%）">
        <el-input-number style="width: 200px" v-model="formData.member_pay_commission_1"  />
      </el-form-item>
      <el-form-item label="二级分销（%）">
        <el-input-number style="width: 200px" v-model="formData.member_pay_commission_2"  />
      </el-form-item>
      <el-form-item>
        <el-button type="primary" style="width: 200px" @click="clickSubmit" :loading="formLoading">保存</el-button>
      </el-form-item>
    </el-form>
  </el-card>
</template>

<style scoped lang="scss"></style>
