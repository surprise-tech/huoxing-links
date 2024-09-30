<template>
  <div :style="'width:' + (props.width || 60) + 'px; height:' + (props.height || 60) + 'px'">
    <div
      v-if="props.paths?.length === 0"
      class="oss-empty"
      :style="'width:' + (props.width || 60) + 'px; height:' + (props.height || 60) + 'px'"
    >
      <el-icon><PictureFilled /></el-icon>
    </div>
    <el-image
      v-else
      v-for="(item, index) in ossPathArr"
      :key="index"
      :src="item"
      :style="'width:' + (props.width || 60) + 'px; height:' + (props.height || 60) + 'px'"
      :initial-index="index"
      preview-teleported
      :preview-src-list="props.disabled ? ossPathArr : []"
      fit="cover"
    >
    </el-image>
  </div>
</template>

<script lang="ts" setup>
import { computed } from 'vue'
import { ImagePath } from '@/hooks/image'
import { PictureFilled } from '@element-plus/icons-vue'

const props = defineProps<{
  paths?: string[]
  width?: number
  height?: number
  disabled?: boolean
}>()
// 展示的图片路径
const ossPathArr = computed(() => {
  return props.paths?.map((item) => ImagePath(item))
})
</script>
<style lang="scss" scoped>
.oss-box {
}

.oss-empty {
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
  background-color: #f5f5f7;
}
</style>
