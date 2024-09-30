import http from '@/utils/http'
import { onMounted, reactive, watch, nextTick } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { userStore } from '@/stores'

// CURD Hooks Options接口
export interface ICurdOptions {
  // 在页面挂在后是否自动调用数据列表接口
  autoInit?: boolean
  // 是否禁用列表
  disableList?: boolean
  // 数据列表 Url
  url?: string
  // 数据列表 Url 后缀
  urlSuffix?: string
  // 是否需要分页
  openPage?: boolean
  // 主键key，用于删除场景
  primaryKey?: string
  // 导出 Url
  exportUrl?: string
  // 查询条件
  queryForm?: any
  // 数据列表
  dataList?: any[]
  meta?: any
  // 排序字段
  order?: string
  // 是否升序
  asc?: boolean
  // 当前页码
  page?: number
  // 每页数
  limit?: number
  // 总条数
  total?: number
  // 预置分页大小选项
  pageSizes?: number[]
  // 数据列表，loading状态
  listLoading?: boolean
  // 数据列表，多选项
  listSelections?: any[]
  // 详情loading
  detailLoading?: boolean
  // 详情数据
  detailData?: any
  // 新增/编辑弹框
  formVisible?: boolean
  // 新增/编辑loading
  formLoading?: boolean
  // 一级/二级
  level?: string
  // 筛选出新人券
  type?: string
}

/**
 * Curd Hooks CURD相关的Hooks
 * @param options
 */
export default function useCurd(options: ICurdOptions) {
  // 原始查询条件
  const state: ICurdOptions = reactive(
    Object.assign(
      {
        autoInit: true,
        disableList: false,
        url: '',
        urlSuffix: '',
        openPage: true,
        primaryKey: 'id',
        exportUrl: '',
        queryForm: {},
        dataList: [],
        meta: [],
        order: '',
        asc: true,
        page: 1,
        limit: 10,
        total: 0,
        pageSizes: [10, 20, 30, 40, 50],
        listLoading: false,
        listSelections: [],
        detailLoading: false,
        detailData: undefined,
        formVisible: false,
        formLoading: false,
        level: '',
        type: ''
      },
      options
    )
  )
  // 列表
  const query = () => {
    userStore.refreshUserInfo()
    if (state.disableList) {
      return
    }
    if (!state.url) {
      return
    }
    state.listLoading = true
    const params = {
      ...state.queryForm
    }
    if (state.order) {
      params.order = state.order
      params.asc = state.asc ? 1 : 0
    }
    if (state.openPage) {
      params.page = state.page
      params.page_size = state.limit
    }

    http
      .get<any>(state.url + state.urlSuffix, { params })
      .then((res) => {
        if (state.openPage) {
          state.dataList = res.data
          state.total = res.meta.total
          if (state.type === 'coupon') {
            state.dataList = state.dataList?.filter((item: any) => item.is_new === false)
          }
          state.meta = res.meta
        } else {
          state.dataList = res
        }
      })
      .finally(() => {
        state.listLoading = false
      })
  }

  // 每页数变化
  const sizeChangeHandle = (val: number) => {
    state.page = 1
    state.limit = val
    query()
  }

  // 当前页码变化
  const currentChangeHandle = (val: number) => {
    state.page = val
    query()
  }

  // 多选
  const selectionChangeHandle = (selections: any[]) => {
    state.listSelections = selections.map((item: any) => state.primaryKey && item[state.primaryKey])
  }

  // 恢复
  const recover = (key: number | string) => {
    return new Promise((resolve, reject) => {
      if (!state.url) {
        return reject()
      }

      ElMessageBox.confirm('是否恢复信息?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      })
        .then(() => {
          state.listLoading = true
          http.delete(state.url + '/' + key + state.urlSuffix).then(() => {
            ElMessage.success('恢复成功')
            query()
            return resolve(true)
          })
        })
        .catch((e) => reject(e))
        .finally(() => {
          state.listLoading = false
        })
    })
  }

  // 删除
  const deleteHandle = (key: number | string) => {
    return new Promise((resolve, reject) => {
      if (!state.url) {
        return reject()
      }

      ElMessageBox.confirm('确定删除?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      })
        .then(() => {
          state.listLoading = true
          http.delete(state.url + '/' + key + state.urlSuffix).then(() => {
            ElMessage.success('删除成功')
            query()
            return resolve(true)
          })
        })
        .catch((e) => reject(e))
        .finally(() => {
          state.listLoading = false
        })
    })
  }

  // 回收站删除
  const recycleDel = (key: number | string, type: number | string) => {
    return new Promise((resolve, reject) => {
      if (!state.url) {
        return reject()
      }

      ElMessageBox.confirm('确定删除? 不可恢复！', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      })
        .then(() => {
          state.listLoading = true
          http.delete(state.url + '/' + key + '/' + type).then(() => {
            ElMessage.success('删除成功')
            query()
            return resolve(true)
          })
        })
        .catch((e) => reject(e))
        .finally(() => {
          state.listLoading = false
        })
    })
  }

  // 获取详情
  const getDetail = (key: number | string) => {
    return new Promise((resolve, reject) => {
      if (!state.url) {
        return reject()
      }
      state.detailLoading = true
      http
        .get(state.url + '/' + key + state.urlSuffix)
        .then((res) => {
          state.detailData = res
          return resolve(res)
        })
        .catch((e) => reject(e))
        .finally(() => {
          state.detailLoading = false
        })
    })
  }

  // 新增/编辑弹框
  const showForm = (idOrObject?: any) => {
    // 表单加载中
    state.formLoading = true

    // 显示弹框
    state.formVisible = true

    // 新增
    if (idOrObject instanceof Event || idOrObject === undefined) {
      state.detailData = null // 这里不要写成undefined，否则会导致表单的值不会清空
      // 表单加载完成
      nextTick(() => (state.formLoading = false))
    } else {
      // 编辑
      if (typeof idOrObject === 'object') {
        // 直接赋值
        state.detailData = JSON.parse(JSON.stringify(idOrObject))
        nextTick(() => (state.formLoading = false)) // 表单加载完成
      } else if (typeof idOrObject === 'number' || typeof idOrObject === 'string') {
        // 先给个ID，快速判断是新增还是编辑
        state.detailData = {}
        // 获取详情并赋值;
        getDetail(idOrObject).finally(() => {
          state.formLoading = false // 表单加载完成
        })
      }
    }
  }

  // 新增
  const addSubmit = (data: any, resetQuery: boolean = true) => {
    return new Promise((resolve, reject) => {
      if (!state.url) {
        return reject()
      }
      state.formLoading = true
      http
        .post(state.url + state.urlSuffix, data)
        .then((res) => {
          ElMessage.success('保存成功')
          state.formVisible = false
          resetQuery && query()
          return resolve(res)
        })
        .catch((e) => reject(e))
        .finally(() => {
          state.formLoading = false
        })
    })
  }

  // 编辑
  const editSubmit = (data: any, id: number, resetQuery: boolean = true) => {
    return new Promise((resolve, reject) => {
      if (!state.url) {
        return reject()
      }

      state.formLoading = true
      http
        .put(`${state.url}/${id}${state.urlSuffix}`, data)
        .then((res) => {
          ElMessage.success('保存成功')
          state.formVisible = false
          resetQuery && query()
          return resolve(res)
        })
        .catch((e) => reject(e))
        .finally(() => {
          state.formLoading = false
        })
    })
  }

  // 行内编辑
  // id: 当前ID field: 需要更改的字段 value：需要需改的值
  const inlineEdit = (id: number, field: string, value: any, resetQuery: boolean = true) => {
    return new Promise((resolve, reject) => {
      if (!state.url) {
        return reject()
      }

      state.formLoading = true
      http
        .post(`${state.url}/${id}${state.urlSuffix}`, { field, value, _method: 'PATCH' })
        .then((res) => {
          ElMessage.success('保存成功')
          state.formVisible = false
          resetQuery && query()
          return resolve(res)
        })
        .catch((e) => reject(e))
        .finally(() => {
          state.formLoading = false
        })
    })
  }

  // 提交表单
  const submitForm = (data: any, detail?: any) => {
    if (!state.url) {
      return
    }
    state.formLoading = true
    if (detail && state.primaryKey) {
      // 编辑
      return editSubmit(data, detail[state.primaryKey])
    } else {
      // 新增
      return addSubmit(data)
    }
  }

  // 重置\刷新功能
  const resetInput = () => {
    state.queryForm = {}
    state.page = 1
    state.limit = 10
    query()
  }

  // 需要queryForm的刷新
  const reset = () => {
    // state.queryForm = {}
    state.page = 1
    state.limit = 10
    query()
  }

  // 启用模板
  const useMode = (id: any) => {
    http.post(`${state.url}/enable/${id}`).then(() => {
      query()
    })
  }

  // 查询重置page
  const research = () => {
    state.page = 1
    state.limit = 10
    query()
  }

  // 订单导出功能
  const outExport = () => {
    if (!state.url) {
      return
    }
    state.listLoading = true
    const arr = []
    for (const value in state.queryForm) {
      arr.push(`${value}=${state.queryForm[value]}`)
    }
    const str = arr.join('&')
    // '/admin/orders?page=1&page_size=10&export=1'
    const url = `${state.url}?${str}&export=1`
    http
      .request('GET', url, {
        responseType: 'blob'
      })
      .then((re) => {
        const a = document.createElement('a')
        a.href = URL.createObjectURL(re as Blob)
        document.body.appendChild(a)
        a.click()
        document.body.removeChild(a)
      })
      .finally(() => {
        state.listLoading = false
      })
  }

  // 发货单导出
  const dispatchOutExport = (status: number, type?: any) => {
    // 订单状态是已支付
    state.queryForm.status = status
    // 配送方式是快递
    state.queryForm.delivery_type = type
    outExport()
    nextTick(() => {
      state.queryForm = {}
    })
  }

  // 页面挂载后，自动调用数据列表接口
  onMounted(() => {
    state.autoInit && query()
  })

  // 监听表单弹框的显示状态，如果关闭了弹框，就清空详情数据
  watch(
    () => state.formVisible,
    (val) => {
      if (!val) {
        state.detailData = undefined
        state.formLoading = false
      }
    }
  )

  return {
    state,
    sizeChangeHandle,
    currentChangeHandle,
    selectionChangeHandle,
    deleteHandle,
    showForm,
    submitForm,
    getDetail,
    addSubmit,
    editSubmit,
    inlineEdit,
    recover,
    resetInput,
    research,
    query,
    recycleDel,
    reset,
    outExport,
    useMode,
    dispatchOutExport
  }
}
