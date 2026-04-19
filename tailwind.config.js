const defaultTheme = require('tailwindcss/defaultTheme')

module.exports = {
    content: [
        './resources/js/Pages/**/*.vue',
        './resources/js/Pages/**/**/*.vue',
        './resources/js/Shared/*.vue',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter var', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                indigo: {
                    '900': '#191e38',
                    '800': '#2f365f',
                    '600': '#5661b3',
                    '500': '#6574cd',
                    '400': '#7886d7',
                    '300': '#b2b7ff',
                    '100': '#e6e8ff',
                },
            },
            boxShadow: theme => ({
                outline: '0 0 0 2px ' + theme('colors.indigo.500'),
            }),
            fill: theme => theme('colors'),
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
        require('@tailwindcss/aspect-ratio'),
    ],
}
