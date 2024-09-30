import { defineStore } from 'pinia'

// 应用配置
const useAppStore = defineStore('app-store', {
  state: () => ({
    sidebarOpened: true, // sidebar 是否展开
    text: {
      loginTitle: '',
      loginDesc: '',
      title: '优秀的私域营销工具',
      minTitle: '火星快链'
    },
    theme: {
      sidebarStyle: 'light', // sidebar 样式 light/dark
      headerStyle: 'light', // header 样式  light/theme
      primaryColor: '#2b6bff', // 主题色 #0256ff
      layout: 'vertical', // 布局方式 columns/vertical/transverse
      uniqueOpened: true, // 是否只保持一个子菜单的展开
      isLogo: true, // 是否显示 logo
      isBreadcrumb: true, // 是否显示面包屑
      isTabsView: true, // 是否显示 tabs
      isTabsCache: true // 是否开启 tabs 缓存
    }
  })
})

export default useAppStore
