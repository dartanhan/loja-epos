<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
        <title>@yield('title', 'SISTEMA KNPOS')</title>
        <link rel="icon" type="image/x-icon" href="assets/img/favicon.ico" />
        <link href="https://fonts.googleapis.com/css?family=Poppins:400,500,600,700&display=swap" rel="stylesheet">
        <!-- BEGIN GLOBAL MANDATORY STYLES -->
        @include('layouts.theme.styles')
        <!-- END PAGE LEVEL PLUGINS/CUSTOM STYLES -->
        @livewireStyles
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

        @include('layouts.theme.sidebar')

        <!-- Page Content -->
        <div id="main" class="mt-3">
            @include('layouts.theme.header2')
        
            <div class="container-fluid mt-5">
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
    </body>
    <script>
        function openNav() {
            document.getElementById("mySidebar").style.width = "250px";
        }

        function closeNav() {
            document.getElementById("mySidebar").style.width = "0";
        }

        function toggleNav() {
            if (document.getElementById("mySidebar").style.width === "250px") {
                closeNav();
            } else {
                openNav();
            }
        }
    </script>
</html> 