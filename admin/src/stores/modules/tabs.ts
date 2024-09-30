import { defineStore } from 'pinia'
import type { RouteLocationNormalizedLoaded } from 'vue-router'

// 顶部标签
const useTabsStore = defineStore('tabs-store', {
  state: () => ({
    visitedViews: [] as any[],
    cachedViews: [] as any[]
  }),
  actions: {
    addView(view: RouteLocationNormalizedLoaded) {
      if (this.visitedViews.some((value) => value.path === view.path)) {
        return
      }
      this.visitedViews.push(
        Object.assign({}, view, {
          title: view.meta?.title || 'unknown'
        })
      )
    },
    addCachedView(view: RouteLocationNormalizedLoaded) {
      if (this.cachedViews.includes(view.name)) {
        return
      }

      if (view.meta.cache) {
        this.cachedViews.push(view.name)
      }
    },
    delView(view: RouteLocationNormalizedLoaded) {
      for (const [i, v] of this.visitedViews.entries()) {
        if (v.path === view.path) {
          this.visitedViews.splice(i, 1)
          break
        }
      }

      this.delCachedView(view).then()
    },
    delCachedView(view: RouteLocationNormalizedLoaded) {
      return new Promise((resolve) => {
        const index = this.cachedViews.indexOf(view.name)
        if (index > -1) {
          this.cachedViews.splice(index, 1)
        }
        resolve([...this.cachedViews])
      })
    },
    delOthersViews(view: RouteLocationNormalizedLoaded) {
      this.visitedViews = this.visitedViews.filter((v) => {
        return v.meta.affix || v.path === view.path
      })

      const index = this.cachedViews.indexOf(view.name)
      if (index > -1) {
        this.cachedViews = this.cachedViews.slice(index, index + 1)
      } else {
        this.cachedViews = []
      }
    },
    delAllViews() {
      this.visitedViews = this.visitedViews.filter((tab) => tab.meta.affix)
      this.cachedViews = []
    }
  }
})

export default useTabsStore
