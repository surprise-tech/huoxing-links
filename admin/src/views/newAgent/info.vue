<template>
  <el-card style="min-height: calc(100vh - 150px)">
    <el-row :gutter="10">
      <el-col :xs="24" :sm="12">
        <div class="m-b-20">上传LOGO图片信息</div>
        <div class="item-box">
          <oss-image
            class="margin-right-20"
            :paths="formData.logo ? [formData.logo] : []"
            :width="100"
            :height="100"
          ></oss-image>
          <div>
            <div class="function-img m-b-20">
              <upload-image
                class="margin-right-10"
                :type="['image/jpg', 'image/jpeg', 'image/png']"
                :size="1"
                v-model="formData.logo"
              ></upload-image>
              <material-select v-model="formData.logo" class="margin-right-20"></material-select>
            </div>
            <span class="tip">只能上传jpg/png，建议大小100*100，且不超过1Mb</span>
          </div>
        </div>
      </el-col>
      <el-col :xs="24" :sm="12">
        <div class="m-b-20">上传微信二维码</div>
        <div class="item-box">
          <oss-image
            class="margin-right-20"
            :paths="formData.wechat_qr ? [formData.wechat_qr] : []"
            :width="100"
            :height="100"
          ></oss-image>
          <div>
            <div class="function-img m-b-20">
              <upload-image
                class="margin-right-10"
                :type="['image/jpg', 'image/jpeg', 'image/png']"
                :size="1"
                v-model="formData.wechat_qr"
              ></upload-image>
              <material-select v-model="formData.wechat_qr" class="margin-right-20"></material-select>
            </div>
            <span class="tip">只能上传jpg/png，建议大小100*100，且不超过1Mb</span>
          </div>
        </div>
      </el-col>
      <el-col :xs="24" :sm="12">
        <div class="margin-b-10" style="margin-top: 40px">网站标题</div>
        <el-input v-model="formData.site_name" placeholder="请输入网站标题"></el-input>
      </el-col>
    </el-row>
    <div style="margin-top: 100px">
      <el-divider></el-divider>
    </div>
    <div style="margin-top: 60px" class="text-center">
      <el-button type="primary" style="width: 200px" @click="save">保存</el-button>
    </div>
  </el-card>
</template>
<script setup lang="ts">
import OssImage from '@/components/OssImage.vue'
import MaterialSelect from '@/components/MaterialSelect/MaterialSelect.vue'
import UploadImage from '@/components/uploadImage.vue'
import { reactive, watch } from 'vue'
import { userStore } from '@/stores'
import { EditAgentLogo } from '@/api/newAgent'
import { ElMessage } from 'element-plus'
const formData = reactive({
  logo: '',
  wechat_qr: '',
  site_name: ''
})
watch(
  () => userStore.userInfo,
  () => {
    formData.logo = userStore.userInfo.logo || ''
    formData.wechat_qr = userStore.userInfo.wechat_qr || ''
    formData.site_name = userStore.userInfo.site_name || ''
  },
  {
    immediate: true,
    deep: true
  }
)
const save = () => {
  EditAgentLogo({
    logo: formData.logo,
    wechat_qr: formData.wechat_qr,
    site_name: formData.site_name
  }).then(() => {
    userStore.refreshUserInfo()
    ElMessage.success('保存成功')
  })
}
</script>
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
