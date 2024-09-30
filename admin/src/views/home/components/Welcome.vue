<script setup lang="ts">
import { userStore } from '@/stores'
</script>

<template>
  <div class="welcome">
    <div class="title margin-b-10">欢迎使用{{ userStore.userInfo.site_name }}系统</div>
    <p>您好，{{ userStore.userInfo.username }}，欢迎回来！</p>
  </div>
</template>

<style scoped lang="scss">
.welcome {
  padding: 0 20px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  height: 140px;
  border-radius: 5px;
  background: url('@/assets/home/left-t-bg.png');
  .title {
    font-size: 24px;
    font-weight: 700;
    color: #333333;
  }
  .time {
    font-size: 16px;
    color: #333333;
  }
}
</style>
