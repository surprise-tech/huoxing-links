<script setup lang="ts">
import useCurd from '@/hooks/curd'

const { state, research, resetInput, sizeChangeHandle, currentChangeHandle } = useCurd({
  url: '/commission-logs',
  queryForm: {
    page: 1,
    type: '1'
  }
})
</script>

<template>
  <el-card>
    <div class="header">
      <div class="header-left">
        <el-form>
          <el-row :gutter="20">
            <el-col :span="4">
              <el-form-item>
                <el-select v-model="state.queryForm.status" clearable placeholder="状态">
                  <el-option key="1" label="提现待审核" value="1"></el-option>
                  <el-option key="2" label="已打款" value="2"></el-option>
                  <el-option key="3" label="提现被拒绝" value="3"></el-option>
                  <el-option key="4" label="打款失败" value="4"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="5">
              <el-button type="primary" @click="research">搜索</el-button>
              <el-button @click="resetInput">重置</el-button>
            </el-col>
          </el-row>
        </el-form>
      </div>
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
      <el-table-column prop="title" label="备注"></el-table-column>
      <el-table-column prop="amount" label="金额"></el-table-column>
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
