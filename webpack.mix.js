const path = require('path');
const mix = require('laravel-mix');
const tailwindcss = require('tailwindcss')
const cssImport = require('postcss-import')
const cssNesting = require('postcss-nesting')

mix.js('resources/js/app.js', 'public/js')
    .postCss('resources/css/app.css', 'public/css')
    .options({
        postCss: [
            cssImport(),
            cssNesting(),
            tailwindcss('tailwind.config.js'),
        ]
    })
    .webpackConfig({
        output: {chunkFilename: 'js/[name].js?id=[chunkhash]'},
        resolve: {
            alias: {
                '@': path.resolve('resources/js'),
            },
        },
    })
    .version()
    .sourceMaps();


