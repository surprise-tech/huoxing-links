import{u}from"./curd-b17e16e8.js";import{d as h,ag as o,aq as C,o as p,H as n,I as a,J as y,u as t,Q as r}from"./@vue-d872a633.js";import"./index-1cb382aa.js";import"./element-plus-baf202ae.js";import"./lodash-es-32f1e46b.js";import"./@element-plus-11e7d0b3.js";import"./@popperjs-c75af06c.js";import"./@ctrl-f8748455.js";import"./dayjs-f13f0179.js";import"./call-bind-apply-helpers-be8cb158.js";import"./function-bind-48f717dc.js";import"./es-errors-c75f5a96.js";import"./async-validator-dee29e8b.js";import"./memoize-one-297ddbcb.js";import"./normalize-wheel-es-ed76fb12.js";import"./@floating-ui-5ec34e26.js";import"./pinia-cb4be529.js";import"./vue-demi-71ba0ef2.js";import"./pinia-plugin-persistedstate-ed167f96.js";import"./axios-439bb627.js";import"./qs-6612e700.js";import"./side-channel-32a248f0.js";import"./object-inspect-110ff94a.js";import"./side-channel-list-1667605c.js";import"./side-channel-map-a9d6574a.js";import"./get-intrinsic-8d8987ad.js";import"./es-object-atoms-11ea1ab9.js";import"./math-intrinsics-9389030e.js";import"./gopd-30775706.js";import"./es-define-property-27915812.js";import"./has-symbols-76fb15e9.js";import"./get-proto-15e8f36b.js";import"./dunder-proto-2a4d8230.js";import"./hasown-31deb62f.js";import"./call-bound-dbfc98a1.js";import"./side-channel-weakmap-a7bef06b.js";import"./vue-router-154b9487.js";import"./@vueuse-9028f0e6.js";import"./nprogress-72cfee09.js";/* empty css                    */const mt=h({__name:"Invitation",setup(b){const{state:e,sizeChangeHandle:m,currentChangeHandle:l}=u({url:"/agent-invite"});return(z,f)=>{const i=o("el-table-column"),s=o("el-empty"),c=o("el-table"),g=o("el-pagination"),_=o("el-card"),d=C("loading");return p(),n(_,null,{default:a(()=>[y((p(),n(c,{data:t(e).dataList,"header-cell-style":{"text-align":"center","background-color":"#F1F1F1",height:"50px",color:"#333"},"cell-style":{padding:"0","text-align":"center",height:"50px",color:"#333"},"row-key":t(e).primaryKey},{empty:a(()=>[r(s)]),default:a(()=>[r(i,{prop:"id",label:"ID",width:"60"}),r(i,{prop:"username",label:"手机号"}),r(i,{prop:"created_at",label:"注册时间"})]),_:1},8,["data","row-key"])),[[d,t(e).listLoading]]),r(g,{"current-page":t(e).page,total:t(e).total,"page-sizes":t(e).pageSizes,"page-size":t(e).limit,background:"",layout:"total, sizes, prev, pager, next",onSizeChange:t(m),onCurrentChange:t(l)},null,8,["current-page","total","page-sizes","page-size","onSizeChange","onCurrentChange"])]),_:1})}}});export{mt as default};
