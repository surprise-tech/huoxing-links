<template>
  <div class="sidebar-logo sidebar-logo-expend">
    <transition enter-active-class="animate__animated animate__fadeInLeft">
      <div class="logo_icon" v-if="appStore.sidebarOpened">
        <img class="logo_icon_item" v-if="!userStore.userInfo.logo" src="@/assets/menu/logo.png" alt="" />
        <img class="logo_icon_item" v-else :src="ImagePath(userStore.userInfo.logo)" alt="" />
        <span class="logo-title">{{ userStore.userInfo.site_name || '火星快链' }}</span>
      </div>
      <div v-else class="logo_icon">
        <img class="logo_icon_item" v-if="!userStore.userInfo.logo" src="@/assets/menu/logo.png" alt="" />
        <img class="logo_icon_item" v-else :src="ImagePath(userStore.userInfo.logo)" alt="" />
      </div>
    </transition>
  </div>
</template>

<script setup lang="ts">
import { appStore, userStore } from '@/stores'
import { ImagePath } from '@/hooks/image'
</script>

<style lang="scss" scoped>
.sidebar-logo {
  width: 200px !important;
  height: var(--theme-header-height);
  line-height: var(--theme-header-height);
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: rgb(0 21 41 / 2%) 0 1px 4px;
  color: var(--theme-logo-text-color);
  font-size: 18px;
  .logo_icon {
    display: flex;
    align-items: center;
    .logo_icon_item {
      width: 40px;
      height: 40px;
      border-radius: 50%;
    }
  }
  .logo-title {
    margin-left: 10px;
    font-size: 16px;
    font-weight: bold;
  }
}
.sidebar-logo-expend {
  width: 100% !important;
}
</style>
