import{c as x}from"./super-7aeb24f0.js";import{m as w,c as h}from"./index-65d2ca96.js";import{d as k,j as p,y as U,z as C,r as m,o as _,a as r,w as d,f as i,i as a,b as B,e as S,F as E,l as N,E as z}from"./.pnpm-0d809032.js";const A={style:{width:"500px"}},F=i("div",{class:"margin-b-10",style:{"margin-top":"20px"}},"是否开启赠送会员",-1),L=i("div",{class:"margin-b-10",style:{"margin-top":"20px"}},"注册赠送会员",-1),M=i("div",{class:"margin-b-10",style:{"margin-top":"20px"}},"注册赠送天数",-1),j=i("div",{class:"margin-b-10",style:{"margin-top":"20px"}},"一级邀请注册佣金",-1),D=i("div",{class:"margin-b-10",style:{"margin-top":"20px"}},"二级邀请注册佣金",-1),P=i("div",{class:"margin-b-10",style:{"margin-top":"20px"}},"一级分销（%）",-1),T=i("div",{class:"margin-b-10",style:{"margin-top":"20px"}},"二级分销（%）",-1),q={class:"m-b-20 text-center",style:{"margin-top":"60px"}},J=k({__name:"sales",props:{data:{}},setup(v){const s=v,e=p({is_give_vip:!1,give_vip_id:void 0,give_vip_days:0,recommend_commission_1:0,recommend_commission_2:0,member_pay_commission_1:0,member_pay_commission_2:0});U(()=>s.data,()=>{e.value.is_give_vip=s.data.is_give_vip,e.value.give_vip_id=s.data.give_vip_id,e.value.give_vip_days=s.data.give_vip_days,e.value.recommend_commission_1=s.data.recommend_commission_1,e.value.recommend_commission_2=s.data.recommend_commission_2,e.value.member_pay_commission_1=s.data.member_pay_commission_1,e.value.member_pay_commission_2=s.data.member_pay_commission_2},{deep:!0,immediate:!0});const n=p([]),c=()=>{w(e.value).then(()=>{z.success("保存成功"),h.refreshConfig()})};return C(()=>{x().then(u=>{n.value=n.value.concat(u)})}),(u,l)=>{const g=m("el-switch"),y=m("el-option"),b=m("el-select"),t=m("el-input-number"),V=m("el-button"),f=m("el-card");return _(),r(f,null,{default:d(()=>[i("div",A,[i("div",null,[F,a(g,{style:{width:"100%"},modelValue:e.value.is_give_vip,"onUpdate:modelValue":l[0]||(l[0]=o=>e.value.is_give_vip=o)},null,8,["modelValue"])]),i("div",null,[L,a(b,{modelValue:e.value.give_vip_id,"onUpdate:modelValue":l[1]||(l[1]=o=>e.value.give_vip_id=o),placeholder:"注册赠送会员",style:{width:"100%"}},{default:d(()=>[(_(!0),B(E,null,S(n.value,o=>(_(),r(y,{key:o.id,label:o.name,value:o.id},null,8,["label","value"]))),128))]),_:1},8,["modelValue"])]),i("div",null,[M,a(t,{style:{width:"100%"},modelValue:e.value.give_vip_days,"onUpdate:modelValue":l[2]||(l[2]=o=>e.value.give_vip_days=o),min:1,max:365},null,8,["modelValue"])]),i("div",null,[j,a(t,{style:{width:"100%"},modelValue:e.value.recommend_commission_1,"onUpdate:modelValue":l[3]||(l[3]=o=>e.value.recommend_commission_1=o),min:0},null,8,["modelValue"])]),i("div",null,[D,a(t,{style:{width:"100%"},modelValue:e.value.recommend_commission_2,"onUpdate:modelValue":l[4]||(l[4]=o=>e.value.recommend_commission_2=o),min:0},null,8,["modelValue"])]),i("div",null,[P,a(t,{style:{width:"100%"},modelValue:e.value.member_pay_commission_1,"onUpdate:modelValue":l[5]||(l[5]=o=>e.value.member_pay_commission_1=o)},null,8,["modelValue"])]),i("div",null,[T,a(t,{style:{width:"100%"},modelValue:e.value.member_pay_commission_2,"onUpdate:modelValue":l[6]||(l[6]=o=>e.value.member_pay_commission_2=o)},null,8,["modelValue"])]),i("div",q,[a(V,{type:"primary",size:"large",onClick:c},{default:d(()=>[N("保存")]),_:1})])])]),_:1})}}});export{J as _};
