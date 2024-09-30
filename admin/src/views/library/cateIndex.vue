<script setup lang="ts">
import useCurd from '@/hooks/curd'
import { Edit, Plus } from '@element-plus/icons-vue'
import CreateOrLook from '@/views/library/components/cate-form.vue'
import FormModal from '@/components/FormModal.vue'
import { ref } from 'vue'

const { state, inlineEdit, showForm, submitForm, deleteHandle } = useCurd({
  url: '/materials-cate',
  openPage: false
})
const isEdit = () => {
  flag.value = false
}
const flag = ref(true)
const handleSort = async (row: any) => {
  await inlineEdit(row.id, 'sort', row.sort)
  flag.value = true
}
</script>

<template>
  <el-card>
    <div class="header-right">
      <el-button type="primary" :icon="Plus" @click="showForm">新增分类</el-button>
    </div>
    <!-- 表格 -->
    <el-table
      :data="state.dataList"
      :header-cell-style="{
        'text-align': 'center',
        'background-color': '#F1F1F1',
        height: '50px',
        color: '#333'
      }"
      :cell-style="{ padding: '0', 'text-align': 'center', height: '50px', color: '#333' }"
      v-loading="state.listLoading"
      :row-key="state.primaryKey"
    >
      <el-table-column prop="name" label="名称"></el-table-column>
      <el-table-column label="排序">
        <template #default="{ row }">
          <div v-if="!flag">
            <el-input-number v-model="row.sort"></el-input-number>
            <el-button type="primary" link @click="flag = true">取消</el-button>
            <el-button type="primary" link @click="handleSort(row)">确认</el-button>
          </div>
          <el-button v-else type="primary" link :icon="Edit" @click="isEdit">
            {{ row.sort }}
          </el-button>
        </template>
      </el-table-column>
      <el-table-column prop="created_at" label="创建时间"></el-table-column>
      <el-table-column prop="" label="操作" fixed="right">
        <template #default="{ row }">
          <el-button type="primary" link @click="showForm(row)">编辑</el-button>
          <el-button type="primary" link @click="deleteHandle(row.id)">删除</el-button>
        </template>
      </el-table-column>
      <template #empty>
        <el-empty></el-empty>
      </template>
    </el-table>
  </el-card>
  <!-- 添加 -->
  <form-modal
    v-if="state.formVisible === true"
    v-model:visible="state.formVisible"
    :loading="state.formLoading"
    :detail="state.detailData"
    @submit="submitForm"
    type="dialog"
    width="540"
    :title="state.detailData?.id ? '编辑' : '新增'"
    cancelBtnName="取消"
    okBtnName="确定"
  >
    <create-or-look></create-or-look>
  </form-modal>
</template>
<style scoped lang="scss">
.header-right {
  margin-bottom: 20px;
}
</style>
