import http from '@/utils/http'


export const ApiDomainEnableList = () => {
  return http.get('/domains?enable=1')
}

