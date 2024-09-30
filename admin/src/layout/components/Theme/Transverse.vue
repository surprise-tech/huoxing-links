<template>
  <el-container class="layout-container layout-transverse">
    <el-header class="navbar-container" :class="headerClass">
      <Logo v-if="theme.isLogo" />
      <el-menu
        :default-active="defaultActive"
        background-color="transparent"
        :collapse-transition="false"
        mode="horizontal"
      >
        <menu-item v-for="menu in routerStore.menuRoutes" :key="menu.path" :menu="menu"></menu-item>
      </el-menu>
      <NavbarRight />
    </el-header>
    <Tabs v-if="theme.isTabsView" />
    <Main />
  </el-container>
</template>

<script setup lang="ts">
import { appStore, routerStore } from '@/stores'
import NavbarRight from '@/layout/components/Navbar/NavbarRight.vue'
import Main from '@/layout/components/Main.vue'
import Tabs from '@/layout/components/Tabs.vue'
import Logo from '@/layout/components/Logo.vue'
import MenuItem from '@/layout/components/MenuItem.vue'
import { computed } from 'vue'
import { useRoute } from 'vue-router'

const theme = computed(() => appStore.theme)

const route = useRoute()

const defaultActive = computed(() => {
  const { path } = route
  return path
})

const headerClass = computed(() => (appStore.theme.headerStyle === 'theme' ? 'header-theme' : ''))
</script>

<style lang="scss" scoped>
.layout-container {
  width: 100%;
  height: 100%;
  .el-header {
    padding-right: 0 !important;
  }
}
.navbar-container {
  height: var(--theme-header-height);
  display: flex;
  align-items: center;
  background: var(--theme-header-bg-color);
  border-bottom: 1px solid var(--theme-border-color-light);
  color: var(--theme-header-text-color);
  ::v-deep(.el-sub-menu__title) {
    &:hover {
      background: var(--theme-header-hover-color) !important;
    }
  }
  ::v-deep(.svg-icon) {
    align-items: center;
    cursor: pointer;
    height: var(--theme-header-height);
    line-height: var(--theme-header-height);
    padding: 0 12px;
    svg {
      font-size: 16px;
    }
    &:hover {
      background: var(--theme-header-hover-color);
    }
  }
  .el-menu {
    width: 100%;
    display: flex;
    align-items: center;
    flex-direction: row;
    margin-left: 20px;
    ::v-deep(.svg-icon) {
      &:hover {
        background: none !important;
      }
      padding: 0;
    }
  }
}
</style>
