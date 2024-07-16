
<link href="{{ asset('assets/css/loader.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/bootstrap/css/bootstrap.css') }}" rel="stylesheet" type="text/css" />

<link href="{{URL::asset('plugins/sweetalerts/sweetalert.css')}}" rel="stylesheet" />
<link href="{{ asset('plugins/notification/snackbar/snackbar.min.css') }}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link href="{{ asset('plugins/font-icons/fontawesome/css/fontawesome.css') }}" rel="stylesheet" type="text/css">

<style>
    *{
        font-family: "Poppins", sans-serif;
    }
    body{
        color: #888ea8;
        height: 100%;
        font-size: 0.875rem;
        background: #f1f2f3;
        overflow-x: hidden;
        overflow-y: auto;
        letter-spacing: 0.0312rem;
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
        width: 82px;
        height: 80px;
        background-size: cover;
        background-position: center;
        position: relative;
        display: inline-block;
        border-bottom-left-radius: 8px; /* Define o raio da borda inferior esquerda */
        border-bottom-right-radius: 8px; /* Define o raio da borda inferior direita */
       /* background-color: rgba(0, 0, 0, 0.2);*/
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

    /*  Table   */
    .table > tbody > tr > td {
        vertical-align: middle;
        color: #515365;
        font-size: 13px;
        letter-spacing: 1px; 
    }
    /**
    Fim div Produtos Table
    */

    /*
    ======================
        Footer-wrapper
    ======================
    */
    .footer-wrapper {
        padding: 10px 20px 10px 20px;
        display: inline-block;
        background: transparent;
        font-weight: 600;
        font-size: 12px;
        width: 100%;
        border-top-left-radius: 6px;
        display: flex;
        justify-content: space-between;
     }

    .main-container.sidebar-closed .footer-wrapper {
    border-radius: 0; }

    .footer-wrapper .footer-section p {
        margin-bottom: 0;
        color: #515365;
        font-size: 13px;
        letter-spacing: 1px; 
    }
    .footer-wrapper .footer-section p a {
        color: #515365; }

    .footer-wrapper .footer-section svg {
        color: #e7515a;
        fill: rgba(231, 81, 90, 0.419608);
        width: 15px;
        height: 15px;
        vertical-align: text-top; 
    }

/* Sidebar Wrapper */
.sidebar-wrapper {
    width: 250px;
    height: 100vh;
    position: fixed;
    top: 0;
    left: 0;
    background-color: #343a40;
    color: #ffffff;
    transition: all 0.3s ease;
}

.sidebar-wrapper .menu-categories {
    list-style-type: none;
    padding: 0;
    margin: 0;
}

.sidebar-wrapper .menu-categories li {
    width: 100%;
}

.sidebar-wrapper .menu-categories li a {
    display: flex;
    align-items: center;
    padding: 15px 20px;
    color: #ffffff;
    text-decoration: none;
    transition: background-color 0.3s ease;
}

.sidebar-wrapper .menu-categories li a .base-menu {
    display: flex;
    align-items: center;
}

.sidebar-wrapper .menu-categories li a .base-icons {
    margin-right: 10px;
}

.sidebar-wrapper .menu-categories li a .base-icons svg {
    width: 24px;
    height: 24px;
}

.sidebar-wrapper .menu-categories li a span {
    font-size: 16px;
}

.sidebar-wrapper .menu-categories li.active a {
    background-color: #495057;
}

.sidebar-wrapper .menu-categories li a:hover {
    background-color: #495057;
}

/* Logout Form */
#logout-form {
    display: none;
}

</style>