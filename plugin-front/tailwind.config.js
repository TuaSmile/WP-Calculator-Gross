const projectPaths = require('./webpack/project.paths.js');

const forbiddenKeys = ['projectDir', 'projectScssPath', 'projectImagesPath', 'projectOutput', 'projectWebpack'];

const availablePaths = Object.entries(projectPaths)
  .reduce((acc, el) => {
    const [key, value] = el;

    if(forbiddenKeys.includes(key)) return acc;

    acc.push(`${value}/**/*.{html,js,twig,php}`);
    return acc;
  }, []);

module.exports = {
  content: availablePaths,
  theme: {
    extend: {},
  },
  variants: {
    extend: {},
  },
  plugins: [
    require('@tailwindcss/forms'),
  ],
}
