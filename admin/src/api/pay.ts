import http from '@/utils/http'

/**
 * 购买VIP. type 1:立即生效 2:顺延生效
 */
export const ApiPayVip = (vip_id: number, type: string) => {
  return http.post('/buy-vip', { vip_id, type })
}

/**
 * 积分记录.
 */
export const ApiCreditLog = (page: number, pageSize: number) => {
  return http.get('/credit-logs?page=' + page + '&pageSize=' + pageSize)
}

/**
 * 佣金记录.
 */
export const ApiCommissionLog = (page: number, pageSize: number) => {
  return http.get('/commission-logs?page=' + page + '&pageSize=' + pageSize)
}

/**
 * 充值积分.
 */
export const ApiRechargeCredit = (amount: string) => {
  return http.post('/buy-credit', { amount })
}

/**
 * 提现.
 */
export const ApiApplyWithdraw = (data: any) => {
  return http.post('/apply-withdraw', data)
}

// 邀请记录
export const ApiInviteLog = (page: number, pageSize: number) => {
  return http.get('/agent-invite?page=' + page + '&pageSize=' + pageSize)
}
