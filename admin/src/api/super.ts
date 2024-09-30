
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

/*
 *  同意提现申请
 *  @param id
 * */
export const ApiAgreeApply = (id: any) => {
  return http.post(`/withdraws-confirm/${id}`)
}

/*
 *  拒绝提现申请
 *  @param id
 * */
export const ApiRejectApply = (id: any) => {
  return http.post(`/withdraws-reject/${id}`)
}

// 代理商等级列表
export const ApiLevelList = () => {
  return http.get('/agent-level')
}

// 卡密修改
export const ApiCardFix = (id: any, data: any) => {
  return http.put(`/cards/${id}`, data)
}

export const getAgentTree = () => {
  return http.get('/agent-tree')
}
export  const  getNotify = () => {
  return http.get('/notice')
}
export  const  editNotify = (data:any) => {
  return http.put('/notice',data)
}
