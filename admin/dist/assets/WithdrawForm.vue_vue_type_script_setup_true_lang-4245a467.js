import{d as r,u as s}from"./index-501ba653.js";import{b as f}from"./pay-4554faf2.js";import{d as y,j as V,r as n,o as b,b as v,f as x,t as w,g as u,i as e,w as l,l as g,E as h}from"./.pnpm-9b0f0a98.js";const k={style:{"padding-bottom":"20px","text-align":"center"}},W=y({__name:"WithdrawForm",setup(C){const o=V({amount:0,payee:{name:"",account:""}}),d=()=>{f(o.value).then(()=>{h.success("提现申请成功"),window.location.reload()})};return(B,a)=>{const i=n("el-input-number"),m=n("el-form-item"),p=n("el-input"),c=n("el-button"),_=n("el-form");return b(),v("div",null,[x("div",k," 当前可提现金额："+w(u(r)(u(s).userInfo,"commission",0)),1),e(_,{model:o.value,"label-width":"100px"},{default:l(()=>[e(m,{label:"提现金额",prop:"amount"},{default:l(()=>[e(i,{modelValue:o.value.amount,"onUpdate:modelValue":a[0]||(a[0]=t=>o.value.amount=t),min:0,max:u(r)(u(s).userInfo,"commission",0),placeholder:"请输入提现金额"},null,8,["modelValue","max"])]),_:1}),e(m,{label:"支付宝姓名",prop:"payee.name"},{default:l(()=>[e(p,{modelValue:o.value.payee.name,"onUpdate:modelValue":a[1]||(a[1]=t=>o.value.payee.name=t),placeholder:"请输入真实的支付宝姓名"},null,8,["modelValue"])]),_:1}),e(m,{label:"支付宝账号",prop:"payee.account"},{default:l(()=>[e(p,{modelValue:o.value.payee.account,"onUpdate:modelValue":a[2]||(a[2]=t=>o.value.payee.account=t),placeholder:"请输入真实的支付宝账号"},null,8,["modelValue"])]),_:1}),e(m,null,{default:l(()=>[e(c,{type:"primary",onClick:d},{default:l(()=>[g("立即提现")]),_:1})]),_:1})]),_:1},8,["model"])])}}});export{W as _};
