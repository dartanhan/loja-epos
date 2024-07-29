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
        <!-- BEGIN GLOBAL MANDATORY STYLES -->
        @include('layouts.theme.styles')
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
        @include('layouts.theme.scripts')
        <!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM SCRIPTS -->

        @livewireScripts

        @stack("scripts")
    </body>
</html>
