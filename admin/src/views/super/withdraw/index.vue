<script setup lang="ts">
import useCurd from '@/hooks/curd'
import { ApiAgreeApply, ApiRejectApply } from '@/api/super'
import { ElMessage } from 'element-plus'

const { state, research, sizeChangeHandle, currentChangeHandle } = useCurd({
  url: '/withdraws'
})

const agree = async (id: any) => {
  try {
    await ApiAgreeApply(id)
    ElMessage.success('已完成')
  } catch {
    ElMessage.error('操作失败')
  }
  research()
}

const reject = async (id: any) => {
  try {
    await ApiRejectApply(id)
    ElMessage.success('已拒绝')
  } catch {
    ElMessage.error('操作失败')
  }
  research()
}
</script>

<template>
  <el-card>
    <el-form>
      <el-row :gutter="20">
        <el-col :span="4">
          <el-form-item>
            <el-select v-model="state.queryForm.status" clearable placeholder="状态">
              <el-option key="0" label="无需处理" value="0"></el-option>
              <el-option key="1" label="未处理" value="1"></el-option>
              <el-option key="2" label="已打款" value="2"></el-option>
              <el-option key="3" label="已拒绝" value="3"></el-option>
              <el-option key="4" label="打款失败" value="4"></el-option>
            </el-select>
          </el-form-item>
        </el-col>
        <el-col :span="4">
          <el-button type="primary" @click="research">查询</el-button>
        </el-col>
      </el-row>
    </el-form>
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
      <el-table-column prop="user.username" label="申请人"> </el-table-column>
      <el-table-column prop="amount" label="金额"> </el-table-column>
      <!--      <el-table-column label="是否完成">-->
      <!--        <template #default="{ row }">-->
      <!--          <el-switch-->
      <!--            v-model="row.status"-->
      <!--            :active-value="1"-->
      <!--            :inactive-value="0"-->
      <!--            inline-prompt-->
      <!--            @change="inlineEdit(row.id, 'status', $event)"-->
      <!--          ></el-switch>-->
      <!--        </template>-->
      <!--      </el-table-column>-->
      <el-table-column prop="created_at" label="申请时间"> </el-table-column>
      <el-table-column prop="payee.name" label="真实姓名"> </el-table-column>
      <el-table-column prop="payee.account" label="支付宝账号"> </el-table-column>
      <el-table-column prop="status" label="状态">
        <template #default="{ row }">
          <el-tag v-if="row.status == 0" type="info">无需处理</el-tag>
          <el-tag v-if="row.status == 1" type="info">未处理</el-tag>
          <el-tag v-if="row.status == 2" type="success">已打款</el-tag>
          <el-tag v-if="row.status == 3" type="danger">已拒绝</el-tag>
          <el-tag v-if="row.status == 4" type="warning">打款失败</el-tag>
        </template>
      </el-table-column>
      <el-table-column prop="" label="操作" fixed="right">
        <template #default="{ row }">
          <el-button v-if="row.status == 1" type="primary" link @click="agree(row.id)">同意</el-button>
          <el-button v-if="row.status == 1" type="primary" link @click="reject(row.id)">拒绝</el-button>
        </template>
      </el-table-column>
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
