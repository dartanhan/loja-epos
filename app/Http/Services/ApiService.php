<?php


namespace App\Http\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Exception;

class ApiService
{
    protected $token;
    protected $tokenExpiry;

    public function __construct()
    {
        // Carregue o token e o tempo de expiração da sessão, se estiverem definidos
        $this->token = Session::get('jwt_token');
        $this->tokenExpiry = Session::get('jwt_token_expiry');
    }

    public function getToken()
    {
        // Gera um novo token se o token não existir ou estiver expirado
        if (!$this->token || $this->isTokenExpired()) {
            $this->generateToken();
        }

        return $this->token;
    }

    protected function isTokenExpired()
    {
        return !$this->tokenExpiry || now()->greaterThan($this->tokenExpiry);
    }

    protected function generateToken()
    {
        $response = Http::post(config('url_api_token'), [
            'login' => 'darta',   // Substitua pelo seu e-mail
            'password' => '959313'          // Substitua pela sua senha
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $this->token = $data['access_token'];
            $this->tokenExpiry = now()->addSeconds($data['expires_in']);

            // Armazena o token e o tempo de expiração na sessão
            Session::put('jwt_token', $this->token);
            Session::put('jwt_token_expiry', $this->tokenExpiry);
        } else {
            throw new Exception('Erro ao obter o token JWT');
        }
    }
}
