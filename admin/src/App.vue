<script setup lang="ts">
import { nextTick, onMounted } from 'vue'
import { appStore } from '@/stores'
import { handleThemeStyle } from '@/utils/theme'

onMounted(() => {
  // 初始化主题
  nextTick(() => {
    handleThemeStyle(appStore.theme.primaryColor)
  })
})
</script>

<template>
  <RouterView />
</template>

<style >
.notify {
  :is(img) {
    width: 100% !important;
  }
}

</style>
