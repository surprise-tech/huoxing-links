import{u as M}from"./form-e5ba5cf1.js";import{d as C,j as n,ak as F,r,o as _,b as I,i as l,w as u,g as c,al as R,a as k,h as B,am as E,E as x,z as j,X as q,Y as D,e as O,F as S}from"./.pnpm-9b0f0a98.js";import{u as $,O as A,_ as N}from"./index-31481636.js";import{a as P}from"./app-959aa9a6.js";const T={class:"upload-box"},H=C({__name:"uploadImg",props:{modelValue:{},type:{},size:{},width:{},height:{},action:{default:"/api/material-upload"}},emits:["update:modelValue"],setup(z,{emit:d}){const e=z,m=n(),f=n(),s=n(!1),i=n(!1),p=n(),a=t=>{p.value=t.response.path,i.value=!0},g={Authorization:"Bearer "+$.getToken()},h=()=>{d("update:modelValue",[])};F("query");const V=t=>{m.value.clearFiles();const o=t[0];o.uid=E(),m.value.handleStart(o)},v=t=>{if(e.type&&e.type.length>0||e.size){s.value=!0,console.log(t.size,"rawFile.size");const o=t.size<1024*1024*e.size,b=e.type.indexOf(t.type)>-1;if(!o)return x.warning(`图片大小不超过${e.size}M`),s.value=!1,!1;if(!b)return x.warning(`图片只能是${e.type.join(",")}格式!`),s.value=!1,!1}else return!0},y=t=>{s.value=!0,setTimeout(()=>{d("update:modelValue",t),x.success("上传成功"),s.value=!1},1e3)};return(t,o)=>{const b=r("el-icon"),U=r("el-upload"),L=r("el-dialog");return _(),I("div",T,[l(U,{ref_key:"uploadRef",ref:m,"file-list":f.value,"onUpdate:fileList":o[0]||(o[0]=w=>f.value=w),action:e.action,headers:g,name:"image",limit:1,class:"avatar-warp","on-exceed":V,"before-upload":v,"on-success":y,"on-preview":a,"on-remove":h},{default:u(()=>[l(b,null,{default:u(()=>[l(c(R))]),_:1})]),_:1},8,["file-list","action"]),l(L,{modelValue:i.value,"onUpdate:modelValue":o[1]||(o[1]=w=>i.value=w)},{default:u(()=>[p.value?(_(),k(A,{key:0,height:200,width:200,paths:[p.value]},null,8,["paths"])):B("",!0)]),_:1},8,["modelValue"])])}}});const X=N(H,[["__scopeId","data-v-9acc117d"]]),Q=C({__name:"create-or-look",setup(z){const d=n(),{formData:e,formLoading:m}=M({formRef:d,defVal(a){return{cate_id:a==null?void 0:a.cate_id,image:a==null?void 0:a.image,name:a==null?void 0:a.name}}}),f={cate_id:[{required:!0,message:"请选择分类",trigger:"blur"}],image:[{required:!0,message:"请上传图片",trigger:"blur"}]},s=a=>{e.value.image=a.path,e.value.name=a.name},i=n(),p=async()=>{i.value=await P()};return j(()=>{p()}),(a,g)=>{const h=r("el-option"),V=r("el-select"),v=r("el-form-item"),y=r("el-form"),t=q("loading");return D((_(),k(y,{model:c(e),rules:f,ref_key:"formRef",ref:d,"label-width":"120px"},{default:u(()=>[l(v,{prop:"name",label:"分类",style:{width:"370px"}},{default:u(()=>[l(V,{modelValue:c(e).cate_id,"onUpdate:modelValue":g[0]||(g[0]=o=>c(e).cate_id=o),clearable:"",placeholder:"请选择"},{default:u(()=>[(_(!0),I(S,null,O(i.value,o=>(_(),k(h,{key:o.id,label:o.name,value:o.id},null,8,["label","value"]))),128))]),_:1},8,["modelValue"])]),_:1}),l(v,{label:"图片"},{default:u(()=>[l(X,{"model-value":c(e).image,"onUpdate:modelValue":s},null,8,["model-value"])]),_:1})]),_:1},8,["model"])),[[t,c(m)]])}}});export{Q as _};
