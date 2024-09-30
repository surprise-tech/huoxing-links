<script setup lang="ts">
import type { FormInstance, FormRules } from 'element-plus'
import useForm from '@/hooks/form'
import { onMounted, ref } from 'vue'
import UploadImage from '@/components/uploadImg.vue'
import { getAgentTree } from '@/api/super'
import { ApiMaterialCate } from '@/api/app'

const formRef = ref<FormInstance>()
const { formData, formLoading } = useForm({
  formRef,
  defVal(detail) {
    return {
      cate_id: detail?.cate_id, // 分类
      image: detail?.image, // 图片
      name: detail?.name // 图片
    }
  }
})
// 表单校验
const rules: FormRules = {
  cate_id: [{ required: true, message: '请选择分类', trigger: 'blur' }],
  image: [{ required: true, message: '请上传图片', trigger: 'blur' }]
}

const getImage = (e: any) => {
  formData.value.image = e.path
  formData.value.name = e.name
}

const materialCate = ref()
const getMaterialCate = async () => {
  materialCate.value = await ApiMaterialCate()
}

onMounted(() => {
  getMaterialCate()
})
</script>

<template>
  <el-form
    :model="formData"
    :rules="rules"
    ref="formRef"
    v-loading="formLoading"
    label-width="120px"
  >
    <el-form-item prop="name" label="分类" style="width: 370px">
      <el-select v-model="formData.cate_id" clearable placeholder="请选择">
        <el-option
          v-for="item in materialCate"
          :key="item.id"
          :label="item.name"
          :value="item.id"
        ></el-option>
      </el-select>
    </el-form-item>
    <el-form-item label="图片">
      <upload-image :model-value="formData.image" @update:modelValue="getImage"></upload-image>
    </el-form-item>
  </el-form>
</template>

<style scoped lang="scss"></style>
