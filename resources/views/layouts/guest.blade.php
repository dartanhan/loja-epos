<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">
        <link href="{{URL::asset('bootstrap/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{URL::asset('assets/css/plugins.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{URL::asset('assets/css/structure.css')}}" rel="stylesheet" type="text/css" class="structure" />
        <link href="{{URL::asset('assets/css/authentication/form-1.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{URL::asset('assets/css/forms/theme-checkbox-radio.css')}}" rel="stylesheet" type="text/css"/>
        <link href="{{URL::asset('assets/css/forms/switches.css')}}" rel="stylesheet" type="text/css"/>
        <!-- link rel="stylesheet" href="{{URL::asset('css/app.css')}}" /-->

        <!-- Scripts -->
        <script src="{{URL::asset('js/app.js')}}"></script>
        <script src="{{URL::asset('assets/js/libs/jquery-3.1.1.min.js')}}"></script>
        <script src="{{URL::asset('bootstrap/js/popper.min.js')}}"></script>
        <script src="{{URL::asset('bootstrap/js/bootstrap.min.js')}}"></script>
        <script src="{{URL::asset('assets/js/authentication/form-1.js')}}"></script>

    </head>
    <body>
        <div class="font-sans text-gray-900 antialiased">
            {{ $slot }}
        </div>
    </body>
</html>
