<template>
  <el-sub-menu
    v-if="menu.children.length > 0"
    :class="{ 'menu-item-express': !appStore.sidebarOpened }"
    :key="menu.path"
    :index="menu.path"
  >
    <template #title>
      <menu-icon
        v-if="showIcon && menu.meta.icon"
        :name="active === menu.path ? menu.meta.icon : menu.meta.iconIn"
      ></menu-icon>
      <span class="menu-title">{{ menu.meta.title }}</span>
    </template>
    <menu-item v-for="children in menu.children" :key="children.path" :menu="children"></menu-item>
  </el-sub-menu>
  <el-menu-item
    v-else-if="menu.children.length === 0"
    :key="`else-${menu.path}`"
    :index="menu.path"
    @click="handleClickMenu(menu)"
  >
    <menu-icon v-if="showIcon && menu.meta.icon" :name="active === menu.path ? menu.meta.icon : menu.meta.iconIn" />
    <template #title>
      <span class="menu-title">{{ menu.meta.title }}</span>
    </template>
  </el-menu-item>
</template>

<script setup lang="ts">
import { computed, type PropType } from 'vue'
import { appStore } from '@/stores'
import { useRouter } from 'vue-router'
import MenuIcon from '@/components/menuIcon.vue'

// 显示icon图标
const showIcon = computed(() => {
  return appStore.theme.layout !== 'columns'
})

defineProps({
  menu: {
    type: Object as PropType<any>,
    required: true
  },
  active: {
    type: String,
    default: ''
  }
})

const router = useRouter()

// 菜单点击事件
const handleClickMenu = (menu: any) => {
  // 不是新开页面，则直接切换路由
  if (!menu.meta.newOpen) {
    router.push(menu.path)
    return
  }

  // 新开页面逻辑
  if (menu.meta.url.startsWith('http://') || menu.meta.url.startsWith('https://')) {
    // 外链
    window.open(menu.meta.url, '_blank')
  } else {
    // 内部组件
    window.open('#' + menu.meta.url, '_blank')
  }
}
</script>
<style lang="scss" scoped>
.menu-title {
  margin-left: 10px;
}
</style>
