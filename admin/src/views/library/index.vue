<template>
  <el-card>
    <div class="top-box">
      <div class="header-right" style="margin-bottom: 10px;">
        <el-button type="primary" :icon="Plus" @click="showForm">上传图片</el-button>
      </div>
      <el-button type="primary" :disabled="!selectList" plain @click="deleteAll">
        删除选中项
      </el-button>
    </div>
    <div class="content-box">
      <!-- 分类 -->
      <div style="margin-bottom: 15px; margin-left: 8px">
        <cate-view @rs-cate="selectCate"></cate-view>
      </div>

      <div class="item-list">
        <div
          class="item-img"
          :class="{ 'active-border' : selectList.find((it: any) => it === item.id) }"
          v-for="item in state.dataList"
          :key="item.id"
          @click="selectImg(item.id)"
          @dblclick="dbPreview(item.path)"
        >
          <oss-image :paths="[item.path]" :width="100" :height="100"></oss-image>
        </div>
      </div>
      <!-- 分页 -->
      <el-pagination
        :current-page="state.page"
        :total="state.total"
        :page-sizes="state.pageSizes"
        :page-size="state.limit"
        background
        layout="total,  prev, pager, next"
        @size-change="sizeChangeHandle"
        @current-change="currentChangeHandle"
      />
    </div>
  </el-card>
  <el-dialog v-model="dialogVisible">
    <img class="preview" :src="dialogImageUrl" alt="Preview Image" style="max-width: 100%; margin: 0 auto;" />
  </el-dialog>
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
</template>
<script setup lang="ts">
import useCurd from '@/hooks/curd'
import { configStore } from '@/stores'
import OssImage from '@/components/OssImage.vue'
import { ImagePath } from '@/hooks/image'
import { provide, ref } from 'vue'
import { Plus } from '@element-plus/icons-vue'
import FormModal from '@/components/FormModal.vue'
import UserForm from '@/views/library/components/create-or-look.vue'
import CateView from '@/views/library/components/cate.vue'
import { ApiDelMaterial } from '@/api/app'
import { ElMessage, ElMessageBox } from 'element-plus'

const cate_id = ref(null)
const { state, showForm, submitForm, sizeChangeHandle, currentChangeHandle, query } = useCurd({
  url: '/material-upload',
  limit: 48,
  queryForm: {
    cate_id: cate_id,
    page: 1
  }
})
// 图片存储
const selectList = ref<any[]>([])
const selectImg = (id: any) => {
  const current = selectList.value.find((item: any) => item === id)
  if (current) {
    selectList.value = selectList.value.filter((item) => item !== current)
  } else {
    selectList.value.push(id)
  }
}
const deleteAll = () => {
  ElMessageBox.confirm('确定删除?', '提示', {
    confirmButtonText: '确定',
    cancelButtonText: '取消',
    type: 'warning'
  }).then(() => {
    ApiDelMaterial(selectList.value).then(() => {
      ElMessage.success('操作成功！')
      window.location.reload()
    })
  })
}
const dialogVisible = ref(false)
const dialogImageUrl = ref('')
const dbPreview = (uri:any) => {
  dialogImageUrl.value = ImagePath(uri)
  dialogVisible.value = true
}
const selectCate = (id: any) => {
  cate_id.value = id
  query()
}
provide('query', query)
</script>

<style lang="scss" scoped>
.top-box {
  margin-bottom: 15px;
  display: flex;
  justify-content: space-between;
}

.content-box {
  width: 100%;
}

.item-list {
  display: flex;
  flex-wrap: wrap;
  width: 100%;

  .item-img {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 120px;
    height: 120px;
    margin: 4px;
    background-color: #f8f8f8;
  }
}

// 默认选中
.active-border {
  border: 1px solid var(--el-color-primary);
}
</style>
