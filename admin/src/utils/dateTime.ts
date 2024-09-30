// 获取当前日期
import { ref } from 'vue'

export const getDate = () => {
  const dateTime = new Date()
  const year = dateTime.getFullYear()
  const month = dateTime.getMonth() + 1
  const date = dateTime.getDate()
  return { start: `${year}-${month}-${date}`, end: `${year}-${month}-${date + 1}` }
}

// 获取本周日期
export const getWeek = () => {
  const days = ref([''])
  const week = ref()
  const date = ref()
  const weeks = ['星期一', '星期二', '星期三', '星期四', '星期五', '星期六', '星期日']
  const dateTime = new Date()
  const year = dateTime.getFullYear()
  const month = dateTime.getMonth() + 1
  date.value = dateTime.getDate() // 日期
  week.value = dateTime.getDay() - 1 // 0 ~ 6 外国的日期是周日在前
  week.value = week.value === -1 ? weeks[6] : weeks[week.value] // -1 则为周日
  if (week.value === weeks[0]) {
    // 如果为周一，获取本周日期
    days.value[0] = `${year}-${month}-${date.value}`
    for (let i = 1; i < 7; i += 1) {
      const oneDay = new Date(days.value[0]).getTime() + i * 86400000
      days.value[i] = `${new Date(oneDay).getFullYear()}-${new Date(oneDay).getMonth() + 1}-${new Date(
        oneDay
      ).getDate()}`
    }
  } else {
    const index = weeks.indexOf(week.value) // 找到今天索引
    const Monday = Date.now() - index * 86400000
    days.value[0] = `${new Date(Monday).getFullYear()}-${new Date(Monday).getMonth() + 1}-${new Date(Monday).getDate()}`
    for (let j = 1; j < 7; j += 1) {
      const oneDay = Monday + j * 86400000
      days.value[j] = `${new Date(oneDay).getFullYear()}-${new Date(oneDay).getMonth() + 1}-${new Date(
        oneDay
      ).getDate()}`
    }
  }

  return { start: days.value[0], end: days.value[6] }
}

// 获取本月日期
export const getMonth = () => {
  const months = ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月']
  const dateTime = new Date()
  const year = dateTime.getFullYear()
  const month = dateTime.getMonth() + 1
  const monthKey = dateTime.getMonth()
  if (months[monthKey] === '1月') {
    return { start: `${year}-${month}-1`, end: `${year}-${month}-31` }
  }
  if (months[monthKey] === '3月') {
    return { start: `${year}-${month}-1`, end: `${year}-${month}-31` }
  }
  if (months[monthKey] === '4月') {
    return { start: `${year}-${month}-1`, end: `${year}-${month}-30` }
  }
  if (months[monthKey] === '5月') {
    return { start: `${year}-${month}-1`, end: `${year}-${month}-31` }
  }
  if (months[monthKey] === '6月') {
    return { start: `${year}-${month}-1`, end: `${year}-${month}-30` }
  }
  if (months[monthKey] === '7月') {
    return { start: `${year}-${month}-1`, end: `${year}-${month}-31` }
  }
  if (months[monthKey] === '8月') {
    return { start: `${year}-${month}-1`, end: `${year}-${month}-31` }
  }
  if (months[monthKey] === '9月') {
    return { start: `${year}-${month}-1`, end: `${year}-${month}-30` }
  }
  if (months[monthKey] === '10月') {
    return { start: `${year}-${month}-1`, end: `${year}-${month}-31` }
  }
  if (months[monthKey] === '11月') {
    return { start: `${year}-${month}-1`, end: `${year}-${month}-30` }
  }
  if (months[monthKey] === '12月') {
    return { start: `${year}-${month}-1`, end: `${year}-${month}-31` }
  }
  if (months[monthKey] === '2月') {
    if (year % 400 === 0 && year % 4 === 0 && year % 100 !== 0) {
      // 闰年
      return { start: `${year}-${month}-1`, end: `${year}-${month}-29` }
    } else {
      // 平年
      return { start: `${year}-${month}-1`, end: `${year}-${month}-28` }
    }
  }
}

// 获取全年数据
export const getYear = () => {
  const dateTime = new Date()
  const year = dateTime.getFullYear()
  return { start: `${year}-1-1`, end: `${year}-12-31` }
}
// 获取今天往后一个月的日期
export const getNextMonth = () => {
  const dateTime = new Date()
  const year = dateTime.getFullYear()
  const month = dateTime.getMonth() + 1
  const date = dateTime.getDate()
  return `${year}-${month + 1 > 10 ?( month + 1) : '0' + (month + 1)}-${date} 00:00:00`
}
