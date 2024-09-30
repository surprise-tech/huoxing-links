<template>
  <div>
    <el-dialog v-model="dialogVisible" width="900" :append-to-body="true" title="素材库">
      <div class="bottom_image">
        <div class="right_image">
          <div class="right-top">
            <el-button
              type="primary"
              class="margin-right-20"
              @click="confirmUseFile"
              :disabled="selectList.length === 0"
            >
              使用选中图片
              <div class="num_icon">({{ selectList.length }} / {{ fileMax }})</div>
            </el-button>
            <upload-image @query="query"></upload-image>
          </div>
          <div class="right_main">
            <!-- 分类 -->
            <cate-view @rs-cate="selectCate"></cate-view>
            <div class="right_image_main">
              <div
                class="image"
                :class="{ on: selectList.find((it: any) => it.id === item.id) }"
                v-for="item in state.dataList"
                :key="item.id"
                v-loading="state.listLoading"
                @click="selectTag(item)"
              >
                <oss-image :paths="[item.path]" :width="100" :height="100" :disabled="false"></oss-image>
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
        </div>
      </div>
    </el-dialog>
  </div>
</template>
<script setup lang="ts">
import { nextTick, onMounted, provide, ref } from 'vue'
import usePropData from '@/hooks/propData'
import OssImage from '@/components/OssImage.vue'
import useCurd from '@/hooks/curd'
import { ElMessage } from 'element-plus'
import UploadImage from '@/components/uploadImage.vue'
import CateView from '@/views/library/components/cate.vue'

const dialogVisible = ref(false) // 最初始弹窗
const cate_id = ref(null)

const { state, query, sizeChangeHandle, currentChangeHandle } = useCurd({
  url: '/materials',
  queryForm: {
    cate_id: cate_id,
    page: 1
  }
})

const emits = defineEmits(['confirm'])
const props = withDefaults(
  defineProps<{
    max?: number
    type?: string
    kind?: string
  }>(),
  {
    max: 1,
    type: 'image',
    kind: 'avatar'
  }
)

const fileMax = usePropData(() => props.max, 0)

// 图片存储
const selectList = ref<any[]>([])

// 点击选中/取消图片
const selectTag = (curItem: any) => {
  const current = selectList.value.find((item: any) => item.id === curItem.id)
  if (current) {
    selectList.value = selectList.value.filter((item: any) => item.id !== curItem.id)
  } else {
    if (selectList.value.length >= fileMax.value) {
      ElMessage.warning('最多只能选择' + fileMax.value + '张图片')
    } else {
      selectList.value.push(curItem)
    }
  }
}

/// 确定使用选中的素材
const confirmUseFile = () => {
  // 获取所有的路径
  emits(
    'confirm',
    selectList.value.map((item: any) => item.path)
  )
  dialogVisible.value = false
  nextTick(() => {
    selectList.value = []
  })
}

// 打开弹窗
const openDialog = () => {
  selectList.value = []
  dialogVisible.value = true
}

const selectCate = (id: any) => {
  selectList.value = []
  cate_id.value = id
  query()
}

provide('query', query)

defineExpose({
  openDialog
})
</script>

<style lang="scss" scoped>
:deep(.el-tree-node__content) {
  padding: 5px 0;
}
:deep(.el-space__item) {
  margin-right: 20px !important;
}
.right_image {
  .right-top {
    display: flex;
    align-items: center;
    width: 100%;
  }
  .right_main {
    .right_image_main {
      margin-bottom: 10px;
      padding-top: 20px;
      display: flex;
      align-content: flex-start;
      flex-wrap: wrap;
      .image {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 120px;
        height: 120px;
        background-color: #f8f8f8;
      }
    }
  }
}
// 高亮
:deep(.el-tree--highlight-current .el-tree-node.is-current > .el-tree-node__content) {
  background-color: rgba(135, 206, 235, 0.2);
  color: #409eff;
}

.on {
  border: 2px solid var(--el-color-primary);
}

// 预览/删除的大小
:deep(.el-upload-list__item-actions) {
  font-size: 14px;
}
.preview {
  width: 100%;
  height: 100%;
}
</style>
