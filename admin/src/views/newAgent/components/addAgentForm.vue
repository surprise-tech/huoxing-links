<script setup lang="ts">
import type { FormInstance, FormRules } from 'element-plus'
import { userStore } from '@/stores'
import useForm from '@/hooks/form'
import { ref } from 'vue'
import { getAgentTree } from '@/api/super'

const formRef = ref<FormInstance>()
const { formData, formLoading } = useForm({
  formRef,
  defVal(detail) {
    return {
      password: detail?.password || '', // 密码
      username: detail?.username || '', // 用户名
      parent_id: detail?.parent_id || undefined, // 父级
      link_amount: detail?.link_amount || 0, // 链接数量
      yuanma_amount: detail?.yuanma_amount || 0, // 圆码数量
      dy_card_amount: detail?.dy_card_amount || 0, // 卡片数量
      status: detail?.status
    }
  }
})
const store = userStore
const treeData = ref()
const getTreeData = async () => {
  treeData.value = await getAgentTree()
}
getTreeData()
const rules: FormRules = {
  username: [{ required: true, message: '请输入用户名', trigger: 'blur' }],
  password: [{ required: true, message: '请输入密码', trigger: 'blur' }]
}
</script>

<template>
  <el-row :gutter="10" class="m-b-20 text-main" style="margin-top: -20px">
    <el-col :xs="24" :sm="8">
      <div>链接剩余个数：{{ store.userInfo && store.userInfo.link_amount }}</div>
    </el-col>
    <el-col :xs="24" :sm="8">
      <div>小圆码剩余个数：{{ store.userInfo && store.userInfo.yuanma_amount }}</div>
    </el-col>
    <el-col :xs="24" :sm="8">
      <div>自动回复剩余个数：{{ store.userInfo && store.userInfo.dy_card_amount }}</div>
    </el-col>
  </el-row>
  <el-form :model="formData" :rules="rules" ref="formRef" v-loading="formLoading" label-position="top">
    <el-form-item label="父级">
      <el-select
        v-model="formData.parent_id"
        clearable
        style="width: 100%"
        popper-class="tree-select"
        placeholder="请选择"
      >
        <!--        <el-option key="0" value="0" label="顶级" />-->
        <el-tree
          :data="treeData"
          node-key="id"
          auto-expand-parent
          :default-checked-keys="[formData.parent_id]"
          :expand-on-click-node="false"
        >
          <template #default="{ data }">
            <el-option style="width: 100%" :key="data.id" :value="data.id" :label="data.label" />
          </template>
        </el-tree>
      </el-select>
    </el-form-item>
    <el-form-item prop="username" label="用户名">
      <el-input v-model="formData.username" placeholder="请输入手机号"></el-input>
    </el-form-item>
    <el-form-item prop="password" label="密码">
      <el-input v-model="formData.password" placeholder="请输入密码"></el-input>
    </el-form-item>
    <el-form-item prop="name" label="链接数量">
      <el-input type="number" v-model="formData.link_amount" placeholder="请输入链接数量"></el-input>
    </el-form-item>
    <el-form-item prop="app_id" label="小圆码数量">
      <el-input type="number" v-model="formData.yuanma_amount" placeholder="请输入小圆码数量"></el-input>
    </el-form-item>
    <el-form-item prop="secret" label="抖音自动回复卡片数量">
      <el-input type="number" v-model="formData.dy_card_amount" placeholder="请输入抖音自动回复卡片数量"></el-input>
    </el-form-item>

    <el-form-item label="账号状态">
      <el-switch
        v-model="formData.status"
        :active-value="true"
        :inactive-value="false"
        active-text="开启"
        inactive-text="禁用"
        inline-prompt
      ></el-switch>
    </el-form-item>
  </el-form>
</template>

<style scoped lang="scss"></style>
