<script setup lang="ts">
import OssImage from '@/components/OssImage.vue'
import UploadImage from '@/components/uploadImage.vue'
import MaterialSelect from '@/components/MaterialSelect/MaterialSelect.vue'
import { ref, watch } from 'vue'
import { ElMessage } from 'element-plus'
import { ApiSaveSet } from '@/api/comment'
import { configStore } from '@/stores'

const props = defineProps<{
  data: any
}>()

const webFormData = ref({
  web_site_title: '',
  web_site_logo: '',
  web_site_bottom_logo: '',
  web_site_customer_service: ''
})
watch(
  () => props.data,
  () => {
    webFormData.value.web_site_title = props.data.web_site_title
    webFormData.value.web_site_logo = props.data.web_site_logo
    webFormData.value.web_site_bottom_logo = props.data.web_site_bottom_logo
    webFormData.value.web_site_customer_service = props.data.web_site_customer_service
  },
  {
    deep: true,
    immediate: true
  }
)
const clickSubmit = () => {
  ApiSaveSet(webFormData.value).then(() => {
    ElMessage.success('保存成功')
    configStore.refreshConfig()
  })
}
</script>

<template>
  <el-card>
    <div style="width: 500px">
      <div class="margin-b-10" style="margin-top: 20px">网站标题</div>
      <el-input v-model="webFormData.web_site_title" placeholder="请输入网站标题"></el-input>
      <div class="m-b-20 m-t-20">顶部LOGO图片信息</div>
      <div class="item-box">
        <oss-image
          class="margin-right-20"
          :paths="webFormData.web_site_logo ? [webFormData.web_site_logo] : []"
          :width="100"
          :height="100"
        ></oss-image>
        <div>
          <div class="function-img m-b-20">
            <upload-image
              class="margin-right-10"
              :type="['image/jpg', 'image/jpeg', 'image/png']"
              :size="1"
              v-model="webFormData.web_site_logo"
            ></upload-image>
            <material-select v-model="webFormData.web_site_logo" class="margin-right-20"></material-select>
          </div>
          <span class="tip">只能上传jpg/png，建议大小100*100，且不超过1Mb</span>
        </div>
      </div>
      <div class="m-b-20 m-t-20">上传客服二维码</div>
      <div class="item-box">
        <oss-image
          class="margin-right-20"
          :paths="webFormData.web_site_customer_service ? [webFormData.web_site_customer_service] : []"
          :width="100"
          :height="100"
        ></oss-image>
        <div>
          <div class="function-img m-b-20">
            <upload-image
              class="margin-right-10"
              :type="['image/jpg', 'image/jpeg', 'image/png']"
              :size="1"
              v-model="webFormData.web_site_customer_service"
            ></upload-image>
            <material-select v-model="webFormData.web_site_customer_service" class="margin-right-20"></material-select>
          </div>
          <span class="tip">只能上传jpg/png，建议大小100*100，且不超过1Mb</span>
        </div>
      </div>
      <div class="m-b-20 text-center" style="margin-top: 60px">
        <el-button type="primary" size="large" @click="clickSubmit">保存</el-button>
      </div>
    </div>
  </el-card>
</template>

<style scoped lang="scss">
.item-box {
  display: flex;
  align-items: center;
}
.function-img {
  margin-top: 10px;
  display: flex;
  align-items: center;
}
</style>
