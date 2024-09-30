<template>
  <el-card>
    <div class="margin-b-20">
      <el-space>
        <el-button type="primary" :icon="Plus" @click="showForm" v-if="store.userInfo.type === 3">新增</el-button>
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
      <el-table-column prop="username" label="手机号"></el-table-column>
      <el-table-column prop="link_amount" label="链接剩余个数"></el-table-column>
      <el-table-column prop="yuanma_amount" label="小圆码剩余个数"></el-table-column>
      <el-table-column prop="dy_card_amount" label="自动回复剩余个数"></el-table-column>
      <el-table-column prop="created_at" label="账户状态">
        <template v-slot:default="{ row }">
          <el-switch
            v-model="row.status"
            :active-value="true"
            :inactive-value="false"
            active-text="开启"
            inactive-text="禁用"
            inline-prompt
            @change="statusHandler(row.id, $event)"
          ></el-switch>
        </template>
      </el-table-column>
      <el-table-column prop="" label="操作" fixed="right">
        <template #default="{ row }">
          <el-button type="primary" link @click="showForm(row.id)">修改</el-button>
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
    <AgentForm v-if="state.detailData && state.detailData.id"></AgentForm>
    <AddAgentForm v-else></AddAgentForm>
  </form-modal>
</template>
<script setup lang="ts">
import useCurd from '@/hooks/curd'
import { Plus } from '@element-plus/icons-vue'
import FormModal from '@/components/FormModal.vue'
import AgentForm from '@/views/newAgent/components/agentForm.vue'
import AddAgentForm from '@/views/newAgent/components/addAgentForm.vue'
import { userStore } from '@/stores'
import { EditAgentStatus } from '@/api/newAgent'
import { ElMessage } from 'element-plus'
import { data_get, setClipboard } from '@/utils'
const { state, sizeChangeHandle, currentChangeHandle, showForm, submitForm, research } = useCurd({
  url: '/users'
})
const store = userStore
// 复制邀请链接
const copyCode = () => {
  const root = window.location.origin
  const code = data_get(store.userInfo, 'referral_code', '')
  var path = window.location.pathname
  setClipboard(root + path + '#/register?code=' + code)
}
const statusHandler = (id: number, value: any) => {
  // console.log(id, value)
  EditAgentStatus(id, value)
    .then(() => {
      let str = value === true ? '操作成功！最多需要10秒才能生效' : '操作成功！'
      ElMessage.success(str)
    })
    .catch(() => {
      state.dataList?.forEach((item) => {
        if (item.id === id) {
          item.is_enable = !value
        }
      })
    })
}
</script>
<style scoped lang="scss"></style>
