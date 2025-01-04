import{d as N,x as F,j as u,u as H,s as I,r as d,X as j,b as z,i as t,w as r,g as c,Z as G,o as _,Y as k,a as B,_ as W,l as b,f as X,E as w}from"./.pnpm-9b0f0a98.js";import{u as R}from"./propData-3c6625bc.js";import{c as P,v as Y,A as Z,b as J,_ as K}from"./index-2667bde9.js";import{i as q}from"./imageCaptcha-9c0c15c1.js";import O from"./PayButton-7bba7641.js";import"./pay-b89b25ad.js";const Q={class:"reg-footer"},ee=N({__name:"register",props:{visible:{type:Boolean},close_btn:{type:Boolean}},emits:["update:visible"],setup($,{emit:A}){const y=$;F();const p=R(()=>y.visible,!1),D=R(()=>y.close_btn,!1),h=u(),m=u(!1),o=u({phone:"",password:"",confirmPassword:"",authCode:"",agent:""}),v=u();v.value=P.code_mode===1?/^1\d{10}$/:/^\w+([.-]?\w+)*@\w+([.-]?\w+)*(\.\w{2,3})+$/;const i=u();i.value=P.code_mode===1?"手机号":"邮箱";const T={phone:[{required:!0,message:`请输入${i.value}`,trigger:"blur"},{pattern:v.value,message:`请输入正确的${i.value}`,trigger:"blur"}],password:[{required:!0,message:"请输入密码",trigger:"blur"},{min:6,max:16,message:"长度在 6 到 16 个字符",trigger:"blur"}],confirmPassword:[{required:!0,message:"请再次输入密码",trigger:"blur"},{validator:(f,e,s)=>{e!==o.value.password?s(new Error("两次输入密码不一致")):s()},trigger:"blur"}],authCode:[{required:!0,message:"请输入验证码",trigger:"blur"}],agent:[{required:!0,message:"请选择代理类型",trigger:"blur"}]},g=u(!1),U=async f=>{if(!v.value.test(o.value.phone))return w.error(`请输入正确的${i.value}！`);const e=f.target;q.show(function(s){q.hide(),J({tel:o.value.phone,captcha:s.code,key:s.key}).then(()=>{g.value=!0,w.success("发送成功");const n=function(){let l=e.getAttribute("data-time");l<=0&&(l=60),l--,e.innerHTML=l+"秒后重新获取",e.setAttribute("data-time",l),l>0?setTimeout(n,1e3):(g.value=!1,e.innerHTML="发送验证码")};n()})})},V=H(),C=I(),E=u(),M=()=>{Y(h.value).then(()=>{m.value=!0,Z({username:o.value.phone,captcha:o.value.authCode,password:o.value.password,password_confirmation:o.value.confirmPassword,agent_id:o.value.agent,referral_code:V.query.code}).then(f=>{w.success("注册成功，请登录"),localStorage.setItem("register","register"),p.value=!1,C.push("/login")}).finally(()=>{m.value=!1})})};return(f,e)=>{const s=d("el-input"),n=d("el-form-item"),l=d("el-button"),S=d("el-form"),L=d("el-drawer"),x=j("loading");return _(),z("div",null,[t(L,{modelValue:c(p),"onUpdate:modelValue":e[6]||(e[6]=a=>G(p)?p.value=a:null),class:"register_drawer",modal:!1,title:"注册账号","show-close":!c(D),onClose:e[7]||(e[7]=a=>A("update:visible",!1))},{default:r(()=>[k((_(),B(S,{ref_key:"formRef",ref:h,model:o.value,rules:T,"label-position":"left","label-width":"80px"},{default:r(()=>[t(n,{prop:"phone",label:i.value},{default:r(()=>[t(s,{modelValue:o.value.phone,"onUpdate:modelValue":e[0]||(e[0]=a=>o.value.phone=a),placeholder:"请输入"+i.value},null,8,["modelValue","placeholder"])]),_:1},8,["label"]),t(n,{prop:"password",label:"密码"},{default:r(()=>[t(s,{modelValue:o.value.password,"onUpdate:modelValue":e[1]||(e[1]=a=>o.value.password=a),type:"password",placeholder:"请输入密码","show-password":""},null,8,["modelValue"])]),_:1}),t(n,{prop:"confirmPassword",label:"确认密码"},{default:r(()=>[t(s,{modelValue:o.value.confirmPassword,"onUpdate:modelValue":e[2]||(e[2]=a=>o.value.confirmPassword=a),type:"password",placeholder:"请再次输入密码"},null,8,["modelValue"])]),_:1}),t(n,{prop:"authCode",label:"验证码"},{default:r(()=>[t(s,{modelValue:o.value.authCode,"onUpdate:modelValue":e[4]||(e[4]=a=>o.value.authCode=a),placeholder:"请输入验证码"},{suffix:r(()=>[t(l,{class:"code-btn",disabled:g.value,type:"primary",onClick:e[3]||(e[3]=W(a=>U(a),["stop"]))},{default:r(()=>[b(" 发送验证码 ")]),_:1},8,["disabled"])]),_:1},8,["modelValue"])]),_:1})]),_:1},8,["model"])),[[x,m.value]]),k((_(),B(l,{class:"sign-btn",type:"primary",onClick:M,disabled:!c(V).query.code},{default:r(()=>[b(" 立即注册 ")]),_:1},8,["disabled"])),[[x,m.value]]),X("div",Q,[t(l,{type:"primary",link:"",onClick:e[5]||(e[5]=a=>c(C).push("/login"))},{default:r(()=>[b("已有账号，去登录！")]),_:1})])]),_:1},8,["modelValue","show-close"]),t(O,{ref_key:"payBtnRef",ref:E,type:"pay-agent",info:{}},null,512)])}}});const ne=K(ee,[["__scopeId","data-v-ed02f84b"]]);export{ne as default};
