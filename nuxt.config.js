export default defineNuxtConfig({
  ssr: false,
  //'@productdevbook/chatwoot'
  modules: ["@nuxtjs/tailwindcss", "nuxt-icon", "@nuxt/devtools", "nuxt-gtag", 'nuxt-module-hotjar'],
  gtag: {
    tags: [
      {
        id: 'G-24K8JMGMKW'
      },
      {
        id: 'AW-16473353139'
      }
    ]
  },
  hotjar: {
    hotjarId: 5017434,
    scriptVersion: 6,
  },
  devtools: {
    enabled: true,
    vscode: {}
  },

  chatwoot: {
    init: {
      websiteToken: 'QZL2X1noKmm71rRcGyb259NF'
    },
    // settings: {
    //   locale: 'pl',
    //   position: 'right',
    //   launcherTitle: 'Możemy w czymś pomóc?',
    //   // ... and more settings
    // },
    // If this is loaded you can make it true, https://github.com/nuxt-modules/partytown
    partytown: false,
  },
  runtimeConfig: {
    public: {
      AUTH_CLIENT_ID: process.env.AUTH_CLIENT_ID,
      AUTH_CLIENT_SECRET: process.env.AUTH_CLIENT_SECRET,
      baseUrl: process.env.APP_STORAGE,
      nuxtNewFront: process.env.NEW_NUXT_SERVER,
      google_analytics_id: process.env.google_analytics_id
    }
  },
  useHead: {
    title: "EPH Polska - styropiany, systemy elewacyjne, ocieplenia"
  }
});
