import http from '@/utils/http'

// 获取系统设置
export const ApiGetSet = () => {
  return http.get('/config')
}

export const ApiGetSetALL = () => {
  return http.get('/config/base')
}

//保存配置
export const ApiSaveSet = (data: any) => {
  return http.put('/config', data)
}

/*
 *  获取图片验证码
 * */
export const ApiGetImgCaptcha = () => {
  return http.get('/captcha/image')
}

/*
 *  获取短信验证码
 * */
export const ApiGetSmsCaptcha = (data: any) => {
  return http.post('/captcha/sms', data)
}

// 设置 type: base基础配置
export const ApiGetSetByType = (type: string) => {
  return http.get(`/config/${type}`)
}
// 设置 type: base基础配置
export const ApiSaveSetByType = (type: string, data: any) => {
  return http.put(`/config/${type}`, data)
}

export const ApiLinkList = () => {
  return http.get(`/link-list/`)
}

// 获取链接详情.
export const ApiLinkDetail = (id: any) => {
  return http.get(`/links/${id}`)
}
// 修改落地 小程序禁用
export const ApiLinkFixApp = (id: any, data: any) => {
  return http.put(`/min-programs/${id}`, data)
}

export const ApiRenewalLink = (data: any) => {
  return http.post('/link-renewal', data)
}
