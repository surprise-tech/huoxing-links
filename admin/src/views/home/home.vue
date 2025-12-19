<script setup lang="ts">
import { nextTick, onMounted, ref } from 'vue'
import { ApiAllData } from '@/api/home'
import { userStore } from '@/stores'
import ChangePassword from '@/views/home/components/ChangePassword.vue'
import Welcome from '@/views/home/components/Welcome.vue'
import { data_get, setClipboard } from '@/utils'
import { getNotify } from '@/api/super'
// 获取概况数据
const dataList = ref<any>({})
const initFlag = ref(false)
const getData = () => {
  ApiAllData(1).then((res) => {
    dataList.value = res
    nextTick(() => {
      initFlag.value = true
    })
  })
}
// 复制邀请链接
const copyCode = () => {
  const root = window.location.origin
  const code = data_get(userStore.userInfo, 'referral_code', '')
  var path = window.location.pathname
  setClipboard(root + path + '#/register?code=' + code)
}

const notify = ref({} as any)
const noticeShow = ref(false)
onMounted(() => {
  userStore.refreshUserInfo()
  getData()
  getNotify().then((res) => {
    notify.value = res
    if (notify.value && notify.value.show) {
      noticeShow.value = true
    }
  })
})
</script>

<template>
  <div class="home_container" v-if="initFlag">
    <el-row :gutter="12">
      <el-col :xs="24" :sm="24">
        <el-row :gutter="10">
          <el-col :xs="22"><Welcome /></el-col>
        </el-row>
        <el-row :gutter="10" style="margin-top: 10px">
          <el-col :xs="24">
            <div class="used-info bgw-box">
              <div class="right-h margin-b-20">
                <img class="margin-right-5" src="@/assets/home/vip-icon.png" alt="" />
                <span class="title">套餐使用情况</span>
              </div>
              <el-row :gutter="10">
                <el-col :xs="24" :sm="12">
                  <div class="home-card" style="background-color: #f6fafe; --num-color: #2b6bff">
                    <div class="home-card-title">链接访问UV</div>
                    <div class="home-card-warp">
                      <div class="home-card-item">
                        <div class="home-card-num">{{ dataList.used_uv ?? 0 }}</div>
                        <div class="home-num-title">总UV次数</div>
                      </div>
                      <div class="home-card-item">
                        <div class="home-card-num">
                          {{ dataList.month_uv ?? 0 }}
                        </div>
                        <div class="home-num-title">本月UV次数</div>
                      </div>
                    </div>
                  </div>
                </el-col>
                <el-col :xs="24" :sm="12">
                  <div class="home-card" style="background-color: #fffaf4; --num-color: #ff832b">
                    <div class="home-card-title">已创建数量</div>
                    <div class="home-card-warp">
                      <div class="home-card-item">
                        <div class="home-card-num"> {{ data_get(userStore.userInfo, 'link_created', 0) }}</div>
                        <div class="home-num-title">卡片数量</div>
                      </div>
                      <div class="home-card-item">
                        <div class="home-card-num">
                          {{ data_get(userStore.userInfo, 'link_expire', 0) }}
                        </div>
                        <div class="home-num-title">到期卡片</div>
                      </div>
                    </div>
                  </div>
                </el-col>
              </el-row>
            </div>
          </el-col>
        </el-row>
      </el-col>
    </el-row>

    <el-row :gutter="10">
      <el-col :xs="24" :sm="12">
        <ChangePassword />
      </el-col>
      <el-col :xs="24" :sm="12">
        <div class="bgw-box" style="min-height: 166px">
          <div class="right-h">
            <img class="margin-right-5" src="@/assets/home/vip-icon.png" alt="" />
            <span class="title">邀请注册</span>
          </div>
          <div class="m-t-20">邀请好友注册，可得高比例佣金提成，赶紧分享给朋友们注册吧</div>
          <div class="m-t-20">
            <el-button class="btn" type="primary" @click="copyCode"
              >邀请码：{{ data_get(userStore.userInfo, 'referral_code', '') }}， 点击复制邀请链接
            </el-button>
          </div>
        </div>
      </el-col>
    </el-row>

    <el-dialog :title="notify.title" :show-close="true" :width="700" append-to-body v-model="noticeShow">
      <div class="notify">
        <div class="notify" v-html="notify.content"></div>
      </div>
      <div style="text-align: center" class="m-t-20">
        <el-button type="primary" @click="noticeShow = false">确定</el-button>
      </div>
    </el-dialog>

    <el-dialog :title="notify.title" :show-close="true" :width="700" append-to-body v-model="noticeShow">
      <div class="notify">
        <div class="notify" v-html="notify.content"></div>
      </div>
      <div style="text-align: center; " class="m-t-20">
        <el-button type="primary" @click="noticeShow=false">确定</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<style lang="scss" scoped>
.successCard {
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  align-items: center;
  img {
    width: 150px;
    height: 130px;
  }
}
.used-info {
  margin-bottom: 10px;
}
.vip-info {
  height: calc(100% - 10px);
  .right-m {
    font-size: 16px;
    line-height: 24px;
    color: #333333;
  }
  .right-f {
    font-size: 16px;
  }
  .right-s {
    font-size: 16px;
  }
  .btn {
    width: 100%;
  }
}
</style>
