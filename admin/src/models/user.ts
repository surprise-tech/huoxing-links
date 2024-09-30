// 登录参数

export interface ILoginRequest {
  username: string
  password: string
}

// 登录响应数据
export interface ILoginResponse {
  token: string
}

// 获取用户信息响应数据
export interface IUserInfoResponse {
  end_at: string
  id: number
  start_at: string
  type?: number
  credit?: number
  username: string
  login_time?: string
  agent_package?: any
  vip_package?: any
  link_amount: number
  dy_card_amount: number
  yuanma_amount: number
  children_count: number
  logo: string
  wechat_qr: string
  site_name: string
  parent: any
}
