import{u as w}from"./form-3c660157.js";import{c as q}from"./super-7177c5c9.js";import{d as D,r as d,b as L,ag as s,aq as M,J as U,u as t,o as p,H as f,I as m,Q as n,c as Y,a5 as h,P as B,M as C}from"./@vue-d872a633.js";const A=D({__name:"create-or-look",setup(H){const a=d(),{formData:l,formLoading:c}=w({formRef:a,defVal(e){return{number:e==null?void 0:e.number,month:e==null?void 0:e.month,expiry_time:e==null?void 0:e.expiry_time,vip_id:e==null?void 0:e.vip_id}}}),i=d(),b=async()=>{i.value=await q()},g={number:[{required:!0,message:"请输入数量",trigger:"blur"}],month:[{required:!0,message:"请输入时长",trigger:"blur"}],vip_id:[{required:!0,message:"请选择类型",trigger:"blur"}],expiry_time:[{required:!0,message:"请选择类型",trigger:"blur"}]};return L(()=>{b()}),(e,r)=>{const _=s("el-input"),u=s("el-form-item"),v=s("el-option"),V=s("el-select"),x=s("el-date-picker"),y=s("el-form"),k=M("loading");return U((p(),f(y,{model:t(l),rules:g,ref_key:"formRef",ref:a,"label-width":"120px"},{default:m(()=>[n(u,{prop:"number",label:"生成数量"},{default:m(()=>[n(_,{modelValue:t(l).number,"onUpdate:modelValue":r[0]||(r[0]=o=>t(l).number=o),style:{width:"350px"}},null,8,["modelValue"])]),_:1}),n(u,{prop:"vip_id",label:"会员类型"},{default:m(()=>[n(V,{modelValue:t(l).vip_id,"onUpdate:modelValue":r[1]||(r[1]=o=>t(l).vip_id=o),style:{width:"350px"}},{default:m(()=>[(p(!0),Y(B,null,h(i.value,o=>(p(),f(v,{key:o.id,label:o.name,value:o.id},null,8,["label","value"]))),128))]),_:1},8,["modelValue"])]),_:1}),n(u,{prop:"month",label:"会员时长"},{default:m(()=>[n(_,{modelValue:t(l).month,"onUpdate:modelValue":r[2]||(r[2]=o=>t(l).month=o),style:{width:"350px"}},{suffix:m(()=>r[4]||(r[4]=[C("个月")])),_:1},8,["modelValue"])]),_:1}),n(u,{prop:"expiry_time",label:"有效期"},{default:m(()=>[n(x,{modelValue:t(l).expiry_time,"onUpdate:modelValue":r[3]||(r[3]=o=>t(l).expiry_time=o),type:"datetime","value-format":"YYYY-MM-DD HH:mm:ss"},null,8,["modelValue"])]),_:1})]),_:1},8,["model"])),[[k,t(c)]])}}});export{A as _};
