import http from '@/utils/http'

/*
 *  获取小程序列表
 * */
export const ApiAppList = () => {
  return http.get('/min-programs?is_enable=1')
}

export const ApiMaterialCate = () => {
  return http.get('/materials-cate')
}
// 批量删除
export const ApiDelMaterial = (data: any) => {
  return http.post('/material-upload-del', data)
}

