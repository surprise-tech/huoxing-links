import axios, {
  type AxiosInstance,
  type AxiosRequestConfig,
  type AxiosResponse,
  type CustomParamsSerializer,
  type InternalAxiosRequestConfig,
  type Method
} from 'axios'
import { stringify } from 'qs'
import router from '@/router'
import { ElMessage } from 'element-plus'
import { userStore } from '@/stores'
import { VITE_API_URL } from '../../public/config.json'

const http: AxiosInstance = axios.create({
  baseURL: import.meta.env.MODE === 'production' ? VITE_API_URL : import.meta.env.VITE_PROXY_PATH,
  timeout: 60000,
  headers: {
    Accept: 'application/json',
    'Content-Type': 'application/json',
    'X-Requested-With': 'XMLHttpRequest'
  },
  // 数组格式参数序列化（https://github.com/axios/axios/issues/5142）
  paramsSerializer: {
    serialize: stringify as CustomParamsSerializer
  }
})

// 请求拦截
http.interceptors.request.use(
  (config: InternalAxiosRequestConfig) => {
    const token = userStore.getToken()
    if (token) {
      config.headers['Authorization'] = 'Bearer ' + token
    }
    return config
  },
  (error) => {
    return Promise.reject(error)
  }
)

// 相应拦截器
http.interceptors.response.use(
  (response: AxiosResponse) => {
    return response
  },
  (error) => {
    if ([400, 422].includes(error.response.status)) {
      ElMessage.error(error.response.data.message)
    } else if (error.response.status === 500) {
      ElMessage.error('服务器错误！请稍后再试')
    } else if (error.response.status === 401) {
      // 未登录/登录过期
      router.push('/login').catch(() => {
        ElMessage.error('/login路由不存在')
      })
    }
    return Promise.reject(error)
  }
)

// 封装request
const request = <T>(method: Method, url: string, config?: AxiosRequestConfig): Promise<T> => {
  // 单独处理自定义请求/响应回掉
  return new Promise((resolve, reject) => {
    http
      .request({
        method,
        url,
        ...config
      })
      .then((response) => {
        resolve(response.data)
      })
      .catch((error) => {
        reject(error)
      })
  })
}

export default {
  request,
  get<T>(url: string, config?: AxiosRequestConfig): Promise<T> {
    return this.request<T>('get', url, config)
  },
  delete<T>(url: string, config?: AxiosRequestConfig): Promise<T> {
    return this.request<T>('delete', url, config)
  },
  post<T, D>(url: string, data?: D, config?: AxiosRequestConfig): Promise<T> {
    return this.request<T>('post', url, {
      ...config,
      data
    })
  },
  put<T, D>(url: string, data?: D, config?: AxiosRequestConfig): Promise<T> {
    return this.request<T>('put', url, {
      ...config,
      data
    })
  }
}
