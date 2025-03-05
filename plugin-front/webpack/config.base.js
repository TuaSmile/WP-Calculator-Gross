/**
 * This holds the configuration that is being used for both development and production.
 * This is being imported and extended in the config.development.js and config.production.js files
 *
 * @since 1.1.0
 */
const magicImporter = require('node-sass-magic-importer') // Add magic import functionalities to SASS
const MiniCssExtractPlugin = require('mini-css-extract-plugin') // Extracts the CSS files into public/css
const WebpackBar = require('webpackbar') // Display elegant progress bar while building or watch
const ESLintPlugin = require('eslint-webpack-plugin') //  Find and fix problems in your JavaScript code
const StylelintPlugin = require('stylelint-webpack-plugin') // Helps you avoid errors and enforce conventions in your styles


module.exports = (projectOptions) => {
  /**
   * CSS Rules
   */
  const cssRules = {
    test:
      projectOptions.projectCss.use === 'sass'
        ? projectOptions.projectCss.rules.sass.test
        : projectOptions.projectCss.rules.postcss.test,
    exclude: /(node_modules|bower_components|vendor)/,
    use: [
      MiniCssExtractPlugin.loader, // Creates `style` nodes from JS strings
      // "css-loader",  // Translates CSS into CommonJS
      {
        loader: 'css-loader',
        options: {
          importLoaders: 2,
        },
      },
      {
        // loads the PostCSS loader
        loader: 'postcss-loader',
        options: require(projectOptions.projectCss.postCss)(projectOptions),
      },
    ],
  }

  if (projectOptions.projectCss.use === 'sass') {
    // if chosen Sass then we're going to add the Sass loader
    cssRules.use.push({
      // Compiles Sass to CSS
      loader: 'sass-loader',
      options: {
        sassOptions: { importer: magicImporter() }, // add magic import functionalities to sass
      },
    })
  }

  /**
   * JavaScript rules
   */
  const jsRules = {
    test: projectOptions.projectJs.rules.test,
    include: projectOptions.projectJsPath,
    exclude: /(node_modules|bower_components|vendor)/,
    use: 'babel-loader', // Configurations in "webpack/babel.config.js"
  }

  /**
   * Images rules
   */
  const imageRules = {
    test: projectOptions.projectImages.rules.test,
    type: 'asset/resource',
  }

  const fileRules = {
    test: /\.(woff(2)?|ttf|eot|otf)(\?v=\d+\.\d+\.\d+)?$/,
    type: 'asset/resource',
  }

  /**
   * Optimization rules
   */
  const optimizations = {}

  /**
   * Plugins
   */
  const plugins = [
    new WebpackBar(), // Adds loading bar during builds
    // Uncomment this to enable profiler https://github.com/nuxt-contrib/webpackbar#options
    // { reporters: [ 'profile' ], profile: true }
    new MiniCssExtractPlugin({
      // Extracts CSS files
      filename: projectOptions.projectCss.filename,
    }),
    new ESLintPlugin(),
    new StylelintPlugin()
  ]

  return {
    cssRules: cssRules,
    jsRules: jsRules,
    imageRules: imageRules,
    fileRules: fileRules,
    optimizations: optimizations,
    plugins: plugins,
  }
}
