/* eslint-disable no-alert, no-console */
/* eslint @typescript-eslint/no-var-requires: "off" */
const StyleLintPlugin = require('stylelint-webpack-plugin')
const CopyPlugin = require('copy-webpack-plugin')

module.exports = {
  configureWebpack: {
    plugins: [
      new StyleLintPlugin({
        files: ['src/**/*.{vue,scss}']
      })
    ]
  }
}
/* eslint-enable no-alert, no-console */
