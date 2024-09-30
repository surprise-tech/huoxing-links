<template>
  <div class="upload-box">
    <el-upload
      ref="uploadRef"
      v-model:file-list="fileList"
      :action="props.action"
      :headers="uploadHeader"
      name="image"
      :before-upload="beforeUpload"
      :on-success="handleSuccess"
    >
      <el-button type="primary">上传图片</el-button>
      <template #file>
        <div style="display: none"></div>
      </template>
    </el-upload>
  </div>
</template>
<script lang="ts" setup>
import { userStore } from '@/stores'
import { inject, ref } from 'vue'
import { ElMessage, genFileId, type UploadProps, type UploadRawFile } from 'element-plus'

const emits = defineEmits(['update:modelValue', 'query'])
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
    action: '/api/materials'
  }
)

const uploadRef = ref()
const fileList = ref()
const uploadLoading = ref(false)
const uploadHeader = {
  Authorization: 'Bearer ' + userStore.getToken()
}
// const query: any = inject('query')
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
    // 读取图片信息
    // const reader = new FileReader()
    // reader.readAsDataURL(rawFile as Blob)
    // reader.onload = () => {
    //   const img = new Image()
    //   img.src = reader.result
    //   img.onload = () => {
    //     const width = img.width
    //     const height = img.height
    //     console.log(width, height)
    //
    //     if (width > props.width || height > props.height) {
    //       ElMessage.warning('图片尺寸必须为正方形')
    //       uploadLoading.value = false
    //       return false
    //     }
    //     return true
    //   }
    // }
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
    emits('update:modelValue', file.path)
    ElMessage.success('上传成功')
    emits('query')
    uploadLoading.value = false
  }, 1000)
}
</script>
<style lang="scss" scoped>
.upload-box {
  width: 100px;
  height: 32px;
}
</style>
