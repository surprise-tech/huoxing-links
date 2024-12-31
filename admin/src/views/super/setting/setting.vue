<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { ApiGetSetByType, ApiSaveSetByType } from '@/api/comment'
import { ApiVipList } from '@/api/super'
import { ElMessage } from 'element-plus'
import UploadImage from '@/components/uploadImage.vue'
import MaterialSelect from '@/components/MaterialSelect/MaterialSelect.vue'
import OssImage from '@/components/OssImage.vue'
import { configStore } from '@/stores'

const formData = ref<any>({})

const vipPackages = ref<any>([])

const formLoading = ref(false)
const webFormData = ref<any>({})

const clickSubmit = () => {
  formLoading.value = true
  ApiSaveSetByType('base', formData.value)
    .then(() => {
      ElMessage.success('保存成功')
    })
    .finally(() => {
      formLoading.value = false
    })
}
const saveSubmit = () => {
  if (webFormData.value.verify_code_is_open) {
    if (!webFormData.value.send_code_mode ){
      ElMessage.error('请选择发送方式')
      return
    }
    if (webFormData.value.send_code_mode === 1) {
      if (!webFormData.value.ali_sms_key) {
        ElMessage.error('请填写阿里短信key')
        return
      }
      if (!webFormData.value.ali_sms_secret) {
        ElMessage.error('请填写阿里短信secret')
        return
      }
      if (!webFormData.value.ali_sms_secret) {
        ElMessage.error('请填写阿里短信secret')
        return
      }
    } else if (webFormData.value === 2) {
      if (!webFormData.value.mail_from_name) {
        ElMessage.error('请填写发信名称')
        return
      }
      if (!webFormData.value.mail_host) {
        ElMessage.error('请填写服务器地址')
        return
      }
      if (!webFormData.value.mail_port) {
        ElMessage.error('请填写端口')
        return
      }
      if (!webFormData.value.mail_from_address) {
        ElMessage.error('请填写发信地址')
        return
      }
      if (!webFormData.value.mail_username) {
        ElMessage.error('请填写邮箱账号')
        return
      }
      if (!webFormData.value.mail_password) {
        ElMessage.error('请填写邮箱密码')
        return
      }
    }
  }
  formLoading.value = true
  ApiSaveSetByType('web_site', webFormData.value)
    .then(() => {
      ElMessage.success('保存成功')
      configStore.refreshConfig()
    })
    .finally(() => {
      formLoading.value = false
    })
}

onMounted(() => {
  formLoading.value = true
  ApiGetSetByType('base')
    .then((res: any) => {
      formData.value = res
    })
    .finally(() => {
      formLoading.value = false
    })
  ApiGetSetByType('web_site').then((res: any) => {
    webFormData.value = res
  })
  ApiVipList().then((res: any) => {
    vipPackages.value = vipPackages.value.concat(res)
  })
})
</script>

<template>
  <div>
    <el-card title="基础配置">
      <el-row :gutter="20">
        <el-col :xs="24" :sm="12" :md="8">
          <div class="m-b-20">顶部LOGO图片信息</div>
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
                <material-select
                  v-model="webFormData.web_site_logo"
                  class="margin-right-20"
                ></material-select>
              </div>
              <span class="tip">只能上传jpg/png，建议大小100*100，且不超过1Mb</span>
            </div>
          </div>
        </el-col>
        <el-col :xs="24" :sm="12" :md="8">
          <div class="m-b-20">底部LOGO图片信息</div>
          <div class="item-box">
            <oss-image
              class="margin-right-20"
              :paths="webFormData.web_site_bottom_logo ? [webFormData.web_site_bottom_logo] : []"
              :width="100"
              :height="100"
            ></oss-image>
            <div>
              <div class="function-img m-b-20">
                <upload-image
                  class="margin-right-10"
                  :type="['image/jpg', 'image/jpeg', 'image/png']"
                  :size="1"
                  v-model="webFormData.web_site_bottom_logo"
                ></upload-image>
                <material-select v-model="webFormData.web_site_bottom_logo" class="margin-right-20"></material-select>
              </div>
              <span class="tip">只能上传jpg/png，且不超过1Mb</span>
            </div>
          </div>
        </el-col>
        <el-col :xs="24" :sm="12" :md="8">
          <div class="m-b-20">上传客服二维码</div>
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
                <material-select
                  v-model="webFormData.web_site_customer_service"
                  class="margin-right-20"
                ></material-select>
              </div>
              <span class="tip">只能上传jpg/png，建议大小100*100，且不超过1Mb</span>
            </div>
          </div>
        </el-col>

        <el-col :xs="24" :sm="12" :md="6">
          <div class="margin-b-10" style="margin-top: 20px">网站标题</div>
          <el-input v-model="webFormData.web_site_title" placeholder="请输入网站标题"></el-input>
        </el-col>
        <el-divider content-position="left">验证码</el-divider>
        <el-col :xs="24" :sm="12" :md="6">
          <div class="margin-b-10" style="margin-top: 20px">是否开启验证码</div>
          <el-switch style="width: 100%" :active-value="1" :inactive-value="0" v-model="webFormData.verify_code_is_open"></el-switch>
        </el-col>
        <el-col :xs="24" :sm="12" :md="6" v-if="webFormData.verify_code_is_open">
          <div class="margin-b-10" style="margin-top: 20px">验证码发送方式</div>
          <el-select style="width: 100%" v-model="webFormData.send_code_mode" placeholder="请选择验证码发送方式">
            <el-option label="短信" :value="1" />
            <el-option label="邮箱" :value="2" />
          </el-select>
        </el-col>
        <el-col :xs="24" :sm="12" :md="6" v-if="webFormData.send_code_mode === 1">
          <div class="margin-b-10" style="margin-top: 20px">阿里短信key</div>
          <el-input v-model="webFormData.ali_sms_key" placeholder="请输入阿里短信key"></el-input>
        </el-col>
        <el-col :xs="24" :sm="12" :md="6" v-if="webFormData.send_code_mode === 1">
          <div class="margin-b-10" style="margin-top: 20px">阿里短信secret</div>
          <el-input v-model="webFormData.ali_sms_secret" placeholder="请输入阿里短信secret"></el-input>
        </el-col>
        <el-col :xs="24" :sm="12" :md="6" v-if="webFormData.send_code_mode === 1">
          <div class="margin-b-10" style="margin-top: 20px">阿里短信签名</div>
          <el-input v-model="webFormData.ali_sms_sign_name" placeholder="请输入阿里短信签名"></el-input>
        </el-col>

        <el-col :xs="24" :sm="12" :md="6" v-if="webFormData.send_code_mode === 2">
          <div class="margin-b-10" style="margin-top: 20px">发信名称</div>
          <el-input v-model="webFormData.mail_from_name" placeholder="请输入发信名称"></el-input>
        </el-col>
        <el-col :xs="24" :sm="12" :md="6" v-if="webFormData.send_code_mode === 2">
          <div class="margin-b-10" style="margin-top: 20px">服务器地址</div>
          <el-input v-model="webFormData.mail_host" placeholder="请输入服务器地址"></el-input>
        </el-col>
        <el-col :xs="24" :sm="12" :md="6" v-if="webFormData.send_code_mode === 2">
          <div class="margin-b-10" style="margin-top: 20px">端口</div>
          <el-input v-model="webFormData.mail_port" placeholder="端口"></el-input>
        </el-col>
        <el-col :xs="24" :sm="12" :md="6" v-if="webFormData.send_code_mode === 2">
          <div class="margin-b-10" style="margin-top: 20px">发信地址</div>
          <el-input v-model="webFormData.mail_from_address" placeholder="请输入发信地址"></el-input>
        </el-col>
        <el-col :xs="24" :sm="12" :md="6" v-if="webFormData.send_code_mode === 2">
          <div class="margin-b-10" style="margin-top: 20px">发信账号</div>
          <el-input v-model="webFormData.mail_username" placeholder="请输入发信账号"></el-input>
        </el-col>
        <el-col :xs="24" :sm="12" :md="6" v-if="webFormData.send_code_mode === 2">
          <div class="margin-b-10" style="margin-top: 20px">发信密码</div>
          <el-input v-model="webFormData.mail_password" placeholder="请输入发信密码"></el-input>
        </el-col>

        <el-divider content-position="left"> 支付配置</el-divider>
        <el-col :xs="24" :sm="12" :md="6">
          <div class="margin-b-10" style="margin-top: 20px">微信商户appId</div>
          <el-input v-model="webFormData.wechat_pay_app_id" placeholder="请输入微信商户appId"></el-input>
        </el-col>
        <el-col :xs="24" :sm="12" :md="6">
          <div class="margin-b-10" style="margin-top: 20px">微信商户号</div>
          <el-input v-model="webFormData.wechat_pay_mch_id" placeholder="请输入微信商户号"></el-input>
        </el-col>
        <el-col :xs="24" :sm="12" :md="6">
          <div class="margin-b-10" style="margin-top: 20px">微信支付密钥</div>
          <el-input v-model="webFormData.wechat_pay_secret_key" placeholder="请输入微信支付密钥"></el-input>
        </el-col>
        <el-col :xs="24" :sm="12" :md="6">
          <div class="margin-b-10" style="margin-top: 20px">微信支付私钥证书</div>
          <el-input v-model="webFormData.wechat_pay_private_cert" placeholder="请输入微信支付私钥证书"></el-input>
        </el-col>
        <el-col :xs="24" :sm="12" :md="6">
          <div class="margin-b-10" style="margin-top: 20px">微信支付公钥证书</div>
          <el-input v-model="webFormData.wechat_pay_certificate" placeholder="请输入微信支付公钥证书"></el-input>
        </el-col>
      </el-row>
      <div class="text-center m-t-30">
        <el-button type="primary" style="width: 200px" @click="saveSubmit" :loading="formLoading">保存</el-button>
      </div>
    </el-card>
    <el-card v-loading="formLoading" title="分销配置" class="m-t-20">
      <el-form label-width="200px" :disabled="formLoading">
        <el-row :gutter="20">
          <el-col :xs="24" :sm="12" :md="6">
            <div class="margin-b-10" style="margin-top: 20px">是否开启赠送会员</div>
            <el-switch style="width: 100%" v-model="formData.is_give_vip"></el-switch>
          </el-col>
          <el-col :xs="24" :sm="12" :md="6">
            <div class="margin-b-10" style="margin-top: 20px">注册赠送会员</div>

            <el-select v-model="formData.give_vip_id" placeholder="注册赠送会员" style="width: 100%">
              <el-option v-for="vip in vipPackages" :key="vip.id" :label="vip.name" :value="vip.id" />
            </el-select>
          </el-col>

          <el-col :xs="24" :sm="12" :md="6">
            <div class="margin-b-10" style="margin-top: 20px">注册赠送天数</div>
            <el-input-number style="width: 100%" v-model="formData.give_vip_days" :min="1" :max="365" />
          </el-col>

          <el-col :xs="24" :sm="12" :md="6">
            <div class="margin-b-10" style="margin-top: 20px">一级邀请注册佣金</div>
            <el-input-number style="width: 100%" v-model="formData.recommend_commission_1" :min="0" />
          </el-col>
          <el-col :xs="24" :sm="12" :md="6">
            <div class="margin-b-10" style="margin-top: 20px">二级邀请注册佣金</div>
            <el-input-number style="width: 100%" v-model="formData.recommend_commission_2" :min="0" />
          </el-col>
          <el-col :xs="24" :sm="12" :md="6">
            <div class="margin-b-10" style="margin-top: 20px">一级分销（%）</div>
            <el-input-number style="width: 100%" v-model="formData.member_pay_commission_1" />
          </el-col>
          <el-col :xs="24" :sm="12" :md="6">
            <div class="margin-b-10" style="margin-top: 20px">二级分销（%）</div>
            <el-input-number style="width: 100%" v-model="formData.member_pay_commission_2" />
          </el-col>
        </el-row>
      </el-form>
      <div class="text-center m-t-30">
        <el-button type="primary" style="width: 200px" @click="clickSubmit" :loading="formLoading">保存</el-button>
      </div>
    </el-card>
  </div>
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
