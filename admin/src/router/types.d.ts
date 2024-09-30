// 扩展 meta 类型
import 'vue-router'
declare module 'vue-router' {
  interface RouteMeta {
    title?: string
    // 图标
    icon?: string
    // 面包屑
    breadcrumb?: string[]
    // 权限
    permission?: string[]
    // 是否隐藏
    hidden?: boolean
  }
}
