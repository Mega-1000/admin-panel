/* eslint-disable no-new */
/* eslint-disable @typescript-eslint/no-var-requires */

import Vue from 'vue'
import App from './App.vue'
import SetsList from '@/views/Sets/SetsList.vue'
import SetEdit from '@/views/Sets/SetEdit.vue'
import OrderDatesTable from '@/components/Orders/Dates/DatesTable.vue'
import store from './store'
import RenderComponent from '@/helpers/renderComponent'
import ActionTrackers from '@/components/ActionTrackers.vue'
import LogsTrackerList from '@/views/LogsTracker/LogsTrackerList.vue'

Vue.config.productionTip = false
Vue.use(require('electron-vue-debugger'))
Vue.config.devtools = true

new Vue({
  store,
  render: h => h(App)
}).$mount('#app')

RenderComponent.startRender()
new RenderComponent(App, '#app')
/* LOGS TRACKER */
new RenderComponent(ActionTrackers, '#actionTracker')
new RenderComponent(LogsTrackerList, '#logsTrackerList')

/* SETS */
new RenderComponent(SetsList, '#setsList')
new RenderComponent(SetEdit, '#setEdit')
RenderComponent.endRender()
