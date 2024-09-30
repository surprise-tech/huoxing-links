import http from '@/utils/http'

// 修改代理状态
export const EditAgentStatus = (id: any, status: Boolean) => {
  return http.put<any, any>(`/users/${id}`, {
    status
  })
}
// 修改logo 和微信二维码
export const EditAgentLogo = (data: any) => {
  return http.put<any, any>(`/userinfo`, data)
}
