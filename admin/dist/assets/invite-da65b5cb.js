import{u}from"./curd-e3316c4c.js";import{d as h,r as n,X as b,o as r,a as i,w as l,Y as C,g as a,i as e}from"./.pnpm-9b0f0a98.js";import"./index-b643f0a0.js";const k=h({__name:"invite",setup(y){const{state:o,sizeChangeHandle:p,currentChangeHandle:c}=u({url:"/agent-invite",queryForm:{page:1}});return(z,f)=>{const t=n("el-table-column"),s=n("el-empty"),g=n("el-table"),_=n("el-pagination"),d=n("el-card"),m=b("loading");return r(),i(d,null,{default:l(()=>[C((r(),i(g,{data:a(o).dataList,"header-cell-style":{"text-align":"center","background-color":"#F1F1F1",height:"50px",color:"#333"},"cell-style":{padding:"0","text-align":"center",height:"50px",color:"#333"},"row-key":a(o).primaryKey},{empty:l(()=>[e(s)]),default:l(()=>[e(t,{prop:"id",label:"ID",width:"60"}),e(t,{prop:"username",label:"手机号"}),e(t,{prop:"agent_package.name",label:"当前套餐"}),e(t,{prop:"credit",label:"剩余积分"}),e(t,{prop:"accumulate_credit",label:"累计充值积分"}),e(t,{prop:"commission",label:"剩余佣金",width:"120"}),e(t,{prop:"accumulate_commission",label:"累计佣金",width:"120"}),e(t,{prop:"created_at",label:"注册时间"})]),_:1},8,["data","row-key"])),[[m,a(o).listLoading]]),e(_,{"current-page":a(o).page,total:a(o).total,"page-sizes":a(o).pageSizes,"page-size":a(o).limit,background:"",layout:"total, sizes, prev, pager, next",onSizeChange:a(p),onCurrentChange:a(c)},null,8,["current-page","total","page-sizes","page-size","onSizeChange","onCurrentChange"])]),_:1})}}});export{k as default};