// 二维码配置
export interface QR {
  sort: number
  name: string
  path: string
  expired_at: string
  uv_limit_num: number
  visit_uv: number
}

// wx 跳转微信二维码配置
interface WX {
  avatar: string
  title: string
  sub_title: string
  qr: QR[]
  switch_type?: number
  uv_limit_type: number
}

// 链接类型
export interface linkConfig {
  domain_id: any
  min_id: any
  url: any
  expired_at: any
  wx: WX
}

// 允许创建的类型
interface allowType {
  MINI_PROGRAM: boolean
  KING_DOC: boolean
  CLI_QR: boolean
  WORK_WECHAT: boolean
  LANDING_MINI: boolean
  QR_QQ: boolean
}

// 权限配置
export interface vipConfig {
  uv_limit: number
  count_limit: number,
  yuanma_limit:number,
  douyin_reply_limit:number,
  min_count_limit: number
  pre_min: boolean
  min_disabled_check: boolean
  support: boolean
  cur_index: boolean
  allow_type: allowType
}

// agent配置
export interface agentConfig {
  uv_limit_price: number
  no_uv_limit_price: number
  uv_price: number
  min_count_limit: number
  pre_min: boolean
  min_disabled_check: boolean
  support: boolean
  cur_index: boolean
  allow_type: allowType
}
