import{u as L}from"./curd-e3316c4c.js";import{_ as N}from"./FormModal.vue_vue_type_script_setup_true_lang-b27420e1.js";import U from"./create-or-look-dd77b514.js";import{j as D,O as $,k as H,_ as I}from"./index-b643f0a0.js";import{d as O,r as i,X as j,o as s,b as u,i as t,w as a,g as e,a as c,h as f,l as d,Y as E}from"./.pnpm-9b0f0a98.js";import"./propData-3c6625bc.js";import"./MaterialSelect-19b6ad9a.js";import"./cate-0ead127d.js";import"./app-0d4c35f9.js";import"./form-e5ba5cf1.js";import"./domain-f26e7393.js";const K={key:0},T={key:1,style:{color:"red"}},W=O({__name:"index",props:{type:{}},setup(k){const C=k,{state:l,sizeChangeHandle:h,currentChangeHandle:v,showForm:_,submitForm:x,research:V,deleteHandle:F}=L({url:"/links",queryForm:{type:C.type,page:1}}),w=D();return(X,r)=>{var y;const g=i("el-input"),m=i("el-col"),p=i("el-button"),z=i("el-row"),n=i("el-table-column"),S=i("el-table"),q=i("el-pagination"),B=j("loading");return s(),u("div",null,[t(z,{gutter:20},{default:a(()=>[t(m,{xs:24,sm:8,md:8,lg:6,class:"m-b-20"},{default:a(()=>[t(g,{style:{width:"100%"},modelValue:e(l).queryForm.title,"onUpdate:modelValue":r[0]||(r[0]=o=>e(l).queryForm.title=o),placeholder:"请输入标题",clearable:""},null,8,["modelValue"])]),_:1}),e(w).userInfo.type===3?(s(),c(m,{key:0,xs:24,sm:8,md:8,lg:6,class:"m-b-20"},{default:a(()=>[t(g,{style:{width:"100%"},placeholder:"请输入用户名",modelValue:e(l).queryForm.username,"onUpdate:modelValue":r[1]||(r[1]=o=>e(l).queryForm.username=o),clearable:""},null,8,["modelValue"])]),_:1})):f("",!0),t(m,{xs:24,sm:8,md:8,lg:6,class:"m-b-20"},{default:a(()=>[t(p,{type:"primary",onClick:e(V)},{default:a(()=>[d("搜索")]),_:1},8,["onClick"]),t(p,{type:"primary",plain:!0,onClick:e(_)},{default:a(()=>[d("创建企业微信")]),_:1},8,["onClick"])]),_:1})]),_:1}),E((s(),c(S,{data:e(l).dataList,"header-cell-style":{"text-align":"center","background-color":"#F1F1F1",height:"50px",color:"#333"},"cell-style":{padding:"0","text-align":"center",height:"80px",color:"#333"},"row-key":e(l).primaryKey},{default:a(()=>[t(n,{prop:"id",label:"序号"}),t(n,{label:"图片"},{default:a(({row:o})=>[t($,{paths:o.icon?[o.icon]:[],width:60,height:60,style:{margin:"0 auto"}},null,8,["paths"])]),_:1}),t(n,{prop:"title",label:"卡片标题"}),t(n,{prop:"description",label:"卡片描述"}),t(n,{prop:"",label:"链接状态"},{default:a(({row:o})=>[o.status==1?(s(),u("span",K,"可用")):(s(),u("span",T,"不可用"))]),_:1}),t(n,{prop:"visit_uv_count",label:"浏览量UV"}),t(n,{prop:"expired_at",label:"到期时间"}),t(n,{prop:"user.username",label:"所属用户"}),t(n,{label:"操作"},{default:a(({row:o})=>[t(p,{type:"primary",link:"",onClick:b=>e(H)(o)},{default:a(()=>[d("复制链接")]),_:2},1032,["onClick"]),t(p,{type:"primary",link:"",onClick:b=>e(_)(o.id)},{default:a(()=>[d("编辑")]),_:2},1032,["onClick"]),t(p,{type:"primary",link:"",onClick:b=>e(F)(o.id)},{default:a(()=>[d("删除")]),_:2},1032,["onClick"])]),_:1})]),_:1},8,["data","row-key"])),[[B,e(l).listLoading]]),t(q,{"current-page":e(l).page,total:e(l).total,"page-sizes":e(l).pageSizes,"page-size":e(l).limit,background:"",layout:"total, sizes, prev, pager, next",onSizeChange:e(h),onCurrentChange:e(v)},null,8,["current-page","total","page-sizes","page-size","onSizeChange","onCurrentChange"]),e(l).formVisible===!0?(s(),c(N,{key:0,visible:e(l).formVisible,"onUpdate:visible":r[2]||(r[2]=o=>e(l).formVisible=o),width:"1200",type:"dialog",title:(y=e(l).detailData)!=null&&y.id?"编辑":"新增",loading:e(l).formLoading,detail:e(l).detailData,onSubmit:e(x),cancelBtnName:"取消",okBtnName:"确定"},{default:a(()=>[t(U)]),_:1},8,["visible","title","loading","detail","onSubmit"])):f("",!0)])}}});const le=I(W,[["__scopeId","data-v-e1e1b4fb"]]);export{le as default};
