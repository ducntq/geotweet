var elixir = require('laravel-elixir');
process.env.DISABLE_NOTIFIER = true;
/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function(mix) {
    mix.sass([
        'app.scss'
    ]);

    mix.scripts([
        '../bower/jquery/dist/jquery.js',
        '../bower/bootstrap-sass/assets/javascripts/bootstrap.js'
    ], 'public/js/vendor.js');

    mix.scripts(
        [
            'main.js'
        ], 'public/js/main.js'
    );
});
