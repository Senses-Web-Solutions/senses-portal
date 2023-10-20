/** @type {import('tailwindcss').Config} */
const colors = require('tailwindcss/colors');
const defaultTheme = require('tailwindcss/defaultTheme');

const customColours = ['blue', 'gray', 'green', 'purple', 'red', 'yellow', 'primary', 'secondary', 'success', 'info', 'danger', 'warning'];
const customColourVariants = ['dark', 'darker', 'light', 'lighter', '50', '100', '200', '300', '400', '500', '600', '700', '800', '900'];
const prefixes = ['hover:bg', 'bg', 'hover:border', 'border', 'hover:text', 'text', 'bg-stripes'];

const customColourSafelist = [];

prefixes.forEach((prefix) => {
    customColours.forEach((colour) => {
        customColourSafelist.push(`${prefix}-${colour}`);

        customColourVariants.forEach((colourVariant) => {
            customColourSafelist.push(`${prefix}-${colour}-${colourVariant}`);
        });
    });
});

export default {
  darkMode: 'class',

  safelist: [
      ...customColourSafelist
  ],

  content: [
    './clients/*/Resources/js/**/*.vue',
    './clients/*/Resources/js/**/*.js',
    './clients/*/Resources/views/**/*.blade.php',
    './node_modules/@senses/builder/src/**/*.vue',
  ],

  theme: {
    extend: {
      nightwind: {
        // transitionDuration: false,
        typography: true,

        colors: {
          white: 'slate.900',
          black: 'gray.50',
        },
      },

      screens: {
        '3xl': '1920px',
        '4xl': '2560px',
      },

      // Updating lighter, light, default, dark & darker will require the php enum for Colour to be updated with correct hex values!
      colors: () => {
        const purple = {
          ...colors.purple,
          50: '#f5f3ff',
          100: '#F5E9FF',
          200: '#D6CAE9',
          300: '#C4B3E2',
          400: '#BDA9E0',
          500: '#A68CD1',
          600: '#886CB3',
          700: '#72569d',
          800: '#4F4779',
          900: '#4B4376',

          lighter: '#F5E9FF', // 100
          light: '#C4B3E2', // 300
          DEFAULT: '#A68CD1', // 500
          dark: '#72569d', // 700
          darker: '#4F4779', // 800
        };

        const red = {
          50: '#FCF9F7',
          100: '#FCF2EE',
          200: '#F9DDD8',
          300: '#F6BEB5',
          400: '#F58C7C',
          500: '#F4604D',
          600: '#E73E31',
          700: '#BF2F2D',
          800: '#902529',
          900: '#6F1F23',

          lighter: '#FCF2EE', // 100
          light: '#F6BEB5', // 300
          DEFAULT: '#F4604D', // 500
          dark: '#BF2F2D', // 700
          darker: '#902529', // 800
        };

        const yellow = {
          50: '#FBFAF6',
          100: '#FAF6E9',
          200: '#F4E9C6',
          300: '#EED492',
          400: '#E3AE4D',
          500: '#D68724',
          600: '#B66115',
          700: '#884917',
          800: '#63371A',
          900: '#4B2C19',

          lighter: '#FAF6E9', // 100
          light: '#EED492', // 300
          DEFAULT: '#D68724', // 500
          dark: '#884917', // 700
          darker: '#63371A', // 800
        };

        const olive = {
          50: '#F9FAF8',
          100: '#F6F7EF',
          200: '#EAEDD8',
          300: '#DADBB5',
          400: '#B8BA7C',
          500: '#8E9549',
          600: '#69702F',
          700: '#50562B',
          800: '#3C4128',
          900: '#2F3323',

          lighter: '#F6F7EF', // 100
          light: '#DADBB5', // 300
          DEFAULT: '#8E9549', // 500
          dark: '#50562B', // 700
          darker: '#3C4128', // 800
        };

        const blue = {
          50: '#F5F9FB',
          100: '#E9F5FA',
          200: '#CDE6F5',
          300: '#ABD1F2',
          400: '#76ADED',
          500: '#4683E7',
          600: '#335FD7',
          700: '#2D4AB3',
          800: '#263A84',
          900: '#1F2F65',

          lighter: '#E9F5FA', // 100
          light: '#ABD1F2', // 300
          DEFAULT: '#4683E7', // 500
          dark: '#2D4AB3', // 700
          darker: '#263A84', // 800
        };

        const gray = {
          50: '#F9FAFB',
          100: '#F3F4F6',
          200: '#E5E7EB',
          300: '#D1D5DB',
          400: '#9CA3AF',
          500: '#6B7280',
          600: '#4B5563',
          700: '#374151',
          800: '#1F2937',
          900: '#111827',

          lighter: '#F3F4F6', // 100
          light: '#D1D5DB', // 300
          DEFAULT: '#6B7280', // 500
          dark: '#374151', // 700
          darker: '#1F2937', // 800
        };

        const green = {
          50: '#F0FDF4',
          100: '#DCFCE7',
          200: '#BBF7D0',
          300: '#86EFAC',
          400: '#4ADE80',
          500: '#22C55E',
          600: '#16A34A',
          700: '#15803D',
          800: '#166534',
          900: '#14532D',

          lighter: '#DCFCE7', // 100
          light: '#86EFAC', // 300
          DEFAULT: '#467A2A', // 500
          dark: '#15803D', // 700
          darker: '#166534', // 800
        };

        return {
          purple,
          red,
          yellow,
          green,
          olive,
          blue,
          gray,

          primary: gray,
          secondary: colors.zinc,

          info: blue,

          danger: red,
          warning: colors.amber,
          success: colors.emerald,
        };
      },

      cursor: {
        'col-resize': 'col-resize',
      },

      fontFamily: {
        sans: ['Inter', ...defaultTheme.fontFamily.sans],
        reg: ['charlesWright', 'sans-serif'],
        graphik: ['graphik', 'sans-serif'],
        TWCen: ['TWCen', 'sans-serif'],
        vagRund: ['vagRund', 'sans-serif'],
        gothic: ['gothic', 'sans-serif'],
        tcen: ['tcen', 'sans-serif'],
      },

      transitionDelay: {
        variable: 'var(--tw-transition-delay)',
      },
    },
  },

  plugins: [
    require('@tailwindcss/typography'),
    require('@tailwindcss/forms'),
    require('@tailwindcss/aspect-ratio'),
  ],
}
