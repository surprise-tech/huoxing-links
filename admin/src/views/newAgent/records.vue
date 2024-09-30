<template>
  <el-card>
    <div class="margin-b-20">
      <el-space>
        <el-input v-model="state.queryForm.username" placeholder="请输入手机号查询"></el-input>
        <el-button type="primary" @click="research">查询</el-button>
      </el-space>
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
      <el-table-column prop="id" label="ID" width="60"></el-table-column>
      <el-table-column prop="user.username" label="手机号"></el-table-column>
      <el-table-column label="数量">
        <template #default="{ row }">
          <div class="text" :class="[row.direction == 1 ? 'text-green' : 'text-red']">
            {{ row.direction == 1 ? '+ ' + row.amount : '- ' + row.amount }}
          </div>
        </template>
      </el-table-column>
      <el-table-column label="类型">
        <template #default="{ row }">
          <div>
            {{
              row.type === 'yuanma_amount'
                ? '小圆码'
                : row.type === 'link_amount'
                ? '链接'
                : row.type === 'dy_card_amount'
                ? '抖音自动回复卡片'
                : ''
            }}
          </div>
        </template>
      </el-table-column>
      <el-table-column prop="remark" label="备注"></el-table-column>
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
<script setup lang="ts">
import useCurd from '@/hooks/curd'
const { state, sizeChangeHandle, currentChangeHandle, research } = useCurd({
  url: '/amount-record'
})
</script>
<style scoped lang="scss">
.text-green {
  color: #67c23a;
}
.text-red {
  color: #f56c6c;
}
</style>
