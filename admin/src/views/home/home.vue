<script setup lang="ts">
import { nextTick, onMounted, ref } from 'vue'
import { ApiAllData } from '@/api/home'
import { userStore } from '@/stores'
import ChangePassword from '@/views/home/components/ChangePassword.vue'
import Welcome from '@/views/home/components/Welcome.vue'
import HomeNotice from '@/views/home/components/HomeNotice.vue'
import { data_get, setClipboard } from '@/utils'
import { ElMessage } from 'element-plus'
import { ApiConsumeCard } from '@/api/user'
import BuyVip from '@/views/home/components/BuyVip.vue'
import WithdrawForm from '@/views/home/components/WithdrawForm.vue'
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
// 卡密兑换
const consumeCardVisible = ref(false)
const success = ref(false)
const card_token = ref('')
const submitLoading = ref(false)
const clickConsumeCard = () => {
  if (card_token.value) {
    ApiConsumeCard(card_token.value).then(() => {
      ElMessage.success('兑换成功！')
      window.location.reload()
    })
  } else {
    ElMessage.error('请输入卡密码')
  }
}
// 提现
const withdrawVisible = ref(false)
// 开通会员
const buyVipVisible = ref(false)
const tiyan = () => {
  success.value = false
  localStorage.removeItem('register')
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
  if (localStorage.getItem('register')) {
    success.value = true
  }
})
</script>

<template>
  <div class="home_container" v-if="initFlag">
    <!--  公告  -->
    <!--    <HomeNotice :notices="dataList.notices" />-->

    <el-row :gutter="10">
      <el-col :xs="24" :sm="16">
        <el-row :gutter="10">
          <el-col :xs="24"><Welcome /></el-col>
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
                    <div class="home-card-title">本月链接访问UV</div>
                    <div class="home-card-warp">
                      <div class="home-card-item">
                        <div class="home-card-num">{{ dataList.used_uv ?? 0 }}</div>
                        <div class="home-num-title">已使用UV次数</div>
                      </div>
                      <div class="home-card-item">
                        <div class="home-card-num">
                          {{ data_get(userStore.userInfo, 'vip_package.config.uv_limit', 0) }}
                        </div>
                        <div class="home-num-title">会员套餐UV次数</div>
                      </div>
                      <div class="home-card-item">
                        <div class="home-card-num">
                          {{ data_get(userStore.userInfo, 'vip_package.config.uv_limit', 0) - (dataList.used_uv ?? 0) }}
                        </div>
                        <div class="home-num-title">剩余可访问UV次数</div>
                      </div>
                    </div>
                  </div>
                </el-col>
                <el-col :xs="24" :sm="12">
                  <div class="home-card" style="background-color: #fffaf4; --num-color: #ff832b">
                    <div class="home-card-title">已创建数量</div>
                    <div class="home-card-warp">
                      <div class="home-card-item">
                        <div class="home-card-num">{{ dataList.link_count ?? 0 }}</div>
                        <div class="home-num-title">链接</div>
                      </div>
                      <div class="home-card-item">
                        <div class="home-card-num">
                          {{ dataList.yuanma_amount ?? 0 }}
                        </div>
                        <div class="home-num-title">小圆码</div>
                      </div>
                      <div class="home-card-item">
                        <div class="home-card-num">
                          {{ dataList.dy_card_amount ?? 0 }}
                        </div>
                        <div class="home-num-title">自动回复</div>
                      </div>
                    </div>
                  </div>
                </el-col>
              </el-row>
            </div>
          </el-col>
        </el-row>
      </el-col>
      <el-col :xs="24" :sm="8">
        <div class="vip-info bgw-box">
          <div class="right-h margin-b-20">
            <img class="margin-right-5" src="@/assets/home/vip-icon.png" alt="" />
            <span class="title">会员信息</span>
          </div>
          <div class="right-m margin-b-20">
            {{
              userStore.userInfo.site_name
            }}作为一款私域引流工具，凭借其强大的功能和灵活的应用场景，成为了推广营销领域的热门选择，它支持从抖音直接跳转到个人微信、微信群、小程序以及企业微信客服，有效打通了不同平台之间的壁垒，实现了流量的高效转化和利用，推广营销必备！
          </div>
          <div class="right-f margin-b-20">
            <span class="margin-right-10">引流平台功能：</span>
            <img class="margin-right-10" src="@/assets/home/q-wx.png" alt="" />
            <img class="margin-right-10" src="@/assets/home/wx.png" alt="" />
          </div>
          <div class="right-s margin-b-20">
            <span class="margin-right-10">当前会员套餐：</span>
            <span>{{ data_get(userStore.userInfo, 'vip_package.name', '未开通会员') }}</span>
          </div>
          <div class="right-s margin-b-20">
            <span class="margin-right-10">会员到期日期：</span>
            <span>{{ userStore.userInfo.end_at }}</span>
          </div>
          <el-row :gutter="10">
            <el-col :xs="12" :sm="12">
              <el-button class="btn" type="primary" @click="buyVipVisible = true">开通会员/续费会员</el-button>
              <el-dialog title="开通会员" width="1000px" append-to-body v-model="buyVipVisible">
                <BuyVip />
              </el-dialog>
            </el-col>
            <el-col :xs="12" :sm="12">
              <el-button class="btn" type="primary" @click="consumeCardVisible = true">卡密兑换</el-button>
              <el-dialog title="卡密兑换" :width="500" append-to-body v-model="consumeCardVisible">
                <div style="display: flex; padding-bottom: 20px" v-loading="submitLoading">
                  <el-input v-model="card_token" placeholder="请输入卡密" style="flex: 1px; margin-right: 10px" />
                  <el-button type="primary" @click="clickConsumeCard" :loading="submitLoading">立即兑换</el-button>
                </div>
              </el-dialog>
            </el-col>
          </el-row>
        </div>
      </el-col>
    </el-row>

    <el-row :gutter="10">
      <el-col :xs="24" :sm="8">
        <ChangePassword />
      </el-col>
      <el-col :xs="24" :sm="8">
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
      <el-col :xs="24" :sm="8">
        <div class="bgw-box">
          <div class="right-h margin-b-20">
            <img class="margin-right-5" src="@/assets/home/vip-icon.png" alt="" />
            <span class="title" style="flex: 1">我的佣金</span>
            <el-button size="small" type="primary" @click="withdrawVisible = true">立即提现</el-button>
            <el-dialog v-model="withdrawVisible" title="提现" width="400px">
              <WithdrawForm />
            </el-dialog>
          </div>
          <div style="background-color: #fffaf4; text-align: center; padding: 10px 0" class="flex row-around">
            <div>
              <div style="font-size: 14px; padding-bottom: 10px">未提现佣金</div>
              <div style="font-size: 24px; color: #ff832b">
                {{ data_get(userStore.userInfo, 'commission', 0) }}
              </div>
            </div>
            <div>
              <div style="font-size: 14px; padding-bottom: 10px">已提现</div>
              <div style="font-size: 24px; color: #ff832b">
                {{ data_get(userStore.userInfo, 'accumulate_commission', 0) }}
              </div>
            </div>
          </div>
        </div>
      </el-col>
    </el-row>
    <el-row class="m-t-10">
      <el-col :span="24">
        <div class="bgw-box">
          <div class="right-h margin-b-20">
            <img class="margin-right-5" src="@/assets/home/vip-icon.png" alt="" />
            <span class="title">会员订单记录</span>
          </div>
          <el-table
            height="249"
            :data="dataList.vip_logs"
            :header-cell-style="{
              'text-align': 'center',
              'background-color': '#F1F1F1',
              height: '50px',
              color: '#333'
            }"
            :cell-style="{ padding: '0', 'text-align': 'center', height: '50px', color: '#333' }"
          >
            <el-table-column prop="vip_package.name" label="类型"></el-table-column>
            <el-table-column prop="amount" label="金额"></el-table-column>
            <el-table-column prop="id" label="支付方式">
              <template v-slot:default>微信</template>
            </el-table-column>
            <el-table-column prop="start_at" label="生效时间"></el-table-column>
            <el-table-column prop="end_at" label="到期时间"></el-table-column>
          </el-table>
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

    <el-dialog title="" :show-close="false" :width="500" append-to-body v-model="success">
      <div class="successCard">
        <div style="flex: 1">
          <h2 class="margin-b-10">注册成功</h2>
          <h3>恭喜您获得1天体验会员，100uv访问量</h3>
        </div>
        <div style="text-align: center">
          <img class="margin-b-20" src="@/assets/home/success.png" alt="" />
        </div>
      </div>
      <div style="text-align: right; margin-right: 30px">
        <el-button type="primary" @click="tiyan">立即体验</el-button>
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
