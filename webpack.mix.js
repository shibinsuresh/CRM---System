const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Compiles the Inertia + Vue 3 app and Bootstrap 5 styles for a
 | Laravel 8 project. Assets are versioned so the mix() helper in
 | resources/views/app.blade.php always loads the freshest build.
 |
 */

mix.js('resources/js/app.js', 'public/js')
    .vue({ version: 3 })
    .sass('resources/sass/app.scss', 'public/css')
    .version();
