import{T as y,E as V}from"./@wangeditor-8417ddf6.js";import{u as w,I as C,_ as N}from"./index-1cb382aa.js";import{g as E,e as I}from"./super-7177c5c9.js";import{E as n}from"./element-plus-baf202ae.js";import{d as B,r as T,s as U,X as k,ag as l,o as M,H as R,I as a,Q as r,a as p,u as d,M as F}from"./@vue-d872a633.js";import"./pinia-cb4be529.js";import"./vue-demi-71ba0ef2.js";import"./pinia-plugin-persistedstate-ed167f96.js";import"./axios-439bb627.js";import"./qs-6612e700.js";import"./side-channel-32a248f0.js";import"./es-errors-c75f5a96.js";import"./object-inspect-110ff94a.js";import"./call-bind-apply-helpers-be8cb158.js";import"./function-bind-48f717dc.js";import"./side-channel-list-1667605c.js";import"./side-channel-map-a9d6574a.js";import"./get-intrinsic-8d8987ad.js";import"./es-object-atoms-11ea1ab9.js";import"./math-intrinsics-9389030e.js";import"./gopd-30775706.js";import"./es-define-property-27915812.js";import"./has-symbols-76fb15e9.js";import"./get-proto-15e8f36b.js";import"./dunder-proto-2a4d8230.js";import"./hasown-31deb62f.js";import"./call-bound-dbfc98a1.js";import"./side-channel-weakmap-a7bef06b.js";import"./vue-router-154b9487.js";import"./@element-plus-11e7d0b3.js";import"./@vueuse-9028f0e6.js";import"./nprogress-72cfee09.js";/* empty css                    */import"./lodash-es-32f1e46b.js";import"./@popperjs-c75af06c.js";import"./@ctrl-f8748455.js";import"./dayjs-f13f0179.js";import"./async-validator-dee29e8b.js";import"./memoize-one-297ddbcb.js";import"./normalize-wheel-es-ed76fb12.js";import"./@floating-ui-5ec34e26.js";const O={style:{border:"1px solid #ccc"}},$={class:"text-center m-t-20"},z=B({__name:"index",setup(A){const t=T({title:"",content:"",show:!1}),m=U(),u={},f={placeholder:"请输入内容...",MENU_CONF:{uploadImage:{fieldName:"image",server:"/api/material-upload",allowedFileTypes:["image/*"],headers:{Authorization:`Bearer ${w.getToken()}`},onError(e){n.error(`${e.name} 上传出错`)},customInsert(e,o){e&&e.path&&(o(C(e.path),"",""),n.success("上传成功"))}}}};k(()=>{const e=m.value;e!=null&&e.destroy()});const c=e=>{m.value=e};E().then(e=>{t.value=e});const _=()=>{if(!t.value.title){n.error("请输入标题");return}I(t.value).then(e=>{n.success("保存成功")})};return(e,o)=>{const v=l("el-input"),s=l("el-form-item"),h=l("el-switch"),g=l("el-form"),x=l("el-button"),b=l("el-card");return M(),R(b,null,{default:a(()=>[r(g,{ref:"formRef","label-width":"120px"},{default:a(()=>[r(s,{label:"标题"},{default:a(()=>[r(v,{modelValue:t.value.title,"onUpdate:modelValue":o[0]||(o[0]=i=>t.value.title=i),style:{width:"700px"},placeholder:"请输入标题"},null,8,["modelValue"])]),_:1}),r(s,{label:"内容"},{default:a(()=>[p("div",O,[r(d(y),{style:{"border-bottom":"1px solid #ccc"},editor:m.value,"default-config":u},null,8,["editor"]),r(d(V),{modelValue:t.value.content,"onUpdate:modelValue":o[1]||(o[1]=i=>t.value.content=i),style:{height:"500px","overflow-y":"hidden"},"default-config":f,onOnCreated:c},null,8,["modelValue"])])]),_:1}),r(s,{label:"是否弹窗显示"},{default:a(()=>[r(h,{modelValue:t.value.show,"onUpdate:modelValue":o[2]||(o[2]=i=>t.value.show=i)},null,8,["modelValue"])]),_:1})]),_:1},512),p("div",$,[r(x,{type:"primary",onClick:_},{default:a(()=>o[3]||(o[3]=[F("保存")])),_:1})])]),_:1})}}});const Ee=N(z,[["__scopeId","data-v-ffbedf16"]]);export{Ee as default};
