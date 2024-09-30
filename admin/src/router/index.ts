import { createRouter, createWebHashHistory, type RouteRecordRaw } from 'vue-router'
import LayoutIndex from '@/layout/index.vue'
import apiRouter from '@/router/apiRouter'
import NProgress from '@/utils/progress'
import { appStore, routerStore, userStore } from '@/stores'
import { ApiUserInfo } from '@/api/user'
import type { IMenu } from '@/models/menu'
import { data_get } from '@/utils'

// 白名单列表
const whiteList = ['/login', '/register']

// 本地路由
const routes: RouteRecordRaw[] = [
  {
    path: '/login',
    name: 'login',
    component: () => import('@/views/auth/LoginView.vue')
  },
  {
    path: '/register',
    name: 'register',
    component: () => import('@/views/auth/RegView.vue')
  }
]

// 异步路由
const asyncRoutes: RouteRecordRaw = {
  path: '/',
  component: LayoutIndex,
  redirect: '/home',
  children: []
}

// 获取扁平化路由，将多级路由转换成一级路由
export const getKeepAliveRoutes = (rs: RouteRecordRaw[], breadcrumb: string[]): RouteRecordRaw[] => {
  const routerList: RouteRecordRaw[] = []
  rs.forEach((item: RouteRecordRaw) => {
    if (item.meta?.title) {
      breadcrumb.push(String(item.meta.title))
    }

    if (item.children && item.children.length > 0) {
      routerList.push(...getKeepAliveRoutes(item.children, breadcrumb))
    } else {
      item.meta?.breadcrumb?.push(...breadcrumb)
      routerList.push(item)
    }

    breadcrumb.pop()
  })
  return routerList
}

// 加载vue组件
const layoutModules = import.meta.glob('/src/views/**/*.vue')

// 根据路径，动态获取vue组件
const getDynamicComponent = (path: string) => {
  return layoutModules[`/src/views${path}.vue`]
}

// 根据菜单列表，生成路由数据
export const generateRoutes = (menuList: IMenu[]): RouteRecordRaw[] => {
  const routerList: RouteRecordRaw[] = []
  menuList.forEach((menu: IMenu) => {
    if (userStore.hasPermission(menu.meta?.permission)) {
      let component
      const path = menu.path
      if (menu.children && menu.children.length > 0) {
        component = () => import('@/layout/index.vue')
      } else {
        if (menu.component) {
          component = getDynamicComponent(menu.component)
        } else {
          component = () => import('@/layout/404.vue')
        }
      }
      const route: RouteRecordRaw = {
        path: path,
        name: menu.name,
        component: component,
        children: [],
        meta: {
          title: menu.meta.title,
          icon: menu.meta.icon,
          iconIn: menu.meta.iconIn,
          newOpen: menu.meta.newOpen,
          url: menu.meta.url,
          breadcrumb: [],
          pluginSlug: data_get(menu, 'meta.pluginSlug', ''),
          permission: menu.meta.permission,
          hidden: Boolean(menu.meta.hidden)
        }
      }

      // 有子菜单的情况
      if (menu.children && menu.children.length > 0) {
        route.children?.push(...generateRoutes(menu.children))
      }

      routerList.push(route)
    }
  })

  return routerList
}

// 创建路由
const router = createRouter({
  history: createWebHashHistory(import.meta.env.BASE_URL),
  routes
})

// 前置守卫
router.beforeEach(async (to, from, next) => {
  // 进度条开始
  NProgress.start()

  // 设置页面标题
  document.title = to.meta.title ? `${to.meta.title} - ${appStore.text.title}` : appStore.text.title

  // 白名单直接跳转
  if (whiteList.includes(to.path)) {
    next()
    return Promise.resolve()
  }

  // 获取token
  const token = userStore.getToken()

  // 没有token，跳转到登录页
  if (!token) {
    next('/login')
    return Promise.resolve()
  }

  // 已经登录时访问登录页，重定向到首页
  if (to.path === '/login' && token) {
    next('/home')
    return Promise.resolve()
  }

  // 已经获取用户信息，可直接跳转
  if (userStore.userInfo.id) {
    if (to.path === '/home') {
      switch (userStore.userInfo?.type) {
        case 1:
          next('/vip-home')
          break
        case 2:
          next('/agent-home')
          break
        case 3:
          next('/admin-home')
          break
        default:
          return Promise.reject()
      }
    }

    next()
    return Promise.resolve()
  }

  // 获取用户信息
  try {
    userStore.userInfo = await ApiUserInfo()
  } catch (e) {
    // 获取用户信息失败，清除token，重新登录
    userStore.logout().then(() => {
      next(`/login`)
    })
    return Promise.reject(e)
  }

  // 获取用户信息成功，构建路由表
  const menus = JSON.parse(JSON.stringify(apiRouter))
  routerStore.menuRoutes.push(...generateRoutes(menus))
  // 获取扁平化路由，将多级路由转换成一级路由
  const keepAliveRoutes = getKeepAliveRoutes(routerStore.menuRoutes, [])
  // 添加动态路由
  asyncRoutes.children?.push(...keepAliveRoutes)
  router.addRoute(asyncRoutes)

  // 保存路由数据
  routerStore.setRoutes(routes.concat(asyncRoutes))

  // 跳转到目标路由
  next({ ...to, replace: true })
})

// 路由加载后
router.afterEach(() => {
  // 结束进度条
  NProgress.done()
})

export default router
