import{u as U}from"./form-e5ba5cf1.js";import{A as h,b as F,a as B}from"./super-6f68a946.js";import{d as C,j as p,z as q,r as s,X as R,Y as T,g as l,o as d,a as m,w as n,i as t,b as j,e as z,F as E}from"./.pnpm-9b0f0a98.js";import{_ as I}from"./index-b643f0a0.js";const M=C({__name:"create-or-look",setup(N){const c=p(),{formData:a,formLoading:b}=U({formRef:c,defVal(e){return{username:e==null?void 0:e.username,password:e==null?void 0:e.password,agent_id:e==null?void 0:e.agent_id,level_id:e==null?void 0:e.level_id,parent_id:e==null?void 0:e.parent_id,type:2}}}),w={username:[{required:!0,message:"请输入手机号码",trigger:"blur"}],password:[{required:!0,message:"请输入密码",trigger:"blur"}]},i=p(),y=async()=>{i.value=await h()},k=p(),V=async()=>{k.value=await F()},f=p(),L=async()=>{f.value=await B()};return q(()=>{y(),V(),L()}),(e,r)=>{const v=s("el-input"),u=s("el-form-item"),_=s("el-option"),g=s("el-select"),x=s("el-tree"),A=s("el-form"),D=R("loading");return T((d(),m(A,{model:l(a),rules:w,ref_key:"formRef",ref:c,"label-width":"120px"},{default:n(()=>[t(u,{prop:"username",label:"手机号码"},{default:n(()=>[t(v,{modelValue:l(a).username,"onUpdate:modelValue":r[0]||(r[0]=o=>l(a).username=o),style:{width:"350px"}},null,8,["modelValue"])]),_:1}),t(u,{prop:"password",label:"密码"},{default:n(()=>[t(v,{modelValue:l(a).password,"onUpdate:modelValue":r[1]||(r[1]=o=>l(a).password=o),style:{width:"350px"}},null,8,["modelValue"])]),_:1}),t(u,{prop:"agent_id",label:"套餐"},{default:n(()=>[t(g,{modelValue:l(a).agent_id,"onUpdate:modelValue":r[2]||(r[2]=o=>l(a).agent_id=o),clearable:"",placeholder:"请选择"},{default:n(()=>[(d(!0),j(E,null,z(i.value,o=>(d(),m(_,{key:o.id,label:o.name,value:o.id},null,8,["label","value"]))),128))]),_:1},8,["modelValue"])]),_:1}),t(u,{prop:"parent_id",label:"上级"},{default:n(()=>[t(g,{modelValue:l(a).parent_id,"onUpdate:modelValue":r[3]||(r[3]=o=>l(a).parent_id=o),clearable:"",style:{width:"80%"},"popper-class":"tree-select",placeholder:"请选择"},{default:n(()=>[t(_,{key:"0",value:"0",label:"顶级"}),t(x,{data:f.value,"node-key":"id","auto-expand-parent":"","default-checked-keys":[l(a).parent_id],"expand-on-click-node":!1},{default:n(({data:o})=>[(d(),m(_,{style:{width:"100%"},key:o.id,value:o.id,label:o.label},null,8,["value","label"]))]),_:1},8,["data","default-checked-keys"])]),_:1},8,["modelValue"])]),_:1})]),_:1},8,["model"])),[[D,l(b)]])}}});const H=I(M,[["__scopeId","data-v-51117c23"]]);export{H as default};