const elixir = require('laravel-elixir');

elixir(function (mix) {
    mix.copy('resources/assets/sass/admin/master.scss', 'public/css/admin/master.css');
    mix.copy('resources/assets/js/admin/master.js', 'public/js/admin/master.js');
    mix.copy('resources/assets/sass/user.scss', 'public/css/user.css');
});
