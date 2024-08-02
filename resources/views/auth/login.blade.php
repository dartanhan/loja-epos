<x-guest-layout :title="''">
    <body class="form">
    <div class="form-container">
        <div class="form-form">
            <div class="form-form-wrap">
                <div class="form-container">
                    <div class="form-content">
                        <!-- Session Status -->
                        <x-auth-session-status class="mb-4" :status="session('status')" />

                        <!-- Validation Errors -->
                        <x-auth-validation-errors class="mb-4" :errors="$errors" />

                        <form method="POST" action="{{ route('login') }}" class="text-left">
                            @csrf
                            <div class="form">

                                <!-- Email Address -->
                                <div id="username-field" class="field-wrapper input">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                    <input id="username" name="login" type="text" class="form-control" placeholder="Usuário" required autofocus/>
                                </div>

                                <div id="password-field" class="field-wrapper input mb-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                         class="feather feather-lock"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                    <input id="password" name="senha" type="password" class="form-control " placeholder="Senha"  required autocomplete="current-password"/>
                                </div>
                                <div class="d-sm-flex justify-content-center">
                                    <div class="field-wrapper">
                                        <button type="submit" class="btn btn-primary btn-lg btn-login" value="">Efetuar Login</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <p class="terms-conditions">© {{date('Y')}} Todos os Direitos reservados.
                    </div>
                </div>
            </div>
        </div>
        <div class="form-image">
            <div class="l-image">
            </div>
        </div>
    </div>
    </body>
</x-guest-layout>
