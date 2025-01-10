<script setup lang="ts">
import { ref, watch } from 'vue'
import { ApiSaveSet } from '@/api/comment'
import { ElMessage } from 'element-plus'
import { configStore } from '@/stores'

const webFormData = ref({
  verify_code_is_open: 0,
  send_code_mode: 1,
  ali_sms_key: '',
  ali_sms_secret: '',
  ali_sms_sign_name: '',
  mail_from_name: '',
  mail_host: '',
  mail_port: '',
  mail_from_address: '',
  mail_username: '',
  mail_password: ''
})
const props = defineProps<{
  data: any
}>()
watch(
  () => props.data,
  () => {
    webFormData.value.verify_code_is_open = props.data.verify_code_is_open
    webFormData.value.send_code_mode = props.data.send_code_mode
    webFormData.value.ali_sms_key = props.data.ali_sms_key
    webFormData.value.ali_sms_secret = props.data.ali_sms_secret
    webFormData.value.ali_sms_sign_name = props.data.ali_sms_sign_name
    webFormData.value.mail_from_name = props.data.mail_from_name
    webFormData.value.mail_host = props.data.mail_host
    webFormData.value.mail_port = props.data.mail_port
    webFormData.value.mail_from_address = props.data.mail_from_address
    webFormData.value.mail_username = props.data.mail_username
    webFormData.value.mail_password = props.data.mail_password
  },{
    deep: true,
    immediate: true
  }
)
const clickSubmit = () => {
  if (webFormData.value.verify_code_is_open) {
    if (!webFormData.value.send_code_mode) {
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
    } else if (webFormData.value.send_code_mode === 2) {
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
  ApiSaveSet(webFormData.value).then(() => {
    ElMessage.success('保存成功')
    configStore.refreshConfig()
  })
}
</script>

<template>
  <el-card>
    <div style="width: 500px">
      <div>
        <div class="margin-b-10" style="margin-top: 20px">是否开启验证码</div>
        <el-switch
          style="width: 100%"
          :active-value="1"
          :inactive-value="0"
          v-model="webFormData.verify_code_is_open"
        ></el-switch>
      </div>
      <div>
        <div class="margin-b-10" style="margin-top: 20px">验证码发送方式</div>
        <el-select style="width: 100%" v-model="webFormData.send_code_mode" placeholder="请选择验证码发送方式">
          <el-option label="短信" :value="1" />
          <el-option label="邮箱" :value="2" />
        </el-select>
      </div>
      <div v-if="webFormData.send_code_mode === 1">
        <div class="margin-b-10" style="margin-top: 20px">阿里短信key</div>
        <el-input v-model="webFormData.ali_sms_key" placeholder="请输入阿里短信key"></el-input>
      </div>
      <div v-if="webFormData.send_code_mode === 1">
        <div class="margin-b-10" style="margin-top: 20px">阿里短信secret</div>
        <el-input v-model="webFormData.ali_sms_secret" placeholder="请输入阿里短信secret"></el-input>
      </div>
      <div v-if="webFormData.send_code_mode === 1">
        <div class="margin-b-10" style="margin-top: 20px">阿里短信签名</div>
        <el-input v-model="webFormData.ali_sms_sign_name" placeholder="请输入阿里短信签名"></el-input>
      </div>
      <div v-if="webFormData.send_code_mode === 2">
        <div class="margin-b-10" style="margin-top: 20px">发信名称</div>
        <el-input v-model="webFormData.mail_from_name" placeholder="请输入发信名称"></el-input>
      </div>
      <div v-if="webFormData.send_code_mode === 2">
        <div class="margin-b-10" style="margin-top: 20px">服务器地址</div>
        <el-input v-model="webFormData.mail_host" placeholder="请输入服务器地址"></el-input>
      </div>
      <div v-if="webFormData.send_code_mode === 2">
        <div class="margin-b-10" style="margin-top: 20px">端口</div>
        <el-input v-model="webFormData.mail_port" placeholder="端口"></el-input>
      </div>
      <div v-if="webFormData.send_code_mode === 2">
        <div class="margin-b-10" style="margin-top: 20px">发信地址</div>
        <el-input v-model="webFormData.mail_from_address" placeholder="请输入发信地址"></el-input>
      </div>
      <div v-if="webFormData.send_code_mode === 2">
        <div class="margin-b-10" style="margin-top: 20px">发信账号</div>
        <el-input v-model="webFormData.mail_username" placeholder="请输入发信账号"></el-input>
      </div>
      <div v-if="webFormData.send_code_mode === 2">
        <div class="margin-b-10" style="margin-top: 20px">发信密码</div>
        <el-input v-model="webFormData.mail_password" placeholder="请输入发信密码"></el-input>
      </div>
      <div class="m-b-20 text-center" style="margin-top: 60px">
        <el-button type="primary" size="large" @click="clickSubmit">保存</el-button>
      </div>
    </div>
  </el-card>
</template>

<style scoped lang="scss"></style>
