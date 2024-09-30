<template>
  <div>
    <el-button type="primary" @click="showDialog">素材库</el-button>

    <file-manage-dialog
      :kind="props?.type"
      :max="fileMax"
      ref="fileMangeDialogRef"
      @confirm="confirmHandle"
    />
  </div>
</template>
<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import FileManageDialog from './dialog.vue'

const emits = defineEmits(['update:modelValue'])
const props = withDefaults(
  defineProps<{
    modelValue?: string | string[]
    type?: 'avatar' | 'list'
    max?: number
  }>(),
  {
    modelValue: '',
    type: 'avatar',
    max: 1
  }
)

const selectPath = ref<string[]>([])
watch(
  () => props.modelValue,
  (val) => {
    if (Array.isArray(val)) {
      selectPath.value = val
    } else {
      selectPath.value = val ? [val] : []
    }
  },
  {
    immediate: true,
    deep: true
  }
)

// 剩余可上传数量
const fileMax = computed(() => {
  return props.max - selectPath.value.length
})

// 更新modelValue
const updateModelValue = () => {
  if (props.max === 1) {
    emits('update:modelValue', selectPath.value[0] || '')
  } else {
    emits('update:modelValue', selectPath.value)
  }
}

const fileMangeDialogRef = ref<InstanceType<typeof FileManageDialog>>()
const showDialog = () => {
  selectPath.value = []
  fileMangeDialogRef.value?.openDialog()
}

const confirmHandle = (data: any) => {
  selectPath.value = selectPath.value.concat(data) // 合并数组
  updateModelValue()
}
</script>

<style lang="scss" scoped>
.avatar-warp {
  width: 60px;
  height: 60px;
  display: flex;
  justify-content: center;
  align-items: center;
  border: 1px dashed #ccc;
  cursor: pointer;
  position: relative;
  &:hover {
    border-color: var(--el-color-primary);
  }
}
.list-warp {
  display: flex;
  .list-item {
    position: relative;
    margin-right: 10px;
    width: 60px;
    height: 60px;
    border: 1px dashed #ccc;
    cursor: pointer;
    &:hover {
      border-color: var(--el-color-primary);
    }
    &.upload-btn {
      display: flex;
      justify-content: center;
      align-items: center;
      svg {
        margin-right: 0;
      }
    }
  }
}
.delete-btn {
  position: absolute;
  background-color: #ff0000;
  top: -5px;
  right: -5px;
  color: #ffffff;
  width: 18px;
  height: 18px;
  border-radius: 9px;
  line-height: 18px;
  text-align: center;
}
</style>
