<script setup lang="ts">
import type { FormInstance } from 'element-plus'
import { userStore } from '@/stores'
import useForm from '@/hooks/form'
import { onMounted, ref } from 'vue'
import { getAgentTree } from '@/api/super'
import { Right } from '@element-plus/icons-vue'
const formRef = ref<FormInstance>()
const { formData, formLoading } = useForm({
  formRef,
  defVal(detail) {
    return {
      parent_id: detail?.parent_id || undefined, // 父级
      link_amount: detail?.link_amount || 0, // 链接数量
      yuanma_amount: detail?.yuanma_amount || 0, // 圆码数量
      dy_card_amount: detail?.dy_card_amount || 0, // 卡片数量
      status: detail?.status,
      getParent: detail?.getParent || [],
      username: detail?.username
    }
  }
})
const store = userStore
const treeData = ref()
let query = ref(false)
const getTreeData = async () => {
  if (query.value) return
  query.value = true
  treeData.value = await getAgentTree()
}

onMounted(() => {
  getTreeData()
})
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
  <el-divider></el-divider>
  <el-breadcrumb :separator-icon="Right">
    <el-breadcrumb-item>
      <div class="text-main">
        {{ formData.username }}
      </div>
    </el-breadcrumb-item>
    <el-breadcrumb-item v-for="item in formData.getParent" :key="item.id">
      {{ item.username }}
    </el-breadcrumb-item>
  </el-breadcrumb>
  <el-divider></el-divider>
  <el-form :model="formData" ref="formRef" v-loading="formLoading" label-position="top">
    <el-form-item label="父级" v-if="store.userInfo.type === 3">
      <el-select
        v-model="formData.parent_id"
        clearable
        style="width: 100%"
        popper-class="tree-select"
        placeholder="请选择"
      >
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
