<template>
  <el-button type="primary" @click="dialogVisible = true">上传图片</el-button>
  <el-dialog
    title="上传图片"
    v-model="dialogVisible"
    :show-close="false"
    :close-on-click-modal="false"
    :close-on-press-escape="false"
    destroy-on-close
    width="600px"
  >
    <div class="avatar-container">
      <!-- 待上传图片 -->

      <div v-show="!options.img">
        <el-upload
          class="upload"
          ref="tailorUpload"
          action="#"
          :on-change="upload"
          :show-file-list="false"
          :auto-upload="false"
        >
          <el-button slot="trigger" type="primary" ref="uploadBtn"> 选择图片 </el-button>
        </el-upload>
        <div>支持jpg、png格式的图片，大小不超过5M</div>
      </div>

      <div v-show="options.img" class="avatar-crop">
        <vueCropper
          class="crop-box"
          ref="cropper"
          :img="options.img"
          :autoCrop="options.autoCrop"
          :fixedBox="options.fixedBox"
          :canMoveBox="options.canMoveBox"
          :autoCropWidth="options.autoCropWidth"
          :autoCropHeight="options.autoCropHeight"
          :centerBox="options.centerBox"
          :fixed="options.fixed"
          :fixedNumber="options.fixedNumber"
          :canMove="options.canMove"
          :canScale="options.canScale"
          :full="options.full"
          :outputSize="0.1"
          outputType="png"
        ></vueCropper>
      </div>
    </div>
    <template #footer>
      <div class="dialog-footer">
        <div class="reupload" @click="reupload">
          <span v-show="options.img">重新上传</span>
        </div>
        <div>
          <el-button @click="closeDialog" :loading="loading">取 消</el-button>
          <el-button type="primary" @click="getCrop" :loading="loading">确 定</el-button>
        </div>
      </div>
    </template>
  </el-dialog>
</template>

<script setup lang="ts">
import { VueCropper } from 'vue-cropper'
import { ref, reactive } from 'vue'
import 'vue-cropper/dist/index.css'
import { ElMessage, type UploadProps } from 'element-plus'
import { ApiUploadBase64 } from '@/api/user'

const cropper = ref()
const uploadBtn = ref()
const tailorUpload = ref()
const dialogVisible = ref(false)
const loading = ref(false)
const options = reactive({
  img: '', // 原图文件
  autoCrop: true, // 默认生成截图框
  fixedBox: false, // 固定截图框大小
  canMoveBox: true, // 截图框可以拖动
  autoCropWidth: 200, // 截图框宽度
  autoCropHeight: 200, // 截图框高度
  fixed: true, // 截图框宽高固定比例
  fixedNumber: [1, 1], // 截图框的宽高比例
  centerBox: true, // 截图框被限制在图片里面
  canMove: true, // 上传图片不允许拖动
  canScale: true, // 上传图片不允许滚轮缩放
  full: false //是否输出原图比例的截图
} as any)
const emits = defineEmits(['update:modelValue'])
const props = defineProps<{
  modelValue?: string | string[]
}>()
const upload: UploadProps['onChange'] = (file: any) => {
  const isIMAGE = file.raw.type === 'image/jpeg' || file.raw.type === 'image/png'
  const isLt5M = file.raw.size / 1024 / 1024 < 5
  if (!isIMAGE) {
    ElMessage.warning('请选择 jpg、png 格式的图片')
    return false
  }
  if (!isLt5M) {
    ElMessage.warning('图片大小不能超过 5MB')
    return false
  }
  let reader = new FileReader()

  reader.readAsDataURL(file.raw)
  //console.log(reader);

  reader.onload = (e: any) => {
    options.img = e.target.result // base64
  }
  tailorUpload.value.clearFiles() //这里处理重新上传时，upload组件change事件错误问题
}
// 关闭弹框
const closeDialog = () => {
  options.img = ''
  dialogVisible.value = false
}

const dealImage = (base64: any, w: any, callback: any) => {
  const newImage: any = new Image()
  let quality = 0.8 //压缩系数0-1之间
  newImage.src = base64
  newImage.setAttribute('crossOrigin', 'Anonymous') //url为外域时需要
  let imgWidth, imgHeight
  newImage.onload = function () {
    imgWidth = newImage.width
    imgHeight = newImage.height
    const canvas = document.createElement('canvas')
    const ctx: any = canvas.getContext('2d')
    if (Math.max(imgWidth, imgHeight) > w) {
      if (imgWidth > imgHeight) {
        canvas.width = w
        canvas.height = (w * imgHeight) / imgWidth
      } else {
        canvas.height = w
        canvas.width = (w * imgWidth) / imgHeight
      }
    } else {
      canvas.width = imgWidth
      canvas.height = imgHeight
      quality = 0.8
    }
    ctx.clearRect(0, 0, canvas.width, canvas.height)
    ctx.drawImage(this, 0, 0, canvas.width, canvas.height)
    let base64 = canvas.toDataURL('image/jpeg', quality) //压缩语句
    // 如想确保图片压缩到自己想要的尺寸,如要求在50-100kb之间，请加以下语句，quality初始值根据情况自定
    //  while (base64.length / 1024 > 100) {
    //  	quality -= 0.01;
    //  	base64 = canvas.toDataURL("image/jpeg", quality);
    //  }
    // // 防止最后一次压缩低于最低尺寸，只要quality递减合理，无需考虑
    //  while (base64.length / 1024 < 10) {
    //  	quality += 0.001;
    //  	base64 = canvas.toDataURL("image/jpeg", quality);
    //  }
    callback(base64 as any) //必须通过回调函数返回，否则无法及时拿到该值
  }
}

// 获取截图信息
const getCrop = () => {
  // 获取截图的 base64 数据

  cropper.value.getCropData((data: any) => {
    dealImage(data, 300, (res: any) => {
      ApiUploadBase64({ image: res })
        .then((resData) => {
          loading.value = false
          emits('update:modelValue', resData.path || '')
          ElMessage.success('上传成功')
          tailorUpload.value.clearFiles()
          closeDialog()
        })
        .catch(() => {
          loading.value = false
        })
    })
  })
}
// 重新上传
const reupload = () => {
  uploadBtn.value.ref.click()
}
</script>

<style lang="scss" scoped>
.dialog-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 14px;
  .reupload {
    color: #409eff;
    cursor: pointer;
  }
}
.avatar-container {
  display: flex;
  justify-content: center;
  align-items: center;
  width: 100%;
  height: 350px;
  background-color: #f0f2f5;
  margin-right: 10px;
  border-radius: 4px;
  .upload {
    text-align: center;
    margin-bottom: 24px;
  }
  .avatar-crop {
    width: 100%;
    height: 350px;
    position: relative;
    .crop-box {
      width: 100%;
      height: 100%;
      border-radius: 4px;
      overflow: hidden;
    }
  }
}
</style>
