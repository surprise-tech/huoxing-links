import{O as d,u as i,_ as V}from"./index-b643f0a0.js";import{U as c,M as p}from"./MaterialSelect-19b6ad9a.js";import{a as x}from"./newAgent-7b7a2a33.js";import{d as y,U as b,y as q,r as l,o as I,a as U,w as n,i as t,f as o,l as j,E as M,A as S,B}from"./.pnpm-9b0f0a98.js";import"./propData-3c6625bc.js";import"./curd-e3316c4c.js";import"./cate-0ead127d.js";import"./app-0d4c35f9.js";const m=r=>(S("data-v-332e69fc"),r=r(),B(),r),C=m(()=>o("div",{class:"m-b-20"},"上传LOGO图片信息",-1)),O={class:"item-box"},k={class:"function-img m-b-20"},E=m(()=>o("span",{class:"tip"},"只能上传jpg/png，建议大小100*100，且不超过1Mb",-1)),N=m(()=>o("div",{class:"m-b-20"},"上传微信二维码",-1)),z={class:"item-box"},A={class:"function-img m-b-20"},L=m(()=>o("span",{class:"tip"},"只能上传jpg/png，建议大小100*100，且不超过1Mb",-1)),D=m(()=>o("div",{class:"margin-b-10",style:{"margin-top":"40px"}},"网站标题",-1)),G={style:{"margin-top":"100px"}},T={style:{"margin-top":"60px"},class:"text-center"},F=y({__name:"info",setup(r){const e=b({logo:"",wechat_qr:"",site_name:""});q(()=>i.userInfo,()=>{e.logo=i.userInfo.logo||"",e.wechat_qr=i.userInfo.wechat_qr||"",e.site_name=i.userInfo.site_name||""},{immediate:!0,deep:!0});const g=()=>{x({logo:e.logo,wechat_qr:e.wechat_qr,site_name:e.site_name}).then(()=>{i.refreshUserInfo(),M.success("保存成功")})};return(H,s)=>{const _=l("el-col"),u=l("el-input"),h=l("el-row"),f=l("el-divider"),v=l("el-button"),w=l("el-card");return I(),U(w,{style:{"min-height":"calc(100vh - 150px)"}},{default:n(()=>[t(h,{gutter:10},{default:n(()=>[t(_,{xs:24,sm:12},{default:n(()=>[C,o("div",O,[t(d,{class:"margin-right-20",paths:e.logo?[e.logo]:[],width:100,height:100},null,8,["paths"]),o("div",null,[o("div",k,[t(c,{class:"margin-right-10",type:["image/jpg","image/jpeg","image/png"],size:1,modelValue:e.logo,"onUpdate:modelValue":s[0]||(s[0]=a=>e.logo=a)},null,8,["modelValue"]),t(p,{modelValue:e.logo,"onUpdate:modelValue":s[1]||(s[1]=a=>e.logo=a),class:"margin-right-20"},null,8,["modelValue"])]),E])])]),_:1}),t(_,{xs:24,sm:12},{default:n(()=>[N,o("div",z,[t(d,{class:"margin-right-20",paths:e.wechat_qr?[e.wechat_qr]:[],width:100,height:100},null,8,["paths"]),o("div",null,[o("div",A,[t(c,{class:"margin-right-10",type:["image/jpg","image/jpeg","image/png"],size:1,modelValue:e.wechat_qr,"onUpdate:modelValue":s[2]||(s[2]=a=>e.wechat_qr=a)},null,8,["modelValue"]),t(p,{modelValue:e.wechat_qr,"onUpdate:modelValue":s[3]||(s[3]=a=>e.wechat_qr=a),class:"margin-right-20"},null,8,["modelValue"])]),L])])]),_:1}),t(_,{xs:24,sm:12},{default:n(()=>[D,t(u,{modelValue:e.site_name,"onUpdate:modelValue":s[4]||(s[4]=a=>e.site_name=a),placeholder:"请输入网站标题"},null,8,["modelValue"])]),_:1})]),_:1}),o("div",G,[t(f)]),o("div",T,[t(v,{type:"primary",style:{width:"200px"},onClick:g},{default:n(()=>[j("保存")]),_:1})])]),_:1})}}});const Z=V(F,[["__scopeId","data-v-332e69fc"]]);export{Z as default};
