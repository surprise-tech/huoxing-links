import{d as E,j,r,X as q,o as c,b as C,i as e,w as a,f as _,g as t,ah as H,l as s,Y as I,a as v,ai as K,t as p,h as P,F as T}from"./.pnpm-9b0f0a98.js";import{u as X}from"./curd-c5cac354.js";import{_ as Y}from"./FormModal.vue_vue_type_script_setup_true_lang-b27420e1.js";import{_ as z}from"./create-or-look.vue_vue_type_script_setup_true_lang-e4cd6d4e.js";import{_ as A}from"./index-2667bde9.js";import"./propData-3c6625bc.js";import"./form-e5ba5cf1.js";const G={class:"header-right"},J={key:0},M=E({__name:"index",setup(O){const{state:i,showForm:y,submitForm:x,deleteHandle:V,query:F,inlineEdit:B}=X({url:"/vip-packages",openPage:!1}),N=()=>{m.value=!1},m=j(!0),$=async f=>{await B(f.id,"level",f.level),await F(),m.value=!0};return(f,u)=>{var b;const o=r("el-button"),d=r("el-table-column"),D=r("el-input-number"),S=r("el-empty"),U=r("el-table"),L=r("el-card"),w=q("loading");return c(),C(T,null,[e(L,null,{default:a(()=>[_("div",G,[e(o,{type:"primary",icon:t(H),onClick:t(y)},{default:a(()=>[s("新增套餐")]),_:1},8,["icon","onClick"])]),I((c(),v(U,{data:t(i).dataList,"header-cell-style":{"text-align":"center","background-color":"#F1F1F1",height:"50px",color:"#333"},"cell-style":{padding:"0","text-align":"center",height:"50px",color:"#333"},"row-key":t(i).primaryKey},{empty:a(()=>[e(S)]),default:a(()=>[e(d,{prop:"name",label:"套餐名称"}),e(d,{prop:"price",label:"价格/月"}),e(d,{prop:"level",label:"等级"},{default:a(({row:l})=>[m.value===!1?(c(),C("div",J,[e(D,{modelValue:l.level,"onUpdate:modelValue":n=>l.level=n},null,8,["modelValue","onUpdate:modelValue"]),e(o,{type:"primary",link:"",onClick:u[0]||(u[0]=n=>m.value=!0)},{default:a(()=>[s("取消")]),_:1}),e(o,{type:"primary",link:"",onClick:n=>$(l)},{default:a(()=>[s("确认")]),_:2},1032,["onClick"])])):(c(),v(o,{key:1,type:"primary",link:"",icon:t(K),onClick:N},{default:a(()=>[s(p(l.level),1)]),_:2},1032,["icon"]))]),_:1}),e(d,{label:"权限配置"},{default:a(({row:l})=>{var n,g,k,h;return[_("div",null,"链接数量："+p((n=l.config)==null?void 0:n.count_limit),1),_("div",null,"小圆码数量："+p((g=l.config)==null?void 0:g.yuanma_limit),1),_("div",null,"抖音自动回复数量："+p((k=l.config)==null?void 0:k.douyin_reply_limit),1),_("div",null,"UV限制："+p((h=l.config)==null?void 0:h.uv_limit),1)]}),_:1}),e(d,{prop:"created_at",label:"创建时间"}),e(d,{prop:"",label:"操作",fixed:"right"},{default:a(({row:l})=>[e(o,{type:"primary",link:"",onClick:n=>t(y)(l)},{default:a(()=>[s("编辑")]),_:2},1032,["onClick"]),e(o,{type:"primary",link:"",onClick:n=>t(V)(l.id)},{default:a(()=>[s("删除")]),_:2},1032,["onClick"])]),_:1})]),_:1},8,["data","row-key"])),[[w,t(i).listLoading]])]),_:1}),t(i).formVisible===!0?(c(),v(Y,{key:0,visible:t(i).formVisible,"onUpdate:visible":u[1]||(u[1]=l=>t(i).formVisible=l),loading:t(i).formLoading,detail:t(i).detailData,onSubmit:t(x),type:"dialog",width:"1000",title:(b=t(i).detailData)!=null&&b.id?"编辑":"新增",cancelBtnName:"取消",okBtnName:"确定"},{default:a(()=>[e(z)]),_:1},8,["visible","loading","detail","onSubmit","title"])):P("",!0)],64)}}});const ae=A(M,[["__scopeId","data-v-2170bfdf"]]);export{ae as default};
