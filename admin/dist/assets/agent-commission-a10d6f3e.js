import{u as b}from"./curd-e3316c4c.js";import{d as x,r as l,X as v,o,a as n,w as e,Y as z,g as t,i as s,l as i,h as _}from"./.pnpm-9b0f0a98.js";import{_ as F}from"./index-b643f0a0.js";const N=x({__name:"agent-commission",props:{user_id:{}},setup(u){const d=u,{state:a,sizeChangeHandle:g,currentChangeHandle:m}=b({url:`/commission-logs/${d.user_id}`,queryForm:{page:1,type:"2,3"}});return(S,V)=>{const r=l("el-table-column"),c=l("el-tag"),y=l("el-empty"),f=l("el-table"),h=l("el-pagination"),C=l("el-card"),k=v("loading");return o(),n(C,null,{default:e(()=>[z((o(),n(f,{data:t(a).dataList,"header-cell-style":{"text-align":"center","background-color":"#F1F1F1",height:"50px",color:"#333"},"cell-style":{padding:"0","text-align":"center",height:"50px",color:"#333"},"row-key":t(a).primaryKey},{empty:e(()=>[s(y)]),default:e(()=>[s(r,{prop:"title",label:"备注"}),s(r,{prop:"amount",label:"金额"}),s(r,{prop:"children_user.username",label:"邀请用户"}),s(r,{prop:"status",label:"状态"},{default:e(({row:p})=>[p.status===0?(o(),n(c,{key:0,type:"success",effect:"dark"},{default:e(()=>[i("已发放")]),_:1})):_("",!0),p.status===1?(o(),n(c,{key:1,type:"info",effect:"dark"},{default:e(()=>[i("提现待审核")]),_:1})):_("",!0),p.status===2?(o(),n(c,{key:2,type:"success"},{default:e(()=>[i("已打款")]),_:1})):_("",!0),p.status===3?(o(),n(c,{key:3,type:"danger"},{default:e(()=>[i("提现被拒绝")]),_:1})):_("",!0),p.status===4?(o(),n(c,{key:4,type:"info"},{default:e(()=>[i("打款失败")]),_:1})):_("",!0)]),_:1}),s(r,{prop:"created_at",label:"时间"})]),_:1},8,["data","row-key"])),[[k,t(a).listLoading]]),s(h,{"current-page":t(a).page,total:t(a).total,"page-sizes":t(a).pageSizes,"page-size":t(a).limit,background:"",layout:"total, sizes, prev, pager, next",onSizeChange:t(g),onCurrentChange:t(m)},null,8,["current-page","total","page-sizes","page-size","onSizeChange","onCurrentChange"])]),_:1})}}});const L=F(N,[["__scopeId","data-v-e02a55d7"]]);export{L as default};
