import { reactive, ref } from 'vue'

/**
 * @description: loading hook
 * @param init 初始化状态
 */
export default function useLoading(init: boolean = false) {
  const status = ref(init)

  const start = () => {
    status.value = true
  }
  const end = () => {
    status.value = false
  }

  return reactive({
    status,
    start,
    end
  })
}
