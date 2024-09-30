<template>
  <div class="cate-main">
    <el-tag key="0" @click="selectAll" class="tg">全部</el-tag>
    <el-tag v-for="item in materialCate" :key="item.id" @click="selectCate(item.id)" class="tg">
      {{ item.name }}
    </el-tag>
  </div>
</template>
<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { ApiMaterialCate } from '@/api/app'
const emits = defineEmits(['rsCate'])
const selectAll = () => {
  emits('rsCate', null)
}
const selectCate = (id: number) => {
  emits('rsCate', id)
}

const materialCate = ref()
const getMaterialCate = async () => {
  materialCate.value = await ApiMaterialCate()
}

onMounted(() => {
  getMaterialCate()
})
</script>

<style lang="scss" scoped>
.cate-main {
  margin-top: 15px;
}
.tg {
  margin-right: 8px;
  cursor: pointer;
}
</style>
