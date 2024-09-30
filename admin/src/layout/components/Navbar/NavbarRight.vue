<template>
  <div class="navbar-right">
    <User />
  </div>
</template>

<script setup lang="ts">
import User from './User.vue'
</script>

<style lang="scss" scoped>
.navbar-right {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  &-link {
    height: 100%;
    display: flex;
    align-items: center;
    white-space: nowrap;
    &-photo {
      width: 25px;
      height: 25px;
      border-radius: 100%;
    }
  }
}
</style>
