// plugins/analytics.ts
import { defineNuxtPlugin } from '#app'

export default defineNuxtPlugin((nuxtApp) => {
    // @ts-ignore
    nuxtApp.$router.afterEach((to, from) => {
        if (typeof window !== 'undefined' && window.gtag) {
            window.gtag('config', 'G-24K8JMGMKW', {
                page_path: to.fullPath,
                page_title: to.name,
                page_location: window.location.href,
            });
        }
    });
});
