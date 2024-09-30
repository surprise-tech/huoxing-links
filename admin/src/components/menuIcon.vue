<script setup lang="ts">
// 提供name属性即可
defineProps<{
  name: string
  size?: number
}>()
</script>

<template>
  <svg
    aria-hidden="true"
    :style="{
      height: size ? `${size}px` : '1.5em',
      width: size ? `${size}px` : '1.5em'
    }"
  >
    <use :href="`#icon-${name}`" />
  </svg>
</template>

<style lang="scss" scoped>
.cp-icon {
  // 和字体一样大
  width: 1.5em;
  height: 1.5em;
}
</style>
