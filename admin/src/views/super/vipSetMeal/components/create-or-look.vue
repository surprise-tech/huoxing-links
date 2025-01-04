<script setup lang="ts">
import type { FormInstance, FormRules } from 'element-plus'
import useForm from '@/hooks/form'
import { ref } from 'vue'
import type { vipConfig } from '@/models/link'

const formRef = ref<FormInstance>()
const { formData, formLoading } = useForm({
  formRef,
  defVal(detail) {
    return {
      name: detail?.name, // 套餐名称
      price: detail?.price ? detail?.price : 0, // 价格
      level: detail?.level ? detail?.level : 0, // 等级
      config: detail?.config
        ? detail?.config
        : ({
            pre_min: false,
            min_disabled_check: false,
            support: false,
            cur_index: false,
            allow_type: {
              MINI_PROGRAM: false,
              KING_DOC: false,
              CLI_QR: false,
              WORK_WECHAT: false,
              LANDING_MINI: false,
              QQ_USER: false,
              QQ_GROUP: false,
              QR_QQ: false
            }
          } as vipConfig) // 权限配置
    }
  }
})
// 表单校验
const rules: FormRules = {
  name: [{ required: true, message: '请输入套餐名称', trigger: 'blur' }],
  price: [{ required: true, message: '请输入套餐价格', trigger: 'blur' }],
  level: [{ required: true, message: '请填写等级', trigger: 'blur' }]
}
</script>

<template>
  <el-form :model="formData" :rules="rules" ref="formRef" v-loading="formLoading" label-width="180px">
    <el-form-item prop="name" label="套餐名称">
      <el-input v-model="formData.name" placeholder="输入套餐名称"></el-input>
    </el-form-item>
    <el-form-item prop="price" label="价格/月">
      <el-input v-model="formData.price" placeholder="0.00"></el-input>
    </el-form-item>
    <el-form-item prop="level" label="等级">
      <el-input-number v-model="formData.level" placeholder="0"></el-input-number>
    </el-form-item>
    <h3>权限配置</h3>
    <el-divider></el-divider>
    <el-form-item :rules="[{ required: true, message: '请输入链接数量限制', trigger: 'blur' }]" label="链接数量限制">
      <el-input-number v-model="formData.config.count_limit" placeholder="0"></el-input-number>
    </el-form-item>
    <!--    <el-form-item-->
    <!--      :rules="[{ required: true, message: '请输入小圆码数量限制', trigger: 'blur' }]"-->
    <!--      label="小圆码数量限制"-->
    <!--    >-->
    <!--      <el-input-number v-model="formData.config.yuanma_limit" placeholder="0"></el-input-number>-->
    <!--    </el-form-item>-->
    <!--    <el-form-item-->
    <!--      :rules="[{ required: true, message: '请输入抖音自动回复卡片数量限制', trigger: 'blur' }]"-->
    <!--      label="抖音自动回复卡片"-->
    <!--    >-->
    <!--      <el-input-number v-model="formData.config.douyin_reply_limit" placeholder="0"></el-input-number>-->
    <!--    </el-form-item>-->
    <el-form-item :rules="[{ required: true, message: '请输入UV限制', trigger: 'blur' }]" label="UV限制">
      <el-input-number v-model="formData.config.uv_limit" placeholder="0"></el-input-number>
    </el-form-item>
    <el-form-item
      :rules="[{ required: true, message: '请输入自建小程序池可创建数量', trigger: 'blur' }]"
      label="自建小程序池可创建数量"
    >
      <el-input-number v-model="formData.config.min_count_limit" placeholder="0"></el-input-number>
    </el-form-item>
    <el-form-item :rules="[{ required: true, message: '请选择创建链接类型', trigger: 'blur' }]" label="创建链接类型">
      <el-checkbox v-model="formData.config.allow_type.MINI_PROGRAM" :label="true">跳转到小程序</el-checkbox>
      <el-checkbox v-model="formData.config.allow_type.LANDING_MINI" :label="true">跳转到微信二维码</el-checkbox>
      <el-checkbox v-model="formData.config.allow_type.WORK_WECHAT" :label="true">跳转到企业微信</el-checkbox>
      <el-checkbox v-model="formData.config.allow_type.KING_DOC" :label="true">跳转到金山文档</el-checkbox>
      <el-checkbox v-model="formData.config.allow_type.CLI_QR" :label="true">跳转到草料二维码</el-checkbox>
      <el-checkbox v-model="formData.config.allow_type.QR_QQ" :label="true">跳转到腾讯优码</el-checkbox>
      <el-checkbox v-model="formData.config.allow_type.QQ_USER" :label="true">跳转到QQ</el-checkbox>
      <el-checkbox v-model="formData.config.allow_type.QQ_GROUP" :label="true">跳转到QQ群</el-checkbox>
    </el-form-item>
    <el-form-item :rules="[{ required: true, message: '请选择链接保留时长', trigger: 'blur' }]" label="链接保留时长">
      <el-checkbox :model-value="true" disabled>永久</el-checkbox>
    </el-form-item>
    <el-form-item
      :rules="[{ required: true, message: '请选择官方小程序池使用', trigger: 'blur' }]"
      label="官方小程序池使用"
    >
      <el-checkbox v-model="formData.config.pre_min" :label="true">支持</el-checkbox>
    </el-form-item>
    <el-form-item
      :rules="[{ required: true, message: '请选择自建小程序池下架自动检测', trigger: 'blur' }]"
      label="自建小程序池下架自动检测"
    >
      <el-checkbox v-model="formData.config.min_disabled_check" :label="true">支持</el-checkbox>
    </el-form-item>
    <el-form-item :rules="[{ required: true, message: '请选择免费技术协助', trigger: 'blur' }]" label="免费技术协助">
      <el-checkbox v-model="formData.config.support" :label="true">支持</el-checkbox>
    </el-form-item>
    <el-form-item :rules="[{ required: true, message: '请选择数据统计/分析', trigger: 'blur' }]" label="数据统计/分析">
      <el-checkbox :model-value="true" disabled>支持</el-checkbox>
    </el-form-item>
    <el-form-item :rules="[{ required: true, message: '请选择自定义落地页', trigger: 'blur' }]" label="自定义落地页">
      <el-checkbox v-model="formData.config.cur_index" :label="true">支持</el-checkbox>
    </el-form-item>
  </el-form>
</template>

<style scoped lang="scss"></style>
