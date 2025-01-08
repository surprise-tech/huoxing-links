import http from '@/utils/http'

export const EditLink = (data: any) => {
  if (data && data.id) {
    return http.put(`/links/${data.id}`, data)
  } else {
    return http.post('/links', data)
  }
}
