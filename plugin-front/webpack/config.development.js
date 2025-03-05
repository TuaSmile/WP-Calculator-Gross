/**
 * Webpack configurations for the development environment
 * based on the script from package.json
 * Run with: "npm run dev" or "npm run dev:watch"
 *
 * @since 1.0.0
 */

module.exports = (projectOptions) => {
  process.env.NODE_ENV = 'development' // Set environment level to 'development'

  /**
   * The base skeleton
   */
  const Base = require('./config.base')(projectOptions)

  /**
   * CSS rules
   */
  const cssRules = {
    ...Base.cssRules,
    ...{
      // add CSS rules for development here
    },
  }

  /**
   * JS rules
   */
  const jsRules = {
    ...Base.jsRules,
    ...{
      // add JS rules for development here
    },
  }

  /**
   * Image rules
   */
  const imageRules = {
    ...Base.imageRules,
    ...{
      // add image rules for development here
    },
  }

  /**
   * File rules
   */
  const fileRules = {
    ...Base.fileRules,
    ...{
      // add image rules for production here
    },
  }

  /**
   * Optimizations rules
   */
  const optimizations = {
    ...Base.optimizations,
    ...{
      // add optimizations rules for development here
    },
  }

  /**
   * Plugins
   */
  const plugins = [
    ...Base.plugins
  ]


  /***
   * Add sourcemap for development if enabled
   */
  const sourceMap = { devtool: false }
  if (
    projectOptions.projectSourceMaps.enable === true &&
    (projectOptions.projectSourceMaps.env === 'dev' ||
      projectOptions.projectSourceMaps.env === 'dev-prod')
  ) {
    sourceMap.devtool = projectOptions.projectSourceMaps.devtool
  }

  /**
   * The configuration that's being returned to Webpack
   */
  return {
    mode: 'development',
    entry: projectOptions.projectJs.entry, // Define the starting point of the application.
    output: {
      path: projectOptions.projectOutput,
      filename: projectOptions.projectJs.filename,
      assetModuleFilename: 'assets/[hash][ext][query]',
    },
    devtool: sourceMap.devtool,
    optimization: optimizations,
    module: { rules: [cssRules, jsRules, imageRules, fileRules] },
    plugins: plugins,
  }
}
