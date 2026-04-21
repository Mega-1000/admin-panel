/* eslint-disable @typescript-eslint/no-explicit-any */
/* eslint-disable no-new */

import Vue from 'vue'
import store from '@/store'

export default class RenderComponent {
  public static render (component: any, hookElement: string): void {
    if (document.querySelector(hookElement)) {
      new Vue({
        store,
        render: h => h(component)
      }).$mount(hookElement)
      const mountElement = !(document.querySelectorAll(hookElement + ':empty').length > 0)
      this.renderComponentLog(hookElement, mountElement)
    }
  }

  public static renderComponents (components: any, hookElement: string): void {
    if (document.querySelector(hookElement)) {
      new Vue({
        store,
        components: components
      }).$mount(hookElement)
      const mountElement = !(document.querySelectorAll(hookElement + ':empty').length > 0)
      this.renderComponentLog(hookElement, mountElement)
    }
  }

  public static startRender (): void {
    console.groupCollapsed('%c Render  %c views ',
      'background:#35495e; padding: 1px; border-radius: 3px 0 0 3px;  color: #fff',
      'background:#41b883; padding: 1px; border-radius: 0 3px 3px 0;  color: #fff'
    )
  }

  public static endRender (): void {
    console.groupEnd()
  }

  public static renderComponentLog (component: string, success: boolean): void {
    const name = component.replace('#', '')
    if (success) {
      console.log('%c [Render] Component ' + name + ' %c success ',
        'background:#35495e; padding: 1px; border-radius: 3px 0 0 3px;  color: #fff',
        'background:#41b883; padding: 1px; border-radius: 0 3px 3px 0;  color: #fff'
      )
    } else {
      console.log('%c [Render] Component ' + name + ' %c failed ',
        'background:#35495e; padding: 1px; border-radius: 3px 0 0 3px;  color: #fff',
        'background:#9c1014; padding: 1px; border-radius: 0 3px 3px 0;  color: #fff'
      )
    }
  }
}
