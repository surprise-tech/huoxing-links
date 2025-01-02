import{u as A}from"./propData-3c6625bc.js";import{c as g,b as R,v as U,e as $,_ as E}from"./index-31481636.js";import{i as _}from"./imageCaptcha-c6e048e7.js";import{d as B,j as d,r as i,b as D,i as o,w as r,g as L,Z as M,o as S,l as b,E as h}from"./.pnpm-9b0f0a98.js";const F=B({__name:"resetPassword",props:{visible:{type:Boolean}},emits:["update:visible"],setup(V,{emit:C}){const y=V,p=A(()=>y.visible,!1),w=d(),m=d(!1),a=d({phone:"",password:"",confirmPassword:"",authCode:"",agent:"1"}),f=d();f.value=g.code_mode===1?/^1\d{10}$/:/^\w+([.-]?\w+)*@\w+([.-]?\w+)*(\.\w{2,3})+$/;const n=d();n.value=g.code_mode===1?"手机号":"邮箱";const x={phone:[{required:!0,message:"请输入手机号",trigger:"blur"},{pattern:f.value,message:`请输入正确的${n.value}`,trigger:"blur"}],password:[{required:!0,message:"请输入密码",trigger:"blur"},{min:6,max:16,message:"长度在 6 到 16 个字符",trigger:"blur"}],confirmPassword:[{required:!0,message:"请再次输入密码",trigger:"blur"},{validator:(c,e,s)=>{e!==a.value.password?s(new Error("两次输入密码不一致")):s()},trigger:"blur"}],authCode:[{required:!0,message:"请输入验证码",trigger:"blur"}],agent:[{required:!0,message:"请选择代理类型",trigger:"blur"}]},v=d(!1),P=async c=>{if(!f.value.test(a.value.phone))return h.error(`请输入正确的${n.value}！`);const e=c.target;_.show(s=>{_.hide(),R({tel:a.value.phone,captcha:s.code,key:s.key}).then(()=>{v.value=!0;const u=function(){let t=e.getAttribute("data-time");t<=0&&(t=60),t--,e.innerHTML=t+"秒后重新获取",e.setAttribute("data-time",t),t>0?setTimeout(u,1e3):(v.value=!1,e.innerHTML="发送验证码")};u()})})},k=()=>{U(w.value).then(()=>{m.value=!0,$({username:a.value.phone,password:a.value.password,password_confirmation:a.value.confirmPassword,captcha:a.value.authCode}).then(()=>{h.success("修改成功，请登录"),p.value=!1}).finally(()=>{m.value=!1})})};return(c,e)=>{const s=i("el-input"),u=i("el-form-item"),t=i("el-button"),T=i("el-form"),q=i("el-drawer");return S(),D("div",null,[o(q,{modelValue:L(p),"onUpdate:modelValue":e[5]||(e[5]=l=>M(p)?p.value=l:null),class:"register_drawer",modal:!1,title:"找回密码",onClose:e[6]||(e[6]=l=>C("update:visible",!1))},{default:r(()=>[o(T,{ref_key:"formRef",ref:w,model:a.value,rules:x,"label-position":"left","label-width":"100px",disabled:m.value},{default:r(()=>[o(u,{prop:"phone",label:n.value},{default:r(()=>[o(s,{modelValue:a.value.phone,"onUpdate:modelValue":e[0]||(e[0]=l=>a.value.phone=l),placeholder:"请输入"+n.value},null,8,["modelValue","placeholder"])]),_:1},8,["label"]),o(u,{prop:"authCode",label:"验证码"},{default:r(()=>[o(s,{modelValue:a.value.authCode,"onUpdate:modelValue":e[2]||(e[2]=l=>a.value.authCode=l),placeholder:"请输入验证码"},{suffix:r(()=>[o(t,{class:"code-btn",disabled:v.value,type:"primary",onClick:e[1]||(e[1]=l=>P(l))},{default:r(()=>[b("发送验证码")]),_:1},8,["disabled"])]),_:1},8,["modelValue"])]),_:1}),o(u,{prop:"password",label:"设置新密码"},{default:r(()=>[o(s,{modelValue:a.value.password,"onUpdate:modelValue":e[3]||(e[3]=l=>a.value.password=l),"show-password":"",type:"password",placeholder:"请输入密码"},null,8,["modelValue"])]),_:1}),o(u,{prop:"confirmPassword",label:"确认新密码"},{default:r(()=>[o(s,{modelValue:a.value.confirmPassword,"onUpdate:modelValue":e[4]||(e[4]=l=>a.value.confirmPassword=l),"show-password":"",type:"password",placeholder:"请再次输入密码"},null,8,["modelValue"])]),_:1})]),_:1},8,["model","disabled"]),o(t,{class:"sign-btn",type:"primary",onClick:k},{default:r(()=>[b("修改密码")]),_:1})]),_:1},8,["modelValue"])])}}});const I=E(F,[["__scopeId","data-v-6f489528"]]);export{I as default};
