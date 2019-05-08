const mix = require('laravel-mix');

const PATHS = {
    src: 'app',
    dist: 'web',
    config: 'app/app.scss',
    proxy: 'http://configurator.yii/'
};

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */
mix
    .disableNotifications()
    .setPublicPath(PATHS.dist)
    .js(`${PATHS.src}/app.js`, (mix.inProduction()) ? `${PATHS.dist}/js/app.min.js` : `${PATHS.dist}/js/app.js`) // VueJS
    .sass(`${PATHS.src}/app.scss`, (mix.inProduction()) ? `${PATHS.dist}/css/style.min.css` : `${PATHS.dist}/css/style.css`, { includePaths: ['./node_modules/'] })
    .copyDirectory(`${PATHS.src}/images`, `${PATHS.dist}/images`)
    .browserSync({
        ui: false,
        proxy: PATHS.proxy,
        files: [`${PATHS.dist}/*.*`],
    })
    .options({
        extractVueStyles: true,
        globalVueStyles: PATHS.config,
        terser: {
            terserOptions: {
                compress: {
                    drop_console: true,
                },
            },
        },
    });

if (!mix.inProduction()) {
    mix.extract(['vue']);
} else {
    // Application
    mix.babel(`${PATHS.dist}/js/app.min.js`, `${PATHS.dist}/js/app.min.js`);
    mix.postCss(`${PATHS.dist}/css/style.css`, `${PATHS.dist}/css/style.min.css`, [
        require('autoprefixer')({
            browsers: ['> 1%', 'last 3 versions', 'Firefox >= 20', 'iOS >=7'],
            grid: true,
        }),
        require('cssnano')({
            preset: ['default', {
                discardComments: {
                    removeAll: true,
                },
            }],
        }),
    ]);
}

mix.webpackConfig({});
