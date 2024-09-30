<script setup lang="ts">
import '@wangeditor/editor/dist/css/style.css' // 引入css
import { Editor, Toolbar } from '@wangeditor/editor-for-vue'
import { ref, shallowRef, watch, onBeforeUnmount } from 'vue'
import type { IEditorConfig } from '@wangeditor/editor'
import { userStore } from '@/stores'
import type { InsertFnType } from '@/models/editor'
import { ImagePath } from '@/hooks/image'
import Dialog from '@/components/MaterialSelect/dialog.vue'
import FileManageDialog from '@/components/MaterialSelect/dialog.vue'

const props = defineProps<{
  modelValue: any
}>()
const emit = defineEmits(['update:modelValue'])

const fileMangeDialogRef = ref<InstanceType<typeof FileManageDialog>>()
const selectPath = ref<string[]>([])
// 确认插入图片

// 编辑器实例，必须用 shallowRef
const editorRef = shallowRef()

// 内容 HTML
const valueHtml = ref('')
// 监听传参，回显到界面
watch(
  () => props.modelValue,
  (val) => {
    valueHtml.value = val
  },
  {
    immediate: true
  }
)
const confirmHandle = (data: any) => {
  selectPath.value = selectPath.value.concat(data) // 合并数组
}
// 配置工具栏
const toolbarConfig = {
  toolbarKeys: [
    'headerSelect',
    'blockquote',
    'bold',
    'underline',
    'italic',
    {
      key: 'group-more-style',
      title: '更多',
      iconSvg:
        '<svg viewBox="0 0 1024 1024"><path d="M204.8 505.6m-76.8 0a76.8 76.8 0 1 0 153.6 0 76.8 76.8 0 1 0-153.6 0Z"></path><path d="M505.6 505.6m-76.8 0a76.8 76.8 0 1 0 153.6 0 76.8 76.8 0 1 0-153.6 0Z"></path><path d="M806.4 505.6m-76.8 0a76.8 76.8 0 1 0 153.6 0 76.8 76.8 0 1 0-153.6 0Z"></path></svg>',
      menuKeys: ['through', 'code', 'sup', 'sub']
    },
    'color',
    'bgColor',
    'fontSize',
    'fontFamily',
    'todo',
    {
      key: 'group-justify',
      title: '对齐',
      iconSvg:
        '<svg viewBox="0 0 1024 1024"><path d="M768 793.6v102.4H51.2v-102.4h716.8z m204.8-230.4v102.4H51.2v-102.4h921.6z m-204.8-230.4v102.4H51.2v-102.4h716.8zM972.8 102.4v102.4H51.2V102.4h921.6z"></path></svg>',
      menuKeys: ['justifyLeft', 'justifyRight', 'justifyCenter', 'justifyJustify']
    },
    {
      key: 'group-indent',
      title: '缩进',
      iconSvg:
        '<svg viewBox="0 0 1024 1024"><path d="M0 64h1024v128H0z m384 192h640v128H384z m0 192h640v128H384z m0 192h640v128H384zM0 832h1024v128H0z m0-128V320l256 192z"></path></svg>',
      menuKeys: ['indent', 'delIndent']
    },
    'uploadImage',
    'divider'
  ]
}

// 配置编辑器
const editorConfig: Partial<IEditorConfig> = {
  placeholder: '请输入内容...',
  // MENU_CONF 属性
  MENU_CONF: {
    // 图片上传属性
    uploadImage: {
      fieldName: 'file', // 上传的文件字段名称
      server: '/api/admin/materials', // api接口
      headers: {
        Authorization: 'Bearer ' + userStore.getToken() // token
      },
      allowedFileTypes: ['image/*'], // 允许上传的类型

      async customInsert(res: any, insertFn: InsertFnType) {
        const path = await ImagePath(res.path)
        // TS 语法
        // res 即服务端的返回结果
        // 从 res 中找到 url alt href ，然后插入图片
        insertFn(path)
      }

      // customBrowseAndUpload(insertFn: InsertFnType) {
      //   fileMangeDialogRef.value?.openDialog()
      // }
    }
  }
}
//
// // 富文本校验
// const onblur: any = inject('onblur')
// const onEditorBlur = () => {
//   onblur()
// }
// 组件销毁时，也及时销毁编辑器
onBeforeUnmount(() => {
  const editor = editorRef.value
  if (editor == null) return
  editor.destroy()
})

// 组件创建时赋值给实例
const handleCreated = (editor: any) => {
  editorRef.value = editor // 记录 editor 实例，重要！
}
</script>

<template>
  <div style="border: 1px solid #ccc">
    <Toolbar
      style="border-bottom: 1px solid #ccc"
      :editor="editorRef"
      :defaultConfig="toolbarConfig"
      mode="simple"
    />
    <Editor
      style="height: 500px; overflow-y: hidden"
      v-model="valueHtml"
      :defaultConfig="editorConfig"
      @onCreated="handleCreated"
      @onChange="emit('update:modelValue', valueHtml)"
    />
    <!--   @onBlur="onEditorBlur" -->
  </div>
  <Dialog :max="5" ref="fileMangeDialogRef" @confirm="confirmHandle"></Dialog>
</template>

<style scoped lang="scss">
#editor—wrapper {
  border: 1px solid #ccc;
  z-index: 100; /* 按需定义 */
}
#toolbar-container {
  border-bottom: 1px solid #ccc;
}
#editor-container {
  height: 500px;
}
</style>
