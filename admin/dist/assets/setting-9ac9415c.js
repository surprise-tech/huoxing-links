import{m as O,O as w,n as L,c as F,_ as G}from"./index-06117f19.js";import{c as T}from"./super-1e5ee443.js";import{U,M as k}from"./MaterialSelect-820834ff.js";import{d as P,j as y,z as X,r as p,X as Y,o as n,b as z,i as t,w as o,f as s,l as b,a as _,h as c,Y as q,F as H,e as J,E as r,A as K,B as Q}from"./.pnpm-9b0f0a98.js";import"./propData-3c6625bc.js";import"./curd-06fa7d62.js";import"./cate-977c4cd5.js";import"./app-d6b245fe.js";const d=V=>(K("data-v-f6d3e756"),V=V(),Q(),V),R=d(()=>s("div",{class:"m-b-20"},"顶部LOGO图片信息",-1)),W={class:"item-box"},Z={class:"function-img m-b-20"},$=d(()=>s("span",{class:"tip"},"只能上传jpg/png，建议大小100*100，且不超过1Mb",-1)),ee=d(()=>s("div",{class:"m-b-20"},"底部LOGO图片信息",-1)),le={class:"item-box"},ae={class:"function-img m-b-20"},te=d(()=>s("span",{class:"tip"},"只能上传jpg/png，且不超过1Mb",-1)),se=d(()=>s("div",{class:"m-b-20"},"上传客服二维码",-1)),oe={class:"item-box"},de={class:"function-img m-b-20"},ie=d(()=>s("span",{class:"tip"},"只能上传jpg/png，建议大小100*100，且不超过1Mb",-1)),me=d(()=>s("div",{class:"margin-b-10",style:{"margin-top":"20px"}},"网站标题",-1)),ue=d(()=>s("div",{class:"margin-b-10",style:{"margin-top":"20px"}},"是否开启验证码",-1)),ne=d(()=>s("div",{class:"margin-b-10",style:{"margin-top":"20px"}},"验证码发送方式",-1)),_e=d(()=>s("div",{class:"margin-b-10",style:{"margin-top":"20px"}},"阿里短信key",-1)),re=d(()=>s("div",{class:"margin-b-10",style:{"margin-top":"20px"}},"阿里短信secret",-1)),pe=d(()=>s("div",{class:"margin-b-10",style:{"margin-top":"20px"}},"阿里短信签名",-1)),ve=d(()=>s("div",{class:"margin-b-10",style:{"margin-top":"20px"}},"发信名称",-1)),ce=d(()=>s("div",{class:"margin-b-10",style:{"margin-top":"20px"}},"服务器地址",-1)),ge=d(()=>s("div",{class:"margin-b-10",style:{"margin-top":"20px"}},"端口",-1)),fe=d(()=>s("div",{class:"margin-b-10",style:{"margin-top":"20px"}},"发信地址",-1)),Ve=d(()=>s("div",{class:"margin-b-10",style:{"margin-top":"20px"}},"发信账号",-1)),ye=d(()=>s("div",{class:"margin-b-10",style:{"margin-top":"20px"}},"发信密码",-1)),be=d(()=>s("div",{class:"margin-b-10",style:{"margin-top":"20px"}},"微信商户appId",-1)),xe=d(()=>s("div",{class:"margin-b-10",style:{"margin-top":"20px"}},"微信商户号",-1)),he=d(()=>s("div",{class:"margin-b-10",style:{"margin-top":"20px"}},"微信支付密钥",-1)),we=d(()=>s("div",{class:"margin-b-10",style:{"margin-top":"20px"}},"微信支付私钥证书",-1)),Ue=d(()=>s("div",{class:"margin-b-10",style:{"margin-top":"20px"}},"微信支付公钥证书",-1)),ke={class:"text-center m-t-30"},je=d(()=>s("div",{class:"margin-b-10",style:{"margin-top":"20px"}},"是否开启赠送会员",-1)),Se=d(()=>s("div",{class:"margin-b-10",style:{"margin-top":"20px"}},"注册赠送会员",-1)),Ie=d(()=>s("div",{class:"margin-b-10",style:{"margin-top":"20px"}},"注册赠送天数",-1)),Be=d(()=>s("div",{class:"margin-b-10",style:{"margin-top":"20px"}},"一级邀请注册佣金",-1)),Ce=d(()=>s("div",{class:"margin-b-10",style:{"margin-top":"20px"}},"二级邀请注册佣金",-1)),Me=d(()=>s("div",{class:"margin-b-10",style:{"margin-top":"20px"}},"一级分销（%）",-1)),Oe=d(()=>s("div",{class:"margin-b-10",style:{"margin-top":"20px"}},"二级分销（%）",-1)),Le={class:"text-center m-t-30"},ze=P({__name:"setting",setup(V){const m=y({}),x=y([]),v=y(!1),e=y({}),A=()=>{v.value=!0,L("base",m.value).then(()=>{r.success("保存成功")}).finally(()=>{v.value=!1})},D=()=>{if(e.value.verify_code_is_open){if(!e.value.send_code_mode){r.error("请选择发送方式");return}if(e.value.send_code_mode===1){if(!e.value.ali_sms_key){r.error("请填写阿里短信key");return}if(!e.value.ali_sms_secret){r.error("请填写阿里短信secret");return}if(!e.value.ali_sms_secret){r.error("请填写阿里短信secret");return}}else if(e.value===2){if(!e.value.mail_from_name){r.error("请填写发信名称");return}if(!e.value.mail_host){r.error("请填写服务器地址");return}if(!e.value.mail_port){r.error("请填写端口");return}if(!e.value.mail_from_address){r.error("请填写发信地址");return}if(!e.value.mail_username){r.error("请填写邮箱账号");return}if(!e.value.mail_password){r.error("请填写邮箱密码");return}}}v.value=!0,L("web_site",e.value).then(()=>{r.success("保存成功"),F.refreshConfig()}).finally(()=>{v.value=!1})};return X(()=>{v.value=!0,O("base").then(g=>{m.value=g}).finally(()=>{v.value=!1}),O("web_site").then(g=>{e.value=g}),T().then(g=>{x.value=x.value.concat(g)})}),(g,a)=>{const i=p("el-col"),u=p("el-input"),j=p("el-divider"),S=p("el-switch"),h=p("el-option"),I=p("el-select"),B=p("el-row"),C=p("el-button"),M=p("el-card"),f=p("el-input-number"),N=p("el-form"),E=Y("loading");return n(),z("div",null,[t(M,{title:"基础配置"},{default:o(()=>[t(B,{gutter:20},{default:o(()=>[t(i,{xs:24,sm:12,md:8},{default:o(()=>[R,s("div",W,[t(w,{class:"margin-right-20",paths:e.value.web_site_logo?[e.value.web_site_logo]:[],width:100,height:100},null,8,["paths"]),s("div",null,[s("div",Z,[t(U,{class:"margin-right-10",type:["image/jpg","image/jpeg","image/png"],size:1,modelValue:e.value.web_site_logo,"onUpdate:modelValue":a[0]||(a[0]=l=>e.value.web_site_logo=l)},null,8,["modelValue"]),t(k,{modelValue:e.value.web_site_logo,"onUpdate:modelValue":a[1]||(a[1]=l=>e.value.web_site_logo=l),class:"margin-right-20"},null,8,["modelValue"])]),$])])]),_:1}),t(i,{xs:24,sm:12,md:8},{default:o(()=>[ee,s("div",le,[t(w,{class:"margin-right-20",paths:e.value.web_site_bottom_logo?[e.value.web_site_bottom_logo]:[],width:100,height:100},null,8,["paths"]),s("div",null,[s("div",ae,[t(U,{class:"margin-right-10",type:["image/jpg","image/jpeg","image/png"],size:1,modelValue:e.value.web_site_bottom_logo,"onUpdate:modelValue":a[2]||(a[2]=l=>e.value.web_site_bottom_logo=l)},null,8,["modelValue"]),t(k,{modelValue:e.value.web_site_bottom_logo,"onUpdate:modelValue":a[3]||(a[3]=l=>e.value.web_site_bottom_logo=l),class:"margin-right-20"},null,8,["modelValue"])]),te])])]),_:1}),t(i,{xs:24,sm:12,md:8},{default:o(()=>[se,s("div",oe,[t(w,{class:"margin-right-20",paths:e.value.web_site_customer_service?[e.value.web_site_customer_service]:[],width:100,height:100},null,8,["paths"]),s("div",null,[s("div",de,[t(U,{class:"margin-right-10",type:["image/jpg","image/jpeg","image/png"],size:1,modelValue:e.value.web_site_customer_service,"onUpdate:modelValue":a[4]||(a[4]=l=>e.value.web_site_customer_service=l)},null,8,["modelValue"]),t(k,{modelValue:e.value.web_site_customer_service,"onUpdate:modelValue":a[5]||(a[5]=l=>e.value.web_site_customer_service=l),class:"margin-right-20"},null,8,["modelValue"])]),ie])])]),_:1}),t(i,{xs:24,sm:12,md:6},{default:o(()=>[me,t(u,{modelValue:e.value.web_site_title,"onUpdate:modelValue":a[6]||(a[6]=l=>e.value.web_site_title=l),placeholder:"请输入网站标题"},null,8,["modelValue"])]),_:1}),t(j,{"content-position":"left"},{default:o(()=>[b("验证码")]),_:1}),t(i,{xs:24,sm:12,md:6},{default:o(()=>[ue,t(S,{style:{width:"100%"},"active-value":1,"inactive-value":0,modelValue:e.value.verify_code_is_open,"onUpdate:modelValue":a[7]||(a[7]=l=>e.value.verify_code_is_open=l)},null,8,["modelValue"])]),_:1}),e.value.verify_code_is_open?(n(),_(i,{key:0,xs:24,sm:12,md:6},{default:o(()=>[ne,t(I,{style:{width:"100%"},modelValue:e.value.send_code_mode,"onUpdate:modelValue":a[8]||(a[8]=l=>e.value.send_code_mode=l),placeholder:"请选择验证码发送方式"},{default:o(()=>[t(h,{label:"短信",value:1}),t(h,{label:"邮箱",value:2})]),_:1},8,["modelValue"])]),_:1})):c("",!0),e.value.send_code_mode===1?(n(),_(i,{key:1,xs:24,sm:12,md:6},{default:o(()=>[_e,t(u,{modelValue:e.value.ali_sms_key,"onUpdate:modelValue":a[9]||(a[9]=l=>e.value.ali_sms_key=l),placeholder:"请输入阿里短信key"},null,8,["modelValue"])]),_:1})):c("",!0),e.value.send_code_mode===1?(n(),_(i,{key:2,xs:24,sm:12,md:6},{default:o(()=>[re,t(u,{modelValue:e.value.ali_sms_secret,"onUpdate:modelValue":a[10]||(a[10]=l=>e.value.ali_sms_secret=l),placeholder:"请输入阿里短信secret"},null,8,["modelValue"])]),_:1})):c("",!0),e.value.send_code_mode===1?(n(),_(i,{key:3,xs:24,sm:12,md:6},{default:o(()=>[pe,t(u,{modelValue:e.value.ali_sms_sign_name,"onUpdate:modelValue":a[11]||(a[11]=l=>e.value.ali_sms_sign_name=l),placeholder:"请输入阿里短信签名"},null,8,["modelValue"])]),_:1})):c("",!0),e.value.send_code_mode===2?(n(),_(i,{key:4,xs:24,sm:12,md:6},{default:o(()=>[ve,t(u,{modelValue:e.value.mail_from_name,"onUpdate:modelValue":a[12]||(a[12]=l=>e.value.mail_from_name=l),placeholder:"请输入发信名称"},null,8,["modelValue"])]),_:1})):c("",!0),e.value.send_code_mode===2?(n(),_(i,{key:5,xs:24,sm:12,md:6},{default:o(()=>[ce,t(u,{modelValue:e.value.mail_host,"onUpdate:modelValue":a[13]||(a[13]=l=>e.value.mail_host=l),placeholder:"请输入服务器地址"},null,8,["modelValue"])]),_:1})):c("",!0),e.value.send_code_mode===2?(n(),_(i,{key:6,xs:24,sm:12,md:6},{default:o(()=>[ge,t(u,{modelValue:e.value.mail_port,"onUpdate:modelValue":a[14]||(a[14]=l=>e.value.mail_port=l),placeholder:"端口"},null,8,["modelValue"])]),_:1})):c("",!0),e.value.send_code_mode===2?(n(),_(i,{key:7,xs:24,sm:12,md:6},{default:o(()=>[fe,t(u,{modelValue:e.value.mail_from_address,"onUpdate:modelValue":a[15]||(a[15]=l=>e.value.mail_from_address=l),placeholder:"请输入发信地址"},null,8,["modelValue"])]),_:1})):c("",!0),e.value.send_code_mode===2?(n(),_(i,{key:8,xs:24,sm:12,md:6},{default:o(()=>[Ve,t(u,{modelValue:e.value.mail_username,"onUpdate:modelValue":a[16]||(a[16]=l=>e.value.mail_username=l),placeholder:"请输入发信账号"},null,8,["modelValue"])]),_:1})):c("",!0),e.value.send_code_mode===2?(n(),_(i,{key:9,xs:24,sm:12,md:6},{default:o(()=>[ye,t(u,{modelValue:e.value.mail_password,"onUpdate:modelValue":a[17]||(a[17]=l=>e.value.mail_password=l),placeholder:"请输入发信密码"},null,8,["modelValue"])]),_:1})):c("",!0),t(j,{"content-position":"left"},{default:o(()=>[b(" 支付配置")]),_:1}),t(i,{xs:24,sm:12,md:6},{default:o(()=>[be,t(u,{modelValue:e.value.wechat_pay_app_id,"onUpdate:modelValue":a[18]||(a[18]=l=>e.value.wechat_pay_app_id=l),placeholder:"请输入微信商户appId"},null,8,["modelValue"])]),_:1}),t(i,{xs:24,sm:12,md:6},{default:o(()=>[xe,t(u,{modelValue:e.value.wechat_pay_mch_id,"onUpdate:modelValue":a[19]||(a[19]=l=>e.value.wechat_pay_mch_id=l),placeholder:"请输入微信商户号"},null,8,["modelValue"])]),_:1}),t(i,{xs:24,sm:12,md:6},{default:o(()=>[he,t(u,{modelValue:e.value.wechat_pay_secret_key,"onUpdate:modelValue":a[20]||(a[20]=l=>e.value.wechat_pay_secret_key=l),placeholder:"请输入微信支付密钥"},null,8,["modelValue"])]),_:1}),t(i,{xs:24,sm:12,md:6},{default:o(()=>[we,t(u,{modelValue:e.value.wechat_pay_private_cert,"onUpdate:modelValue":a[21]||(a[21]=l=>e.value.wechat_pay_private_cert=l),placeholder:"请输入微信支付私钥证书"},null,8,["modelValue"])]),_:1}),t(i,{xs:24,sm:12,md:6},{default:o(()=>[Ue,t(u,{modelValue:e.value.wechat_pay_certificate,"onUpdate:modelValue":a[22]||(a[22]=l=>e.value.wechat_pay_certificate=l),placeholder:"请输入微信支付公钥证书"},null,8,["modelValue"])]),_:1})]),_:1}),s("div",ke,[t(C,{type:"primary",style:{width:"200px"},onClick:D,loading:v.value},{default:o(()=>[b("保存")]),_:1},8,["loading"])])]),_:1}),q((n(),_(M,{title:"分销配置",class:"m-t-20"},{default:o(()=>[t(N,{"label-width":"200px",disabled:v.value},{default:o(()=>[t(B,{gutter:20},{default:o(()=>[t(i,{xs:24,sm:12,md:6},{default:o(()=>[je,t(S,{style:{width:"100%"},modelValue:m.value.is_give_vip,"onUpdate:modelValue":a[23]||(a[23]=l=>m.value.is_give_vip=l)},null,8,["modelValue"])]),_:1}),t(i,{xs:24,sm:12,md:6},{default:o(()=>[Se,t(I,{modelValue:m.value.give_vip_id,"onUpdate:modelValue":a[24]||(a[24]=l=>m.value.give_vip_id=l),placeholder:"注册赠送会员",style:{width:"100%"}},{default:o(()=>[(n(!0),z(H,null,J(x.value,l=>(n(),_(h,{key:l.id,label:l.name,value:l.id},null,8,["label","value"]))),128))]),_:1},8,["modelValue"])]),_:1}),t(i,{xs:24,sm:12,md:6},{default:o(()=>[Ie,t(f,{style:{width:"100%"},modelValue:m.value.give_vip_days,"onUpdate:modelValue":a[25]||(a[25]=l=>m.value.give_vip_days=l),min:1,max:365},null,8,["modelValue"])]),_:1}),t(i,{xs:24,sm:12,md:6},{default:o(()=>[Be,t(f,{style:{width:"100%"},modelValue:m.value.recommend_commission_1,"onUpdate:modelValue":a[26]||(a[26]=l=>m.value.recommend_commission_1=l),min:0},null,8,["modelValue"])]),_:1}),t(i,{xs:24,sm:12,md:6},{default:o(()=>[Ce,t(f,{style:{width:"100%"},modelValue:m.value.recommend_commission_2,"onUpdate:modelValue":a[27]||(a[27]=l=>m.value.recommend_commission_2=l),min:0},null,8,["modelValue"])]),_:1}),t(i,{xs:24,sm:12,md:6},{default:o(()=>[Me,t(f,{style:{width:"100%"},modelValue:m.value.member_pay_commission_1,"onUpdate:modelValue":a[28]||(a[28]=l=>m.value.member_pay_commission_1=l)},null,8,["modelValue"])]),_:1}),t(i,{xs:24,sm:12,md:6},{default:o(()=>[Oe,t(f,{style:{width:"100%"},modelValue:m.value.member_pay_commission_2,"onUpdate:modelValue":a[29]||(a[29]=l=>m.value.member_pay_commission_2=l)},null,8,["modelValue"])]),_:1})]),_:1})]),_:1},8,["disabled"]),s("div",Le,[t(C,{type:"primary",style:{width:"200px"},onClick:A,loading:v.value},{default:o(()=>[b("保存")]),_:1},8,["loading"])])]),_:1})),[[E,v.value]])])}}});const Xe=G(ze,[["__scopeId","data-v-f6d3e756"]]);export{Xe as default};
