<script setup lang="ts">
import useCurd from '@/hooks/curd'
import FormModal from '@/components/FormModal.vue'
import { ref } from 'vue'

const { state, showForm, submitForm, resetInput, research, sizeChangeHandle, currentChangeHandle } =
  useCurd({
    url: '/users'
  })

const userType = ref([
  { value: 1, label: '会员' },
  { value: 2, label: '代理商' }
])
</script>

<template>
  <el-card>
    <div class="header">
      <div class="header-left">
        <el-form>
          <el-row :gutter="20">
            <el-col :span="3">
              <el-form-item>
                <el-input v-model="state.queryForm.username" placeholder="搜索用户名"></el-input>
              </el-form-item>
            </el-col>
            <el-col :span="3">
              <el-form-item>
                <el-select v-model="state.queryForm.type" placeholder="搜索会员等级">
                  <el-option
                    v-for="item in userType"
                    :key="item.value"
                    :label="item.label"
                    :value="item.value"
                  ></el-option>
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
      <el-table-column prop="username" label="用户名"> </el-table-column>
      <el-table-column prop="username" label="昵称"> </el-table-column>
      <el-table-column label="会员等级">
        <template #default="{ row }">{{ row?.vip_package?.id }}</template>
      </el-table-column>
      <el-table-column prop="end_at" label="会员过期时间"> </el-table-column>
      <el-table-column prop="created_at" label="创建时间"> </el-table-column>
      <el-table-column label="操作" width="200">
        <template #default="{ row }">
          <el-button type="primary" @click="showForm(row)" link>充值积分</el-button>
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
    <!--  弹窗  -->
    <form-modal
      v-model:visible="state.formVisible"
      :width="600"
      type="dialog"
      :title="state.detailData?.id ? '编辑' : '新增'"
      :loading="state.formLoading"
      :detail="state.detailData"
      @submit="submitForm"
      cancelBtnName="重置"
      okBtnName="确定"
    >
    </form-modal>
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
