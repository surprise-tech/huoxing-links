import{u as X}from"./curd-fc6336b0.js";import{_ as B}from"./FormModal.vue_vue_type_script_setup_true_lang-b27420e1.js";import{l as N}from"./index-501ba653.js";import{d as Y,j as k,r as o,X as G,o as r,b as J,i as t,w as e,g as l,l as s,f as O,ah as Q,Y as R,a as c,h as u,ai as w,t as D,F as W,E as S}from"./.pnpm-9b0f0a98.js";import{_ as Z}from"./create.vue_vue_type_script_setup_true_lang-b2f4f162.js";import ee from"./user-action-561ed978.js";import"./propData-3c6625bc.js";import"./form-e5ba5cf1.js";import"./super-331b96f6.js";const te={class:"header-right",style:{"margin-bottom":"10px"}},ue=Y({__name:"index",setup(ae){const{state:i,showForm:$,submitForm:U,research:b,sizeChangeHandle:E,currentChangeHandle:q}=X({url:"/users",queryForm:{page:1,type:1}}),C=d=>{N(d.id,{status:!d.status}).then(()=>{S.success("操作成功！"),b()})},f=k(!1),x=k(),g=k(),v=k(),F=(d,n)=>{f.value=!0,x.value=d,g.value=n,n=="vip_id"?v.value="更改套餐":n=="end_at"?v.value="更改到期时间":n=="credit"&&(v.value="充值积分")},L=(d,n)=>{var _={};g.value=="vip_id"?_={vip_id:d.vip_id}:g.value=="end_at"?_={end_at:d.end_at}:g.value=="credit"&&(_={credit:d.credit}),N(n.id,_).then(()=>{S.success("操作成功！"),f.value=!1,b()})};return(d,n)=>{var z;const _=o("el-input"),A=o("el-form-item"),V=o("el-col"),m=o("el-button"),H=o("el-row"),P=o("el-form"),p=o("el-table-column"),y=o("el-tag"),T=o("el-empty"),j=o("el-table"),I=o("el-pagination"),K=o("el-card"),M=G("loading");return r(),J(W,null,[t(K,null,{default:e(()=>[t(P,null,{default:e(()=>[t(H,{gutter:20},{default:e(()=>[t(V,{span:4},{default:e(()=>[t(A,null,{default:e(()=>[t(_,{modelValue:l(i).queryForm.username,"onUpdate:modelValue":n[0]||(n[0]=a=>l(i).queryForm.username=a),placeholder:"请输入手机号",style:{width:"260px"},clearable:""},null,8,["modelValue"])]),_:1})]),_:1}),t(V,{span:4},{default:e(()=>[t(m,{type:"primary",onClick:l(b)},{default:e(()=>[s("查询")]),_:1},8,["onClick"])]),_:1})]),_:1})]),_:1}),O("div",te,[t(m,{type:"primary",icon:l(Q),onClick:l($)},{default:e(()=>[s("新增")]),_:1},8,["icon","onClick"])]),R((r(),c(j,{data:l(i).dataList,"header-cell-style":{"text-align":"center","background-color":"#F1F1F1",height:"50px",color:"#333"},"cell-style":{padding:"0","text-align":"center",height:"50px",color:"#333"},"row-key":l(i).primaryKey},{empty:e(()=>[t(T)]),default:e(()=>[t(p,{prop:"id",label:"ID",width:"80"}),t(p,{prop:"status",label:"状态",width:"100"},{default:e(({row:a})=>[a.status==0?(r(),c(y,{key:0,type:"info"},{default:e(()=>[s("禁用")]),_:1})):u("",!0),a.status==1?(r(),c(y,{key:1,type:"success"},{default:e(()=>[s("正常")]),_:1})):u("",!0)]),_:1}),t(p,{prop:"username",label:"手机号"}),t(p,{label:"账号类型",width:"100"},{default:e(({row:a})=>[a.type==1?(r(),c(y,{key:0,type:"warning"},{default:e(()=>[s("会员")]),_:1})):u("",!0),a.type==2?(r(),c(y,{key:1,type:"success"},{default:e(()=>[s("代理商")]),_:1})):u("",!0),a.type==3?(r(),c(y,{key:2,type:"danger"},{default:e(()=>[s("管理员")]),_:1})):u("",!0)]),_:1}),t(p,{prop:"vip_package.name",label:"当前套餐"},{default:e(({row:a})=>[t(m,{type:"primary",link:"",icon:l(w),onClick:h=>F(a,"vip_id")},{default:e(()=>[s(D(a.vip_package&&a.vip_package.name),1)]),_:2},1032,["icon","onClick"])]),_:1}),t(p,{prop:"start_at",label:"生效时间"}),t(p,{prop:"end_at",label:"过期时间"},{default:e(({row:a})=>[t(m,{type:"primary",link:"",icon:l(w),onClick:h=>F(a,"end_at")},{default:e(()=>[s(D(a.end_at),1)]),_:2},1032,["icon","onClick"])]),_:1}),t(p,{prop:"credit",label:"积分"}),t(p,{prop:"created_at",label:"注册时间"}),t(p,{prop:"",label:"操作",fixed:"right",width:"80"},{default:e(({row:a})=>[a.status?(r(),c(m,{key:0,type:"primary",link:"",onClick:h=>C(a)},{default:e(()=>[s("禁用")]),_:2},1032,["onClick"])):(r(),c(m,{key:1,type:"primary",link:"",onClick:h=>C(a),style:{color:"#ff0000"}},{default:e(()=>[s("启用")]),_:2},1032,["onClick"]))]),_:1})]),_:1},8,["data","row-key"])),[[M,l(i).listLoading]]),t(I,{"current-page":l(i).page,total:l(i).total,"page-sizes":l(i).pageSizes,"page-size":l(i).limit,background:"",layout:"total, sizes, prev, pager, next",onSizeChange:l(E),onCurrentChange:l(q)},null,8,["current-page","total","page-sizes","page-size","onSizeChange","onCurrentChange"])]),_:1}),l(i).formVisible===!0?(r(),c(B,{key:0,visible:l(i).formVisible,"onUpdate:visible":n[1]||(n[1]=a=>l(i).formVisible=a),loading:l(i).formLoading,detail:l(i).detailData,onSubmit:l(U),type:"dialog",width:"600",title:(z=l(i).detailData)!=null&&z.id?"编辑":"新增",cancelBtnName:"取消",okBtnName:"确定"},{default:e(()=>[t(Z)]),_:1},8,["visible","loading","detail","onSubmit","title"])):u("",!0),f.value?(r(),c(B,{key:1,visible:f.value,"onUpdate:visible":n[2]||(n[2]=a=>f.value=a),detail:x.value,onSubmit:L,type:"dialog",width:"600",title:v.value,cancelBtnName:"取消",okBtnName:"确定"},{default:e(()=>[t(ee,{"form-feild":g.value},null,8,["form-feild"])]),_:1},8,["visible","detail","title"])):u("",!0)],64)}}});export{ue as default};
