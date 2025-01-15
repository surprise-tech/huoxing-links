
import http from '@/utils/http'
/*
 *  获取vip套餐
 * */

export const ApiVipList = () => {
  return http.get('/vip-packages')
}
export const ApiAgentList = () => {
  return http.get('/agent-packages')
}

// 代理商等级列表
export const ApiLevelList = () => {
  return http.get('/agent-level')
}

export const getAgentTree = () => {
  return http.get('/agent-tree')
}

export const getNotify = () => {
  return http.get('/notice')
}

export const editNotify = (data:any) => {
  return http.put('/notice', data)
}
