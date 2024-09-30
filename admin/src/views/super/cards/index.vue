<script setup lang="ts">
import { Plus } from '@element-plus/icons-vue'
import useCurd from '@/hooks/curd'
import FormModal from '@/components/FormModal.vue'
import CreateOrLook from '@/views/super/cards/components/create-or-look.vue'
import { ApiCardFix } from '@/api/super'
import { ElMessage, ElMessageBox } from 'element-plus'

const { state, showForm, submitForm, research, resetInput, sizeChangeHandle, currentChangeHandle } =
  useCurd({
    url: '/cards',
    queryForm: {
      page: 1
    }
  })
const inlineEdit = (row: any) => {
  ElMessageBox.confirm('确定禁用 不可恢复！', '提示', {
    confirmButtonText: '确定',
    cancelButtonText: '取消',
    type: 'warning'
  }).then(() => {
    ApiCardFix(row.id, {}).then(() => {
      ElMessage.success('操作成功！')
      research()
    })
  })
}
const destroy = (row: any) => {
  ElMessageBox.confirm('确定销毁卡密吗？创建次数会释放到原账户,此操作不可恢复！', '提示', {
    confirmButtonText: '确定',
    cancelButtonText: '取消',
    type: 'warning'
  }).then(() => {
    ApiCardFix(row.id, {}).then(() => {
      ElMessage.success('操作成功！')
      research()
    })
  })
}
</script>

<template>
  <el-card>
    <div class="header">
      <div class="header-left">
        <el-form>
          <el-row :gutter="20">
            <el-col :span="2">
              <el-form-item>
                <el-select v-model="state.queryForm.status" clearable placeholder="状态">
                  <el-option key="1" label="正常" value="1"></el-option>
                  <el-option key="0" label="禁用" value="0"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="3">
              <el-form-item>
                <el-input v-model="state.queryForm.user_username" placeholder="兑换人"></el-input>
              </el-form-item>
            </el-col>
            <el-col :span="5">
              <el-form-item label="兑换时间">
                <el-date-picker
                  v-model="state.queryForm.updated_at"
                  placeholder="兑换时间"
                  type="daterange"
                  value-format="YYYY-MM-DD HH:mm:ss"
                ></el-date-picker>
              </el-form-item>
            </el-col>
            <el-col :span="3">
              <el-form-item>
                <el-select v-model="state.queryForm.used" clearable placeholder="兑换状态">
                  <el-option key="0" label="未兑换" value="0"></el-option>
                  <el-option key="1" label="已兑换" value="1"></el-option>
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
    <div class="header-right">
      <el-button type="primary" :icon="Plus" @click="showForm">新增</el-button>
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
      <el-table-column label="状态" width="90">
        <template #default="{ row }">
          <el-tag v-if="row.status" type="success">正常</el-tag>
          <el-tag v-else type="info">禁用</el-tag>
        </template>
      </el-table-column>
      <el-table-column prop="secret" label="卡密" width="340"></el-table-column>
      <el-table-column prop="vip_package.name" label="类型"></el-table-column>
      <el-table-column label="会员时常">
        <template #default="{ row }">
          <span>{{ row.month }}个月</span>
        </template>
      </el-table-column>
      <el-table-column label="兑换状态">
        <template #default="{ row }">
          <el-tag v-if="row.used" type="info">已兑换</el-tag>
          <el-tag v-else type="success">未兑换</el-tag>
        </template>
      </el-table-column>
      <el-table-column prop="user.username" label="兑换人"></el-table-column>
      <el-table-column prop="expiry_time" label="有效期"></el-table-column>
      <el-table-column prop="created_at" label="创建时间"></el-table-column>
      <el-table-column prop="" label="操作" fixed="right" width="240">
        <template #default="{ row }">
          <el-button type="primary" link v-if="row.status" @click="inlineEdit(row)">禁用</el-button>
<!--          <el-button type="danger" link v-if="!row.used" @click="destroy(row)">销毁</el-button>-->
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

  <!-- 添加标签 -->
  <form-modal
    v-if="state.formVisible === true"
    v-model:visible="state.formVisible"
    :loading="state.formLoading"
    :detail="state.detailData"
    @submit="submitForm"
    type="dialog"
    width="600"
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
