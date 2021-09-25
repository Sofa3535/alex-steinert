
<h1 class="text-center">Find A Movie</h1>
<div id="movie-finder-app">
    <movie-finder-app
        :routes='{!! json_encode($routes) !!}'
    ></movie-finder-app>
</div>

<script src="{{ asset('js/app.js') }}" defer></script>

