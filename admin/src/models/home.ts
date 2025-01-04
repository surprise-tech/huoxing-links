// 概况统计
export interface dataType {
  member_count: string | number
  agent_count: string | number
  user_count: string | number
  pay_vip: string | number
  pay_agent: string | number
  pay_count: string | number
}

// 曲线order类型
export interface turnOverType {
  title: string
  grid: any
  tooltip: any
  legend: any
  xAxis: any
  yAxis: any
  series: any[]
}

