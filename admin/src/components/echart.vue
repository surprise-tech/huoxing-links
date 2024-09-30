<script setup lang="ts">
import { onMounted, ref, watch } from 'vue'
import echarts from '@/utils/echarts'

const props = defineProps<{
  dataValue: any
  height: string
}>()
const chartHeight = ref(props.height + 'px')
const chart = ref()
const chartCard = ref()
let myChart: any = null
// echarts自适应宽度
const resizeObserver = new ResizeObserver((entries) => {
  for (let entry of entries) {
    if (entry.contentBoxSize) {
      myChart.resize()
    }
  }
})

onMounted(() => {
  myChart = echarts.init(chart.value)
  resizeObserver.observe(chartCard.value)
})

function init() {
  if (!myChart) {
    setTimeout(init, 60)
    return
  }
  const option = props.dataValue
  console.log('option:', option)
  myChart.setOption(option)
}

watch(
  () => props.dataValue,
  () => {
    init()
  },
  {
    deep: true,
    immediate: true
  }
)
</script>

<template>
  <div ref="chartCard">
    <div ref="chart" class="chart_box"></div>
  </div>
</template>

<style scoped lang="scss">
.chart_box {
  height: v-bind(chartHeight);
}
</style>
