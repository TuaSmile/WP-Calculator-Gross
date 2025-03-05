/**
 * Webpack configurations for the production environment
 * based on the script from package.json
 * Run with: "npm run prod" or or "npm run prod:watch"
 *
 * @since 1.0.0
 */
const glob = require('glob-all');
const { PurgeCSSPlugin } = require('purgecss-webpack-plugin'); // A tool to remove unused CSS
const ImageMinimizerPlugin = require('image-minimizer-webpack-plugin'); // To optimize (compress) all images using

module.exports = projectOptions => {
  process.env.NODE_ENV = 'production'; // Set environment level to 'production'

  /**
   * The base skeleton
   */
  const Base = require('./config.base')(projectOptions);

  /**
   * CSS rules
   */
  const cssRules = {
    ...Base.cssRules,
    ...{
      // add CSS rules for production here
    },
  };

  /**
   * JS rules
   */
  const jsRules = {
    ...Base.jsRules,
    ...{
      // add JS rules for production here
    },
  };

  /**
   * Image rules
   */
  const imageRules = {
    ...Base.imageRules,
    ...{
      // add image rules for production here
    },
  };

  /**
   * File rules
   */
  const fileRules = {
    ...Base.fileRules,
    ...{
      // add image rules for production here
    },
  };

  /**
   * Optimizations rules
   */
  const optimizations = {
    ...Base.optimizations,
    ...{
      splitChunks: {
        cacheGroups: {
          styles: {
            // Configured for PurgeCSS
            name: 'styles',
            test: /\.css$/,
            chunks: 'all',
            enforce: true,
          },
        },
      },
      // add optimizations rules for production here
    },
  };

  /**
   * Plugins
   */
  const plugins = [
    ...Base.plugins,
    ...[
      new PurgeCSSPlugin({
        // Scans files and removes unused CSS
        paths: glob.sync(projectOptions.projectCss.purgeCss.paths, {
          nodir: true,
        }),
        safelist: {
          deep: [/-\[(.*)\]$/],
        },
        // Fix for Purge CSS removing Tailwind class like 2xl:,lg: & !container etc.
        defaultExtractor: content => {
          const tags = content.match(/([\w-/:\!]|\[([\w*<>&]+)\])+(?<!:)/g);
          return tags || [];
        },
      }),
      new ImageMinimizerPlugin({
        // Optimizes images
        minimizer: [
          {
            implementation: ImageMinimizerPlugin.sharpMinify,
            options: projectOptions.projectImages.minimizerOptions,
          },
          {
            implementation: ImageMinimizerPlugin.svgoMinify,
            options: {
              encodeOptions: {
                // Pass over SVGs multiple times to ensure all optimizations are applied. False by default
                multipass: true,
                plugins: [
                  // set of built-in plugins enabled by default
                  // see: https://github.com/svg/svgo#default-preset
                  'preset-default',
                ],
              },
            },
          },
        ],
      }),
      // add plugins for production here
    ],
  ];

  /**
   * Add sourcemap for production if enabled
   */
  const sourceMap = { devtool: false };
  if (
    projectOptions.projectSourceMaps.enable === true &&
    (projectOptions.projectSourceMaps.env === 'prod' || projectOptions.projectSourceMaps.env === 'dev-prod')
  ) {
    sourceMap.devtool = projectOptions.projectSourceMaps.devtool;
  }

  /**
   * The configuration that's being returned to Webpack
   */
  return {
    mode: 'production',
    entry: projectOptions.projectJs.entry, // Define the starting point of the application.
    output: {
      path: projectOptions.projectOutput,
      filename: projectOptions.projectJs.filename,
      assetModuleFilename: 'assets/[hash][ext][query]',
      asyncChunks: true,
    },
    devtool: sourceMap.devtool,
    optimization: optimizations,
    module: { rules: [cssRules, jsRules, imageRules, fileRules] },
    plugins: plugins,
  };
};
