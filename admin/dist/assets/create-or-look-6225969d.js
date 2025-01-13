import{U as q,M as B}from"./MaterialSelect-7c3644d3.js";import{u as C}from"./form-3c660157.js";import{A as M}from"./domain-0ccd45bf.js";import{O as f,u as j,_ as S}from"./index-1cb382aa.js";import{d as A,r as V,b as E,ag as m,aq as F,o as u,c as v,a as n,Q as l,I as s,O as d,u as o,J as N,H as b,P as h,a5 as R}from"./@vue-d872a633.js";import"./propData-8a0097e5.js";import"./curd-b17e16e8.js";import"./element-plus-baf202ae.js";import"./lodash-es-32f1e46b.js";import"./@element-plus-11e7d0b3.js";import"./@popperjs-c75af06c.js";import"./@ctrl-f8748455.js";import"./dayjs-f13f0179.js";import"./call-bind-apply-helpers-be8cb158.js";import"./function-bind-48f717dc.js";import"./es-errors-c75f5a96.js";import"./async-validator-dee29e8b.js";import"./memoize-one-297ddbcb.js";import"./normalize-wheel-es-ed76fb12.js";import"./@floating-ui-5ec34e26.js";import"./cate-f1806917.js";import"./app-62f4ec8e.js";import"./pinia-cb4be529.js";import"./vue-demi-71ba0ef2.js";import"./pinia-plugin-persistedstate-ed167f96.js";import"./axios-439bb627.js";import"./qs-6612e700.js";import"./side-channel-32a248f0.js";import"./object-inspect-110ff94a.js";import"./side-channel-list-1667605c.js";import"./side-channel-map-a9d6574a.js";import"./get-intrinsic-8d8987ad.js";import"./es-object-atoms-11ea1ab9.js";import"./math-intrinsics-9389030e.js";import"./gopd-30775706.js";import"./es-define-property-27915812.js";import"./has-symbols-76fb15e9.js";import"./get-proto-15e8f36b.js";import"./dunder-proto-2a4d8230.js";import"./hasown-31deb62f.js";import"./call-bound-dbfc98a1.js";import"./side-channel-weakmap-a7bef06b.js";import"./vue-router-154b9487.js";import"./@vueuse-9028f0e6.js";import"./nprogress-72cfee09.js";/* empty css                    */const z={style:{"margin-top":"-30px"},class:"m-b-20"},H={class:"main"},J={class:"item-box"},P={class:"function-img"},Q={class:"show hidden-md-and-down"},G={class:"show-box"},K={class:"show-des"},T={class:"show-des-t"},W={class:"show-des-b"},X=A({__name:"create-or-look",setup(Y){const c=V(),{formData:e,formLoading:k}=C({formRef:c,defVal(r){return{title:r==null?void 0:r.title,type:"4",description:r==null?void 0:r.description,icon:r==null?void 0:r.icon,remark:r==null?void 0:r.remark,config:r!=null&&r.config?r==null?void 0:r.config:{}}}}),_=V(),x=async()=>{_.value=await M()},w={title:[{required:!0,message:"请输入标题",trigger:"blur"}],"config.domain_id":[{required:!0,message:"请选择域名",trigger:"blur"}],"config.url":[{required:!0,message:"请输入链接",trigger:"blur"}]},y=j;return E(()=>{x()}),(r,t)=>{const U=m("el-space"),p=m("el-input"),a=m("el-form-item"),g=m("el-divider"),L=m("el-option"),D=m("el-select"),I=m("el-form"),O=F("loading");return u(),v(h,null,[n("div",z,[l(U,{class:"text-main"},{default:s(()=>[n("span",null,"总个数："+d(o(y).userInfo.link_amount)+"个",1),t[7]||(t[7]=n("span",null,"消耗个数：1个",-1))]),_:1})]),n("div",H,[N((u(),b(I,{model:o(e),rules:w,ref_key:"formRef",ref:c,class:"form-box margin-right-40","label-position":"top"},{default:s(()=>[l(a,{prop:"title",label:"卡片标题"},{default:s(()=>[l(p,{modelValue:o(e).title,"onUpdate:modelValue":t[0]||(t[0]=i=>o(e).title=i),placeholder:"请输入卡片标题"},null,8,["modelValue"])]),_:1}),l(a,{label:"卡片描述"},{default:s(()=>[l(p,{modelValue:o(e).description,"onUpdate:modelValue":t[1]||(t[1]=i=>o(e).description=i),placeholder:"请输入卡片描述"},null,8,["modelValue"])]),_:1}),l(a,{label:"卡片封面图"},{default:s(()=>[n("div",J,[l(f,{class:"margin-right-20",paths:o(e).icon?[o(e).icon]:[],width:100,height:100},null,8,["paths"]),n("div",null,[n("div",P,[l(q,{class:"margin-right-10",type:["image/jpg","image/jpeg","image/png"],size:1,modelValue:o(e).icon,"onUpdate:modelValue":t[2]||(t[2]=i=>o(e).icon=i)},null,8,["modelValue"]),l(B,{modelValue:o(e).icon,"onUpdate:modelValue":t[3]||(t[3]=i=>o(e).icon=i),class:"margin-right-20"},null,8,["modelValue"])]),t[8]||(t[8]=n("span",{class:"tip"},"只能上传jpg/png，建议大小100*100，且不超过1Mb",-1))])])]),_:1}),l(g),l(a,{label:"卡片备注"},{default:s(()=>[l(p,{modelValue:o(e).remark,"onUpdate:modelValue":t[4]||(t[4]=i=>o(e).remark=i),placeholder:"请输入卡片备注，例如：xxx活动（选填）"},null,8,["modelValue"])]),_:1}),l(a,{prop:"config.domain_id",label:"无风险域名"},{default:s(()=>[l(D,{modelValue:o(e).config.domain_id,"onUpdate:modelValue":t[5]||(t[5]=i=>o(e).config.domain_id=i),placeholder:"请选择"},{default:s(()=>[(u(!0),v(h,null,R(_.value,i=>(u(),b(L,{key:i.id,label:i.title,value:i.id},null,8,["label","value"]))),128))]),_:1},8,["modelValue"])]),_:1}),l(a,{prop:"config.url",label:"企业微信客服链接或者获客链接"},{default:s(()=>[l(p,{modelValue:o(e).config.url,"onUpdate:modelValue":t[6]||(t[6]=i=>o(e).config.url=i)},null,8,["modelValue"])]),_:1})]),_:1},8,["model"])),[[O,o(k)]]),n("div",Q,[l(g,null,{default:s(()=>t[9]||(t[9]=[n("span",null,"抖音卡片预览",-1)])),_:1}),n("div",G,[l(f,{class:"margin-right-20",paths:o(e).icon?[o(e).icon]:[],width:100,height:100},null,8,["paths"]),n("div",K,[n("span",T,d(o(e).title||"卡片标题"),1),n("span",W,d(o(e).description||"卡片描述"),1)])])])])],64)}}});const Qo=S(X,[["__scopeId","data-v-5463c8a1"]]);export{Qo as default};
