import http from '@/utils/http'

// 获取概况总数据
export const ApiAllData = (type: any) => {
  return http.get<any>(`/home?type=${type}`)
}
