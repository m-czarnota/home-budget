const defaultTheme = require('tailwindcss/defaultTheme');

/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      boxShadow: {
        '3xl': '0 35px 60px -15px rgba(0, 0, 0, 0.3)',
      }
      // fontFamily: {
      //   'sans': ['Inter', ...defaultTheme.fontFamily.sans],
      //   'inter': ['Inter', 'sans-serif'],
      // },
    },
  },
  plugins: [
    // plugin(function({ addBase }) {
    //   addBase({
    //     '@font-face': {
    //       fontFamily: 'Inter',
    //       fontWeight: '400',
    //       src: 'url(https://fonts.gstatic.com/s/inter/v13/UcCO3FwrK3iLTeHuS_fvQtMwCp50KnMw2boKoduKmMEVuLyfAZJhiI2B.woff2) format(\'woff2\')',
    //     }
    //   });
    // }),
  ],
}

