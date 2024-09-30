<template>
  <el-icon class="svg-icon">
    <component :is="props.icon"></component>
  </el-icon>
</template>

<script setup lang="ts">
defineOptions({
  name: 'SvgIcon'
})
const props = defineProps({
  icon: {
    type: String,
    required: true
  }
})
</script>

<style scoped>
.svg-icon svg {
  width: 1em;
  height: 1em;
  vertical-align: -0.15em;
  fill: currentColor;
  overflow: hidden;
  flex-shrink: 0;
}
</style>
