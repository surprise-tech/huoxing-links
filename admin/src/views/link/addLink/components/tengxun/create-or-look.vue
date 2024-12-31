<script setup lang="ts">
import { QuestionFilled } from '@element-plus/icons-vue'
import MaterialSelect from '@/components/MaterialSelect/MaterialSelect.vue'
import useForm from '@/hooks/form'
import { onMounted, ref } from 'vue'
import { ApiDomainEnableList } from '@/api/domain'
import { ApiAppList } from '@/api/app'
import type { linkConfig } from '@/models/link'
import OssImage from '@/components/OssImage.vue'
import UploadImage from '@/components/uploadImage.vue'
import QrLink from '@/views/link/addLink/components/qr/qrLink.vue'
import { configStore, userStore } from '@/stores'
import { getNextMonth } from '@/utils/dateTime'

const formRef = ref()
const { formData, formLoading } = useForm({
  formRef,
  defVal(detail) {
    return {
      title: detail?.title, // 标题
      type: '11', // 类型
      description: detail?.description, // 描述
      icon: detail?.icon, // 封面图
      remark: detail?.remark, // 备注
      config: detail?.config ? detail?.config : ({} as linkConfig) // 无风险域名 / 小程序 / 地址
    }
  }
})

// 获取域名列表
const domainList = ref()
const getDomainList = async () => {
  domainList.value = await ApiDomainEnableList()
}

// 获取小程序列表
const miniProgramList = ref()
const getMiniProgramList = async () => {
  miniProgramList.value = await ApiAppList()
}

const rules = {
  title: [{ required: true, message: '请输入标题', trigger: 'blur' }],
  'config.domain_id': [{ required: true, message: '请选择域名', trigger: 'blur' }],
  // 'config.min_id': [{ required: true, message: '请选择小程序', trigger: 'blur' }],
  'config.url': [{ required: true, message: '请选择二维码图片', trigger: 'blur' }]
}

const userInfo = userStore
onMounted(() => {
  getDomainList()
  getMiniProgramList()
})
</script>

<template>
  <div style="margin-top: -30px" class="m-b-20">
    <el-space class="text-main">
      <span>总个数：{{ userInfo.userInfo.link_amount }}个</span>
      <span>消耗个数：1个</span>
    </el-space>
  </div>
  <div class="main">
    <el-form
      :model="formData"
      :rules="rules"
      ref="formRef"
      v-loading="formLoading"
      class="form-box margin-right-40"
      label-position="top"
    >
      <el-form-item prop="title" label="卡片标题">
        <el-input v-model="formData.title" placeholder="请输入卡片标题"></el-input>
      </el-form-item>
      <el-form-item label="卡片描述">
        <el-input v-model="formData.description" placeholder="请输入卡片描述"></el-input>
      </el-form-item>
      <el-form-item label="卡片封面图">
        <div class="item-box">
          <oss-image
            class="margin-right-20"
            :paths="formData.icon ? [formData.icon] : []"
            :width="100"
            :height="100"
          ></oss-image>
          <div>
            <div class="function-img">
              <upload-image
                class="margin-right-10"
                :type="['image/jpg', 'image/jpeg', 'image/png']"
                :size="1"
                v-model="formData.icon"
              ></upload-image>
              <material-select v-model="formData.icon" class="margin-right-20"></material-select>
            </div>
            <span class="tip">只能上传jpg/png，建议大小100*100，且不超过1Mb</span>
          </div>
        </div>
      </el-form-item>
      <el-form-item label="卡片备注">
        <el-input v-model="formData.remark" placeholder="请输入卡片备注，例如：xxx活动（选填）"></el-input>
      </el-form-item>
      <el-form-item prop="config.domain_id" label="无风险域名">
        <el-select v-model="formData.config.domain_id" placeholder="请选择">
          <el-option v-for="item in domainList" :key="item.id" :label="item.title" :value="item.id"></el-option>
        </el-select>
      </el-form-item>

      <el-form-item prop="config.url" label="优码二维码">
        <qr-link class="margin-b-10" v-model="formData.config.url"></qr-link>
        <el-input v-model="formData.config.url" disabled></el-input>
        <div style="font-size: 12px">
          <el-icon><QuestionFilled /></el-icon>
          上传在优码图片类使用微信二维码、公众号二维码、企业微信二维码、视频号二维码生成的二维码<br />
        </div>
      </el-form-item>
    </el-form>
    <div class="show hidden-md-and-down">
      <el-divider>
        <span>抖音卡片预览</span>
      </el-divider>
      <div class="show-box">
        <oss-image
          class="margin-right-20"
          :paths="formData.icon ? [formData.icon] : []"
          :width="100"
          :height="100"
        ></oss-image>
        <div class="show-des">
          <span class="show-des-t">{{ formData.title || '卡片标题' }}</span>
          <span class="show-des-b">{{ formData.description || '卡片描述' }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped lang="scss">
.main {
  display: flex;
  .form-box {
    flex: 2;
  }
  .show {
    flex: 1;
  }
}
.tip {
  color: #999;
}

.show-box {
  display: flex;
  width: 100%;
  height: 100px;
  border: 1px solid #f5f5f7;
  background-color: #f9fafe;
}

.show-des {
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: center;
  .show-des-t {
    font-size: 20px;
    color: #333;
  }
  .show-des-b {
    font-size: 14px;
    color: #999;
  }
}

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
