import { type WatchSource, type Ref, ref, watch, nextTick } from 'vue'

/**
 * 监听props并生成响应式数据.
 * @param source
 * @param defVal
 * @param deep
 */
export default function usePropData<T>(source: WatchSource<T>, defVal: T, deep: boolean = false) {
  const data = ref<T>() as Ref<T>
  watch(
    source,
    (val) => {
      data.value = val // 先赋值，再判断，否则会导致外部监听不到变化
      if (!val) {
        nextTick(() => {
          data.value = typeof defVal === 'object' ? JSON.parse(JSON.stringify(defVal)) : defVal
        })
      }
    },
    {
      deep,
      immediate: true
    }
  )
  return data
}
