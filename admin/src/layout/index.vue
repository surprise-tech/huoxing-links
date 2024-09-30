<template>
  <component :is="LayoutComponents[layout]"></component>
</template>

<script setup lang="ts">
import { appStore } from '@/stores'
import Vertical from '@/layout/components/Theme/Vertical.vue'
import Columns from '@/layout/components/Theme/Columns.vue'
import Transverse from '@/layout/components/Theme/Transverse.vue'
import { computed } from 'vue'

defineOptions({
  name: 'LayoutIndex'
})

const LayoutComponents: { [key: string]: any } = {
  vertical: Vertical,
  columns: Columns,
  transverse: Transverse
}

const layout = computed(() => appStore.theme.layout)
</script>
