<script setup lang="ts">
import { configStore } from '@/stores'
import { data_get } from '@/utils'
import PayButton from '@/views/home/components/PayButton.vue'
</script>

<template>
  <table class="equity-table">
    <thead>
      <tr>
        <th>权益/版本</th>
        <th :class="`th-${index + 1}`" v-for="(item, index) in configStore.package" :key="index">
          <div class="margin-b-5">{{ item.name }}</div>
          <div>￥{{ item.price }}/月</div>
        </th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td class="td-1 padding-l-20">每月访问数量（UV）上限</td>
        <td :class="`td-${index + 2}`" v-for="(item, index) in configStore.package" :key="index">
          {{ data_get(item, 'config.uv_limit') }}
        </td>
      </tr>
      <tr>
        <td class="td-1 padding-l-20">链接创建数量上限（同时）</td>
        <td :class="`td-${index + 2}`" v-for="(item, index) in configStore.package" :key="index">
          {{ data_get(item, 'config.count_limit') }}条
        </td>
      </tr>
      <tr>
        <td class="td-1 padding-l-20">自建小程序池可创建数量</td>
        <td :class="`td-${index + 2}`" v-for="(item, index) in configStore.package" :key="index">
          {{ data_get(item, 'config.min_count_limit') }}/个
        </td>
      </tr>
      <tr>
        <td class="td-1 padding-l-20">链接保留时长</td>
        <td :class="`td-${index + 2}`" v-for="(item, index) in configStore.package" :key="item.id">
          永久
        </td>
      </tr>
      <tr>
        <td class="td-1 padding-l-20">支持的类型</td>
        <td :class="`td-${index + 2}`" v-for="(item, index) in configStore.package" :key="index">
          <div v-if="data_get(item, 'config.allow_type.CLI_QR')">草料二维码</div>
          <div v-if="data_get(item, 'config.allow_type.KING_DOC')">金山文档</div>
          <div v-if="data_get(item, 'config.allow_type.WORK_WECHAT')">企业微信</div>
          <div v-if="data_get(item, 'config.allow_type.LANDING_MINI')">微信二维码</div>
          <div v-if="data_get(item, 'config.allow_type.MINI_PROGRAM')">自定义小程序</div>
        </td>
      </tr>
      <tr>
        <td class="td-1 padding-l-20">官方小程序池使用</td>
        <td :class="`td-${index + 2}`" v-for="(item, index) in configStore.package" :key="index">
          <span v-if="data_get(item, 'config.pre_min')">支持</span>
          <span v-else class="red">不支持</span>
        </td>
      </tr>
      <tr>
        <td class="td-1 padding-l-20">数据统计/分析</td>
        <td :class="`td-${index + 2}`" v-for="(item, index) in configStore.package" :key="item.id">
          支持
        </td>
      </tr>
      <tr>
        <td class="td-1 padding-l-20">自定义落地页</td>
        <td :class="`td-${index + 2}`" v-for="(item, index) in configStore.package" :key="index">
          <span v-if="data_get(item, 'config.cur_index')">支持</span>
          <span v-else class="red">不支持</span>
        </td>
      </tr>
      <tr>
        <td class="td-1 padding-l-20">自建小程序池下架自动检测</td>
        <td :class="`td-${index + 2}`" v-for="(item, index) in configStore.package" :key="index">
          <span v-if="data_get(item, 'config.min_disabled_check')">支持</span>
          <span v-else class="red">不支持</span>
        </td>
      </tr>
      <tr>
        <td class="td-1 padding-l-20">免费技术协助</td>
        <td :class="`td-${index + 2}`" v-for="(item, index) in configStore.package" :key="item.id">
          支持
        </td>
      </tr>
      <tr>
        <td class="td-1"></td>
        <td :class="`td-${index + 2}`" v-for="(item, index) in configStore.package" :key="item.id">
          <PayButton v-if="item.price > 0" type="pay-vip" :info="item">
            <el-button type="primary">立即开通/续费</el-button>
          </PayButton>
          <el-button v-else>免费</el-button>
        </td>
      </tr>
    </tbody>
  </table>
</template>

<style scoped lang="scss">
table,
tr,
td,
th {
  border: 1px solid #eaebee;
  border-collapse: collapse;
}
.equity-table {
  width: 100%;
}
thead tr {
  height: 80px;
  font-size: 16px;
  .th-1 {
    background-color: #d7e9ff;
  }
  .th-2 {
    background-color: #ffebcc;
  }
  .th-3 {
    background-color: #ffe6e6;
  }
}

tbody tr {
  td {
    padding-top: 8px;
    padding-bottom: 8px;
  }
  font-size: 16px;
  text-align: center;
  .td-1 {
    width: 260px;
    text-align: left;
  }
  .td-2 {
    background-color: #f9fcff;
  }
  .td-3 {
    background-color: #fffdf8;
  }
  .td-4 {
    background-color: #fff9fa;
  }
}
.red {
  color: #ff0000;
}
</style>
