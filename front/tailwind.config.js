const defaultTheme = require('tailwindcss/defaultTheme');

/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
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

