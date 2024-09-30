<script setup lang="ts">
import { ElMessage, type FormInstance, type FormRules } from 'element-plus'
import '@wangeditor/editor/dist/css/style.css'; // 引入 css
import { ref,shallowRef,onBeforeUnmount } from 'vue'
import { Editor, Toolbar } from '@wangeditor/editor-for-vue';
import { userStore } from '@/stores'
import { ImagePath } from '@/hooks/image'
import { editNotify, getNotify } from '@/api/super'

const  formData=ref({
  title:'',
  content:'',
  show:false
})

// 编辑器实例，必须用 shallowRef
const editorRef = shallowRef();

const toolbarConfig = {};
const editorConfig = {
  placeholder: '请输入内容...',
  MENU_CONF: {
    uploadImage: {
      fieldName: 'image',
      server: `/api/material-upload`,
      allowedFileTypes: ['image/*'],
      headers: {
        Authorization: `Bearer ${userStore.getToken()}`,
      },
      // 上传错误，或者触发 timeout 超时
      onError(file: File) {
        // JS 语法
        ElMessage.error(`${file.name} 上传出错`);
      },
      customInsert(res: any, insertFn: any) {
        if (res && res.path) {
          insertFn(ImagePath(res.path), '', '');
          ElMessage.success('上传成功');
        }
      },
    },
  },
};

// 组件销毁时，也及时销毁编辑器
onBeforeUnmount(() => {
  const editor = editorRef.value;
  if (editor == null) return;
  editor.destroy();
});

const handleCreated = (editor:any) => {
  editorRef.value = editor; // 记录 editor 实例，重要！
};
getNotify().then(res=>{
  formData.value=res as any
})

const  save=()=>{
  if(!formData.value.title){
    ElMessage.error('请输入标题')
    return
  }
  editNotify(formData.value).then(res=>{
    ElMessage.success('保存成功')
  })
}
</script>

<template>
  <el-card>
    <el-form
      ref="formRef"
      label-width="120px"
    >
      <el-form-item label="标题">
        <el-input v-model="formData.title" style="width: 700px" placeholder="请输入标题"></el-input>
      </el-form-item>
      <el-form-item  label="内容">
        <div style="border: 1px solid #ccc;">
          <Toolbar
            style="border-bottom: 1px solid #ccc"
            :editor="editorRef"
            :default-config="toolbarConfig"
          />
          <Editor
            v-model="formData.content"
            style="height: 500px; overflow-y: hidden"
            :default-config="editorConfig"
            @on-created="handleCreated"
          />
        </div>
      </el-form-item>
      <el-form-item  label="是否弹窗显示">
      <el-switch v-model="formData.show"></el-switch>
      </el-form-item>
    </el-form>
    <div class="text-center m-t-20">
      <el-button type="primary" @click="save">保存</el-button>
    </div>
  </el-card>

</template>

<style scoped lang="scss">
.top_card {
  margin-bottom: 20px;
  height: 72px;
}
//.header {
//  display: flex;
//  justify-content: space-between;
//  .header-left {
//    flex: 1;
//  }
//}
.header-right {
  margin-bottom: 20px;
}
:deep(.el-form) {
  margin-bottom: 15px;
}
</style>
