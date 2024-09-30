import { defineStore } from 'pinia'
import { type RouteRecordRaw } from 'vue-router'

// 路由菜单
const useRouterStore = defineStore('router-store', {
  state: () => ({
    menuRoutes: [] as RouteRecordRaw[],
    routes: [] as RouteRecordRaw[]
  }),
  actions: {
    setRoutes(routers: RouteRecordRaw[]) {
      this.routes = routers
    }
  }
})

export default useRouterStore
