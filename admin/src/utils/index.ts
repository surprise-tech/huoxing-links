import { ElMessage, type FormInstance } from 'element-plus'
import { ApiLinkDetail } from '@/api/comment'

/**
 * 验证码表单.
 * @param formRef
 */
export const validateForm = (formRef?: FormInstance) => {
  return new Promise((resolve, reject) => {
    if (!formRef) {
      reject(false)
    }
    formRef?.validate((valid) => {
      if (valid) {
        resolve(valid)
      } else {
        reject(valid)
      }
    })
  })
}

/**
 * 是否全屏。
 */
export const isFullscreen = () => {
  const doc: any = window.document
  return !!(
    doc.webkitIsFullScreen ||
    doc.mozFullScreen ||
    doc.msFullscreenElement ||
    doc.fullscreenElement
  )
}

/**
 * 退出全屏.
 */
export const exitFullscreen = () => {
  const doc: any = window.document
  if (doc.exitFullscreen) {
    doc.exitFullscreen()
  } else if (doc.msExitFullscreen) {
    doc.msExitFullscreen()
  } else if (doc.mozCancelFullScreen) {
    doc.mozCancelFullScreen()
  } else if (doc.webkitExitFullscreen) {
    doc.webkitExitFullscreen()
  }
}

/**
 * 请求全屏.
 */
export const requestFullscreen = () => {
  const element: any = window.document.documentElement
  if (element.requestFullscreen) {
    element.requestFullscreen()
  } else if (element.msRequestFullscreen) {
    element.msRequestFullscreen()
  } else if (element.mozRequestFullScreen) {
    element.mozRequestFullScreen()
  } else if (element.webkitRequestFullscreen) {
    element.webkitRequestFullscreen()
  }
}

/**
 * 获取数据.
 * @param obj
 * @param key
 * @param defaultVal
 */
export const data_get = (obj: any, key: string, defaultVal: any = '') => {
  if (!obj) {
    return defaultVal
  }
  const keys = key.split('.')
  let result = obj
  for (const key of keys) {
    result = result[key]
    if (!result) {
      return defaultVal
    }
  }
  return result
}

export const data_get_length = (obj: any, key: string) => {
  const keys = key.split('.')
  let result = obj
  for (const key of keys) {
    result = result[key]
  }
  let num = 0
  for (const kk in result) {
    if (result[kk]) {
      num++
    }
  }

  return num === Object.keys(result).length
}

/**
 * 计算文件大小
 * @param size
 */
export const calcFileSize = (size: number) => {
  if (!size) {
    return '0 Bytes'
  }
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB']
  const i = Math.floor(Math.log(size) / Math.log(k))
  return (size / Math.pow(k, i)).toFixed(2) + ' ' + sizes[i]
}

/**
 * 是否图片.
 * @param path
 */
export const isImage = (path?: string) => {
  if (!path) {
    return false
  }
  const ext = path.split('.').pop()?.toLowerCase()
  return ['png', 'jpg', 'jpeg', 'gif'].includes(ext || '')
}

/**
 * 复制到剪切板.
 * @param text
 */
export const setClipboard = (text: string) => {
  const root = window as any
  if (root.clipboardData && root.clipboardData.setData) {
    root.clipboardData.setData('Text', text)
    ElMessage.success('复制成功')
  } else if (document.queryCommandSupported && document.queryCommandSupported('copy')) {
    const textarea = document.createElement('textarea')
    textarea.textContent = text
    textarea.style.position = 'fixed'
    document.body.appendChild(textarea)
    textarea.select()
    try {
      document.execCommand('copy')
      ElMessage.success('复制成功')
    } catch (ex) {
      console.warn('Copy to clipboard failed.', ex)
    } finally {
      document.body.removeChild(textarea)
    }
  }
}

// 复制链接
export const copyLink = (row: any) => {
  ApiLinkDetail(row.id).then((res: any) => {
    if (res.share_link) {
      setClipboard(res.share_link)
    } else {
      ElMessage.error('链接不存在!')
    }
  })
}