// Form Hooks Options接口
import type { Ref } from 'vue'
import type { FormInstance } from 'element-plus'
import { inject, ref, watch } from 'vue'

export interface IFormOptions<T> {
  formRef: Ref<FormInstance | undefined>
  defVal: (a: any) => T
  submitCall?: () => boolean
}

/**
 * Form Hooks CURD表单相关的Hooks
 * @param options
 */
export default function useForm<T>(options: IFormOptions<T>) {
  const detail = inject('detail', ref({} as any))
  const formLoading = inject('formLoading')
  const setFormData = inject('setFormData')
  const submitFlag = inject('submitFlag') as Ref<boolean>
  const formData = ref({} as T) as Ref<T>

  // 监听详情数据变化，重置表单数据
  watch(
    () => detail.value,
    (val) => {
      formData.value = options.defVal(val)
      // 重置表单验证
     options.formRef.value?.clearValidate()
    },
    { immediate: true }
  )

  watch(
    () => submitFlag.value,
    (val) => {
      if (!val) {
        return
      }
      // 默认提交前的校验是通过的
      let flag = true

      // 如果有自定义的提交前校验，执行自定义的校验
      if (typeof options.submitCall === 'function') {
        flag = options.submitCall()
      }

      // 如果校验通过，执行提交
      if (flag && typeof setFormData === 'function') {
        setFormData(formData.value, options.formRef.value)
      }
    }
  )

  return {
    formData,
    formLoading
  }
}
