<script setup lang="ts">
import useForm from '@/hooks/form'
import { onMounted, ref } from 'vue'
import { InfoFilled } from '@element-plus/icons-vue'
import { ApiDomainEnableList } from '@/api/domain'
import { ApiAppList } from '@/api/app'
import type { linkConfig, QR } from '@/models/link'
import OssImage from '@/components/OssImage.vue'
import UploadImage from '@/components/uploadImage.vue'
import { userStore } from '@/stores'
import { getNextMonth } from '@/utils/dateTime'
import MaterialSelect from '@/components/MaterialSelect/MaterialSelect.vue'

const formRef = ref()
const { formData, formLoading } = useForm({
  formRef,
  defVal(detail) {
    return {
      type: '5', // 类型
      icon: detail?.icon, // 封面图
      remark: detail?.remark, // 备注
      config: detail?.config
        ? detail?.config
        : ({ wx: { qr: [] as QR[], switch_type: 1, uv_limit_type: 1 } } as linkConfig) // 无风险域名 / 小程序 / 地址
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
  'config.wx.title': [{ required: true, message: '请输入标题', trigger: 'blur' }],
  'config.domain_id': [{ required: true, message: '请选择域名', trigger: 'blur' }],
  'config.min_id': [{ required: true, message: '请选择小程序', trigger: 'blur' }],
  'config.url': [{ required: true, message: '请选择二维码图片', trigger: 'blur' }]
}

// 创建二维码管理列表
const qrItem = ref<any>({
  sort: null,
  name: '',
  path: '',
  expired_at: '',
  uv_limit_num: null
})
const addQRItem = () => {
  if (formData.value.config.wx.qr) {
    qrItem.value.sort = formData.value.config.wx.qr.length + 1
    qrItem.value.name = '默认名称' + qrItem.value.sort
    formData.value.config.wx.qr.push(JSON.parse(JSON.stringify(qrItem.value)))
  }
}

// 删除二维码
const deleteItem = (index: any) => {
  // 删除点击对应下表一致的数据
  formData.value.config.wx.qr.splice(index, 1)
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
      <el-form-item prop="config.wx.title" label="标题">
        <el-input style="width: 100%" v-model="formData.config.wx.title" placeholder="请输入标题"></el-input>
      </el-form-item>
      <el-form-item label="副标题">
        <el-input v-model="formData.config.wx.sub_title" placeholder="请输入副标题"></el-input>
      </el-form-item>

      <el-form-item label="卡片封面图" required>
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
      <el-form-item label="微信头像" >
        <div class="item-box">
          <oss-image
            class="margin-right-20"
            :paths="formData.config.wx.avatar ? [formData.config.wx.avatar] : []"
            :width="100"
            :height="100"
          ></oss-image>
          <div>
            <div class="function-img">
              <upload-image
                class="margin-right-10"
                :type="['image/jpg', 'image/jpeg', 'image/png']"
                :size="1"
                v-model="formData.config.wx.avatar"
              ></upload-image>
              <material-select v-model="formData.config.wx.avatar" class="margin-right-20"></material-select>
            </div>
            <span class="tip">只能上传jpg/png，建议大小80*80，且不超过1Mb</span>
          </div>
        </div>
      </el-form-item>
      <el-form-item label="二维码管理" required>
        <el-table
          :data="formData.config.wx.qr"
          :header-cell-style="{
            'text-align': 'center',
            'background-color': '#F1F1F1',
            height: '50px',
            color: '#333'
          }"
          :cell-style="{ padding: '0', 'text-align': 'center', height: '50px', color: '#333' }"
        >
          <el-table-column prop="sort" label="优先级">
            <template #default="{ row }">
              <el-input-number style="width: 100px" v-model="row.sort"> </el-input-number>
            </template>
          </el-table-column>
          <el-table-column prop="name" label="二维码文件名">
            <template #default="{ row }">
              <el-input v-model="row.name"> </el-input>
            </template>
          </el-table-column>
          <el-table-column prop="path" label="二维码">
            <template #default="{ row }">
              <upload-image v-if="!row.path" v-model="row.path"></upload-image>
              <oss-image v-else :paths="[row.path]" :width="40" :height="40"></oss-image>
            </template>
          </el-table-column>
          <el-table-column prop="expired_at" label="失效日期">
            <template #default="{ row }">
              <el-date-picker v-model="row.expired_at" value-format="YYYY-MM-DD" type="date" placeholder="不选为永久" />
            </template>
          </el-table-column>
          <el-table-column prop="uv_limit_num" label="访问上限(UV)" :width="200">
            <template #default="{ row }">
              <el-input-number style="width: 170px" v-model="row.uv_limit_num" placeholder="不填为无限"> </el-input-number>
            </template>
          </el-table-column>
          <el-table-column label="操作">
            <template #default="{ $index }">
              <el-button type="primary" link @click="deleteItem($index)">删除</el-button>
            </template>
          </el-table-column>
        </el-table>
        <el-button class="add-qr" @click="addQRItem">点击添加二维码</el-button>
      </el-form-item>
      <el-form-item label="二维码切换方式">
        <div>
          <el-radio-group v-model="formData.config.wx.switch_type">
            <el-radio :value="1" :label="1">轮询切换</el-radio>
            <el-radio :value="3" :label="2">随机切换</el-radio>
          </el-radio-group>
          <div class="qr-tip">
            <el-icon class="margin-right-10"><InfoFilled /></el-icon>
            <span v-if="formData.config.wx.switch_type === 1">根据二维码的优先级顺序，平均展示给访问用户</span>
            <span v-if="formData.config.wx.switch_type === 2">将所上传的二维码随机展示</span>
          </div>
        </div>
      </el-form-item>
      <el-form-item label="二维码访问上限类型">
        <div>
          <el-radio-group v-model="formData.config.wx.uv_limit_type">
            <el-radio :value="1" :label="1">积累限制</el-radio>
            <el-radio :value="2" :label="2">每日限制</el-radio>
          </el-radio-group>
          <div class="qr-tip">
            <el-icon class="margin-right-10"><InfoFilled /></el-icon>
            <span v-if="formData.config.wx.uv_limit_type === 1"
              >所上传的每个二维码，[累计访问]次数达到所设置的访问上限后，则按规则切换至其他二维码</span
            >
            <span v-if="formData.config.wx.uv_limit_type === 2"
              >所上传的每个二维码，[每日限制]次数达到所设置的访问上限后，则按规则切换至其他二维码</span
            >
          </div>
        </div>
      </el-form-item>
      <el-form-item prop="config.domain_id" label="无风险域名">
        <el-select v-model="formData.config.domain_id" placeholder="请选择">
          <el-option v-for="item in domainList" :key="item.id" :label="item.title" :value="item.id"></el-option>
        </el-select>
      </el-form-item>
      <el-form-item prop="config.min_id" label="落地小程序">
        <el-select v-model="formData.config.min_id" placeholder="请选择">
          <el-option v-for="item in miniProgramList" :key="item.id" :label="item.name" :value="item.id"></el-option>
        </el-select>
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
          <span class="show-des-t">{{ formData.config.wx.title || '卡片标题' }}</span>
          <span class="show-des-b">{{ formData.config.wx.sub_title || '卡片描述' }}</span>
        </div>
      </div>
      <!--   微信预览   -->
      <div class="wx-box">
        <img class="wx-img" src="@/assets/link/electric.png" alt="" />
        <div class="wx-main">
          <div class="wx-header">
            <oss-image
              class="margin-right-20"
              :paths="formData.config.wx.avatar ? [formData.config.wx.avatar] : []"
              :width="60"
              :height="60"
            ></oss-image>
            <div class="show-des">
              <span class="show-des-t">{{ formData.config.wx.title || '标题' }}</span>
              <span class="show-des-b">{{ formData.config.wx.sub_title || '副标题' }}</span>
            </div>
          </div>
          <div class="wx-qr">
            <oss-image
              class="margin-right-20"
              :paths="formData.config.wx.qr.length > 0 ? [formData.config.wx.qr[0].path] : []"
              :width="160"
              :height="160"
            ></oss-image>
          </div>
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
  margin-bottom: 180px;
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
