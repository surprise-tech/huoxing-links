import{u as I}from"./curd-c5cac354.js";import{_ as U}from"./FormModal.vue_vue_type_script_setup_true_lang-b27420e1.js";import{d as H,j as T,r as n,X as $,o as i,a as d,w as a,f,i as e,g as l,b as j,e as E,F as K,l as _,Y as X,t as Y}from"./.pnpm-9b0f0a98.js";import{_ as A}from"./index-2667bde9.js";import"./propData-3c6625bc.js";const G={class:"header"},J={class:"header-left"},M=H({__name:"index",setup(O){const{state:t,showForm:b,submitForm:y,resetInput:v,research:h,sizeChangeHandle:C,currentChangeHandle:k}=I({url:"/users"}),x=T([{value:1,label:"会员"},{value:2,label:"代理商"}]);return(P,s)=>{const F=n("el-input"),m=n("el-form-item"),p=n("el-col"),V=n("el-option"),z=n("el-select"),u=n("el-button"),w=n("el-row"),B=n("el-form"),r=n("el-table-column"),D=n("el-empty"),S=n("el-table"),N=n("el-pagination"),q=n("el-card"),L=$("loading");return i(),d(q,null,{default:a(()=>{var g;return[f("div",G,[f("div",J,[e(B,null,{default:a(()=>[e(w,{gutter:20},{default:a(()=>[e(p,{span:3},{default:a(()=>[e(m,null,{default:a(()=>[e(F,{modelValue:l(t).queryForm.username,"onUpdate:modelValue":s[0]||(s[0]=o=>l(t).queryForm.username=o),placeholder:"搜索用户名"},null,8,["modelValue"])]),_:1})]),_:1}),e(p,{span:3},{default:a(()=>[e(m,null,{default:a(()=>[e(z,{modelValue:l(t).queryForm.type,"onUpdate:modelValue":s[1]||(s[1]=o=>l(t).queryForm.type=o),placeholder:"搜索会员等级"},{default:a(()=>[(i(!0),j(K,null,E(x.value,o=>(i(),d(V,{key:o.value,label:o.label,value:o.value},null,8,["label","value"]))),128))]),_:1},8,["modelValue"])]),_:1})]),_:1}),e(p,{span:5},{default:a(()=>[e(u,{type:"primary",onClick:l(h)},{default:a(()=>[_("搜索")]),_:1},8,["onClick"]),e(u,{onClick:l(v)},{default:a(()=>[_("重置")]),_:1},8,["onClick"])]),_:1})]),_:1})]),_:1})])]),X((i(),d(S,{data:l(t).dataList,"header-cell-style":{"text-align":"center","background-color":"#F1F1F1",height:"50px",color:"#333"},"cell-style":{padding:"0","text-align":"center",height:"50px",color:"#333"},"row-key":l(t).primaryKey},{empty:a(()=>[e(D)]),default:a(()=>[e(r,{prop:"id",label:"ID"}),e(r,{prop:"username",label:"用户名"}),e(r,{prop:"username",label:"昵称"}),e(r,{label:"会员等级"},{default:a(({row:o})=>{var c;return[_(Y((c=o==null?void 0:o.vip_package)==null?void 0:c.id),1)]}),_:1}),e(r,{prop:"end_at",label:"会员过期时间"}),e(r,{prop:"created_at",label:"创建时间"}),e(r,{label:"操作",width:"200"},{default:a(({row:o})=>[e(u,{type:"primary",onClick:c=>l(b)(o),link:""},{default:a(()=>[_("充值积分")]),_:2},1032,["onClick"])]),_:1})]),_:1},8,["data","row-key"])),[[L,l(t).listLoading]]),e(N,{"current-page":l(t).page,total:l(t).total,"page-sizes":l(t).pageSizes,"page-size":l(t).limit,background:"",layout:"total, sizes, prev, pager, next",onSizeChange:l(C),onCurrentChange:l(k)},null,8,["current-page","total","page-sizes","page-size","onSizeChange","onCurrentChange"]),e(U,{visible:l(t).formVisible,"onUpdate:visible":s[2]||(s[2]=o=>l(t).formVisible=o),width:600,type:"dialog",title:(g=l(t).detailData)!=null&&g.id?"编辑":"新增",loading:l(t).formLoading,detail:l(t).detailData,onSubmit:l(y),cancelBtnName:"重置",okBtnName:"确定"},null,8,["visible","title","loading","detail","onSubmit"])]}),_:1})}}});const le=A(M,[["__scopeId","data-v-c6222005"]]);export{le as default};
