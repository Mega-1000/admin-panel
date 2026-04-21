/* eslint-disable no-alert, no-console */
/* eslint @typescript-eslint/no-var-requires: "off" */
const StyleLintPlugin = require('stylelint-webpack-plugin')

module.exports = {
  runtimeCompiler: true,
  configureWebpack: {
    plugins: [
      new StyleLintPlugin({
        files: ['src/**/*.{vue,scss}']
      })
    ]
  }
}
/* eslint-enable no-alert, no-console */
