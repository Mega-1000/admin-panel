/* eslint-disable no-new */
/* eslint-disable @typescript-eslint/no-var-requires */

import Vue from 'vue'
import SetsList from '@/views/Sets/SetsList.vue'
import SetEdit from '@/views/Sets/SetEdit.vue'
import store from './store'
import VueCookies from 'vue-cookies-reactive'
import RenderComponent from '@/helpers/renderComponent'
import ActionTrackers from '@/components/ActionTrackers.vue'
import LogsTrackerList from '@/views/LogsTracker/LogsTrackerList.vue'

Vue.config.productionTip = false
Vue.use(require('electron-vue-debugger'))
Vue.use(VueCookies)
Vue.$cookies.config('1d')
Vue.config.devtools = true

const components = {
  tracker: ActionTrackers
}

RenderComponent.startRender()
/* Render Components */
RenderComponent.renderComponents(components, '.vue-components')

/* LOGS TRACKER */
RenderComponent.render(ActionTrackers, '#actionTracker')
RenderComponent.render(LogsTrackerList, '#logsTrackerList')

/* SETS */
RenderComponent.render(SetsList, '#setsList')
RenderComponent.render(SetEdit, '#setEdit')

RenderComponent.endRender()
