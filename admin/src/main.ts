import { createApp } from 'vue'
import ElementPlus from 'element-plus'
import zhCn from 'element-plus/es/locale/lang/zh-cn'
import store, { configStore, userStore } from './stores'
import App from './App.vue'
import router from './router'
import * as ElementPlusIconsVue from '@element-plus/icons-vue'
import AuthDirective from '@/utils/authDirective'
import 'element-plus/dist/index.css'
import 'element-plus/theme-chalk/display.css'
import 'virtual:svg-icons-register'
import '@/assets/main.scss'
import '@/assets/public.scss'
import 'animate.css'
import '@/assets/iconfonts/iconfont.css'
import { ApiGetSet } from '@/api/comment'

ApiGetSet().then((res: any) => {
  configStore.refresh(res)
  userStore.dialogVisible = true
  const app = createApp(App)
  app.use(store)
  app.use(ElementPlus, { locale: zhCn })
  app.use(router)

  // 注冊所有element-plus图标
  for (const [key, component] of Object.entries(ElementPlusIconsVue)) {
    app.component(`ElIcon${key}`, component)
  }

  app.directive('auth', AuthDirective)
  app.mount('#app')
})
