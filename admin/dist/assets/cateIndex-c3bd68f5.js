import{u as $}from"./curd-e3316c4c.js";import{d as D,j as I,r as i,X as S,o as d,b,i as t,w as a,f as w,g as e,ah as E,l as r,Y as L,a as u,ai as U,t as j,h as H,F as K}from"./.pnpm-9b0f0a98.js";import{_ as P}from"./cate-form.vue_vue_type_script_setup_true_lang-0b1f509c.js";import{_ as T}from"./FormModal.vue_vue_type_script_setup_true_lang-b27420e1.js";import{_ as X}from"./index-b643f0a0.js";import"./form-e5ba5cf1.js";import"./propData-3c6625bc.js";const Y={class:"header-right"},q={key:0},z=D({__name:"cateIndex",setup(A){const{state:l,inlineEdit:k,showForm:f,submitForm:g,deleteHandle:v}=$({url:"/materials-cate",openPage:!1}),C=()=>{c.value=!1},c=I(!0),h=async p=>{await k(p.id,"sort",p.sort),c.value=!0};return(p,_)=>{var y;const n=i("el-button"),m=i("el-table-column"),x=i("el-input-number"),V=i("el-empty"),F=i("el-table"),B=i("el-card"),N=S("loading");return d(),b(K,null,[t(B,null,{default:a(()=>[w("div",Y,[t(n,{type:"primary",icon:e(E),onClick:e(f)},{default:a(()=>[r("新增分类")]),_:1},8,["icon","onClick"])]),L((d(),u(F,{data:e(l).dataList,"header-cell-style":{"text-align":"center","background-color":"#F1F1F1",height:"50px",color:"#333"},"cell-style":{padding:"0","text-align":"center",height:"50px",color:"#333"},"row-key":e(l).primaryKey},{empty:a(()=>[t(V)]),default:a(()=>[t(m,{prop:"name",label:"名称"}),t(m,{label:"排序"},{default:a(({row:o})=>[c.value?(d(),u(n,{key:1,type:"primary",link:"",icon:e(U),onClick:C},{default:a(()=>[r(j(o.sort),1)]),_:2},1032,["icon"])):(d(),b("div",q,[t(x,{modelValue:o.sort,"onUpdate:modelValue":s=>o.sort=s},null,8,["modelValue","onUpdate:modelValue"]),t(n,{type:"primary",link:"",onClick:_[0]||(_[0]=s=>c.value=!0)},{default:a(()=>[r("取消")]),_:1}),t(n,{type:"primary",link:"",onClick:s=>h(o)},{default:a(()=>[r("确认")]),_:2},1032,["onClick"])]))]),_:1}),t(m,{prop:"created_at",label:"创建时间"}),t(m,{prop:"",label:"操作",fixed:"right"},{default:a(({row:o})=>[t(n,{type:"primary",link:"",onClick:s=>e(f)(o)},{default:a(()=>[r("编辑")]),_:2},1032,["onClick"]),t(n,{type:"primary",link:"",onClick:s=>e(v)(o.id)},{default:a(()=>[r("删除")]),_:2},1032,["onClick"])]),_:1})]),_:1},8,["data","row-key"])),[[N,e(l).listLoading]])]),_:1}),e(l).formVisible===!0?(d(),u(T,{key:0,visible:e(l).formVisible,"onUpdate:visible":_[1]||(_[1]=o=>e(l).formVisible=o),loading:e(l).formLoading,detail:e(l).detailData,onSubmit:e(g),type:"dialog",width:"540",title:(y=e(l).detailData)!=null&&y.id?"编辑":"新增",cancelBtnName:"取消",okBtnName:"确定"},{default:a(()=>[t(P)]),_:1},8,["visible","loading","detail","onSubmit","title"])):H("",!0)],64)}}});const Z=X(z,[["__scopeId","data-v-60ab26d1"]]);export{Z as default};