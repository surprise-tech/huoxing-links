import{U as C,M}from"./MaterialSelect-829aaccc.js";import{u as O}from"./form-e5ba5cf1.js";import{A as q}from"./domain-19077ac3.js";import{O as V,u as A,_ as F}from"./index-31481636.js";import{d as z,j as v,z as E,r as i,X as N,o as u,b,f as a,i as l,w as r,t as p,g as e,Y as R,a as k,F as w,e as X,A as Y,B as G}from"./.pnpm-9b0f0a98.js";import"./propData-3c6625bc.js";import"./curd-590e7fca.js";import"./cate-d48a821d.js";import"./app-959aa9a6.js";const _=d=>(Y("data-v-5463c8a1"),d=d(),G(),d),H={style:{"margin-top":"-30px"},class:"m-b-20"},J=_(()=>a("span",null,"消耗个数：1个",-1)),K={class:"main"},P={class:"item-box"},Q={class:"function-img"},T=_(()=>a("span",{class:"tip"},"只能上传jpg/png，建议大小100*100，且不超过1Mb",-1)),W={class:"show hidden-md-and-down"},Z=_(()=>a("span",null,"抖音卡片预览",-1)),$={class:"show-box"},ee={class:"show-des"},oe={class:"show-des-t"},se={class:"show-des-b"},le=z({__name:"create-or-look",setup(d){const g=v(),{formData:o,formLoading:x}=O({formRef:g,defVal(s){return{title:s==null?void 0:s.title,type:"4",description:s==null?void 0:s.description,icon:s==null?void 0:s.icon,remark:s==null?void 0:s.remark,config:s!=null&&s.config?s==null?void 0:s.config:{}}}}),f=v(),y=async()=>{f.value=await q()},U={title:[{required:!0,message:"请输入标题",trigger:"blur"}],"config.domain_id":[{required:!0,message:"请选择域名",trigger:"blur"}],"config.url":[{required:!0,message:"请输入链接",trigger:"blur"}]},I=A;return E(()=>{y()}),(s,t)=>{const L=i("el-space"),c=i("el-input"),m=i("el-form-item"),h=i("el-divider"),D=i("el-option"),S=i("el-select"),B=i("el-form"),j=N("loading");return u(),b(w,null,[a("div",H,[l(L,{class:"text-main"},{default:r(()=>[a("span",null,"总个数："+p(e(I).userInfo.link_amount)+"个",1),J]),_:1})]),a("div",K,[R((u(),k(B,{model:e(o),rules:U,ref_key:"formRef",ref:g,class:"form-box margin-right-40","label-position":"top"},{default:r(()=>[l(m,{prop:"title",label:"卡片标题"},{default:r(()=>[l(c,{modelValue:e(o).title,"onUpdate:modelValue":t[0]||(t[0]=n=>e(o).title=n),placeholder:"请输入卡片标题"},null,8,["modelValue"])]),_:1}),l(m,{label:"卡片描述"},{default:r(()=>[l(c,{modelValue:e(o).description,"onUpdate:modelValue":t[1]||(t[1]=n=>e(o).description=n),placeholder:"请输入卡片描述"},null,8,["modelValue"])]),_:1}),l(m,{label:"卡片封面图"},{default:r(()=>[a("div",P,[l(V,{class:"margin-right-20",paths:e(o).icon?[e(o).icon]:[],width:100,height:100},null,8,["paths"]),a("div",null,[a("div",Q,[l(C,{class:"margin-right-10",type:["image/jpg","image/jpeg","image/png"],size:1,modelValue:e(o).icon,"onUpdate:modelValue":t[2]||(t[2]=n=>e(o).icon=n)},null,8,["modelValue"]),l(M,{modelValue:e(o).icon,"onUpdate:modelValue":t[3]||(t[3]=n=>e(o).icon=n),class:"margin-right-20"},null,8,["modelValue"])]),T])])]),_:1}),l(h),l(m,{label:"卡片备注"},{default:r(()=>[l(c,{modelValue:e(o).remark,"onUpdate:modelValue":t[4]||(t[4]=n=>e(o).remark=n),placeholder:"请输入卡片备注，例如：xxx活动（选填）"},null,8,["modelValue"])]),_:1}),l(m,{prop:"config.domain_id",label:"无风险域名"},{default:r(()=>[l(S,{modelValue:e(o).config.domain_id,"onUpdate:modelValue":t[5]||(t[5]=n=>e(o).config.domain_id=n),placeholder:"请选择"},{default:r(()=>[(u(!0),b(w,null,X(f.value,n=>(u(),k(D,{key:n.id,label:n.title,value:n.id},null,8,["label","value"]))),128))]),_:1},8,["modelValue"])]),_:1}),l(m,{prop:"config.url",label:"企业微信客服链接或者获客链接"},{default:r(()=>[l(c,{modelValue:e(o).config.url,"onUpdate:modelValue":t[6]||(t[6]=n=>e(o).config.url=n)},null,8,["modelValue"])]),_:1})]),_:1},8,["model"])),[[j,e(x)]]),a("div",W,[l(h,null,{default:r(()=>[Z]),_:1}),a("div",$,[l(V,{class:"margin-right-20",paths:e(o).icon?[e(o).icon]:[],width:100,height:100},null,8,["paths"]),a("div",ee,[a("span",oe,p(e(o).title||"卡片标题"),1),a("span",se,p(e(o).description||"卡片描述"),1)])])])])],64)}}});const pe=F(le,[["__scopeId","data-v-5463c8a1"]]);export{pe as default};
