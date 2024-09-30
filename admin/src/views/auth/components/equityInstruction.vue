<script setup lang="ts">
import { configStore } from '@/stores'
import { data_get, data_get_length } from '@/utils'
import usePropData from '@/hooks/propData'

const emits = defineEmits(['update:visible'])
const props = defineProps<{
  visible: boolean
}>()

const visible = usePropData(() => props.visible, false)
</script>

<template>
  <div>
    <el-dialog class="equity-dialog" v-model="visible" title="权益说明" @close="emits('update:visible', false)">
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
            <td class="td-1 padding-l-20">有UV限制链接价格</td>
            <td :class="`td-${index + 2}`" v-for="(item, index) in configStore.package" :key="index">
              {{ data_get(item, 'config.uv_limit_price') }}/条
            </td>
          </tr>
          <tr>
            <td class="td-1 padding-l-20">无UV限制链接价格</td>
            <td :class="`td-${index + 2}`" v-for="(item, index) in configStore.package" :key="index">
              {{ data_get(item, 'config.no_uv_limit_price') }}/条
            </td>
          </tr>
          <tr>
            <td class="td-1 padding-l-20">UV价格</td>
            <td :class="`td-${index + 2}`" v-for="(item, index) in configStore.package" :key="index">
              {{ data_get(item, 'config.uv_price') }}/个
            </td>
          </tr>
          <tr>
            <td class="td-1 padding-l-20">自建小程序池可创建数量</td>
            <td :class="`td-${index + 2}`" v-for="(item, index) in configStore.package" :key="index">
              {{ data_get(item, 'config.min_count_limit') }}个
            </td>
          </tr>
          <tr>
            <td class="td-1 padding-l-20">链接保留时长</td>
            <td :class="`td-${index + 2}`" v-for="(item, index) in configStore.package" :key="index">
              <span>永久</span>
            </td>
          </tr>
          <tr>
            <td class="td-1 padding-l-20">所有类型链接创建</td>
            <td :class="`td-${index + 2}`" v-for="(item, index) in configStore.package" :key="index">
              <span v-if="data_get_length(item, 'config.allow_type')">支持</span>
              <span v-else class="red">不支持</span>
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
            <td :class="`td-${index + 2}`" v-for="(item, index) in configStore.package" :key="index">
              <span>支持</span>
            </td>
          </tr>
          <tr>
            <td class="td-1 padding-l-20">短链接可创建数量</td>
            <td :class="`td-${index + 2}`" v-for="(item, index) in configStore.package" :key="index">5个</td>
          </tr>
          <tr>
            <td class="td-1 padding-l-20">自定义落地页</td>
            <td :class="`td-${index + 2}`" v-for="(item, index) in configStore.package" :key="item.id">
              <span v-if="data_get(item, 'config.cur_index')">支持</span>
              <span v-else class="red">不支持</span>
            </td>
          </tr>
          <tr>
            <td class="td-1 padding-l-20">自建小程序池下架自动检测</td>
            <td :class="`td-${index + 2}`" v-for="(item, index) in configStore.package" :key="item.id">
              <span v-if="data_get(item, 'config.min_disabled_check')">支持</span>
              <span v-else class="red">不支持</span>
            </td>
          </tr>
          <tr>
            <td class="td-1 padding-l-20">免费技术协助</td>
            <td :class="`td-${index + 2}`" v-for="(item, index) in configStore.package" :key="item.id">
              <span v-if="data_get(item, 'config.support')">支持</span>
              <span v-else class="red">不支持</span>
            </td>
          </tr>
        </tbody>
      </table>
    </el-dialog>
  </div>
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
  height: 50px;
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

:deep(.el-drawer__title) {
  font-weight: 700;
}

@media screen and (max-width: 1200px) {
  :deep(.equity-dialog) {
    width: 80%;
  }
}

@media screen and (max-width: 700px) {
  :deep(.equity-dialog) {
    width: 100%;
  }
}
</style>
