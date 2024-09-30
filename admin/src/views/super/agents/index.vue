<script setup lang="ts">
import useCurd from '@/hooks/curd'
import FormModal from '@/components/FormModal.vue'
import { ApiUserFix } from '@/api/user'
import { ElMessage } from 'element-plus'
import { ref } from 'vue'
import UserAction from '@/views/super/users/components/user-action.vue'
import UserForm from '@/views/super/agents/components/create-or-look.vue'
import { Edit, Plus } from '@element-plus/icons-vue'
import AgentCommission from '@/views/super/agents/components/agent-commission.vue'

const { state, showForm, submitForm, research, sizeChangeHandle, currentChangeHandle } = useCurd({
  url: '/users',
  queryForm: {
    page: 1,
    type: 2
  }
})
const inlineEdit = (row: any) => {
  ApiUserFix(row.id, { status: !row.status }).then(() => {
    ElMessage.success('操作成功！')
    window.location.reload()
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
  if (value == 'level_id') {
    formTitle.value = '更改等级'
  } else if (value == 'credit') {
    formTitle.value = '充值积分'
  } else if (value == 'parent_id') {
    formTitle.value = '更改级别'
  }
}
const vipPackageChange = (data: any, detail?: any) => {
  var params = {}
  // console.log('userFeild:', userFeild.value)
  if (userFeild.value == 'level_id') {
    params = { level_id: data.level_id }
  } else if (userFeild.value == 'credit') {
    params = { credit: data.credit }
  } else if (userFeild.value == 'parent_id') {
    params = { parent_id: data.parent_id }
  }
  ApiUserFix(detail.id, params).then(() => {
    ElMessage.success('操作成功！')
    window.location.reload()
  })
}
const commissionVisible = ref(false)
const agentUserId = ref()
const commissionShow = (user_id: any) => {
  console.log(user_id)
  commissionVisible.value = true
  agentUserId.value = user_id
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
    <div class="header-right">
      <el-button type="primary" :icon="Plus" @click="showForm">新增代理</el-button>
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
      <el-table-column prop="username" label="手机号" width="120"> </el-table-column>
      <el-table-column prop="agent_package.name" label="当前套餐"> </el-table-column>
      <!--<el-table-column prop="agent_level.name" label="级别">
        <template #default="{ row }">
          <el-button type="primary" link :icon="Edit" @click="changeForm(row, 'level_id')">
            <span v-if="row.agent_level && row.agent_level.name">{{ row.agent_level.name }}</span>
            <span v-else></span>
          </el-button>
        </template>
      </el-table-column>-->
      <el-table-column prop="credit" label="剩余积分">
        <template #default="{ row }">
          <el-button type="primary" link :icon="Edit" @click="changeForm(row, 'credit')">
            {{ row.credit }}
          </el-button>
        </template>
      </el-table-column>
      <el-table-column prop="accumulate_credit" label="累计充值积分"> </el-table-column>
      <el-table-column prop="commission" label="剩余佣金" width="120"> </el-table-column>
      <el-table-column prop="accumulate_commission" label="累计佣金" width="120"> </el-table-column>
      <el-table-column prop="referral_code" label="推荐码"> </el-table-column>
      <el-table-column prop="parent.username" label="上级">
        <template #default="{ row }">
          <el-button type="primary" link :icon="Edit" @click="changeForm(row, 'parent_id')">
            <span v-if="row.parent && row.parent.username">{{ row.parent.username }}</span>
            <span v-else></span>
          </el-button>
        </template>
      </el-table-column>
      <el-table-column prop="created_at" label="注册时间" width="160"> </el-table-column>
      <el-table-column prop="" label="操作" fixed="right" width="200">
        <template #default="{ row }">
          <el-button type="primary" link v-if="row.status" @click="inlineEdit(row)">禁用</el-button>
          <el-button type="primary" link v-else @click="inlineEdit(row)" style="color: #ff0000">启用</el-button>
          <el-button type="primary" link @click="commissionShow(row.id)">佣金记录</el-button>
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

  <form-modal
    v-if="commissionVisible"
    v-model:visible="commissionVisible"
    type="drawer_v"
    width="900"
    title="佣金记录"
  >
    <agent-commission :user_id="agentUserId"></agent-commission>
  </form-modal>
</template>
<style scoped lang="scss">
.header-right {
  margin-bottom: 20px;
}
</style>
