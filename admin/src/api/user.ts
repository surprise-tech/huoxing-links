import type { ILoginRequest, ILoginResponse, IUserInfoResponse } from '@/models/user'
import http from '@/utils/http'

/**
 * 登录.
 * @param data
 * @constructor
 */
export const ApiLogin = (data: ILoginRequest) => {
  return http.post<ILoginResponse, ILoginRequest>('/login', data)
}

/**
 * 获取用户信息.
 * @constructor
 */
export const ApiUserInfo = () => {
  return http.get<IUserInfoResponse>('/userinfo')
}

/**
 * 注册.
 */
export const ApiRegister = (data: any) => {
  return http.post('/register', data)
}

/**
 * 重置密码.
 */
export const ApiResetPassword = (data: any) => {
  return http.post('/reset-password', data)
}

/**
 * 修改密码.
 */
export const ApiChangePassword = (data: any) => {
  return http.post('/change-password', data)
}

/**
 * 兑换卡密码.
 */
export const ApiConsumeCard = (secret: string) => {
  return http.post('/consume-card', { secret })
}
export const ApiUserFix = (id: any, data: any) => {
  return http.put(`/users/${id}`, data)
}
