import{u as C,h as s}from"./index-31481636.js";import{U as V,z as H,y as K,aj as c,E as u,M as m}from"./.pnpm-9b0f0a98.js";function z(L){const e=V(Object.assign({autoInit:!0,disableList:!1,url:"",urlSuffix:"",openPage:!0,primaryKey:"id",exportUrl:"",queryForm:{},dataList:[],meta:[],order:"",asc:!0,page:1,limit:10,total:0,pageSizes:[10,20,30,40,50],listLoading:!1,listSelections:[],detailLoading:!1,detailData:void 0,formVisible:!1,formLoading:!1,level:"",type:""},L)),o=()=>{if(C.refreshUserInfo(),e.disableList||!e.url)return;e.listLoading=!0;const t={...e.queryForm};e.order&&(t.order=e.order,t.asc=e.asc?1:0),e.openPage&&(t.page=e.page,t.page_size=e.limit),s.get(e.url+e.urlSuffix,{params:t}).then(r=>{var a;e.openPage?(e.dataList=r.data,e.total=r.meta.total,e.type==="coupon"&&(e.dataList=(a=e.dataList)==null?void 0:a.filter(n=>n.is_new===!1)),e.meta=r.meta):e.dataList=r}).finally(()=>{e.listLoading=!1})},h=t=>{e.page=1,e.limit=t,o()},x=t=>{e.page=t,o()},S=t=>{e.listSelections=t.map(r=>e.primaryKey&&r[e.primaryKey])},b=t=>new Promise((r,a)=>{if(!e.url)return a();c.confirm("是否恢复信息?","提示",{confirmButtonText:"确定",cancelButtonText:"取消",type:"warning"}).then(()=>{e.listLoading=!0,s.delete(e.url+"/"+t+e.urlSuffix).then(()=>(u.success("恢复成功"),o(),r(!0)))}).catch(n=>a(n)).finally(()=>{e.listLoading=!1})}),w=t=>new Promise((r,a)=>{if(!e.url)return a();c.confirm("确定删除?","提示",{confirmButtonText:"确定",cancelButtonText:"取消",type:"warning"}).then(()=>{e.listLoading=!0,s.delete(e.url+"/"+t+e.urlSuffix).then(()=>(u.success("删除成功"),o(),r(!0)))}).catch(n=>a(n)).finally(()=>{e.listLoading=!1})}),$=(t,r)=>new Promise((a,n)=>{if(!e.url)return n();c.confirm("确定删除? 不可恢复！","提示",{confirmButtonText:"确定",cancelButtonText:"取消",type:"warning"}).then(()=>{e.listLoading=!0,s.delete(e.url+"/"+t+"/"+r).then(()=>(u.success("删除成功"),o(),a(!0)))}).catch(i=>n(i)).finally(()=>{e.listLoading=!1})}),d=t=>new Promise((r,a)=>{if(!e.url)return a();e.detailLoading=!0,s.get(e.url+"/"+t+e.urlSuffix).then(n=>(e.detailData=n,r(n))).catch(n=>a(n)).finally(()=>{e.detailLoading=!1})}),P=t=>{e.formLoading=!0,e.formVisible=!0,t instanceof Event||t===void 0?(e.detailData=null,m(()=>e.formLoading=!1)):typeof t=="object"?(e.detailData=JSON.parse(JSON.stringify(t)),m(()=>e.formLoading=!1)):(typeof t=="number"||typeof t=="string")&&(e.detailData={},d(t).finally(()=>{e.formLoading=!1}))},g=(t,r=!0)=>new Promise((a,n)=>{if(!e.url)return n();e.formLoading=!0,s.post(e.url+e.urlSuffix,t).then(i=>(u.success("保存成功"),e.formVisible=!1,r&&o(),a(i))).catch(i=>n(i)).finally(()=>{e.formLoading=!1})}),p=(t,r,a=!0)=>new Promise((n,i)=>{if(!e.url)return i();e.formLoading=!0,s.put(`${e.url}/${r}${e.urlSuffix}`,t).then(l=>(u.success("保存成功"),e.formVisible=!1,a&&o(),n(l))).catch(l=>i(l)).finally(()=>{e.formLoading=!1})}),q=(t,r,a,n=!0)=>new Promise((i,l)=>{if(!e.url)return l();e.formLoading=!0,s.post(`${e.url}/${t}${e.urlSuffix}`,{field:r,value:a,_method:"PATCH"}).then(f=>(u.success("保存成功"),e.formVisible=!1,n&&o(),i(f))).catch(f=>l(f)).finally(()=>{e.formLoading=!1})}),v=(t,r)=>{if(e.url)return e.formLoading=!0,r&&e.primaryKey?p(t,r[e.primaryKey]):g(t)},F=()=>{e.queryForm={},e.page=1,e.limit=10,o()},T=()=>{e.page=1,e.limit=10,o()},E=t=>{s.post(`${e.url}/enable/${t}`).then(()=>{o()})},D=()=>{e.page=1,e.limit=10,o()},y=()=>{if(!e.url)return;e.listLoading=!0;const t=[];for(const n in e.queryForm)t.push(`${n}=${e.queryForm[n]}`);const r=t.join("&"),a=`${e.url}?${r}&export=1`;s.request("GET",a,{responseType:"blob"}).then(n=>{const i=document.createElement("a");i.href=URL.createObjectURL(n),document.body.appendChild(i),i.click(),document.body.removeChild(i)}).finally(()=>{e.listLoading=!1})},B=(t,r)=>{e.queryForm.status=t,e.queryForm.delivery_type=r,y(),m(()=>{e.queryForm={}})};return H(()=>{e.autoInit&&o()}),K(()=>e.formVisible,t=>{t||(e.detailData=void 0,e.formLoading=!1)}),{state:e,sizeChangeHandle:h,currentChangeHandle:x,selectionChangeHandle:S,deleteHandle:w,showForm:P,submitForm:v,getDetail:d,addSubmit:g,editSubmit:p,inlineEdit:q,recover:b,resetInput:F,research:D,query:o,recycleDel:$,reset:T,outExport:y,useMode:E,dispatchOutExport:B}}export{z as u};