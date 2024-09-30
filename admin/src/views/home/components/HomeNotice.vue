<script setup lang="ts">
defineProps<{
  notices: any
}>()
</script>

<template>
  <div class="tip margin-b-10" :key="notice.id" v-for="notice in notices">
    <span class="iconfont icon-gong-gao icon-style"></span>
    <p>公告：{{ notice.content }}</p>
  </div>
</template>

<style scoped lang="scss"></style>
