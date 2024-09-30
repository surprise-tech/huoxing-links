<script setup lang="ts">
import type { FormInstance, FormRules } from 'element-plus'
import useForm from '@/hooks/form'
import { onMounted, ref } from 'vue'
import { ApiAgentList, ApiLevelList, ApiVipList, getAgentTree } from '@/api/super'

const formRef = ref<FormInstance>()
const { formData, formLoading } = useForm({
  formRef,
  defVal(detail) {
    return {
      username: detail?.username,
      password: detail?.password,
      agent_id: detail?.agent_id,
      level_id: detail?.level_id,
      parent_id: detail?.parent_id,
      type: 2
    }
  }
})
// 表单校验
const rules: FormRules = {
  username: [{ required: true, message: '请输入手机号码', trigger: 'blur' }],
  password: [{ required: true, message: '请输入密码', trigger: 'blur' }]
}

// 获取套餐列表
const vipList = ref()
const getVipList = async () => {
  vipList.value = await ApiAgentList()
}

// 获取级别列表
const levelList = ref()
const getLevelList = async () => {
  levelList.value = await ApiLevelList()
}
const treeData = ref()
const getTreeData = async () => {
  treeData.value = await getAgentTree()
}

onMounted(() => {
  getVipList()
  getLevelList()
  getTreeData()
})
</script>

<template>
  <el-form
    :model="formData"
    :rules="rules"
    ref="formRef"
    v-loading="formLoading"
    label-width="120px"
  >
    <el-form-item prop="username" label="手机号码">
      <el-input v-model="formData.username" style="width: 350px"></el-input>
    </el-form-item>
    <el-form-item prop="password" label="密码">
      <el-input v-model="formData.password" style="width: 350px"></el-input>
    </el-form-item>
    <el-form-item prop="agent_id" label="套餐">
      <el-select v-model="formData.agent_id" clearable placeholder="请选择">
        <el-option
          v-for="item in vipList"
          :key="item.id"
          :label="item.name"
          :value="item.id"
        ></el-option>
      </el-select>
    </el-form-item>
    <!--<el-form-item prop="level_id" label="等级">
      <el-select v-model="formData.level_id" clearable placeholder="请选择">
        <el-option
          v-for="item in levelList"
          :key="item.id"
          :label="item.name"
          :value="item.id"
        ></el-option>
      </el-select>
    </el-form-item>-->
    <el-form-item prop="parent_id" label="上级">
      <el-select
        v-model="formData.parent_id"
        clearable
        style="width: 80%"
        popper-class="tree-select"
        placeholder="请选择"
      >
        <el-option key="0" value="0" label="顶级" />
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
  </el-form>
</template>

<style scoped lang="scss">
.tree-select {
  padding: 5px 2px;
}

.tree-select .el-tree {
  max-height: 200px;
}

.tree-select .el-select-dropdown__item {
  padding: 0 5px;
  height: 30px;
  line-height: 30px;
  border-radius: 5px;
}

.tree-select .el-select-dropdown__item.hover {
  background-color: #e6f7ff;
}

.tree-select .el-tree .el-tree-node__content:hover {
  background: #ffffff;
}

.tree-select .el-tree .el-tree-node:focus > .el-tree-node__content {
  background-color: #ffffff;
}
</style>
