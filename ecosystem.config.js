module.exports = {
    apps: [
        {
            name: 'mega1000.pl',
            exec_mode: 'cluster',
            instances: '1',
            script: './.output/server/index.mjs'
        }
    ]
}
