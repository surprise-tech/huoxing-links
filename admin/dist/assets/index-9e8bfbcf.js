import{d as x,r as i,X as C,o as c,b as F,i as e,w as a,f as V,g as t,ah as w,l as r,Y as B,a as m,t as D,h as N,F as $}from"./.pnpm-9b0f0a98.js";import{u as L}from"./curd-e3316c4c.js";import{_ as S}from"./FormModal.vue_vue_type_script_setup_true_lang-b27420e1.js";import{_ as I}from"./create-or-look.vue_vue_type_script_setup_true_lang-3fe6db88.js";import{_ as E}from"./index-b643f0a0.js";import"./propData-3c6625bc.js";import"./form-e5ba5cf1.js";const H={class:"header-right"},K=x({__name:"index",setup(P){const{state:l,showForm:d,submitForm:u,deleteHandle:f}=L({url:"/domains",openPage:!1});return(T,_)=>{var p;const s=i("el-button"),o=i("el-table-column"),b=i("el-tag"),g=i("el-empty"),y=i("el-table"),k=i("el-card"),h=C("loading");return c(),F($,null,[e(k,null,{default:a(()=>[V("div",H,[e(s,{type:"primary",icon:t(w),onClick:t(d)},{default:a(()=>[r("新增")]),_:1},8,["icon","onClick"])]),B((c(),m(y,{data:t(l).dataList,"header-cell-style":{"text-align":"center","background-color":"#F1F1F1",height:"50px",color:"#333"},"cell-style":{padding:"0","text-align":"center",height:"50px",color:"#333"},"row-key":t(l).primaryKey},{empty:a(()=>[e(g)]),default:a(()=>[e(o,{prop:"id",label:"ID"}),e(o,{prop:"title",label:"标题"}),e(o,{prop:"url",label:"链接",width:"500"}),e(o,{label:"是否启用"},{default:a(({row:n})=>[e(b,{type:n.enable?"success":"danger"},{default:a(()=>[r(D(n.enable?"启用":"禁用"),1)]),_:2},1032,["type"])]),_:1}),e(o,{prop:"created_at",label:"创建时间"}),e(o,{prop:"updated_at",label:"更新时间"}),e(o,{prop:"",label:"操作",fixed:"right"},{default:a(({row:n})=>[e(s,{type:"primary",link:"",onClick:v=>t(d)(n)},{default:a(()=>[r("编辑")]),_:2},1032,["onClick"]),e(s,{type:"primary",link:"",onClick:v=>t(f)(n.id)},{default:a(()=>[r("删除")]),_:2},1032,["onClick"])]),_:1})]),_:1},8,["data","row-key"])),[[h,t(l).listLoading]])]),_:1}),t(l).formVisible===!0?(c(),m(S,{key:0,visible:t(l).formVisible,"onUpdate:visible":_[0]||(_[0]=n=>t(l).formVisible=n),loading:t(l).formLoading,detail:t(l).detailData,onSubmit:t(u),type:"dialog",width:"1000",title:(p=t(l).detailData)!=null&&p.id?"编辑":"新增",cancelBtnName:"取消",okBtnName:"确定"},{default:a(()=>[e(I)]),_:1},8,["visible","loading","detail","onSubmit","title"])):N("",!0)],64)}}});const G=E(K,[["__scopeId","data-v-81b971ec"]]);export{G as default};
