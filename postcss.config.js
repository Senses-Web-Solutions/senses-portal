require('dotenv').config();

module.exports = {
  plugins: [
    // require('tailwindcss')(`./clients/${process.env.SENSES_CLIENT}/Resources/css/tailwind.config.js`),
    require('tailwindcss')(`./tailwind.config.js`),

    require('autoprefixer'),
  ],
}
