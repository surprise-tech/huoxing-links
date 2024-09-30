<template>
  <div class="tabs-container">
    <div class="tabs-item">
      <el-tabs
        v-model="activeTabName"
        class="tabs-item-style-1"
        @tab-click="tabClick"
        @tab-remove="tabRemove"
      >
        <el-tab-pane
          v-for="tab in tabsStore.visitedViews"
          :key="tab"
          :label="tab.title"
          :name="tab.path"
          :closable="!isAffix(tab)"
        ></el-tab-pane>
      </el-tabs>
    </div>
    <el-dropdown class="tabs-action" trigger="click" placement="bottom-end" @command="onClose">
      <template #dropdown>
        <el-dropdown-menu>
          <el-dropdown-item icon="el-icon-close" command="close">关闭</el-dropdown-item>
          <el-dropdown-item icon="el-icon-circle-close" command="closeOthers"
            >关闭其他</el-dropdown-item
          >
          <el-dropdown-item icon="el-icon-circle-close-filled" command="closeAll"
            >关闭所有</el-dropdown-item
          >
        </el-dropdown-menu>
      </template>
      <el-icon><el-icon-arrow-down /></el-icon>
    </el-dropdown>
  </div>
</template>

<script setup lang="ts">
import { watch, onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { tabsStore, routerStore } from '@/stores'
import { closeAllTabs, closeOthersTabs, closeTab } from '@/utils/tabs'
import type { TabPaneName } from 'element-plus'

const route = useRoute()
const router = useRouter()

const activeTabName = ref(route.path)

// 是否固定
const isAffix = (tab: any) => {
  return tab.meta && tab.meta.affix
}

watch(route, () => {
  // 当前路由，添加到tabs里
  if (route.name) {
    addTab()
  }
})

onMounted(() => {
  // 初始化
  initTabs()
  addTab()
})

// 初始化固定tab
const initTabs = () => {
  const affixTabs = getAffixTabs(routerStore.routes)
  for (const tab of affixTabs) {
    // 需要有tab名称
    if (tab.name) {
      tabsStore.addView(tab)
    }
  }
}

// 获取需要固定的tabs
const getAffixTabs = (routes: any) => {
  let tabs: any[] = []
  routes.forEach((route: any) => {
    if (route.meta && route.meta.affix) {
      tabs.push({
        fullPath: route.path,
        path: route.path,
        name: route.name,
        meta: { ...route.meta }
      })
    }
    if (route.children) {
      const tempTabs = getAffixTabs(route.children)
      if (tempTabs.length >= 1) {
        tabs = [...tabs, ...tempTabs]
      }
    }
  })
  return tabs
}

// 添加tab
const addTab = () => {
  tabsStore.addView(route)
  tabsStore.addCachedView(route)
  activeTabName.value = route.path
}

// tab被选中
const tabClick = (tab: any) => {
  tab.props.name && router.push(tab.props.name)
}

// 点击关闭tab
const tabRemove = (path: TabPaneName) => {
  const tab = tabsStore.visitedViews.filter((tab: any) => tab.path === path)
  closeTab(router, tab[0])
}

// dropdown 关闭事件
const onClose = (type: string) => {
  switch (type) {
    case 'close':
      closeTab(router, route)
      break
    case 'closeOthers':
      closeOthersTabs(router, route)
      break
    case 'closeAll':
      closeAllTabs(router, route)
      break
  }
}
</script>

<style lang="scss" scoped>
.tabs-container {
  display: flex;
  position: relative;
  z-index: 6;
  height: 40px;
  background-color: #fff;
  .tabs-item {
    transition: left 0.3s;
    flex-grow: 1;
    overflow: hidden;
    ::v-deep(.el-tabs__nav-prev) {
      padding: 0 10px;
      border-right: var(--el-border-color-extra-light) 1px solid;
    }
    ::v-deep(.el-tabs__nav-next) {
      padding: 0 10px;
      border-left: var(--el-border-color-extra-light) 1px solid;
    }
    ::v-deep(.is-scrollable) {
      padding: 0 32px;
    }
    ::v-deep(.el-tabs__active-bar) {
      height: 0;
    }
    ::v-deep(.el-tabs__item) {
      .is-icon-close {
        transition: none !important;
        &:hover {
          color: var(--el-color-primary-light-9);
          background-color: var(--el-color-primary);
          border-radius: 50%;
        }
      }
    }
  }
}
.tabs-item-style-1 {
  ::v-deep(.el-tabs__item) {
    padding: 0 15px !important;
    border-right: var(--el-border-color-extra-light) 1px solid;
    user-select: none;
    color: #8c8c8c;
    &:hover {
      color: #444;
      background: rgba(0, 0, 0, 0.02);
    }
    &.is-active {
      color: var(--el-color-primary);
      background-color: var(--el-color-primary-light-9);
      border-bottom: var(--el-border-color-light) 2px solid;
      &:before {
        background-color: var(--el-color-primary);
      }
    }
    &:before {
      content: '';
      width: 9px;
      height: 9px;
      margin-right: 8px;
      display: inline-block;
      background-color: #ddd;
      border-radius: 50%;
    }
  }
}
.tabs-action {
  height: 40px;
  line-height: 40px;
  box-sizing: border-box;
  padding: 0 12px;
  align-items: center;
  cursor: pointer;
  color: #666;
  border-left: var(--el-border-color-extra-light) 1px solid;
  border-bottom: var(--el-border-color-light) 2px solid;
}
</style>
