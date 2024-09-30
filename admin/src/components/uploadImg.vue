<template>
  <div class="upload-box">
    <el-upload
      ref="uploadRef"
      v-model:file-list="fileList"
      :action="props.action"
      :headers="uploadHeader"
      name="image"
      :limit="1"
      class="avatar-warp"
      :on-exceed="handleExceed"
      :before-upload="beforeUpload"
      :on-success="handleSuccess"
      :on-preview="handlePictureCardPreview"
      :on-remove="handleRemove"
    >
      <el-icon><Picture /></el-icon>
    </el-upload>

    <el-dialog v-model="dialogVisible">
      <oss-image
        v-if="dialogImageUrl"
        :height="200"
        :width="200"
        :paths="[dialogImageUrl]"
      ></oss-image>
    </el-dialog>
  </div>
</template>
<script lang="ts" setup>
import { Picture } from '@element-plus/icons-vue'
import { userStore } from '@/stores'
import { inject, ref } from 'vue'
import {ElMessage, genFileId, type UploadInstance, type UploadProps, type UploadRawFile} from 'element-plus'
import type { UploadFile } from 'element-plus'
import OssImage from '@/components/OssImage.vue'

const emits = defineEmits(['update:modelValue'])
const props = withDefaults(
  defineProps<{
    modelValue?: string | string[]
    type?: any
    size?: any
    width?: any
    height?: any
    action?: string
  }>(),
  {
    action: '/api/material-upload'
  }
)

const uploadRef = ref()
const fileList = ref()
const uploadLoading = ref(false)
const dialogVisible = ref(false)
const dialogImageUrl = ref()
// 预览
const handlePictureCardPreview = (file: any) => {
  dialogImageUrl.value = file.response.path
  dialogVisible.value = true
}
const uploadHeader = {
  Authorization: 'Bearer ' + userStore.getToken()
}
// 删除
const handleRemove = () => {
  emits('update:modelValue', [])
}

const query: any = inject('query')
const handleExceed: UploadProps['onExceed'] = (files) => {
  uploadRef.value!.clearFiles()
  const file = files[0] as UploadRawFile
  file.uid = genFileId()
  uploadRef.value!.handleStart(file)
}
const beforeUpload = (rawFile: any) => {
  if ((props.type && props.type.length > 0) || props.size) {
    uploadLoading.value = true
    console.log(rawFile.size, 'rawFile.size')
    const isLtM = rawFile.size < 1024 * 1024 * props.size
    const isValidFormat = props.type.indexOf(rawFile.type) > -1
    if (!isLtM) {
      ElMessage.warning(`图片大小不超过${props.size}M`)
      uploadLoading.value = false
      return false
    }
    if (!isValidFormat) {
      ElMessage.warning(`图片只能是${props.type.join(',')}格式!`)
      uploadLoading.value = false
      return false
    }
  } else {
    return true
  }
}

// 文件上传

const handleSuccess = (file: any) => {
  uploadLoading.value = true
  setTimeout(() => {
    emits('update:modelValue', file)
    ElMessage.success('上传成功')
    uploadLoading.value = false
  }, 1000)
}
</script>
<style lang="scss" scoped>
.upload-box {
  width: 100px;
  height: 32px;
}
.preview {
  width: 100%;
  height: 100%;
}
// 上传样式
// 框架尺寸
:deep(.el-upload) {
  width: 60px;
  height: 60px;
  border-radius: 0;
  background-color: #ffffff;
  border: 1px solid #efefef;
}
// 框架的icon大小
:deep(.el-upload--picture-card .el-icon) {
  width: 20px;
  height: 20px;
  color: #cccccc;
}
// 添加图片后框架大小
:deep(.el-upload-list--picture-card .el-upload-list__item) {
  width: 60px;
  height: 60px;
  border-radius: 0;
}
// 预览/删除的大小
:deep(.el-upload-list__item-actions) {
  font-size: 14px;
}
.preview {
  width: 100%;
  height: 100%;
}

// 限制一张隐藏框架
.disabled :deep(.el-upload--picture-card) {
  display: none;
}

// 隐藏删除提示
:deep(.el-icon--close-tip) {
  display: none !important;
}
</style>
