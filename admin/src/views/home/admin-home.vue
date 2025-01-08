<script setup lang="ts">
import { onMounted, ref } from 'vue'
import type { dataType, turnOverType } from '@/models/home'
import { ApiAllData } from '@/api/home'
import Echarts from '@/components/echart.vue'

// 创建数据概览
const dataValue = ref<dataType>()
// 创销售数据趋势
const useSalesData = ref<turnOverType>({
  title: '',
  legend: {
    data: ['用户']
  },
  tooltip: {
    trigger: 'axis'
  },
  grid: {
    show: false,
    top: '16%', // 一下数值可为百分比也可为具体像素值
    right: '2%',
    bottom: '10%',
    left: '8%'
  },
  xAxis: {
    type: 'category',
    boundaryGap: false, // 坐标轴两端空白策略
    data: []
  },
  yAxis: {
    name: '单位',
    nameTextStyle: {
      align: 'right'
    }
  },
  series: [
    {
      name: '用户',
      type: 'line',
      smooth: true,
      color: '#5470c6',
      areaStyle: {
        color: {
          type: 'linear',
          x: 0,
          y: 0,
          x2: 0,
          y2: 1,
          colorStops: [
            {
              offset: 0,
              color: '#89a6dc' // 100% 处的颜色
            },
            {
              offset: 1,
              color: '#fff' //   0% 处的颜色
            }
          ],
          global: false // 缺省为 false
        }
      },
      data: []
    }
  ]
})

const getHomeData = async (type: any) => {
  const res: any = await ApiAllData(type)
  // 数据概览
  dataValue.value = {
    member_count: res.member_count,
    agent_count: res.agent_count,
    user_count: res.user_count,
    pay_vip: res.pay_vip,
    pay_agent: res.pay_agent,
    pay_count: res.pay_count,
  }
  // 用户增长趋势
  if (res.user_growth.length > 0) {
    useSalesData.value.xAxis.data = res.user_growth.map((item: any) => {
      return item.date
    })
  }
  useSalesData.value.series[0].data = res.user_growth.map((item: any) => {
    return item.member_count
  })
}

// 创建标签页
const dateNum = ref(1)
// 切换标签页
const toggleDate = async (num: any) => {
  dateNum.value = num
  await getHomeData(num)
}

onMounted(() => {
  getHomeData(dateNum.value)
})
</script>

<template>
  <el-card class="margin-b-20">
    <div class="title-box margin-b-10">
      <h3>数据概览</h3>
    </div>
    <div class="tab-box">
      <!--   用户数   -->
      <div class="item-box">
        <div class="item-box-l">
          <span class="name">用户数</span>
          <div>
            <span class="value">{{ dataValue?.user_count  || 0}}</span>
            <span>人</span>
          </div>
        </div>
        <img src="@/assets/admin-home/use-count.png" alt="" />
      </div>
      <!--   会员数   -->
      <div class="item-box vip-count-bg">
        <div class="item-box-l">
          <span class="name">支付会员数</span>
          <div>
            <span class="value">{{ dataValue?.pay_count || 0 }}</span>
            <span>人</span>
          </div>
        </div>
        <img src="@/assets/admin-home/use-count.png" alt="" />
      </div>
      <!--   会员收入   -->
      <div class="item-box vip-inp-bg">
        <div class="item-box-l">
          <span class="name">购买会员收入</span>
          <div>
            <span class="value">{{ dataValue?.pay_vip }}</span>
            <span>元</span>
          </div>
        </div>
        <img src="@/assets/admin-home/use-count.png" alt="" />
      </div>
    </div>
  </el-card>
  <el-card>
    <!-- 用户增长趋势 -->
    <div class="sale_trend">
      <div class="sale_trend_header">
        <div class="header">
          <h3>用户增长趋势</h3>
        </div>
        <div class="sale_trend_date">
          <el-tabs>
            <el-button
              style="border-radius: 40px"
              @click="toggleDate(1)"
              :type="dateNum === 1 ? 'primary' : 'info'"
            >
              近30日
            </el-button>
            <el-button
              style="border-radius: 40px"
              @click="toggleDate(2)"
              :type="dateNum === 2 ? 'primary' : 'info'"
            >
              近1年
            </el-button>
          </el-tabs>
        </div>
      </div>
      <div class="sale_trend_echarts">
        <echarts v-if="useSalesData.series[0].data.length > 0" height="400" :data-value="useSalesData" />
        <el-empty v-else></el-empty>
      </div>
    </div>
  </el-card>
</template>

<style scoped lang="scss">
.title-box {
  display: flex;
  align-items: center;
}

.title-box::before {
  margin-right: 10px;
  content: '';
  display: inline-block;
  width: 3px;
  height: 1em;
  background-color: var(--el-color-primary);
}

.tab-box {
  display: flex;
  justify-content: center;
  flex-wrap: wrap;
}

.item-box {
  padding: 30px;
  display: flex;
  flex: 1;
  margin: 5px;
  justify-content: space-between;
  align-items: center;
  font-size: 16px;
  min-width: 220px;
  height: 100px;
  background-color: #fff8f8;

  .item-box-l {
    display: flex;
    flex-direction: column;
    line-height: 2em;
    color: #999;

    .value {
      font-size: 24px;
      font-weight: 700;
      color: #333;
    }
  }
}

.vip-count-bg {
  background-color: #fffbf0;
}

.agent-count-bg {
  background-color: #fff8fb;
}

.vip-inp-bg {
  background-color: #f7fff5;
}

.agent-inp-bg {
  background-color: #f8fbff;
}

// 用户增长趋势
.sale_trend {
  width: 100%;

  .sale_trend_header {
    display: flex;
    justify-content: space-between;
    align-items: center;

    .header {
      display: flex;
      align-items: center;
    }
  }

  .sale_trend_echarts {
    width: 100%;
    height: 400px;
    border-radius: 5px;
    background-color: #fff;
  }
}

.sale_trend_header .header::before {
  margin-right: 10px;
  content: '';
  display: inline-block;
  width: 3px;
  height: 1em;
  background-color: var(--el-color-primary);
}
</style>
