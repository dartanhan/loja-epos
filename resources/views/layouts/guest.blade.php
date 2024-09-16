<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
        <link href="{{URL::asset('assets/bootstrap/css/bootstrap.css')}}" rel="stylesheet" type="text/css" />

        <link href="{{URL::asset('assets/css/authentication/form-1.css')}}" rel="stylesheet" type="text/css" />

        <!-- Scripts -->
        <script src="{{URL::asset('js/app.js')}}"></script>
        <script src="{{URL::asset('assets/js/libs/jquery-3.1.1.min.js')}}"></script>

        <script src="{{URL::asset('assets/js/authentication/form-1.js')}}"></script>
        <script src="https://www.google.com/recaptcha/api.js"></script>
        <style>
            *{
                font-family: "Poppins", sans-serif;
            }
        </style>
        <script>
            function onSubmit() {
                document.getElementById("login-form").submit();
            }
        </script>
    </head>
    <body>
        <div class="font-sans text-gray-900 antialiased">
            {{ $slot }}
        </div>
    </body>
</html>
