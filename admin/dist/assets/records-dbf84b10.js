import{u as k}from"./curd-590e7fca.js";import{d as z,r as o,X as V,o as i,a as p,w as l,f as s,i as t,g as e,l as F,Y as D,t as _,v as S}from"./.pnpm-9b0f0a98.js";import{_ as B}from"./index-31481636.js";const N={class:"margin-b-20"},q=z({__name:"records",setup(H){const{state:n,sizeChangeHandle:d,currentChangeHandle:u,research:m}=k({url:"/amount-record"});return(I,c)=>{const g=o("el-input"),y=o("el-button"),b=o("el-space"),r=o("el-table-column"),f=o("el-empty"),h=o("el-table"),C=o("el-pagination"),x=o("el-card"),v=V("loading");return i(),p(x,null,{default:l(()=>[s("div",N,[t(b,null,{default:l(()=>[t(g,{modelValue:e(n).queryForm.username,"onUpdate:modelValue":c[0]||(c[0]=a=>e(n).queryForm.username=a),placeholder:"请输入手机号查询"},null,8,["modelValue"]),t(y,{type:"primary",onClick:e(m)},{default:l(()=>[F("查询")]),_:1},8,["onClick"])]),_:1})]),D((i(),p(h,{data:e(n).dataList,"header-cell-style":{"text-align":"center","background-color":"#F1F1F1",height:"50px",color:"#333"},"cell-style":{padding:"0","text-align":"center",height:"50px",color:"#333"},"row-key":e(n).primaryKey},{empty:l(()=>[t(f)]),default:l(()=>[t(r,{prop:"id",label:"ID",width:"60"}),t(r,{prop:"user.username",label:"手机号"}),t(r,{label:"数量"},{default:l(({row:a})=>[s("div",{class:S(["text",[a.direction==1?"text-green":"text-red"]])},_(a.direction==1?"+ "+a.amount:"- "+a.amount),3)]),_:1}),t(r,{label:"类型"},{default:l(({row:a})=>[s("div",null,_(a.type==="yuanma_amount"?"小圆码":a.type==="link_amount"?"链接":a.type==="dy_card_amount"?"抖音自动回复卡片":""),1)]),_:1}),t(r,{prop:"remark",label:"备注"}),t(r,{prop:"created_at",label:"时间"})]),_:1},8,["data","row-key"])),[[v,e(n).listLoading]]),t(C,{"current-page":e(n).page,total:e(n).total,"page-sizes":e(n).pageSizes,"page-size":e(n).limit,background:"",layout:"total, sizes, prev, pager, next",onSizeChange:e(d),onCurrentChange:e(u)},null,8,["current-page","total","page-sizes","page-size","onSizeChange","onCurrentChange"])]),_:1})}}});const T=B(q,[["__scopeId","data-v-2dfb79a6"]]);export{T as default};
