import{d as L,x as F,j as u,u as H,s as I,r as d,X as j,b as z,i as a,w as r,g as f,Z as G,o as c,Y as B,a as b,_ as W,l as y,h as X,f as Y,E as h}from"./.pnpm-0d809032.js";import{u as R}from"./propData-9c18804a.js";import{c as v,v as Z,A as J,b as K,_ as O}from"./index-eed46f6d.js";import{i as P}from"./imageCaptcha-0ae09b82.js";import Q from"./PayButton-076155ed.js";import"./pay-e9e47bae.js";const ee={class:"reg-footer"},oe=L({__name:"register",props:{visible:{type:Boolean},close_btn:{type:Boolean}},emits:["update:visible"],setup($,{emit:q}){const V=$;F();const p=R(()=>V.visible,!1),A=R(()=>V.close_btn,!1),C=u(),m=u(!1),o=u({phone:"",password:"",confirmPassword:"",authCode:"",agent:""}),g=u(/^\w+([.-]?\w+)*@\w+([.-]?\w+)*(\.\w{2,3})+$/),i=u("邮箱");v.verify_code_is_open===1&&(g.value=v.code_mode===1?/^1\d{10}$/:/^\w+([.-]?\w+)*@\w+([.-]?\w+)*(\.\w{2,3})+$/,i.value=v.code_mode===1?"手机号":"邮箱");const D={phone:[{required:!0,message:`请输入${i.value}`,trigger:"blur"},{pattern:g.value,message:`请输入正确的${i.value}`,trigger:"blur"}],password:[{required:!0,message:"请输入密码",trigger:"blur"},{min:6,max:16,message:"长度在 6 到 16 个字符",trigger:"blur"}],confirmPassword:[{required:!0,message:"请再次输入密码",trigger:"blur"},{validator:(w,e,s)=>{e!==o.value.password?s(new Error("两次输入密码不一致")):s()},trigger:"blur"}],authCode:[{required:!0,message:"请输入验证码",trigger:"blur"}],agent:[{required:!0,message:"请选择代理类型",trigger:"blur"}]},_=u(!1),T=async w=>{if(!g.value.test(o.value.phone))return h.error(`请输入正确的${i.value}！`);const e=w.target;P.show(function(s){P.hide(),K({tel:o.value.phone,captcha:s.code,key:s.key}).then(()=>{_.value=!0,h.success("发送成功");const n=function(){let l=e.getAttribute("data-time");l<=0&&(l=60),l--,e.innerHTML=l+"秒后重新获取",e.setAttribute("data-time",l),l>0?setTimeout(n,1e3):(_.value=!1,e.innerHTML="发送验证码")};n()})})},U=H(),x=I(),E=u(),M=()=>{Z(C.value).then(()=>{m.value=!0,J({username:o.value.phone,captcha:o.value.authCode,password:o.value.password,password_confirmation:o.value.confirmPassword,agent_id:o.value.agent,referral_code:U.query.code}).then(()=>{h.success("注册成功，请登录"),localStorage.setItem("register","register"),p.value=!1,x.push("/login")}).finally(()=>{m.value=!1})})};return(w,e)=>{const s=d("el-input"),n=d("el-form-item"),l=d("el-button"),N=d("el-form"),S=d("el-drawer"),k=j("loading");return c(),z("div",null,[a(S,{modelValue:f(p),"onUpdate:modelValue":e[6]||(e[6]=t=>G(p)?p.value=t:null),class:"register_drawer",modal:!1,title:"注册账号","show-close":!f(A),onClose:e[7]||(e[7]=t=>q("update:visible",!1))},{default:r(()=>[B((c(),b(N,{ref_key:"formRef",ref:C,model:o.value,rules:D,"label-position":"left","label-width":"80px"},{default:r(()=>[a(n,{prop:"phone",label:i.value},{default:r(()=>[a(s,{modelValue:o.value.phone,"onUpdate:modelValue":e[0]||(e[0]=t=>o.value.phone=t),placeholder:"请输入"+i.value},null,8,["modelValue","placeholder"])]),_:1},8,["label"]),a(n,{prop:"password",label:"密码"},{default:r(()=>[a(s,{modelValue:o.value.password,"onUpdate:modelValue":e[1]||(e[1]=t=>o.value.password=t),type:"password",placeholder:"请输入密码","show-password":""},null,8,["modelValue"])]),_:1}),a(n,{prop:"confirmPassword",label:"确认密码"},{default:r(()=>[a(s,{modelValue:o.value.confirmPassword,"onUpdate:modelValue":e[2]||(e[2]=t=>o.value.confirmPassword=t),type:"password",placeholder:"请再次输入密码"},null,8,["modelValue"])]),_:1}),f(v).verify_code_is_open===1?(c(),b(n,{key:0,prop:"authCode",label:"验证码"},{default:r(()=>[a(s,{modelValue:o.value.authCode,"onUpdate:modelValue":e[4]||(e[4]=t=>o.value.authCode=t),placeholder:"请输入验证码"},{suffix:r(()=>[a(l,{class:"code-btn",disabled:_.value,type:"primary",onClick:e[3]||(e[3]=W(t=>T(t),["stop"]))},{default:r(()=>[y(" 发送验证码 ")]),_:1},8,["disabled"])]),_:1},8,["modelValue"])]),_:1})):X("",!0)]),_:1},8,["model"])),[[k,m.value]]),B((c(),b(l,{class:"sign-btn",type:"primary",onClick:M},{default:r(()=>[y(" 立即注册 ")]),_:1})),[[k,m.value]]),Y("div",ee,[a(l,{type:"primary",link:"",onClick:e[5]||(e[5]=t=>f(x).push("/login"))},{default:r(()=>[y("已有账号，去登录！")]),_:1})])]),_:1},8,["modelValue","show-close"]),a(Q,{ref_key:"payBtnRef",ref:E,type:"pay-agent",info:{}},null,512)])}}});const ue=O(oe,[["__scopeId","data-v-71ec65e8"]]);export{ue as default};
