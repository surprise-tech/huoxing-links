<script setup lang="ts">
import useCurd from '@/hooks/curd'
import FormModal from '@/components/FormModal.vue'
import { ApiUserFix } from '@/api/user'
import { ElMessage } from 'element-plus'
import { Edit, Plus } from '@element-plus/icons-vue'
import UserForm from '@/views/super/users/components/create.vue'
import UserAction from '@/views/super/users/components/user-action.vue'
import { ref } from 'vue'

const { state, showForm, submitForm, research, sizeChangeHandle, currentChangeHandle, } = useCurd({
  url: '/users',
  queryForm: {
    page: 1,
    type: 1
  }
})
const inlineEdit = (row: any) => {
  ApiUserFix(row.id, { status: !row.status }).then(() => {
    ElMessage.success('操作成功！')
    research()
  })
}
const formPackVisible = ref(false)
const userData = ref()
const userFeild = ref()
const formTitle = ref()
const changeForm = (row: any, value: string) => {
  formPackVisible.value = true
  userData.value = row
  userFeild.value = value
  if (value == 'vip_id') {
    formTitle.value = '更改套餐'
  } else if (value == 'end_at') {
    formTitle.value = '更改到期时间'
  } else if (value == 'credit') {
    formTitle.value = '充值积分'
  }
}
const vipPackageChange = (data: any, detail?: any) => {
  var params = {}
  if (userFeild.value == 'vip_id') {
    params = { vip_id: data.vip_id }
  } else if (userFeild.value == 'end_at') {
    params = { end_at: data.end_at }
  } else if (userFeild.value == 'credit') {
    params = { credit: data.credit }
  }
  ApiUserFix(detail.id, params).then(() => {
    ElMessage.success('操作成功！')
    formPackVisible.value = false
    research()
  })
}
</script>

<template>
  <el-card>
    <el-form>
      <el-row :gutter="20">
        <el-col :span="4">
          <el-form-item>
            <el-input
              v-model="state.queryForm.username"
              placeholder="请输入手机号"
              style="width: 260px"
              clearable
            ></el-input>
          </el-form-item>
        </el-col>
        <el-col :span="4">
          <el-button type="primary" @click="research">查询</el-button>
        </el-col>
      </el-row>
    </el-form>
    <div class="header-right" style="margin-bottom: 10px">
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
      <el-table-column prop="id" label="ID" width="80"></el-table-column>
      <el-table-column prop="status" label="状态" width="100">
        <template #default="{ row }">
          <el-tag v-if="row.status == 0" type="info">禁用</el-tag>
          <el-tag v-if="row.status == 1" type="success">正常</el-tag>
        </template>
      </el-table-column>
      <el-table-column prop="username" label="手机号"></el-table-column>
      <el-table-column label="账号类型" width="100">
        <template #default="{ row }">
          <el-tag v-if="row.type == 1" type="warning">会员</el-tag>
          <el-tag v-if="row.type == 2" type="success">代理商</el-tag>
          <el-tag v-if="row.type == 3" type="danger">管理员</el-tag>
        </template>
      </el-table-column>
      <el-table-column prop="start_at" label="生效时间"></el-table-column>
      <el-table-column prop="end_at" label="过期时间">
        <template #default="{ row }">
          <el-button type="primary" link :icon="Edit" @click="changeForm(row, 'end_at')">{{ row.end_at }}</el-button>
        </template>
      </el-table-column>
      <el-table-column prop="created_at" label="注册时间"></el-table-column>
      <el-table-column prop="" label="操作" fixed="right" width="80">
        <template #default="{ row }">
          <el-button type="primary" link v-if="row.status" @click="inlineEdit(row)">禁用</el-button>
          <el-button type="primary" link v-else @click="inlineEdit(row)" style="color: #ff0000">启用</el-button>
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
    <user-form></user-form>
  </form-modal>
  <form-modal
    v-if="formPackVisible"
    v-model:visible="formPackVisible"
    :detail="userData"
    @submit="vipPackageChange"
    type="dialog"
    width="600"
    :title="formTitle"
    cancelBtnName="取消"
    okBtnName="确定"
  >
    <user-action :form-feild="userFeild"></user-action>
  </form-modal>
</template>
