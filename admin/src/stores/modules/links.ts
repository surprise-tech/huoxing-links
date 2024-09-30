import { defineStore } from 'pinia'
import { ref } from 'vue'

const linkEvent = {} as { [key: string]: (...args: any[]) => any }

// 链接
const useLinkStore = defineStore(
  'link-store',
  () => {
    const link = ref()
    const num = ref(-1)
    const saveLink = (path: any, index: any, callback?: any) => {
      link.value = path
      num.value = index
      if (callback instanceof Function) {
        linkEvent.change = callback
      } else {
        linkEvent.change && linkEvent.change(path, index)
      }
    }
    return { link, num, saveLink }
  },
  {
    persist: true
  }
)

export default useLinkStore
