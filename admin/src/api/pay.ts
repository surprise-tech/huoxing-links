import http from '@/utils/http'

/**
 * 购买VIP. type 1:立即生效 2:顺延生效
 */
export const ApiPayVip = (vip_id: number, type: string) => {
  return http.post('/buy-vip', { vip_id, type })
}
