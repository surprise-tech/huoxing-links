import { defineStore } from 'pinia'
import { ApiGetSet } from '@/api/comment'

const useConfigStore = defineStore('init', {
  state: () => ({
    code_mode: 0,
    package: [] as Array<any>,
    asset_url: '',
    web_site_bottom_logo: '',
    web_site_customer_service: '',
    web_site_logo: '',
    web_site_title: ''
  }),
  actions: {
    refresh(res: any) {
      this.code_mode = res.code_mode
      this.package = res.package
      this.asset_url = res.asset_url
      this.web_site_bottom_logo = res.web_site_bottom_logo
      this.web_site_customer_service = res.web_site_customer_service
      this.web_site_logo = res.web_site_logo
      this.web_site_title = res.web_site_title
    },
    // 刷新配置
    refreshConfig() {
      ApiGetSet().then(res=>{
        this.refresh(res)
      })
    }
  }

})
export default useConfigStore
