import Vue from 'vue'
import App from './App.vue'
import SetsList from '@/views/Sets/SetsList.vue'
import SetEdit from '@/views/Sets/SetEdit.vue'
import store from './store'

Vue.config.productionTip = false

new Vue({
  store,
  render: h => h(App)
}).$mount('#app')

if (document.querySelector('#setsList')) {
  new Vue({
    store,
    render: h => h(SetsList)
  }).$mount('#setsList')
}

if (document.querySelector('#setEdit')) {
  new Vue({
    store,
    render: h => h(SetEdit)
  }).$mount('#setEdit')
}
