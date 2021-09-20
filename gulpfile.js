var elixir = require('laravel-elixir');

elixir(function(mix) {

    mix
        .babel(['../../../resources/js/app.js'])
        .version([
            '/js/*.js',
            '/css/*.css'
        ])
    ;
});
