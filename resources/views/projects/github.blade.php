<h1 class="text-center">Find a GitHub User</h1>
<div id="github-finder-app">
    <github-finder-app
        :routes='{!! json_encode($routes) !!}'
        :access-token='{!! json_encode($accessToken) !!}'
        :client-id='{!! json_encode($clientId) !!}'
    ></github-finder-app>
</div>

<script src="{{ asset('js/app.js') }}" defer></script>
