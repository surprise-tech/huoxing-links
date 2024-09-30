<template>
  <div>
    <el-button type="primary" @click="onFileChange">选择图片</el-button>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import jsQR from 'jsqr'

const emit = defineEmits(['update:modelValue'])
defineProps<{
  modelValue: any
}>()

const decodedUrl = ref<any>(null)

const onFileChange = async () => {
  const inp = document.createElement('input')
  inp.type = 'file'
  inp.accept = 'image/*'
  // 打开文件选择对话框
  inp.click()
  inp.addEventListener('change', (event: any) => {
    if (!event.target.files || !event.target.files[0]) return

    const file = event.target.files[0]
    const reader = new FileReader()

    reader.onload = (e: any) => {
      const imageData = e.target.result
      const canvas = document.createElement('canvas')
      const ctx: any = canvas.getContext('2d')

      const img: any = new Image()
      img.onload = () => {
        canvas.width = img.naturalWidth
        canvas.height = img.naturalHeight
        ctx.drawImage(img, 0, 0)
        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height)

        const code = jsQR(imageData.data, imageData.width, imageData.height)
        if (code) {
          decodedUrl.value = code.data
          emit('update:modelValue', decodedUrl.value)
        } else {
          alert('无法识别二维码，请确保上传的是有效的二维码图片。')
        }
      }
      img.src = imageData
    }

    reader.readAsDataURL(file)
  })
}
</script>
<style lang="scss" scoped></style>
