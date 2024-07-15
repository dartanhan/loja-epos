
<link href="{{ asset('assets/css/loader.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('bootstrap/css/bootstrap.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/main.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/structure.css') }}" rel="stylesheet" type="text/css" class="structure" />
<link href="{{URL::asset('plugins/sweetalerts/sweetalert.css')}}" rel="stylesheet" />
<link href="{{ asset('plugins/notification/snackbar/snackbar.min.css') }}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link href="{{ asset('plugins/font-icons/fontawesome/css/fontawesome.css') }}" rel="stylesheet" type="text/css">

<style>
    *{
     font-family: "Poppins", sans-serif;
    }
    aside {
        display: none !important;
    }

    .page-item.active .page-link {
        z-index: 3;
        color: #fff;
        background-color: #3b3f5c;
        border-color: #3b3f5c;
    }

    @media (max-width: 480px) {
        .mtmobile {
            margin-bottom: 20px !important;
        }

        .mbmobile {
            margin-bottom: 10px !important;
        }

        .hideonsm {
            display: none !important;
        }

        .inblock {
            display: block;
        }
    }

    /*sidebar background*/
    .sidebar-theme #compactSidebar {
        background: #191e3a !important;
    }

    /*sidebar collapse background */
    .header-container .sidebarCollapse {
        color: #3B3F5C !important;
    }

    .navbar .navbar-item .nav-item form.form-inline input.search-form-control {
        font-size: 15px;
        background-color: #3B3F5C !important;
        padding-right: 40px;
        padding-top: 12px;
        border: none;
        color: #fff;
        box-shadow: none;
        border-radius: 30px;
    }
    .input-sapace-custom{
        padding: 0px 0px 0px 5px
    }
    .input-altura-custom {
        height: 40px; /* Ajuste conforme necessário */
    }

    .cliente-associado h5 {
        display: inline;
        cursor: pointer;
        color: inherit; /* Mantém a cor original inicialmente */
        transition: color 0.3s ease; /* Adiciona uma transição suave para a cor */
    }

    .cliente-associado h5:hover {
        color: #2b71f9;
        text-decoration: underline;
    }

    /**
    Div da table de produtos
    */
    .div-scroll-container {
        height: 350px;
        border: 1px solid #ccc;
        /*overflow: auto; !* Permite a rolagem interna *!*/
        /*scrollbar-width: none; !* Firefox *!*/
        /*-ms-overflow-style: none; !* IE 10+ *!*/
    }

    /*.div-scroll-container::-webkit-scrollbar {*/
    /*    width: 0;*/
    /*    height: 0;*/
    /*}*/

    .div-content {
        height: auto;
    }

    .cart-product-img {
        width: 80px;
        height: 80px;
        background-size: cover;
        background-position: center;
        position: relative;
        display: inline-block;
        border-bottom-left-radius: 8px; /* Define o raio da borda inferior esquerda */
        border-bottom-right-radius: 8px; /* Define o raio da borda inferior direita */
    }

    .cart-product-img-tip {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        background-color: rgba(0, 0, 0, 0.7);
        color: #fff;
        text-align: center;
        padding: 3px 0;
        font-size: 7px;
        opacity: 0.8;
        border-bottom-left-radius: 8px; /* Define o raio da borda inferior esquerda */
        border-bottom-right-radius: 8px; /* Define o raio da borda inferior direita */
    }

    .text-left {
        display: flex;
        align-items: center;
    }

    .item-description {
        display: inline-block;
        vertical-align: middle;
        padding-left: 20px;
    }
    /**
    Fim div Produtos Table
    */

    .sidebar {
            height: 100%;
            width: 0;
            position: fixed;
            z-index: 1;
            top: 0;
            left: 0;
            background-color: #111;
            overflow-x: hidden;
            transition: 0.5s;
            padding-top: 60px;
        }
        .sidebar a {
            padding: 8px 8px 8px 32px;
            text-decoration: none;
            font-size: 25px;
            color: #818181;
            display: block;
            transition: 0.3s;
        }
        .sidebar a:hover {
            color: #f1f1f1;
        }
        .sidebar .closebtn {
            position: absolute;
            top: 0;
            right: 25px;
            font-size: 36px;
            margin-left: 50px;
        }
        #main {
            padding: 16px;
        }
</style>


{{--<link href="{{ asset('plugins/flatpickr/flatpickr.dark.css') }}" rel="stylesheet" type="text/css" />--}}

<style>
    /*.modal.modal-fullscreen .modal-dialog {*/
    /*    width: 100vw;*/
    /*    height: 100vh;*/
    /*    margin: 0;*/
    /*    padding: 0;*/
    /*    max-width: none;*/
    /*}*/

    /*.modal.modal-fullscreen .modal-content {*/
    /*    height: auto;*/
    /*    height: 100vh;*/
    /*    border-radius: 0;*/
    /*    border: none;*/
    /*}*/

    /*.modal.modal-fullscreen .modal-body {*/
    /*    overflow-y: auto;*/
    /*}*/
</style>

