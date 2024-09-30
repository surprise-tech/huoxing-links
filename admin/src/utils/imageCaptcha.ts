import { ApiGetImgCaptcha } from '@/api/comment'
let JSImageCaptchaCallback: any = null
let captchaKey = ''

const getImageCaptcha = () => {
  ApiGetImgCaptcha().then((res: any) => {
    captchaKey = res.key
    document.getElementById('Js_image_captcha')?.setAttribute('src', res.img)
  })
}

let warp = document.getElementById('Js_image_warp')
if (warp) {
  document.body.removeChild(warp)
}
warp = document.createElement('div')
warp.id = 'Js_image_warp'
warp.style.display = 'none'
warp.style.zIndex = '-1'
warp.innerHTML =
  '<div id="Js_image_main"><div id="Js_image_close">x</div><img id="Js_image_captcha" />' +
  '<div id="Js_image_input_warp"><div id="Js_image_input_warp_fix"></div><input id="JS_image_input" placeholder="请输入图片验证码" type="text" /></div>' +
  '<div id="Js_image_btn">确定</div></div>'
document.body.appendChild(warp)

const show = (callback: (res: any) => void) => {
  if (warp) {
    warp.style.display = 'block'
    warp.style.zIndex = '9999'
    JSImageCaptchaCallback = callback
    getImageCaptcha()
    ;(document.getElementById('JS_image_input') as HTMLInputElement).value = ''
    const Js_image_input_warp_fix = document.getElementById('Js_image_input_warp_fix')
    if (Js_image_input_warp_fix) {
      Js_image_input_warp_fix.style.zIndex = '1'
    }
  }
}

const hide = () => {
  if (warp) {
    warp.style.display = 'none'
    warp.style.zIndex = '-1'
    JSImageCaptchaCallback = null
  }
}

document.getElementById('Js_image_btn')?.addEventListener('click', () => {
  if (typeof JSImageCaptchaCallback === 'function') {
    const code = (document.getElementById('JS_image_input') as HTMLInputElement).value
    if (code) {
      JSImageCaptchaCallback({
        key: captchaKey,
        code
      })
    }
  }
})

document.getElementById('Js_image_close')?.addEventListener('click', () => {
  hide()
})
document.getElementById('Js_image_captcha')?.addEventListener('click', () => {
  getImageCaptcha()
})

const Js_image_input_warp_fix = document.getElementById('Js_image_input_warp_fix')
if (Js_image_input_warp_fix) {
  Js_image_input_warp_fix.addEventListener('click', () => {
    Js_image_input_warp_fix.style.zIndex = '-1'
    ;(document.getElementById('JS_image_input') as HTMLInputElement).focus()
  })
}

export default {
  show,
  hide
}
