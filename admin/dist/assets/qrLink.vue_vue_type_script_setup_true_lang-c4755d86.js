import{d as p,j as h,r as _,o as f,b as w,i as x,w as C,l as k,ao as v}from"./.pnpm-9b0f0a98.js";const D=p({__name:"qrLink",props:{modelValue:{}},emits:["update:modelValue"],setup(V,{emit:d}){const c=h(null),m=async()=>{const e=document.createElement("input");e.type="file",e.accept="image/*",e.click(),e.addEventListener("change",n=>{if(!n.target.files||!n.target.files[0])return;const o=n.target.files[0],l=new FileReader;l.onload=u=>{const g=u.target.result,t=document.createElement("canvas"),r=t.getContext("2d"),a=new Image;a.onload=()=>{t.width=a.naturalWidth,t.height=a.naturalHeight,r.drawImage(a,0,0);const s=r.getImageData(0,0,t.width,t.height),i=v(s.data,s.width,s.height);i?(c.value=i.data,d("update:modelValue",c.value)):alert("无法识别二维码，请确保上传的是有效的二维码图片。")},a.src=g},l.readAsDataURL(o)})};return(e,n)=>{const o=_("el-button");return f(),w("div",null,[x(o,{type:"primary",onClick:m},{default:C(()=>[k("选择图片")]),_:1})])}}});export{D as _};
