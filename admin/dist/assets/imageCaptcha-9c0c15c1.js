import{f as p}from"./index-2667bde9.js";let n=null,c="";const s=()=>{p().then(t=>{var i;c=t.key,(i=document.getElementById("Js_image_captcha"))==null||i.setAttribute("src",t.img)})};let e=document.getElementById("Js_image_warp");e&&document.body.removeChild(e);e=document.createElement("div");e.id="Js_image_warp";e.style.display="none";e.style.zIndex="-1";e.innerHTML='<div id="Js_image_main"><div id="Js_image_close">x</div><img id="Js_image_captcha" /><div id="Js_image_input_warp"><div id="Js_image_input_warp_fix"></div><input id="JS_image_input" placeholder="请输入图片验证码" type="text" /></div><div id="Js_image_btn">确定</div></div>';document.body.appendChild(e);const g=t=>{if(e){e.style.display="block",e.style.zIndex="9999",n=t,s(),document.getElementById("JS_image_input").value="";const i=document.getElementById("Js_image_input_warp_fix");i&&(i.style.zIndex="1")}},l=()=>{e&&(e.style.display="none",e.style.zIndex="-1",n=null)};var a;(a=document.getElementById("Js_image_btn"))==null||a.addEventListener("click",()=>{if(typeof n=="function"){const t=document.getElementById("JS_image_input").value;t&&n({key:c,code:t})}});var m;(m=document.getElementById("Js_image_close"))==null||m.addEventListener("click",()=>{l()});var _;(_=document.getElementById("Js_image_captcha"))==null||_.addEventListener("click",()=>{s()});const d=document.getElementById("Js_image_input_warp_fix");d&&d.addEventListener("click",()=>{d.style.zIndex="-1",document.getElementById("JS_image_input").focus()});const u={show:g,hide:l};export{u as i};
