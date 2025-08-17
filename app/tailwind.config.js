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
      colors: {
        morocco: {
          red: '#C1272D',      // Moroccan Red
          blue: '#005CA8',     // Majorelle Blue
          green: '#007A3D',    // Emerald Moroccan Green
          yellow: '#FFD700',   // Golden Yellow
          ivory: '#FAF3E0',    // Ivory Beige
        },
      },
    },
  },
  plugins: [
    forms,
    typography,
  ],
};
