import{c as o,d as _,a as f,_ as v}from"./index-06117f19.js";import{u as $}from"./propData-3c6625bc.js";import{d as b,r as V,o as t,b as s,i as I,w as q,f as l,F as c,e as i,g as n,Z as B,A as C,B as S,v as r,t as u}from"./.pnpm-9b0f0a98.js";const d=p=>(C("data-v-ea563e91"),p=p(),S(),p),w={class:"equity-table"},U=d(()=>l("th",null,"权益/版本",-1)),D={class:"margin-b-5"},F=d(()=>l("td",{class:"td-1 padding-l-20"},"有UV限制链接价格",-1)),N=d(()=>l("td",{class:"td-1 padding-l-20"},"无UV限制链接价格",-1)),z=d(()=>l("td",{class:"td-1 padding-l-20"},"UV价格",-1)),A=d(()=>l("td",{class:"td-1 padding-l-20"},"自建小程序池可创建数量",-1)),E=d(()=>l("td",{class:"td-1 padding-l-20"},"链接保留时长",-1)),L=d(()=>l("span",null,"永久",-1)),P=[L],R=d(()=>l("td",{class:"td-1 padding-l-20"},"所有类型链接创建",-1)),Z={key:0},j={key:1,class:"red"},G=d(()=>l("td",{class:"td-1 padding-l-20"},"官方小程序池使用",-1)),H={key:0},J={key:1,class:"red"},K=d(()=>l("td",{class:"td-1 padding-l-20"},"数据统计/分析",-1)),M=d(()=>l("span",null,"支持",-1)),O=[M],Q=d(()=>l("td",{class:"td-1 padding-l-20"},"短链接可创建数量",-1)),T=d(()=>l("td",{class:"td-1 padding-l-20"},"自定义落地页",-1)),W={key:0},X={key:1,class:"red"},Y=d(()=>l("td",{class:"td-1 padding-l-20"},"自建小程序池下架自动检测",-1)),x={key:0},tt={key:1,class:"red"},st=d(()=>l("td",{class:"td-1 padding-l-20"},"免费技术协助",-1)),et={key:0},lt={key:1,class:"red"},at=b({__name:"equityInstruction",props:{visible:{type:Boolean}},emits:["update:visible"],setup(p,{emit:h}){const y=p,k=$(()=>y.visible,!1);return(nt,g)=>{const m=V("el-dialog");return t(),s("div",null,[I(m,{class:"equity-dialog",modelValue:n(k),"onUpdate:modelValue":g[0]||(g[0]=a=>B(k)?k.value=a:null),title:"权益说明",onClose:g[1]||(g[1]=a=>h("update:visible",!1))},{default:q(()=>[l("table",w,[l("thead",null,[l("tr",null,[U,(t(!0),s(c,null,i(n(o).package,(a,e)=>(t(),s("th",{class:r(`th-${e+1}`),key:e},[l("div",D,u(a.name),1),l("div",null,"￥"+u(a.price)+"/月",1)],2))),128))])]),l("tbody",null,[l("tr",null,[F,(t(!0),s(c,null,i(n(o).package,(a,e)=>(t(),s("td",{class:r(`td-${e+2}`),key:e},u(n(_)(a,"config.uv_limit_price"))+"/条 ",3))),128))]),l("tr",null,[N,(t(!0),s(c,null,i(n(o).package,(a,e)=>(t(),s("td",{class:r(`td-${e+2}`),key:e},u(n(_)(a,"config.no_uv_limit_price"))+"/条 ",3))),128))]),l("tr",null,[z,(t(!0),s(c,null,i(n(o).package,(a,e)=>(t(),s("td",{class:r(`td-${e+2}`),key:e},u(n(_)(a,"config.uv_price"))+"/个 ",3))),128))]),l("tr",null,[A,(t(!0),s(c,null,i(n(o).package,(a,e)=>(t(),s("td",{class:r(`td-${e+2}`),key:e},u(n(_)(a,"config.min_count_limit"))+"个 ",3))),128))]),l("tr",null,[E,(t(!0),s(c,null,i(n(o).package,(a,e)=>(t(),s("td",{class:r(`td-${e+2}`),key:e},P,2))),128))]),l("tr",null,[R,(t(!0),s(c,null,i(n(o).package,(a,e)=>(t(),s("td",{class:r(`td-${e+2}`),key:e},[n(f)(a,"config.allow_type")?(t(),s("span",Z,"支持")):(t(),s("span",j,"不支持"))],2))),128))]),l("tr",null,[G,(t(!0),s(c,null,i(n(o).package,(a,e)=>(t(),s("td",{class:r(`td-${e+2}`),key:e},[n(_)(a,"config.pre_min")?(t(),s("span",H,"支持")):(t(),s("span",J,"不支持"))],2))),128))]),l("tr",null,[K,(t(!0),s(c,null,i(n(o).package,(a,e)=>(t(),s("td",{class:r(`td-${e+2}`),key:e},O,2))),128))]),l("tr",null,[Q,(t(!0),s(c,null,i(n(o).package,(a,e)=>(t(),s("td",{class:r(`td-${e+2}`),key:e},"5个",2))),128))]),l("tr",null,[T,(t(!0),s(c,null,i(n(o).package,(a,e)=>(t(),s("td",{class:r(`td-${e+2}`),key:a.id},[n(_)(a,"config.cur_index")?(t(),s("span",W,"支持")):(t(),s("span",X,"不支持"))],2))),128))]),l("tr",null,[Y,(t(!0),s(c,null,i(n(o).package,(a,e)=>(t(),s("td",{class:r(`td-${e+2}`),key:a.id},[n(_)(a,"config.min_disabled_check")?(t(),s("span",x,"支持")):(t(),s("span",tt,"不支持"))],2))),128))]),l("tr",null,[st,(t(!0),s(c,null,i(n(o).package,(a,e)=>(t(),s("td",{class:r(`td-${e+2}`),key:a.id},[n(_)(a,"config.support")?(t(),s("span",et,"支持")):(t(),s("span",lt,"不支持"))],2))),128))])])])]),_:1},8,["modelValue"])])}}});const it=v(at,[["__scopeId","data-v-ea563e91"]]);export{it as default};
