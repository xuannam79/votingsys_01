const elixir = require('laravel-elixir');
require('es6-promise').polyfill();

elixir(function (mix) {
    mix.sass('layout/master.scss', 'public/css/layout/master.css');
    mix.sass('user.scss');
    mix.scripts('layout/master.js', 'public/js/layout/master.js');
    mix.scripts('poll.js');
    mix.scripts('multipleLanguage.js');
    mix.scripts('managePoll.js');
    mix.scripts('listPolls.js');
    mix.scripts('voteSocket.js');
    mix.scripts('editLink.js');
    mix.scripts('shareSocial.js');
    mix.scripts('vote.js');
    mix.scripts('comment.js');
    mix.scripts('common.js');
    mix.scripts('requiredPassword.js');
    mix.scripts('chart.js');
});
