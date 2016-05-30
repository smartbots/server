var app_url = 'http://dev.env/smartbots';

var
    elixir = require('laravel-elixir'),
    gulp = require('gulp'),
    gulpif = require('gulp-if');

var vendor_dir = 'resources/assets/vendor/',
    libs_dir = 'public/libs/',
    js_dir = 'public/js/',
    css_dir = 'public/css/';

var less = { // LESS file to compile => css
    'components.less' : 'components.css',
    'core.less' : 'core.css',
    'pages.less' : 'pages.css',
    'responsive.less' : 'responsive.css'
},
    js = { // JS file vendored => libs
    'jquery/dist/jquery.js' : 'jquery/jquery.js',
    'bootstrap/dist/js/bootstrap.js' : 'bootstrap/js/bootstrap.js',
    'jquery.scrollTo/jquery.scrollTo.js' : 'jquery.scrollTo/jquery.scrollTo.js',
    'jquery.nicescroll/jquery.nicescroll.min.js' : 'jquery.nicescroll/jquery.nicescroll.js',
    'slimscroll/jquery.slimscroll.js' : 'slimscroll/jquery.slimscroll.js',
    'fastclick/lib/fastclick.js' : 'fastclick/fastclick.js',
    'blockUI/jquery.blockUI.js' : 'blockUI/jquery.blockUI.js',
    'Waves/dist/waves.js' : 'Waves/waves.js',
    'wow/dist/wow.js' : 'wow/wow.js',
    'sweetalert/dist/sweetalert.min.js' : 'sweetalert/sweetalert.js'
},
    css = { // CSS file vendored => libs
    'bootstrap/dist/css/bootstrap.css' : 'bootstrap/css/bootstrap.css',
    'font-awesome/css/font-awesome.css' : 'font-awesome/css/font-awesome.css',
    'Waves/dist/waves.css' : 'Waves/waves.css',
    'sweetalert/dist/sweetalert.css' : 'sweetalert/sweetalert.css',
    'animate.css/animate.css' : 'animate.css/animate.css'
},
    assets = { // Assets file & folder vendored => libs
    'bootstrap/dist/fonts' : 'bootstrap/fonts',
    'font-awesome/fonts' : 'font-awesome/fonts'
},
    jsx = { // JS (not vendored) files => js
    'jquery.custom.js' : 'jquery.custom.js',
    'jquery.core.js' : 'jquery.core.js',
    'jquery.app.js' : 'jquery.app.js',
};

elixir(function(mix) {

    for(var key in less) {
        mix.less(key, css_dir+less[key], vendor_dir);
    }

    for(var key in js) {
        mix.scripts(key, libs_dir+js[key], vendor_dir);
    }

    for(var key in jsx) {
        mix.scripts(key, js_dir+jsx[key]);
    }

    for(var key in css) {
        mix.styles(key, libs_dir+css[key], vendor_dir);
    }

    for (var key in assets) {
        mix.copy(vendor_dir+key, libs_dir+assets[key]);
    }

    mix.browserSync({
        // online: false,
        notify: false,
        open: false,
        proxy: app_url,
        ghostMode: {
            clicks: true,
            forms: true,
            scroll: true
        }
    });
});
