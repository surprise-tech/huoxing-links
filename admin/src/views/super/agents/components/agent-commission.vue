<script setup lang="ts">
import useCurd from '@/hooks/curd'

const props = defineProps<{
  user_id: number
}>()

const { state, sizeChangeHandle, currentChangeHandle } = useCurd({
  url: `/commission-logs/${props.user_id}`,
  queryForm: {
    page: 1,
    type: '2,3'
  }
})
</script>

<template>
  <el-card>
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
      <el-table-column prop="title" label="备注"></el-table-column>
      <el-table-column prop="amount" label="金额"></el-table-column>
      <el-table-column prop="children_user.username" label="邀请用户"></el-table-column>
      <el-table-column prop="status" label="状态">
        <template v-slot:default="{ row }">
          <el-tag v-if="row.status === 0" type="success" effect="dark">已发放</el-tag>
          <el-tag v-if="row.status === 1" type="info" effect="dark">提现待审核</el-tag>
          <el-tag v-if="row.status === 2" type="success">已打款</el-tag>
          <el-tag v-if="row.status === 3" type="danger">提现被拒绝</el-tag>
          <el-tag v-if="row.status === 4" type="info">打款失败</el-tag>
        </template>
      </el-table-column>
      <el-table-column prop="created_at" label="时间"></el-table-column>
      <template #empty>
        <el-empty></el-empty>
      </template>
    </el-table>
    <!-- 分页 -->
    <el-pagination
      :current-page="state.page"
      :total="state.total"
      :page-sizes="state.pageSizes"
      :page-size="state.limit"
      background
      layout="total, sizes, prev, pager, next"
      @size-change="sizeChangeHandle"
      @current-change="currentChangeHandle"
    />
  </el-card>
</template>
<style scoped lang="scss">
.el-tag {
  padding-top: 3px;
}
</style>
