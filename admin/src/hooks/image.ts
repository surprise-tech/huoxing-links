import { configStore } from '@/stores'

/**
 * 自动片接图片路径
 * @param images
 * @returns {*|string}
 */
export const ImagePath = (images: any) => {
  let path
  if (Array.isArray(images) && images.length > 0) {
    path = images[0]
  } else {
    path = images
  }
  if (!path) {
    return path
  }
  if (path.indexOf('http') === 0) {
    return path
  }

  let newPath: any
  if (configStore.asset_url) {
    // asset_url 或者path第一位是否带有斜杠
    if (path.indexOf('/') === 0 && configStore.asset_url.replace(/\/$/, '').length > 0) {
      newPath = configStore.asset_url.slice(0, -1) + path
    } else {
      newPath = configStore.asset_url + path
    }
  }
  return newPath
}
