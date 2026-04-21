module.exports = {
  env: {
    browser: true,
    node: true
  },
  rules: {
    '@typescript-eslint/no-var-requires': 0,
    '@typescript-eslint/no-empty-function': ['error', { allow: ['constructors'] }],
    '@typescript-eslint/ban-ts-comment': 'off'
  }
}
