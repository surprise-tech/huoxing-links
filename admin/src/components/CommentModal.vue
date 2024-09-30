<template>
  <el-drawer
    v-if="type === 'drawer'"
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
    :width="width"
    draggable
    append-to-body
    v-else
    v-model="formVisible"
    :close-on-click-modal="false"
    @close="emits('update:visible', false)"
  >
    <slot></slot>
  </el-dialog>
</template>

<script setup lang="ts">
import usePropData from '@/hooks/propData'
import { provide } from 'vue'

defineOptions({
  name: 'FormModal'
})
const emits = defineEmits(['update:visible', 'reset'])
const props = defineProps<{
  type: 'dialog' | 'drawer'
  width: string | number
  visible?: boolean
  detail?: any
  loading?: boolean
}>()

const formVisible = usePropData(() => props.visible, false)

provide(
  'modalDetail',
  usePropData(() => props.detail, null)
)
provide(
  'modalLoading',
  usePropData(() => props.loading, false)
)

provide('modalReset', () => {
  emits('reset')
})
</script>
