import type { IMenu } from '@/models/menu'

const apiRouter: IMenu[] = [
  {
    path: '/admin-home',
    name: 'admin-home',
    meta: {
      title: '系统首页',
      icon: 'menu-home',
      iconIn: 'menu-home-in',
      permission: ['admin']
    },
    component: '/home/admin-home'
  },
  {
    path: '/vip-home',
    name: 'vip-home',
    meta: {
      title: '会员中心',
      icon: 'menu-vip-in',
      iconIn: 'menu-vip-in',
      permission: ['vip']
    },
    component: '/home/home'
  },
  {
    path: '/link',
    name: 'link',
    redirect: '/link',
    meta: {
      title: '智慧链接管理',
      icon: 'menu-link',
      iconIn: 'menu-link-in',
      permission: ['admin', 'vip']
    },
    children: [
      {
        path: '/link/addLink',
        name: 'addLink',
        component: '/link/addLink/index',
        meta: {
          title: '抖音卡片跳转'
        }
      },
      {
        path: '/link/fallApp',
        name: 'fallApp',
        component: '/link/fallApp/index',
        meta: {
          title: '落地小程序'
        }
      },
    ]
  },
  {
    path: '/data',
    name: 'data',
    redirect: '/data',
    meta: {
      title: '数据中心',
      icon: 'menu-credit',
      iconIn: 'menu-credit',
      permission: ['vip']
    },
    children: [
      {
        path: '/super/invitation',
        name: 'agentInvitation',
        meta: {
          title: '邀请记录'
        },
        component: '/newAgent/Invitation'
      }
    ]
  },
  {
    path: '/librarylist',
    name: 'librarylist',
    redirect: '/library/index',
    meta: {
      title: '素材管理',
      icon: 'menu-library',
      iconIn: 'menu-library-in',
      permission: ['admin', 'vip']
    },
    children: [
      {
        path: '/library',
        name: 'library',
        component: '/library/index',
        meta: {
          title: '素材管理'
        }
      },
      {
        path: '/library/cate',
        name: 'librarycate',
        component: '/library/cateIndex',
        meta: {
          title: '素材分类',
          permission: ['admin']
        }
      }
    ]
  },
  {
    path: '/user',
    name: 'user',
    redirect: '/super/user',
    meta: {
      title: '会员中心',
      icon: 'menu-vip',
      iconIn: 'menu-vip-in',
      permission: ['admin']
    },
    children: [
      {
        path: '/super/users',
        name: 'super-users',
        component: '/super/users/index',
        meta: {
          title: '会员管理'
        }
      },
    ]
  },
  {
    path: '/sys-setting',
    name: 'setting',
    component: '/super/setting/setting',
    meta: {
      title: '网站设置',
      icon: 'menu-set',
      iconIn: 'menu-set-in',
      permission: ['admin']
    },
  },
  {
    path: '/super',
    name: 'super',
    redirect: '/super/vipSetMeal',
    meta: {
      title: '超级管理',
      icon: 'menu-super',
      iconIn: 'menu-super-in',
      permission: ['admin']
    },
    children: [
      {
        path: '/super/notices',
        name: 'notices',
        component: '/super/notices/index',
        meta: {
          title: '公告管理'
        }
      },
      {
        path: '/super/domains',
        name: 'domains',
        component: '/super/domains/index',
        meta: {
          title: '域名管理'
        }
      }
    ]
  },
  {
    path: '/help',
    name: 'help',
    meta: {
      title: '帮助文档',
      icon: 'menu-link',
      iconIn: 'menu-link-in',
      newOpen: true,
      url: 'https://rcnnlbqjb13m.feishu.cn/wiki/VWxywr2zKikjmOkLCb4cYeh7nif'
    }
  }
]

export default apiRouter
