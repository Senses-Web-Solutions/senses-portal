/** @type {import('tailwindcss').Config} */
const colors = require('tailwindcss/colors');
const defaultTheme = require('tailwindcss/defaultTheme');

const customColours = ['blue', 'gray', 'green', 'purple', 'red', 'yellow', 'orange', 'sky', 'violet', 'lime', 'primary', 'secondary', 'success', 'info', 'danger', 'warning'];
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

module.exports = {
  darkMode: 'class',

  safelist: [
      ...customColourSafelist
  ],

  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    './node_modules/@senses/builder/src/**/*.vue',
  ],

  theme: {
    extend: {
      nightwind: {
        transitionClasses: false,
        transitionDuration: false,
        typography: true,

        colors: {
          white: 'zinc.900',
          black: 'zinc.50',
        },
      },

      // boxShadow: {
      //     'md': '0 4px 6px -1px rgba(0, 0, 0, 0.05)',
      // },

      borderRadius: {
        '3xl': '2.5rem',
      },

      screens: {
        '3xl': '1920px',
        '4xl': '2560px',
      },

      //updating lighter,light,default,dark,darker will require php enum for Colour to be updated with correct hex values!
      colors: () => {
        const purple = {
          ...colors.purple,
          // 50: '#EDECFE',
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
          50: '#FEFCE8',
          100: '#FEF9C3',
          200: '#FEF08A',
          300: '#FDE047',
          400: '#FACC15',
          500: '#EAB308',
          600: '#CA8A04',
          700: '#A16207',
          800: '#854D0E',
          900: '#713F12',
          950: '#422006',

          lighter: '#FEFCE8', // 100
          light: '#FDE047', // 300
          DEFAULT: '#EAB308', // 500
          dark: '#A16207', // 700
          darker: '#854D0E', // 800
        };

        const orange = {
          50: '#FFF7ED',
          100: '#FFEDD5',
          200: '#FED7AA',
          300: '#FDBA74',
          400: '#FB923C',
          500: '#F97316',
          600: '#EA580C',
          700: '#C2410C',
          800: '#9A3412',
          900: '#7C2D12',
          950: '#431407',

          lighter: '#FFEDD5', // 100
          light: '#FDBA74', // 300
          DEFAULT: '#F97316', // 500
          dark: '#C2410C', // 700
          darker: '#9A3412', // 800
        }

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
          DEFAULT: '#22C55E', // 500
          dark: '#15803D', // 700
          darker: '#166534', // 800
        };

        const lime = {
          50: '#F7FEE7',
          100: '#ECFCCB',
          200: '#D9F99D',
          300: '#BEF264',
          400: '#A3E635',
          500: '#84CC16',
          600: '#65A30D',
          700: '#4D7C0F',
          800: '#3F6212',
          900: '#365314',
          950: '#1A2E05',

          lighter: '#ECFCCB', // 100
          light: '#BEF264', // 300
          DEFAULT: '#84CC16', // 500
          dark: '#4D7C0F', // 700
          darker: '#3F6212', // 800
        };

        const sky = {
          50: '#F0F9FF',
          100: '#E0F2FE',
          200: '#BAE6FD',
          300: '#7DD3FC',
          400: '#38BDF8',
          500: '#0EA5E9',
          600: '#0284C7',
          700: '#0369A1',
          800: '#075985',
          900: '#0C4A6E',
          950: '#082F49',

          lighter: '#E0F2FE', // 100
          light: '#7DD3FC', // 300
          DEFAULT: '#0EA5E9', // 500
          dark: '#0369A1', // 700
          darker: '#075985', // 800
        };

        const violet = {
          50: '#F5F3FF',
          100: '#EDE9FE',
          200: '#DDD6FE',
          300: '#C4B5FD',
          400: '#A78BFA',
          500: '#8B5CF6',
          600: '#7C3AED',
          700: '#6D28D9',
          800: '#5B21B6',
          900: '#4C1D95',
          950: '#2E1065',

          lighter: '#EDE9FE', // 100
          light: '#C4B5FD', // 300
          DEFAULT: '#8B5CF6', // 500
          dark: '#6D28D9', // 700
          darker: '#5B21B6', // 800
        };

        return {
          purple,
          red,
          yellow,
          green,
          olive,
          blue,
          gray,
          orange,
          sky,
          lime,
          violet,

          primary: purple,
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

      fontSize: {
        xs: ['0.66rem', '0.75rem'],
        sm: ['0.75rem', '1rem'],
        base: ['0.875rem', '1.25rem'],
        lg: ['1rem', '1.5rem'],
        xl: ['1.125rem', '1.75rem'],
        '2xl': ['1.25rem', '1.75rem'],
        '3xl': ['1.5rem', '2rem'],
        '4xl': ['1.875rem', '2.25rem'],
        '5xl': ['2.25rem', '2.5rem'],
        '6xl': ['3rem', '1'],
        '7xl': ['3.75rem', '1'],
        '8xl': ['4.5rem', '1'],
        '9xl': ['6rem', '1'],
        '10xl': ['8rem', '1'],
      },

      keyframes: {
        leftToRight: {
          '0%': { transform: 'translateX(-200%)' },
          '100%': { transform: 'translateX(200%)' },
        },

        gradient: {
          '0%': {
            'background-size': '200% 200%',
            'background-position': 'left center',
          },
          '100%': {
            'background-size': '200% 200%',
            'background-position': 'right center',
          },
        },

        stripedBgLeftToRight: {
          '0%': {
            'background-position': '0 center',
          },
          '100%': {
            'background-position': '32px center',
          },
        },
      },

      animation: {
        'left-to-right': 'leftToRight 1s linear infinite',
        'striped-bg-left-to-right':
          'stripedBgLeftToRight 0.5s linear infinite',
        gradient: 'gradient 3s linear infinite',
      },

      maxWidth: ({ theme }) => ({
        ...theme('spacing'),
      }),

      minWidth: ({ theme }) => ({
        ...theme('spacing'),
      }),

      maxHeight: ({ theme }) => ({
        ...theme('spacing'),
      }),

      minHeight: ({ theme }) => ({
        ...theme('spacing'),
      }),

      transitionDelay: {
        variable: 'var(--tw-transition-delay)',
      },

      padding: {
        table: '0.5rem',
      },

      // VERY IMPORTANT: Keep the spacing scale PROPORTIONAL
      // w-16 will always be (2 * w-8) or (4 * w-2)
      spacing: {
        18: '4.5rem',
        96: '24rem',
        112: '28rem',
        128: '32rem',
        px: '1px',
        0: '0',
        0.5: '0.125rem',
        1: '0.25rem',
        1.5: '0.375rem',
        2: '0.5rem',
        2.5: '0.625rem',
        3: '0.75rem',
        3.5: '0.875rem',
        4: '1rem',
        5: '1.25rem',
        6: '1.5rem',
        7: '1.75rem',
        8: '2rem',
        9: '2.25rem',
        10: '2.5rem',
        11: '2.75rem',
        12: '3rem',
        14: '3.5rem',
        16: '4rem',
        20: '5rem',
        24: '6rem',
        28: '7rem',
        32: '8rem',
        36: '9rem',
        40: '10rem',
        44: '11rem',
        48: '12rem',
        52: '13rem',
        56: '14rem',
        60: '15rem',
        64: '16rem',
        72: '18rem',
        80: '20rem',
        96: '24rem',
        'content': 'calc(100vh - 4rem)'
      },

      zIndex: {
        '-10': '-10',
        60: '60',
        70: '70',
      },
    },
  },

  plugins: [
    require('@tailwindcss/typography'),
    require('@tailwindcss/forms'),
    require('@tailwindcss/aspect-ratio'),
    require('nightwind'),

    // function ({ addUtilities, theme }) {
    //     let backgroundSize = '32px 32px';
    //     let backgroundImage = (color) =>
    //         `linear-gradient(135deg, ${color} 10%, transparent 10%, transparent 50%, ${color} 50%, ${color} 60%, transparent 60%, transparent 100%)`;
    //     let colors = Object.entries(theme('backgroundColor')).filter(
    //         ([, value]) =>
    //             typeof value === 'object' && value[400] && value[500]
    //     );

    //     addUtilities(
    //         Object.fromEntries(
    //             colors.map(([name, colors]) => {
    //                 let backgroundColor = colors[100]; // 10% opacity -> + '1a'
    //                 let stripeColor = colors[500] + '1e'; // 30% opacity

    //                 return [
    //                     `.bg-stripes-${name}`,
    //                     {
    //                         backgroundColor,
    //                         backgroundImage: backgroundImage(stripeColor),
    //                         backgroundSize,
    //                     },
    //                 ];
    //             })
    //         )
    //     );
    // },
  ],
}
