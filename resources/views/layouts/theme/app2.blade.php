<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title', 'SISTEMA KNPOS')</title>
        <link rel="icon" type="image/x-icon" href="assets/img/favicon.ico" />
        <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

        <link href="{{ asset('assets/css/loader.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/bootstrap/css/bootstrap.css') }}" rel="stylesheet" type="text/css" />

        <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.2/dist/sweetalert2.min.css" rel="stylesheet">
        <link href="{{ asset('plugins/notification/snackbar/snackbar.min.css') }}" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <link href="{{ asset('plugins/font-icons/fontawesome/css/fontawesome.css') }}" rel="stylesheet" type="text/css">
        <link href="{{ asset('css/chosen.css') }}" rel="stylesheet" type="text/css">

        <script src="{{URL::asset('assets/js/libs/jquery-3.1.1.min.js') }}"></script>
        <script src="{{URL::asset('assets/bootstrap/js/bootstrap.min.js') }}"></script>
        <script src="{{URL::asset('assets/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

        <script src="{{URL::asset('assets/js/loader.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.2/dist/sweetalert2.all.min.js"></script>

        <script src="{{URL::asset('plugins/jquery-ui/jquery-ui.min.js')}}"></script>
        <script src="{{URL::asset('plugins/notification/snackbar/snackbar.min.js')}}"></script>
        <script src="{{URL::asset('assets/fontawesome/js/all.min.js')}}"></script>
        <script src="{{URL::asset('js/url.js')}}"></script>
        <script src="{{URL::asset('plugins/input-mask/jquery.maskMoney.min.js')}}"></script>
        <script src="{{URL::asset('js/jquery.mask.min.js')}}"></script>
        <script src="{{URL::asset('js/chosen.jquery.js')}}"></script>
        <script src="https://www.google.com/recaptcha/api.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/spin.js/2.3.2/spin.min.js"></script>
        <!-- BEGIN GLOBAL MANDATORY STYLES -->
        <link href="{{ asset('css/styles.css') }}" rel="stylesheet" type="text/css">
        <!-- END PAGE LEVEL PLUGINS/CUSTOM STYLES -->

        @livewireStyles

        @stack("styles")
    </head>
    <body>
        <!-- BEGIN LOADER -->
        <div id="load_screen">
            <div class="loader">
                <div class="loader-content">
                    <div class="spinner-grow align-self-center"></div>
                </div>
            </div>
        </div>
        <!--  END LOADER -->

        <div class="d-flex" id="wrapper">

            <!--@include('layouts.theme.sidebar')-->

            <!-- Page Content -->
            <div class="container mt-3 p-3">
                @include('layouts.theme.header2')

                <div class="container-fluid mt-5 ">
                    @yield('content')
                </div>

                @include('layouts.theme.footer')
            </div>
            <!-- /#page-content-wrapper -->

        </div>
        <!-- /#wrapper -->
        <!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
        <script src="{{URL::asset('js/scripts.js')}}"></script>
        <!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM SCRIPTS -->

        @livewireScripts

        @stack("scripts")
    </body>
</html>
