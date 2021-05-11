/* eslint-disable no-new */
/* eslint-disable @typescript-eslint/no-var-requires */

import Vue from 'vue'
import App from './App.vue'
import SetsList from '@/views/Sets/SetsList.vue'
import SetEdit from '@/views/Sets/SetEdit.vue'
import store from './store'
import RenderComponent from '@/helpers/renderComponent'

Vue.config.productionTip = false
Vue.use(require('electron-vue-debugger'))

new Vue({
  store,
  render: h => h(App)
}).$mount('#app')

RenderComponent.startRender()
new RenderComponent(App, '#app')
new RenderComponent(SetsList, '#setsList')
new RenderComponent(SetEdit, '#setEdit')
RenderComponent.endRender()
