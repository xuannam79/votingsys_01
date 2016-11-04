const elixir = require('laravel-elixir');

elixir(function (mix) {
    mix.copy('resources/assets/sass/admin/master.scss', 'public/css/admin/master.css');
    mix.copy('resources/assets/js/admin/master.js', 'public/js/admin/master.js');
    mix.copy('resources/assets/js/comment.js', 'public/js/comment.js');
    mix.copy('resources/assets/js/vote.js', 'public/js/vote.js');
    mix.copy('resources/assets/js/shareSocial.js', 'public/js/shareSocial.js');
    mix.copy('resources/assets/js/listPolls.js', 'public/js/listPolls.js');
    mix.copy('resources/assets/js/managePoll.js', 'public/js/managePoll.js');
    mix.copy('resources/assets/js/editLink.js', 'public/js/editLink.js');
    mix.copy('resources/assets/js/multipleLanguage.js', 'public/js/multipleLanguage.js');
    mix.copy('resources/assets/js/requiredPassword.js', 'public/js/requiredPassword.js');
    mix.copy('resources/assets/sass/user.scss', 'public/css/user.css');
    mix.copy('resources/assets/sass/layout/mail_notification.scss', 'public/css/layout/mail_notification.css');
    mix.copy('resources/assets/js/layout/master.js', 'public/js/layout/master.js');
    mix.copy('resources/assets/sass/layout/master.scss', 'public/css/layout/master.css');
});
