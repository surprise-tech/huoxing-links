import { userStore } from '@/stores'
import type { Directive, DirectiveBinding } from 'vue'

export default {
  mounted(el: HTMLElement, binding: DirectiveBinding<string | string[]>) {
    const permission = Array.isArray(binding.value) ? binding.value : [binding.value]
    if (!userStore.hasPermission(permission)) {
      el.parentNode?.removeChild(el)
    }
  }
} as Directive
