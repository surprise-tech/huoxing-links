<script setup lang="ts">
import { Plus, Edit } from '@element-plus/icons-vue'
import useCurd from '@/hooks/curd'
import FormModal from '@/components/FormModal.vue'
import { ref } from 'vue'
import CreateOrLook from '@/views/super/vipSetMeal/components/create-or-look.vue'

const {
  state,

  showForm,
  submitForm,
  deleteHandle,
  query,
  inlineEdit
} = useCurd({
  url: '/vip-packages',
  openPage: false
})

// 行内编辑
// 表格排序switch和按钮的切换
const isShow = () => {
  flag.value = false
}
// 实现表格排序及数值变化
const flag = ref(true)
const handleSort = async (row: any) => {
  await inlineEdit(row.id, 'level', row.level)
  await query()
  flag.value = true
}
</script>

<template>
  <el-card>
    <div class="header-right">
      <el-button type="primary" :icon="Plus" @click="showForm">新增套餐</el-button>
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
      <el-table-column prop="name" label="套餐名称"></el-table-column>
      <el-table-column prop="price" label="价格/月"> </el-table-column>
      <el-table-column prop="level" label="等级">
        <template #default="{ row }">
          <div v-if="flag === false">
            <el-input-number v-model="row.level"></el-input-number>
            <el-button type="primary" link @click="flag = true">取消</el-button>
            <el-button type="primary" link @click="handleSort(row)">确认</el-button>
          </div>
          <el-button v-else type="primary" link :icon="Edit" @click="isShow">
            {{ row.level }}
          </el-button>
        </template>
      </el-table-column>
      <el-table-column label="权限配置">
        <template #default="{ row }">
          <div>链接数量：{{ row.config?.count_limit }}</div>
          <div>小圆码数量：{{ row.config?.yuanma_limit }}</div>
          <div>抖音自动回复数量：{{ row.config?.douyin_reply_limit }}</div>
          <div>UV限制：{{ row.config?.uv_limit }}</div>
        </template>
      </el-table-column>
      <el-table-column prop="created_at" label="创建时间"> </el-table-column>
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
  <!-- 添加标签 -->
  <form-modal
    v-if="state.formVisible === true"
    v-model:visible="state.formVisible"
    :loading="state.formLoading"
    :detail="state.detailData"
    @submit="submitForm"
    type="dialog"
    width="1000"
    :title="state.detailData?.id ? '编辑' : '新增'"
    cancelBtnName="取消"
    okBtnName="确定"
  >
    <create-or-look></create-or-look>
  </form-modal>
</template>

<style scoped lang="scss">
.top_card {
  margin-bottom: 20px;
  height: 72px;
}
//.header {
//  display: flex;
//  justify-content: space-between;
//  .header-left {
//    flex: 1;
//  }
//}
.header-right {
  margin-bottom: 20px;
}
:deep(.el-form) {
  margin-bottom: 15px;
}
</style>
