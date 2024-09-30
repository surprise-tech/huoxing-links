<template>
  <el-scrollbar>
    <el-main class="layout-main">
      <el-scrollbar class="layout-scrollbar">
        <div class="layout-card" :style="layoutMainHeight">
          <router-view v-slot="{ Component, route }">
            <keep-alive v-if="theme.isTabsCache" :include="[...tabsStore.cachedViews]">
              <component :is="Component" :key="route.name" />
            </keep-alive>
            <component :is="Component" v-else :key="route.name" />
          </router-view>
        </div>
      </el-scrollbar>
    </el-main>
  </el-scrollbar>
</template>

<script setup lang="ts">
import { RouterView } from 'vue-router'
import { appStore, tabsStore } from '@/stores'
import { computed } from 'vue'

const theme = computed(() => appStore.theme)
const layoutMainHeight = computed(() => {
  if (!theme.value.isTabsView) {
    return 'min-height: calc(100vh - var(--theme-header-height) - 30px) !important'
  } else {
    return ''
  }
})
</script>
