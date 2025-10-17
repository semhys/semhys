module.exports = {
  content: ['./index.html', './src/**/*.{ts,tsx,js,jsx}'],
  theme: {
    extend: {
      colors: {
        // SEMHYS brand palette (shades approximated)
        semhys: {
          50: '#f3fbf8',
          100: '#dbf6ee',
          200: '#bff0dd',
          300: '#92e6c7',
          400: '#57d2a3',
          500: '#0F9B79',
          600: '#0d7f63',
          700: '#0b654e',
          800: '#08433a',
          900: '#052727'
        },
        accent: {
          50: '#fff6ef',
          100: '#ffe5d0',
          200: '#ffc8a1',
          300: '#ff9a60',
          400: '#ff7a35',
          500: '#F36C13',
          600: '#d85b10',
          700: '#9a3f0b',
          800: '#6b2c07',
          900: '#3a1603'
        },
        emerald: {
          50:'#f4fbf5',100:'#e7f6e4',200:'#cfecc8',300:'#b1e6a6',400:'#86dd73',500:'#6DBE45',600:'#5aa238',700:'#41782b',800:'#2b4e1d',900:'#16270f'
        },
        'brand-dark': '#0f172a',
        'brand-muted': '#64748b'
      },
      boxShadow: {
        'brand-lg': '0 10px 30px rgba(15,23,42,0.12)'
      },
      keyframes: {
        float: {
          '0%': { transform: 'translateY(0px)' },
          '50%': { transform: 'translateY(-6px)' },
          '100%': { transform: 'translateY(0px)' }
        }
      },
      animation: {
        float: 'float 6s ease-in-out infinite'
      }
      ,
      fontFamily: {
        sans: ['Inter', 'ui-sans-serif', 'system-ui'],
        serif: ['Merriweather', 'ui-serif', 'Georgia']
      },
      fontSize: {
        'xs': '.75rem',
        'sm': '.875rem',
        'base': '1rem',
        'lg': '1.125rem',
        'xl': '1.25rem',
        '2xl': '1.563rem',
        '3xl': '1.953rem',
        '4xl': '2.441rem',
        '5xl': '3.052rem'
      }
    }
  },
  plugins: [],
}
