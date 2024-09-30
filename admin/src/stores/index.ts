import { createPinia } from 'pinia'
import piniaPersist from 'pinia-plugin-persistedstate'
const pinia = createPinia()
pinia.use(piniaPersist)

import useUserStore from '@/stores/modules/user'
import useAppStore from '@/stores/modules/app'
import useTabsStore from '@/stores/modules/tabs'
import useRouterStore from '@/stores/modules/router'
import useConfigStore from '@/stores/modules/config'
import useLinkStore from '@/stores/modules/links'

// 用户信息
export const userStore = useUserStore(pinia)

// 应用配置
export const appStore = useAppStore(pinia)

// 顶部标签
export const tabsStore = useTabsStore(pinia)

// 路由菜单
export const routerStore = useRouterStore(pinia)

// 图片路径初始化
export const configStore = useConfigStore(pinia)

// link链接
export const linkStore = useLinkStore(pinia)

export default pinia
