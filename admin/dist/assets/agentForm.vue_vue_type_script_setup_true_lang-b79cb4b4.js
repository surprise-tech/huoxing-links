import{u as q}from"./index-06117f19.js";import{u as L}from"./form-e5ba5cf1.js";import{a as R}from"./super-1e5ee443.js";import{d as S,j as y,z as j,r,X as z,o as m,b as x,i as n,w as a,f as c,t as p,g as e,F as w,e as A,aq as E,Y as M,a as d,h as X,l as Y}from"./.pnpm-9b0f0a98.js";const G={class:"text-main"},W=S({__name:"agentForm",setup(H){const v=y(),{formData:l,formLoading:I}=L({formRef:v,defVal(o){return{parent_id:(o==null?void 0:o.parent_id)||void 0,link_amount:(o==null?void 0:o.link_amount)||0,yuanma_amount:(o==null?void 0:o.yuanma_amount)||0,dy_card_amount:(o==null?void 0:o.dy_card_amount)||0,status:o==null?void 0:o.status,getParent:(o==null?void 0:o.getParent)||[],username:o==null?void 0:o.username}}}),s=q,b=y();let V=y(!1);const h=async()=>{V.value||(V.value=!0,b.value=await R())};return j(()=>{h()}),(o,u)=>{const f=r("el-col"),D=r("el-row"),k=r("el-divider"),g=r("el-breadcrumb-item"),U=r("el-breadcrumb"),B=r("el-option"),C=r("el-tree"),F=r("el-select"),_=r("el-form-item"),i=r("el-input"),N=r("el-switch"),P=r("el-form"),T=z("loading");return m(),x(w,null,[n(D,{gutter:10,class:"m-b-20 text-main",style:{"margin-top":"-20px"}},{default:a(()=>[n(f,{xs:24,sm:8},{default:a(()=>[c("div",null,"链接剩余个数："+p(e(s).userInfo&&e(s).userInfo.link_amount),1)]),_:1}),n(f,{xs:24,sm:8},{default:a(()=>[c("div",null,"小圆码剩余个数："+p(e(s).userInfo&&e(s).userInfo.yuanma_amount),1)]),_:1}),n(f,{xs:24,sm:8},{default:a(()=>[c("div",null,"自动回复剩余个数："+p(e(s).userInfo&&e(s).userInfo.dy_card_amount),1)]),_:1})]),_:1}),n(k),n(U,{"separator-icon":e(E)},{default:a(()=>[n(g,null,{default:a(()=>[c("div",G,p(e(l).username),1)]),_:1}),(m(!0),x(w,null,A(e(l).getParent,t=>(m(),d(g,{key:t.id},{default:a(()=>[Y(p(t.username),1)]),_:2},1024))),128))]),_:1},8,["separator-icon"]),n(k),M((m(),d(P,{model:e(l),ref_key:"formRef",ref:v,"label-position":"top"},{default:a(()=>[e(s).userInfo.type===3?(m(),d(_,{key:0,label:"父级"},{default:a(()=>[n(F,{modelValue:e(l).parent_id,"onUpdate:modelValue":u[0]||(u[0]=t=>e(l).parent_id=t),clearable:"",style:{width:"100%"},"popper-class":"tree-select",placeholder:"请选择"},{default:a(()=>[n(C,{data:b.value,"node-key":"id","auto-expand-parent":"","default-checked-keys":[e(l).parent_id],"expand-on-click-node":!1},{default:a(({data:t})=>[(m(),d(B,{style:{width:"100%"},key:t.id,value:t.id,label:t.label},null,8,["value","label"]))]),_:1},8,["data","default-checked-keys"])]),_:1},8,["modelValue"])]),_:1})):X("",!0),n(_,{prop:"name",label:"链接数量"},{default:a(()=>[n(i,{type:"number",modelValue:e(l).link_amount,"onUpdate:modelValue":u[1]||(u[1]=t=>e(l).link_amount=t),placeholder:"请输入链接数量"},null,8,["modelValue"])]),_:1}),n(_,{prop:"app_id",label:"小圆码数量"},{default:a(()=>[n(i,{type:"number",modelValue:e(l).yuanma_amount,"onUpdate:modelValue":u[2]||(u[2]=t=>e(l).yuanma_amount=t),placeholder:"请输入小圆码数量"},null,8,["modelValue"])]),_:1}),n(_,{prop:"secret",label:"抖音自动回复卡片数量"},{default:a(()=>[n(i,{type:"number",modelValue:e(l).dy_card_amount,"onUpdate:modelValue":u[3]||(u[3]=t=>e(l).dy_card_amount=t),placeholder:"请输入抖音自动回复卡片数量"},null,8,["modelValue"])]),_:1}),n(_,{label:"账号状态"},{default:a(()=>[n(N,{modelValue:e(l).status,"onUpdate:modelValue":u[4]||(u[4]=t=>e(l).status=t),"active-value":!0,"inactive-value":!1,"active-text":"开启","inactive-text":"禁用","inline-prompt":""},null,8,["modelValue"])]),_:1})]),_:1},8,["model"])),[[T,e(I)]])],64)}}});export{W as _};
