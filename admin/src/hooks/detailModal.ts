import { nextTick, reactive } from 'vue'
import http from '@/utils/http'

// 详情弹框.
export default function useDetailModal(url: string) {
  const state = reactive<{
    visible: boolean
    loading: boolean
    data: any
  }>({
    visible: false,
    loading: false,
    data: undefined
  })

  const showDetail = (id: any) => {
    state.data = null // 这里不要写成undefined，否则会导致详情的值不会清空.
    state.visible = true
    state.loading = true
    if (typeof id === 'object') {
      // 直接赋值
      state.data = JSON.parse(JSON.stringify(id))
      nextTick(() => {
        state.loading = false
      })
    } else if (typeof id === 'number' || typeof id === 'string') {
      http
        .get(url + '/' + id + '?page=1$page_size=10')
        .then((res) => {
          state.data = res
        })
        .finally(() => {
          state.loading = false
        })
    }
  }

  // 评价获取详情id
  const getDetailId = (id: any) => {
    state.data = null // 这里不要写成undefined，否则会导致详情的值不会清空.
    state.loading = true
    if (typeof id === 'object') {
      // 直接赋值
      state.data = JSON.parse(JSON.stringify(id))
      nextTick(() => {
        state.loading = false
      })
    } else if (typeof id === 'number' || typeof id === 'string') {
      http
        .get(url + '/' + id)
        .then((res) => {
          state.data = res
        })
        .finally(() => {
          state.loading = false
        })
    }
  }

  const resetDetail = () => {
    if (state.data.id) {
      showDetail(state.data.id)
    }
  }
  return {
    state,
    showDetail,
    resetDetail,
    getDetailId
  }
}
