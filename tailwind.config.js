/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./templates/**/*.php",
    "./modules/**/*.php",
    "./assets/js/**/*.js",
    "./index.php",
  ],
  theme: {
    extend: {
      colors: {
        'malaysia-blue': '#0052B4',
        'malaysia-blue-dark': '#003D87',
      }
    },
  },
  plugins: [],
}