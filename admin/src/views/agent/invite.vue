<script setup lang="ts">
import useCurd from '@/hooks/curd'
const { state, sizeChangeHandle, currentChangeHandle } = useCurd({
  url: '/agent-invite',
  queryForm: {
    page: 1
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
      <el-table-column prop="id" label="ID" width="60"></el-table-column>
      <el-table-column prop="username" label="手机号"></el-table-column>
      <el-table-column prop="agent_package.name" label="当前套餐"> </el-table-column>
      <el-table-column prop="credit" label="剩余积分"></el-table-column>
      <el-table-column prop="accumulate_credit" label="累计充值积分"> </el-table-column>
      <el-table-column prop="commission" label="剩余佣金" width="120"> </el-table-column>
      <el-table-column prop="accumulate_commission" label="累计佣金" width="120"> </el-table-column>
      <el-table-column prop="created_at" label="注册时间"></el-table-column>
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
<style scoped lang="scss"></style>
