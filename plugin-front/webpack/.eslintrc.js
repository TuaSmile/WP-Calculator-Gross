/**
 * Eslint config file
 * as configured in package.json under eslintConfig.extends
 *
 * @docs BabelJS: https://babeljs.io/
 * @docs Webpack babel-loader: https://webpack.js.org/loaders/babel-loader/
 * @since 1.0.0
 */
module.exports = {
    parser:  "@babel/eslint-parser",
    env:     {
        "es6":     true,
        "browser": true,
        "node":    true,
        "jquery":  true,
        "amd":     true
    },
    extends: [ "eslint:recommended", "prettier" ],
    rules:   {
        "no-unused-vars": ["error", {
            "argsIgnorePattern": "^_",
            "varsIgnorePattern": "^_",
            "caughtErrorsIgnorePattern": "^_"
        }]
    },
    globals: {
        "wp":     true,
        "jQuery": true,
    },
    ignorePatterns: [
        "tests/**/*.js",
        "temp.js",
        "/vendor/**/**/*.js",
        "/node_modules/**/**/*.js"
    ],
}