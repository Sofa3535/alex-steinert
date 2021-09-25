<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Krona+One">
<div class="container">
    <div class="title">
        <span id="alex">Alex Steinert</span>
        <a href="{{route('projects')}}">Projects</a>
    {{--    <img src="https://alexsteinert.s3.ca-central-1.amazonaws.com/welcome/ezgif.com-gif-maker.gif">--}}
    </div>
</div>


<style>
    body {
        font-family: "Krona One";
    }

    .title {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
    }

    .container {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 70%;
    }

    #alex {
        font-size: 100px;
    }

    a:hover {
        background-color: #b8db70;
        transition: all 1s ease;
        -webkit-transition: all 1s ease;
    }
</style>

