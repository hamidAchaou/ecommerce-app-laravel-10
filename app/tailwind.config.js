/** @type {import('tailwindcss').Config} */
const typography = require('@tailwindcss/typography');
const forms = require('@tailwindcss/forms');

module.exports = {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
  ],
  theme: {
    extend: {
      // You can extend your theme here, e.g. colors, fonts, spacing...
    },
  },
  plugins: [
    forms,
    typography,
  ],
};
