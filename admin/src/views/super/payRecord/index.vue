<script setup lang="ts">
import useCurd from '@/hooks/curd'

const { state, resetInput, research, sizeChangeHandle, currentChangeHandle } = useCurd({
  url: '/payments'
})
</script>

<template>
  <el-card>
    <div class="header">
      <div class="header-left">
        <el-form>
          <el-row :gutter="20">
            <el-col :span="3">
              <el-form-item label="状态">
                <el-select v-model="state.queryForm.status" placeholder="请选择">
                  <el-option key="1" label="已下单" value="1"></el-option>
                  <el-option key="2" label="已支付" value="2"></el-option>
                  <el-option key="3" label="已取消" value="3"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="3">
              <el-form-item label="付款人">
                <el-input
                  v-model="state.queryForm.payer_username"
                  placeholder="输入付款人"
                ></el-input>
              </el-form-item>
            </el-col>
            <el-col :span="5">
              <el-form-item label="下单时间">
                <el-date-picker
                  v-model="state.queryForm.created_at"
                  placeholder="下单时间"
                  type="daterange"
                  value-format="YYYY-MM-DD HH:mm:ss"
                ></el-date-picker>
              </el-form-item>
            </el-col>
            <el-col :span="5">
              <el-form-item label="支付时间">
                <el-date-picker
                  v-model="state.queryForm.success_at"
                  placeholder="支付时间"
                  type="daterange"
                  value-format="YYYY-MM-DD HH:mm:ss"
                ></el-date-picker>
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
      <el-table-column prop="id" label="ID"></el-table-column>
      <el-table-column prop="no" label="编号" width="200"></el-table-column>
      <el-table-column prop="status" label="状态">
        <template #default="{ row }">
          <el-tag v-if="row.status === 1">已下单</el-tag>
          <el-tag v-if="row.status === 2" type="success" effect="dark">已支付</el-tag>
          <el-tag v-if="row.status === 3" type="info" effect="dark">已取消</el-tag>
        </template>
      </el-table-column>
      <el-table-column prop="job" label="套餐">
        <template #default="{ row }">
          <el-tag v-if="row.job === 'App\\Jobs\\PayAgent'" type="warning">
            {{ state.meta && state.meta['agent'][row.attach.agent_id] }}
          </el-tag>
          <el-tag v-if="row.job === 'App\\Jobs\\PayVip' && row.attach.vip_id" type="success">
            {{ state.meta && state.meta['vip'][row.attach.vip_id] }}
          </el-tag>
          <el-tag v-if="row.job === 'App\\Jobs\\PayCredit'" type="danger">充值积分</el-tag>
          <span v-else></span>
        </template>
      </el-table-column>
      <el-table-column prop="amount" label="金额"> </el-table-column>
      <el-table-column prop="payer.username" label="付款人">
        <template #default="{ row }">
          <span v-if="row.payer && row.payer.username"> {{ row.payer.username }}</span>
          <span v-else style="color: #999">{{ row.attach.username }}</span>
        </template>
      </el-table-column>
      <el-table-column prop="payer.type" label="付款人类型">
        <template #default="{ row }">
          <el-tag v-if="row.payer && row.payer.type === 1">会员</el-tag>
          <el-tag v-if="row.payer && row.payer.type === 2" type="warning">代理</el-tag>
          <el-tag v-if="row.payer && row.payer.type === 3">管理员</el-tag>
        </template>
      </el-table-column>
      <el-table-column prop="success_at" label="支付时间"></el-table-column>
      <el-table-column prop="created_at" label="下单时间"></el-table-column>
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
.header {
  display: flex;
  justify-content: space-between;
  .header-left {
    flex: 1;
  }
}

:deep(.el-form) {
  margin-bottom: 15px;
}
</style>
