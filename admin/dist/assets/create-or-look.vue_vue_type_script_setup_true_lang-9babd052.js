import{u as I}from"./form-e5ba5cf1.js";import{d as v,j as N,r as s,X as U,Y as w,g as e,o as R,a as y,w as a,i as u,l as d,f as q}from"./.pnpm-9b0f0a98.js";const C=q("h3",null,"权限配置",-1),A=v({__name:"create-or-look",setup(Q){const i=N(),{formData:l,formLoading:p}=I({formRef:i,defVal(t){return{name:t==null?void 0:t.name,price:t!=null&&t.price?t==null?void 0:t.price:0,level:t!=null&&t.level?t==null?void 0:t.level:0,config:t!=null&&t.config?t==null?void 0:t.config:{pre_min:!1,min_disabled_check:!1,support:!1,cur_index:!1,allow_type:{MINI_PROGRAM:!1,KING_DOC:!1,CLI_QR:!1,WORK_WECHAT:!1,LANDING_MINI:!1,QR_QQ:!1}}}}}),g={name:[{required:!0,message:"请输入套餐名称",trigger:"blur"}],price:[{required:!0,message:"请输入套餐价格",trigger:"blur"}],level:[{required:!0,message:"请填写等级",trigger:"blur"}]};return(t,o)=>{const _=s("el-input"),n=s("el-form-item"),f=s("el-input-number"),V=s("el-divider"),m=s("el-checkbox"),b=s("el-form"),c=U("loading");return w((R(),y(b,{model:e(l),rules:g,ref_key:"formRef",ref:i,"label-width":"180px"},{default:a(()=>[u(n,{prop:"name",label:"套餐名称"},{default:a(()=>[u(_,{modelValue:e(l).name,"onUpdate:modelValue":o[0]||(o[0]=r=>e(l).name=r),placeholder:"输入套餐名称"},null,8,["modelValue"])]),_:1}),u(n,{prop:"price",label:"价格/月"},{default:a(()=>[u(_,{modelValue:e(l).price,"onUpdate:modelValue":o[1]||(o[1]=r=>e(l).price=r),placeholder:"0.00"},null,8,["modelValue"])]),_:1}),u(n,{prop:"level",label:"等级"},{default:a(()=>[u(f,{modelValue:e(l).level,"onUpdate:modelValue":o[2]||(o[2]=r=>e(l).level=r),placeholder:"0"},null,8,["modelValue"])]),_:1}),C,u(V),u(n,{rules:[{required:!0,message:"请输入链接数量限制",trigger:"blur"}],label:"链接数量限制"},{default:a(()=>[u(f,{modelValue:e(l).config.count_limit,"onUpdate:modelValue":o[3]||(o[3]=r=>e(l).config.count_limit=r),placeholder:"0"},null,8,["modelValue"])]),_:1}),u(n,{rules:[{required:!0,message:"请输入UV限制",trigger:"blur"}],label:"UV限制"},{default:a(()=>[u(f,{modelValue:e(l).config.uv_limit,"onUpdate:modelValue":o[4]||(o[4]=r=>e(l).config.uv_limit=r),placeholder:"0"},null,8,["modelValue"])]),_:1}),u(n,{rules:[{required:!0,message:"请输入自建小程序池可创建数量",trigger:"blur"}],label:"自建小程序池可创建数量"},{default:a(()=>[u(f,{modelValue:e(l).config.min_count_limit,"onUpdate:modelValue":o[5]||(o[5]=r=>e(l).config.min_count_limit=r),placeholder:"0"},null,8,["modelValue"])]),_:1}),u(n,{rules:[{required:!0,message:"请选择创建链接类型",trigger:"blur"}],label:"创建链接类型"},{default:a(()=>[u(m,{modelValue:e(l).config.allow_type.MINI_PROGRAM,"onUpdate:modelValue":o[6]||(o[6]=r=>e(l).config.allow_type.MINI_PROGRAM=r),label:!0},{default:a(()=>[d("跳转到小程序")]),_:1},8,["modelValue"]),u(m,{modelValue:e(l).config.allow_type.LANDING_MINI,"onUpdate:modelValue":o[7]||(o[7]=r=>e(l).config.allow_type.LANDING_MINI=r),label:!0},{default:a(()=>[d("跳转到微信二维码")]),_:1},8,["modelValue"]),u(m,{modelValue:e(l).config.allow_type.WORK_WECHAT,"onUpdate:modelValue":o[8]||(o[8]=r=>e(l).config.allow_type.WORK_WECHAT=r),label:!0},{default:a(()=>[d("跳转到企业微信")]),_:1},8,["modelValue"]),u(m,{modelValue:e(l).config.allow_type.KING_DOC,"onUpdate:modelValue":o[9]||(o[9]=r=>e(l).config.allow_type.KING_DOC=r),label:!0},{default:a(()=>[d("跳转到金山文档")]),_:1},8,["modelValue"]),u(m,{modelValue:e(l).config.allow_type.CLI_QR,"onUpdate:modelValue":o[10]||(o[10]=r=>e(l).config.allow_type.CLI_QR=r),label:!0},{default:a(()=>[d("跳转到草料二维码")]),_:1},8,["modelValue"]),u(m,{modelValue:e(l).config.allow_type.QR_QQ,"onUpdate:modelValue":o[11]||(o[11]=r=>e(l).config.allow_type.QR_QQ=r),label:!0},{default:a(()=>[d("跳转到腾讯优码")]),_:1},8,["modelValue"])]),_:1}),u(n,{rules:[{required:!0,message:"请选择链接保留时长",trigger:"blur"}],label:"链接保留时长"},{default:a(()=>[u(m,{"model-value":!0,disabled:""},{default:a(()=>[d("永久")]),_:1})]),_:1}),u(n,{rules:[{required:!0,message:"请选择官方小程序池使用",trigger:"blur"}],label:"官方小程序池使用"},{default:a(()=>[u(m,{modelValue:e(l).config.pre_min,"onUpdate:modelValue":o[12]||(o[12]=r=>e(l).config.pre_min=r),label:!0},{default:a(()=>[d("支持")]),_:1},8,["modelValue"])]),_:1}),u(n,{rules:[{required:!0,message:"请选择自建小程序池下架自动检测",trigger:"blur"}],label:"自建小程序池下架自动检测"},{default:a(()=>[u(m,{modelValue:e(l).config.min_disabled_check,"onUpdate:modelValue":o[13]||(o[13]=r=>e(l).config.min_disabled_check=r),label:!0},{default:a(()=>[d("支持")]),_:1},8,["modelValue"])]),_:1}),u(n,{rules:[{required:!0,message:"请选择免费技术协助",trigger:"blur"}],label:"免费技术协助"},{default:a(()=>[u(m,{modelValue:e(l).config.support,"onUpdate:modelValue":o[14]||(o[14]=r=>e(l).config.support=r),label:!0},{default:a(()=>[d("支持")]),_:1},8,["modelValue"])]),_:1}),u(n,{rules:[{required:!0,message:"请选择数据统计/分析",trigger:"blur"}],label:"数据统计/分析"},{default:a(()=>[u(m,{"model-value":!0,disabled:""},{default:a(()=>[d("支持")]),_:1})]),_:1}),u(n,{rules:[{required:!0,message:"请选择自定义落地页",trigger:"blur"}],label:"自定义落地页"},{default:a(()=>[u(m,{modelValue:e(l).config.cur_index,"onUpdate:modelValue":o[15]||(o[15]=r=>e(l).config.cur_index=r),label:!0},{default:a(()=>[d("支持")]),_:1},8,["modelValue"])]),_:1})]),_:1},8,["model"])),[[c,e(p)]])}}});export{A as _};