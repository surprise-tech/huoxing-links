<template>
  <el-drawer
    v-if="props.type === 'drawer'"
    v-model="formVisible"
    :close-on-click-modal="false"
    append-to-body
    direction="rtl"
    :size="width"
    @close="emits('update:visible', false)"
  >
    <slot></slot>
    <template v-slot:footer>
      <slot name="footer">
        <el-button v-if="props.cancelBtnName === '取消'" @click="emits('update:visible', false)">取消</el-button>
        <el-button v-else @click="resetForm">{{ props.cancelBtnName }}</el-button>
        <el-button v-if="props.okBtnName === '确定'" type="primary" @click="clickSubmit">确定</el-button>
        <el-button v-else type="primary" @click="clickSubmit">立即开通</el-button>
      </slot>
    </template>
  </el-drawer>
  <el-drawer
    v-else-if="props.type === 'drawer_v'"
    v-model="formVisible"
    :close-on-click-modal="false"
    append-to-body
    direction="rtl"
    :size="width"
    @close="emits('update:visible', false)"
  >
    <slot></slot>
  </el-drawer>
  <el-dialog
    v-else
    align-center
    :width="width"
    v-model="formVisible"
    :close-on-click-modal="false"
    @close="emits('update:visible', false)"
  >
    <slot></slot>
    <template v-slot:footer>
      <slot name="footer">
        <el-button v-if="props.cancelBtnName === '取消'" @click="emits('update:visible', false)">{{
          props.cancelBtnName
        }}</el-button>
        <el-button v-else @click="resetForm">{{ props.cancelBtnName }}</el-button>
        <el-button type="primary" @click="clickSubmit">{{ props.okBtnName }}</el-button>
      </slot>
    </template>
  </el-dialog>
</template>

<script setup lang="ts">
import usePropData from '@/hooks/propData'
import { nextTick, provide, ref } from 'vue'
import type { FormInstance } from 'element-plus'

defineOptions({
  name: 'FormModal'
})
const emits = defineEmits(['update:visible', 'submit', 'reset'])
const props = defineProps<{
  type: 'dialog' | 'drawer' | 'drawer_v'
  width: string | number
  visible?: boolean
  detail?: any
  loading?: boolean
  cancelBtnName?: '重置' | '取消'
  okBtnName?: string
}>()

const formLoading = usePropData(() => props.loading, false)
const detail = usePropData(() => props.detail, null)
const formVisible = usePropData(() => props.visible, false)
const formData = ref({} as any)
const formInstance = ref<FormInstance>()
const submitFlag = ref(false) // 提交标识, 子组件监听到变化后, 执行提交操作
const setFormData = (data: any, formRef: FormInstance | undefined) => {
  formData.value = data
  formInstance.value = formRef
}

const clickSubmit = () => {
  submitFlag.value = true
  nextTick(() => {
    if (Object.keys(formData.value).length > 0) {
      // 子组件已经设置了formData，可以验证并提交
      formInstance.value
        ?.validate()
        .then(() => {
          emits('submit', formData.value, detail.value)
        })
        .catch((e) => {
          console.log(e, 'e')
        })
    }
    submitFlag.value = false
  })
}

// 重置
const resetForm = () => {
  submitFlag.value = true
  nextTick(() => {
    if (formInstance.value) {
      formInstance.value.resetFields()
      submitFlag.value = false
    }
  })
}

provide('detail', detail)
provide('formLoading', formLoading)
provide('setFormData', setFormData)
provide('submitFlag', submitFlag)
</script>
