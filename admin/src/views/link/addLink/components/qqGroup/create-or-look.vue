<script setup lang="ts">
import useForm from '@/hooks/form'
import { onMounted, ref } from 'vue'
import OssImage from '@/components/OssImage.vue'
import MaterialSelect from '@/components/MaterialSelect/MaterialSelect.vue'
import TailorUpload from '@/components/tailorUpload.vue'
import UploadImage from '@/components/uploadImage.vue'
import { ApiDomainEnableList } from '@/api/domain'

const formRef = ref()
const { formData, formLoading } = useForm({
  formRef,
  defVal(detail) {
    return {
      id: detail?.id,
      type: 7, // 类型
      title: detail?.title,
      icon: detail?.icon,
      remark: detail?.remark, // 备注
      description: detail?.description, //描述
      config: detail?.config
        ? detail?.config
        : ({
            version: 1, // 1是号码，2是二维码,
            nickname: '',
            number: '',
            qr: ''
          } as any)
    }
  }
})

const rules = {
  title: [{ required: true, message: '请输入标题', trigger: 'blur' }],
  icon: [{ required: true, message: '请上传卡片封面图', trigger: 'blur' }],
  'config.number': [{ required: true, message: '请输入QQ群号码', trigger: 'blur' }],
  'config.qr': [{ required: true, message: '请上传QQ群二维码', trigger: 'blur' }],
  'config.nickname': [{ required: true, message: '请输入QQ群昵称', trigger: 'blur' }],
  'config.domain_id': [{ required: true, message: '请选择域名', trigger: 'blur' }],
}
// 获取域名列表
const domainList = ref()
const getDomainList = async () => {
  domainList.value = await ApiDomainEnableList()
}
onMounted(() => {
  getDomainList()
})
</script>

<template>
  <div class="main">
    <el-form
      :model="formData"
      :rules="rules"
      ref="formRef"
      v-loading="formLoading"
      class="form-box margin-right-40"
      label-position="top"
    >
      <el-form-item prop="title" label="标题">
        <el-input style="width: 100%" v-model="formData.title" placeholder="请输入标题"></el-input>
      </el-form-item>
      <el-form-item prop="description" label="描述">
        <el-input style="width: 100%" v-model="formData.description" placeholder="请输入描述"></el-input>
      </el-form-item>
      <el-form-item label="备注">
        <el-input v-model="formData.remark" placeholder="请输入备注"></el-input>
      </el-form-item>
      <el-form-item label="卡片封面图" prop="icon">
        <div class="item-box">
          <oss-image
            class="margin-right-20"
            :paths="formData.icon ? [formData.icon] : []"
            :width="100"
            :height="100"
          ></oss-image>
          <div>
            <div class="function-img">
              <div class="m-r-20">
                <TailorUpload v-model="formData.icon"></TailorUpload>
              </div>
              <material-select v-model="formData.icon" class="margin-right-20"></material-select>
            </div>
            <span class="tip">只能上传jpg/png，建议大小100*100，且不超过1Mb</span>
          </div>
        </div>
      </el-form-item>
      <el-form-item prop="config.nickname" label="QQ群昵称">
        <el-input style="width: 100%" v-model="formData.config.nickname" placeholder="请输QQ群昵称"></el-input>
      </el-form-item>
      <el-form-item required class="m-t-20">
        <el-radio-group v-model="formData.config.version">
          <el-radio :value="1" :label="1">旧版</el-radio>
          <el-radio :value="2" :label="2">新版</el-radio>
        </el-radio-group>
      </el-form-item>

      <el-form-item label="QQ群号码" prop="config.number" v-if="formData.config.version === 1">
        <el-input v-model="formData.config.number" placeholder="请输入QQ群号码"></el-input>
      </el-form-item>
      <el-form-item label="QQ群二维码" prop="config.qr" v-if="formData.config.version === 2">
        <div class="item-box">
          <oss-image
            class="margin-right-20"
            :paths="formData.config.qr ? [formData.config.qr] : []"
            :width="100"
            :height="100"
          ></oss-image>
          <div>
            <div class="function-img">
              <div class="m-r-20">
                <upload-image
                  :type="['image/jpg', 'image/jpeg', 'image/png']"
                  :size="1"
                  v-model="formData.config.qr"
                ></upload-image>
                <!--                <TailorUpload v-model="formData.config.qr"></TailorUpload>-->
              </div>
              <material-select v-model="formData.config.qr" class="margin-right-20"></material-select>
            </div>
            <span class="tip">只能上传jpg/png，建议大小100*100，且不超过1Mb</span>
          </div>
        </div>
      </el-form-item>
      <el-form-item prop="config.domain_id" label="无风险域名">
        <el-select v-model="formData.config.domain_id" placeholder="请选择" style="width: 100%">
          <el-option v-for="item in domainList" :key="item.id" :label="item.title" :value="item.id"></el-option>
        </el-select>
      </el-form-item>
    </el-form>
    <div class="show hidden-md-and-down">
      <el-divider>
        <span>预览</span>
      </el-divider>
      <div class="show-box">
        <img class="show-box" src="@/assets/link/qqluodi.png" />
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
  height: 400px;
  margin: 0 auto;
  width: 200px;
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

.wx-box {
  width: 100%;
  height: 500px;
  border: 2px solid #f1f1f1;
  border-radius: 20px;
}
.wx-img {
  width: 100%;
}
.wx-main {
  padding: 100px 0 0 80px;
  display: flex;
  flex-direction: column;
  width: 100%;
}
.wx-header {
  margin-bottom: 20px;
  display: flex;
  width: 200px;
  height: 60px;
}

.add-qr {
  width: 100%;
  color: var(--el-color-primary);
}

.qr-tip {
  padding: 0 10px;
  display: flex;
  align-items: center;
  width: 100%;
  color: var(--el-color-primary);
  background-color: #eff5ff;
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
