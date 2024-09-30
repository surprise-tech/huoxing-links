import { inject, ref } from 'vue'

export default function useDetail() {
  const detail = inject('modalDetail', ref({} as any))
  const loading = inject('modalLoading')
  const reset = inject('modalReset') as () => void
  const cancel = inject('cancel') as () => void
  return {
    detail,
    loading,
    reset,
    cancel
  }
}
