import{n as k,c as b}from"./index-1cb382aa.js";import{E as t}from"./element-plus-baf202ae.js";import{d as x,r as w,w as U,ag as u,o as m,H as C,I as _,a as o,Q as s,c as i,L as n,M as S}from"./@vue-d872a633.js";const B={style:{width:"500px"}},N={key:0},E={key:1},M={key:2},z={key:3},A={key:4},D={key:5},F={key:6},H={key:7},I={key:8},L={class:"m-b-20 text-center",style:{"margin-top":"60px"}},G=x({__name:"codeSetUp",props:{data:{}},setup(p){const e=w({verify_code_is_open:0,send_code_mode:1,ali_sms_key:"",ali_sms_secret:"",ali_sms_sign_name:"",mail_from_name:"",mail_host:"",mail_port:"",mail_from_address:"",mail_username:"",mail_password:""}),d=p;U(()=>d.data,()=>{e.value.verify_code_is_open=d.data.verify_code_is_open,e.value.send_code_mode=d.data.send_code_mode,e.value.ali_sms_key=d.data.ali_sms_key,e.value.ali_sms_secret=d.data.ali_sms_secret,e.value.ali_sms_sign_name=d.data.ali_sms_sign_name,e.value.mail_from_name=d.data.mail_from_name,e.value.mail_host=d.data.mail_host,e.value.mail_port=d.data.mail_port,e.value.mail_from_address=d.data.mail_from_address,e.value.mail_username=d.data.mail_username,e.value.mail_password=d.data.mail_password},{deep:!0,immediate:!0});const f=()=>{if(e.value.verify_code_is_open){if(!e.value.send_code_mode){t.error("请选择发送方式");return}if(e.value.send_code_mode===1){if(!e.value.ali_sms_key){t.error("请填写阿里短信key");return}if(!e.value.ali_sms_secret){t.error("请填写阿里短信secret");return}if(!e.value.ali_sms_secret){t.error("请填写阿里短信secret");return}}else if(e.value.send_code_mode===2){if(!e.value.mail_from_name){t.error("请填写发信名称");return}if(!e.value.mail_host){t.error("请填写服务器地址");return}if(!e.value.mail_port){t.error("请填写端口");return}if(!e.value.mail_from_address){t.error("请填写发信地址");return}if(!e.value.mail_username){t.error("请填写邮箱账号");return}if(!e.value.mail_password){t.error("请填写邮箱密码");return}}}k(e.value).then(()=>{t.success("保存成功"),b.refreshConfig()})};return(Q,l)=>{const y=u("el-switch"),v=u("el-option"),V=u("el-select"),r=u("el-input"),g=u("el-button"),c=u("el-card");return m(),C(c,null,{default:_(()=>[o("div",B,[o("div",null,[l[11]||(l[11]=o("div",{class:"margin-b-10",style:{"margin-top":"20px"}},"是否开启验证码",-1)),s(y,{style:{width:"100%"},"active-value":1,"inactive-value":0,modelValue:e.value.verify_code_is_open,"onUpdate:modelValue":l[0]||(l[0]=a=>e.value.verify_code_is_open=a)},null,8,["modelValue"])]),o("div",null,[l[12]||(l[12]=o("div",{class:"margin-b-10",style:{"margin-top":"20px"}},"验证码发送方式",-1)),s(V,{style:{width:"100%"},modelValue:e.value.send_code_mode,"onUpdate:modelValue":l[1]||(l[1]=a=>e.value.send_code_mode=a),placeholder:"请选择验证码发送方式"},{default:_(()=>[s(v,{label:"短信",value:1}),s(v,{label:"邮箱",value:2})]),_:1},8,["modelValue"])]),e.value.send_code_mode===1?(m(),i("div",N,[l[13]||(l[13]=o("div",{class:"margin-b-10",style:{"margin-top":"20px"}},"阿里短信key",-1)),s(r,{modelValue:e.value.ali_sms_key,"onUpdate:modelValue":l[2]||(l[2]=a=>e.value.ali_sms_key=a),placeholder:"请输入阿里短信key"},null,8,["modelValue"])])):n("",!0),e.value.send_code_mode===1?(m(),i("div",E,[l[14]||(l[14]=o("div",{class:"margin-b-10",style:{"margin-top":"20px"}},"阿里短信secret",-1)),s(r,{modelValue:e.value.ali_sms_secret,"onUpdate:modelValue":l[3]||(l[3]=a=>e.value.ali_sms_secret=a),placeholder:"请输入阿里短信secret"},null,8,["modelValue"])])):n("",!0),e.value.send_code_mode===1?(m(),i("div",M,[l[15]||(l[15]=o("div",{class:"margin-b-10",style:{"margin-top":"20px"}},"阿里短信签名",-1)),s(r,{modelValue:e.value.ali_sms_sign_name,"onUpdate:modelValue":l[4]||(l[4]=a=>e.value.ali_sms_sign_name=a),placeholder:"请输入阿里短信签名"},null,8,["modelValue"])])):n("",!0),e.value.send_code_mode===2?(m(),i("div",z,[l[16]||(l[16]=o("div",{class:"margin-b-10",style:{"margin-top":"20px"}},"发信名称",-1)),s(r,{modelValue:e.value.mail_from_name,"onUpdate:modelValue":l[5]||(l[5]=a=>e.value.mail_from_name=a),placeholder:"请输入发信名称"},null,8,["modelValue"])])):n("",!0),e.value.send_code_mode===2?(m(),i("div",A,[l[17]||(l[17]=o("div",{class:"margin-b-10",style:{"margin-top":"20px"}},"服务器地址",-1)),s(r,{modelValue:e.value.mail_host,"onUpdate:modelValue":l[6]||(l[6]=a=>e.value.mail_host=a),placeholder:"请输入服务器地址"},null,8,["modelValue"])])):n("",!0),e.value.send_code_mode===2?(m(),i("div",D,[l[18]||(l[18]=o("div",{class:"margin-b-10",style:{"margin-top":"20px"}},"端口",-1)),s(r,{modelValue:e.value.mail_port,"onUpdate:modelValue":l[7]||(l[7]=a=>e.value.mail_port=a),placeholder:"端口"},null,8,["modelValue"])])):n("",!0),e.value.send_code_mode===2?(m(),i("div",F,[l[19]||(l[19]=o("div",{class:"margin-b-10",style:{"margin-top":"20px"}},"发信地址",-1)),s(r,{modelValue:e.value.mail_from_address,"onUpdate:modelValue":l[8]||(l[8]=a=>e.value.mail_from_address=a),placeholder:"请输入发信地址"},null,8,["modelValue"])])):n("",!0),e.value.send_code_mode===2?(m(),i("div",H,[l[20]||(l[20]=o("div",{class:"margin-b-10",style:{"margin-top":"20px"}},"发信账号",-1)),s(r,{modelValue:e.value.mail_username,"onUpdate:modelValue":l[9]||(l[9]=a=>e.value.mail_username=a),placeholder:"请输入发信账号"},null,8,["modelValue"])])):n("",!0),e.value.send_code_mode===2?(m(),i("div",I,[l[21]||(l[21]=o("div",{class:"margin-b-10",style:{"margin-top":"20px"}},"发信密码",-1)),s(r,{modelValue:e.value.mail_password,"onUpdate:modelValue":l[10]||(l[10]=a=>e.value.mail_password=a),placeholder:"请输入发信密码"},null,8,["modelValue"])])):n("",!0),o("div",L,[s(g,{type:"primary",size:"large",onClick:f},{default:_(()=>l[22]||(l[22]=[S("保存")])),_:1})])])]),_:1})}}});export{G as _};
