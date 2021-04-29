import Vue from 'vue'
import App from './App.vue'
import SetsList from '@/views/Sets/SetsList.vue'
import store from './store'

Vue.config.productionTip = false

new Vue({
  store,
  render: h => h(App)
}).$mount('#app')

new Vue({
  store,
  render: h => h(SetsList)
}).$mount('#setsList')
