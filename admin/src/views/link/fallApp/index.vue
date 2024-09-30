<script setup lang="ts">
import { Message, Plus } from '@element-plus/icons-vue'
import useCurd from '@/hooks/curd'
import FormModal from '@/components/FormModal.vue'
import CreateOrLook from '@/views/link/fallApp/components/create-or-look.vue'
import { ApiLinkFixApp } from '@/api/comment'
import useUserStore from '@/stores/modules/user'

const { state, showForm, submitForm, deleteHandle, research } = useCurd({
  url: '/min-program',
  openPage: false,
  queryForm: {
    username: ''
  }
})
const changeStatus = (e: any) => {
  console.log(e)
  ApiLinkFixApp(e.id, { is_enable: e.is_enable }).then(() => {
    Message.success('操作成功')
  })
}
const user = useUserStore()
</script>

<template>
  <el-card>
    <el-row :gutter="20">
      <el-col :xs="24" :sm="8" :md="8" :lg="6" class="m-b-20" v-if="user.userInfo.type === 3">
        <el-input
          style="width: 100%"
          placeholder="请输入用户名"
          v-model="state.queryForm.username"
          clearable
        ></el-input>
      </el-col>
      <el-col :xs="24" :sm="8" :md="8" :lg="6" class="m-b-20">
        <el-button type="primary" v-if="user.userInfo.type === 3" @click="research">搜索</el-button>
        <el-button type="primary" :plain="true" @click="showForm">新建</el-button>
      </el-col>
    </el-row>

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
      <el-table-column prop="id" label="序号"></el-table-column>
      <el-table-column prop="name" label="小程序名称"> </el-table-column>
      <el-table-column label="类型">
        <template #default="{ row }">
          <span>{{ row.type === 1 ? '落地小程序' : '自有小程序' }}</span>
        </template>
      </el-table-column>
      <el-table-column prop="app_id" label="小程序APPID"> </el-table-column>
      <el-table-column prop="secret" label="小程序AppSecret"> </el-table-column>
      <el-table-column prop="user.username" label="创建人"> </el-table-column>
      <el-table-column prop="created_at" label="创建时间"> </el-table-column>
      <el-table-column label="状态">
        <template #default="{ row }">
          <el-tag v-if="row.is_enable === 1" type="success" effect="dark">启用</el-tag>
          <el-tag v-else type="danger" effect="dark">禁用</el-tag>
        </template>
      </el-table-column>
      <el-table-column label="小程序池">
        <template #default="{ row }">
          <el-tag v-if="row.is_pre_min === 1" type="success">是</el-tag>
          <el-tag v-else type="danger">否</el-tag>
        </template>
      </el-table-column>
      <el-table-column prop="" label="操作">
        <template #default="{ row }">
          <el-button type="primary" link @click="showForm(row)">编辑</el-button>
          <el-button type="primary" link @click="deleteHandle(row.id)">删除</el-button>
        </template>
      </el-table-column>
      <template #empty>
        <el-empty></el-empty>
      </template>
    </el-table>
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
