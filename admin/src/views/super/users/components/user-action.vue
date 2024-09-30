<script setup lang="ts">
import type { FormInstance, FormRules } from 'element-plus'
import useForm from '@/hooks/form'
import { onMounted, ref } from 'vue'
import { ApiLevelList, ApiVipList, getAgentTree } from '@/api/super'
import { Warning } from '@element-plus/icons-vue'

defineProps<{
  formFeild: any
}>()
const formRef = ref<FormInstance>()
const { formData, formLoading } = useForm({
  formRef,
  defVal(detail) {
    return {
      credit: 0,
      end_at: detail?.end_at,
      vip_id: detail?.vip_package?.id,
      level_id: detail?.level_id,
      parent_id: detail?.parent?.id,
      user_id: detail?.id
    }
  }
})

console.log('formData:', formData.value)
// 获取套餐列表
const vipList = ref()
const getVipList = async () => {
  vipList.value = await ApiVipList()
}

// 获取级别列表
const levelList = ref()
const getLevelList = async () => {
  levelList.value = await ApiLevelList()
}

const treeData = ref()
const getTreeList = async () => {
  treeData.value = await getAgentTree()
}

// 表单校验
const rules: FormRules = {
  // credit: [{ required: true, message: '请输入积分', trigger: 'blur' }]
}
onMounted(() => {
  getVipList()
  getLevelList()
  getTreeList()
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
    <el-form-item v-if="formFeild == 'credit'" prop="credit" label="积分">
      <el-input v-model="formData.credit" placeholder="请输入积分"></el-input>
    </el-form-item>
    <el-form-item v-else-if="formFeild == 'end_at'" prop="end_at" label="到期时间">
      <el-date-picker
        v-model="formData.end_at"
        type="datetime"
        value-format="YYYY-MM-DD HH:mm:ss"
      ></el-date-picker>
    </el-form-item>
    <el-form-item v-else-if="formFeild == 'vip_id'" prop="vip_id" label="套餐">
      <el-select v-model="formData.vip_id" placeholder="请选择">
        <el-option
          v-for="item in vipList"
          :key="item.id"
          :label="item.name"
          :value="item.id"
        ></el-option>
      </el-select>
      <div class="ptip">
        <el-icon><Warning /></el-icon> 修改套餐后需要修改过期时间！
      </div>
    </el-form-item>
    <el-form-item v-else-if="formFeild == 'level_id'" prop="vip_id" label="代理级别">
      <el-select v-model="formData.level_id" placeholder="请选择">
        <el-option
          v-for="item in levelList"
          :key="item.id"
          :label="item.name"
          :value="item.id"
        ></el-option>
      </el-select>
    </el-form-item>

    <el-form-item v-else-if="formFeild == 'parent_id'" prop="parent_id" label="上级">
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
.ptip {
  margin-top: 15px;
  width: 100%;
  font-size: 12px;
  color: #777;
}
</style>
