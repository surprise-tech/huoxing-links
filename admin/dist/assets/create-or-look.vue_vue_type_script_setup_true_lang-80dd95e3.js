import{u as w}from"./form-e5ba5cf1.js";import{c as h}from"./super-6f68a946.js";import{d as D,j as d,z as L,r as s,X as Y,Y as q,g as r,o as p,a as f,w as m,i as n,b as U,e as B,F as C,l as F}from"./.pnpm-9b0f0a98.js";const j=D({__name:"create-or-look",setup(M){const a=d(),{formData:t,formLoading:c}=w({formRef:a,defVal(e){return{number:e==null?void 0:e.number,month:e==null?void 0:e.month,expiry_time:e==null?void 0:e.expiry_time,vip_id:e==null?void 0:e.vip_id}}}),i=d(),b=async()=>{i.value=await h()},g={number:[{required:!0,message:"请输入数量",trigger:"blur"}],month:[{required:!0,message:"请输入时长",trigger:"blur"}],vip_id:[{required:!0,message:"请选择类型",trigger:"blur"}],expiry_time:[{required:!0,message:"请选择类型",trigger:"blur"}]};return L(()=>{b()}),(e,l)=>{const _=s("el-input"),u=s("el-form-item"),v=s("el-option"),V=s("el-select"),x=s("el-date-picker"),y=s("el-form"),k=Y("loading");return q((p(),f(y,{model:r(t),rules:g,ref_key:"formRef",ref:a,"label-width":"120px"},{default:m(()=>[n(u,{prop:"number",label:"生成数量"},{default:m(()=>[n(_,{modelValue:r(t).number,"onUpdate:modelValue":l[0]||(l[0]=o=>r(t).number=o),style:{width:"350px"}},null,8,["modelValue"])]),_:1}),n(u,{prop:"vip_id",label:"会员类型"},{default:m(()=>[n(V,{modelValue:r(t).vip_id,"onUpdate:modelValue":l[1]||(l[1]=o=>r(t).vip_id=o),style:{width:"350px"}},{default:m(()=>[(p(!0),U(C,null,B(i.value,o=>(p(),f(v,{key:o.id,label:o.name,value:o.id},null,8,["label","value"]))),128))]),_:1},8,["modelValue"])]),_:1}),n(u,{prop:"month",label:"会员时长"},{default:m(()=>[n(_,{modelValue:r(t).month,"onUpdate:modelValue":l[2]||(l[2]=o=>r(t).month=o),style:{width:"350px"}},{suffix:m(()=>[F("个月")]),_:1},8,["modelValue"])]),_:1}),n(u,{prop:"expiry_time",label:"有效期"},{default:m(()=>[n(x,{modelValue:r(t).expiry_time,"onUpdate:modelValue":l[3]||(l[3]=o=>r(t).expiry_time=o),type:"datetime","value-format":"YYYY-MM-DD HH:mm:ss"},null,8,["modelValue"])]),_:1})]),_:1},8,["model"])),[[k,r(c)]])}}});export{j as _};