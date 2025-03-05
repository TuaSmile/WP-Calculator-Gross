const projectDevDir = '../plugin-dev'

module.exports = {
  projectDir: '.', // Current project directory absolute path.
  projectWebpack: './webpack',
  projectJsPath: './assets/src/js',
  projectScssPath: './assets/src/sass',
  projectImagesPath: './assets/src/images',
  projectOutput: `${projectDevDir}/dist`,
  projectClasses: `${projectDevDir}/src`,
  projectTemplates: `${projectDevDir}/views`,
};
