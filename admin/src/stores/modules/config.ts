import { defineStore } from 'pinia'

const useConfigStore = defineStore('init', {
  state: () => ({
    code_mode: 0,
    package: [] as Array<any>,
    asset_url: ''
  }),
  actions: {
    refresh(res: any) {
      this.code_mode = res.code_mode
      this.package = res.package
      this.asset_url = res.asset_url
    }
  }
})
export default useConfigStore
