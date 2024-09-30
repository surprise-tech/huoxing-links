// 菜单数据类型
export interface IMenuMeta {
  title: string
  icon?: string
  iconIn?: string
  permission?: string[]
  hidden?: boolean
  pluginSlug?: string
  newOpen?: boolean
  url?: string
}
export interface IMenu {
  path: string
  name: string
  redirect?: string
  component?: string
  meta: IMenuMeta
  children?: IMenu[]
}
