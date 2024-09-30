import { defineStore } from 'pinia'
import type { ILoginRequest, ILoginResponse, IUserInfoResponse } from '@/models/user'
import { ApiLogin, ApiUserInfo } from '@/api/user'
import { ApiGetSet } from '@/api/comment'
import { configStore, userStore } from '@/stores'
import useAppStore from '@/stores/modules/app'

// 用户信息
const TOKEN_KEY = 'token'
const useUserStore = defineStore('user-store', {
  state: () => ({
    tokenInfo: {} as ILoginResponse,
    userInfo: {} as IUserInfoResponse,
    checkedPermission: true,
    dialogVisible: false
  }),
  actions: {
    logout() {
      return new Promise<void>((resolve) => {
        localStorage.clear()
        resolve()

      })
    },
    login(data: ILoginRequest, remember: boolean = false) {
      return new Promise<void>((resolve, reject) => {
        ApiLogin(data)
          .then((res) => {
            this.setToken(res, remember)
            // this.dialogVisible = true
            ApiGetSet().then((res: any) => {
              configStore.refresh(res)
              userStore.dialogVisible = true
            })
            resolve()
          })
          .catch((err) => {
            reject(err)
          })
      })
    },
    setToken(tokenInfo: ILoginResponse, remember: boolean = false) {
      if (remember) {
        window.localStorage.setItem(TOKEN_KEY, JSON.stringify(tokenInfo))
      } else {
        window.sessionStorage.setItem(TOKEN_KEY, JSON.stringify(tokenInfo))
      }
      this.tokenInfo = tokenInfo
    },

    getToken() {
      if (this.tokenInfo.token) {
        return this.tokenInfo.token
      }
      const tokenInfo = window.localStorage.getItem(TOKEN_KEY) || window.sessionStorage.getItem(TOKEN_KEY)
      if (tokenInfo) {
        try {
          this.tokenInfo = JSON.parse(tokenInfo)
          return this.tokenInfo.token
        } catch (e) {
          return ''
        }
      }
      return ''
    },

    hasPermission(permission?: Array<string>) {
      if (Array.isArray(permission) && this.userInfo.type) {
        const role = ['vip', 'agent', 'admin'][this.userInfo.type - 1]

        return permission.includes(role)
      }
      return true
    },

    // 刷新用户信息
    refreshUserInfo() {
      ApiUserInfo().then((res) => {
        this.userInfo = res
      })
    }
  }
})

export default useUserStore
