<script setup lang="ts">
import useCurd from '@/hooks/curd'
import FormModal from '@/components/FormModal.vue'
import CreateOrLook from '@/views/link/addLink/components/app/create-or-look.vue'
import OssImage from '@/components/OssImage.vue'
import { copyLink } from '@/utils'
import { ApiRenewalLink } from '@/api/comment'
import { ElMessage, ElMessageBox } from 'element-plus'
import useUserStore from '@/stores/modules/user'

const user = useUserStore()
const props = defineProps<{
  type: any
}>()
const { state, sizeChangeHandle, currentChangeHandle, showForm, submitForm, research, deleteHandle } = useCurd({
  url: '/links',
  queryForm: {
    type: props.type,
    page: 1,
    username: ''
  }
})
// 续费
const renewal = (id: number) => {
  ElMessageBox.confirm('确定要续费一个月吗?这将要扣除1个创建个数', 'Warning', {
    confirmButtonText: '确认',
    cancelButtonText: '取消',
    type: 'warning',
    center: true
  })
    .then(() => {
      ApiRenewalLink({
        id: id
      }).then(() => {
        ElMessage.success('操作成功！')
        research()
      })
    })
    .catch(() => {})
}
</script>

<template>
  <div>
    <el-row :gutter="20">
      <el-col :xs="24" :sm="8" :md="8" :lg="6" class="m-b-20">
        <el-input style="width: 100%" v-model="state.queryForm.title" placeholder="请输入标题" clearable></el-input>
      </el-col>
      <el-col :xs="24" :sm="8" :md="8" :lg="6" class="m-b-20" v-if="user.userInfo.type === 3">
        <el-input
          style="width: 100%"
          placeholder="请输入用户名"
          v-model="state.queryForm.username"
          clearable
        ></el-input>
      </el-col>
      <el-col :xs="24" :sm="8" :md="8" :lg="6" class="m-b-20">
        <el-button type="primary" @click="research">搜索</el-button>
        <el-button type="primary" :plain="true" @click="showForm">创建小程序</el-button>
      </el-col>
    </el-row>

    <!--  表格  -->
    <el-table
      :data="state.dataList"
      v-loading="state.listLoading"
      :header-cell-style="{
        'text-align': 'center',
        'background-color': '#F1F1F1',
        height: '50px',
        color: '#333'
      }"
      :cell-style="{ padding: '0', 'text-align': 'center', height: '80px', color: '#333' }"
      :row-key="state.primaryKey"
    >
      <el-table-column prop="id" label="序号"></el-table-column>
      <el-table-column label="图标">
        <template #default="{ row }">
          <oss-image :paths="row.icon ? [row.icon] : []" :width="60" :height="60"></oss-image>
        </template>
      </el-table-column>
      <el-table-column prop="title" label="卡片标题"></el-table-column>
      <el-table-column prop="description" label="卡片描述"></el-table-column>
      <el-table-column prop="" label="链接状态">
        <template #default="{ row }">
          <span v-if="row.status == 1">可用</span>
          <span v-else style="color: red">不可用</span>
        </template>
      </el-table-column>
      <el-table-column prop="visit_uv_count" label="浏览量UV"></el-table-column>
      <el-table-column prop="expired_at" label="到期时间"></el-table-column>
      <el-table-column prop="user.username" label="所属用户"></el-table-column>
      <el-table-column label="操作">
        <template #default="{ row }">
          <el-button type="primary" link @click="copyLink(row)">复制链接</el-button>
          <el-button type="primary" link @click="showForm(row.id)">编辑</el-button>
          <el-button type="primary" link @click="deleteHandle(row.id)">删除</el-button>
        </template>
      </el-table-column>
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
      v-if="state.formVisible === true"
      v-model:visible="state.formVisible"
      width="1000"
      type="dialog"
      :title="state.detailData?.id ? '编辑' : '新增'"
      :loading="state.formLoading"
      :detail="state.detailData"
      @submit="submitForm"
      cancelBtnName="取消"
      okBtnName="确定"
    >
      <create-or-look></create-or-look>
    </form-modal>
  </div>
</template>

<style scoped lang="scss">
.header-right {
  float: right;
}
</style>
