import{u as C}from"./form-e5ba5cf1.js";import{c as N,b as M,a as T}from"./super-bbf9dff0.js";import{d as H,j as m,z as R,r as p,X as j,Y as z,g as o,o as n,a as d,w as r,i as s,b as y,e as b,F as g,f as E,ar as I,l as X,h as q}from"./.pnpm-9b0f0a98.js";import{_ as G}from"./index-2667bde9.js";const J={class:"ptip"},K=H({__name:"user-action",props:{formFeild:{}},setup(O){const f=m(),{formData:a,formLoading:L}=C({formRef:f,defVal(e){var t,u;return{credit:0,end_at:e==null?void 0:e.end_at,vip_id:(t=e==null?void 0:e.vip_package)==null?void 0:t.id,level_id:e==null?void 0:e.level_id,parent_id:(u=e==null?void 0:e.parent)==null?void 0:u.id,user_id:e==null?void 0:e.id}}});console.log("formData:",a.value);const v=m(),w=async()=>{v.value=await N()},k=m(),F=async()=>{k.value=await M()},V=m(),D=async()=>{V.value=await T()},U={};return R(()=>{w(),F(),D()}),(e,t)=>{const u=p("el-input"),i=p("el-form-item"),A=p("el-date-picker"),_=p("el-option"),c=p("el-select"),Y=p("el-icon"),h=p("el-tree"),x=p("el-form"),B=j("loading");return z((n(),d(x,{model:o(a),rules:U,ref_key:"formRef",ref:f,"label-width":"120px"},{default:r(()=>[e.formFeild=="credit"?(n(),d(i,{key:0,prop:"credit",label:"积分"},{default:r(()=>[s(u,{modelValue:o(a).credit,"onUpdate:modelValue":t[0]||(t[0]=l=>o(a).credit=l),placeholder:"请输入积分"},null,8,["modelValue"])]),_:1})):e.formFeild=="end_at"?(n(),d(i,{key:1,prop:"end_at",label:"到期时间"},{default:r(()=>[s(A,{modelValue:o(a).end_at,"onUpdate:modelValue":t[1]||(t[1]=l=>o(a).end_at=l),type:"datetime","value-format":"YYYY-MM-DD HH:mm:ss"},null,8,["modelValue"])]),_:1})):e.formFeild=="vip_id"?(n(),d(i,{key:2,prop:"vip_id",label:"套餐"},{default:r(()=>[s(c,{modelValue:o(a).vip_id,"onUpdate:modelValue":t[2]||(t[2]=l=>o(a).vip_id=l),placeholder:"请选择"},{default:r(()=>[(n(!0),y(g,null,b(v.value,l=>(n(),d(_,{key:l.id,label:l.name,value:l.id},null,8,["label","value"]))),128))]),_:1},8,["modelValue"]),E("div",J,[s(Y,null,{default:r(()=>[s(o(I))]),_:1}),X(" 修改套餐后需要修改过期时间！ ")])]),_:1})):e.formFeild=="level_id"?(n(),d(i,{key:3,prop:"vip_id",label:"代理级别"},{default:r(()=>[s(c,{modelValue:o(a).level_id,"onUpdate:modelValue":t[3]||(t[3]=l=>o(a).level_id=l),placeholder:"请选择"},{default:r(()=>[(n(!0),y(g,null,b(k.value,l=>(n(),d(_,{key:l.id,label:l.name,value:l.id},null,8,["label","value"]))),128))]),_:1},8,["modelValue"])]),_:1})):e.formFeild=="parent_id"?(n(),d(i,{key:4,prop:"parent_id",label:"上级"},{default:r(()=>[s(c,{modelValue:o(a).parent_id,"onUpdate:modelValue":t[4]||(t[4]=l=>o(a).parent_id=l),clearable:"",style:{width:"80%"},"popper-class":"tree-select",placeholder:"请选择"},{default:r(()=>[s(_,{key:"0",value:"0",label:"顶级"}),s(h,{data:V.value,"node-key":"id","auto-expand-parent":"","default-checked-keys":[o(a).parent_id],"expand-on-click-node":!1},{default:r(({data:l})=>[(n(),d(_,{style:{width:"100%"},key:l.id,value:l.id,label:l.label},null,8,["value","label"]))]),_:1},8,["data","default-checked-keys"])]),_:1},8,["modelValue"])]),_:1})):q("",!0)]),_:1},8,["model"])),[[B,o(L)]])}}});const Z=G(K,[["__scopeId","data-v-a9be9cf5"]]);export{Z as default};
