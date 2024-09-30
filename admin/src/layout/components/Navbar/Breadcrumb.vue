<template>
  <el-breadcrumb separator="/" separator-icon="el-icon-arrow-right" class="navbar-breadcrumb">
    <el-breadcrumb-item v-for="(item, index) in breadcrumb" :key="index">
      <span>{{ item }}</span>
    </el-breadcrumb-item>
  </el-breadcrumb>
</template>

<script setup lang="ts">
import { useRoute } from 'vue-router'
import { computed } from 'vue'

const route = useRoute()
const breadcrumb = computed(() => route.meta.breadcrumb)
</script>

<style lang="scss" scoped>
.navbar-breadcrumb {
  ::v-deep(.el-breadcrumb__inner) {
    color: var(--theme-header-text-color) !important;
  }
  padding-left: 10px;
}
</style>
