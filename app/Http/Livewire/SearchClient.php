<?php

namespace App\Http\Livewire;

use App\Http\Models\Carts;
use App\Http\Models\Cliente;
use App\Traits\CartTrait;
use http\Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use App\Constants\IconConstants;


class SearchClient extends Component
{
    public $cpfTelefone,$nome, $cpf, $email, $telefone, $cep, $logradouro, $numero, $complemento, $bairro,$localidade, $uf, $taxa;
    public $client;
    public $clienteId;
    public $cartItems;

    protected $listeners = [
        'resetInputFields'  =>  'resetInputFields'
    ];

    protected $rules_cpf = [
          'cpfTelefone' => 'required|digits:11'
    ];

    protected $rules_telefone = [
        'cpfTelefone' => 'required|digits_between:9,11'
    ];

    protected $rules = [
        'nome' => 'required|string|max:255',
         'cpf' => 'required|digits:11',
        'telefone' => 'required|digits:9,11',
        'email' => 'required|email|max:255',
        'cep' => 'required|digits:8',
        'logradouro' => 'required|string|max:255',
        'numero' => 'required|min:1|max:5',
        'complemento' => 'nullable|string|max:255',
        'bairro' => 'required|string|max:255',
        'localidade' => 'required|string|max:255',
        'uf' => 'required|string|max:2',
        'taxa' => 'required|string'
    ];

    protected $messages = [
        'nome.required' => 'O campo nome é obrigatório.',
        'nome.string' => 'O campo nome deve ser um texto.',
        'nome.max' => 'O campo nome não pode ter mais que 255 caracteres.',

        'cpf.required' => 'O campo CPF é obrigatório.',
        'cpf.digits' => 'O campo CPF deve ter exatamente 11 dígitos.',

        'telefone.required' => 'O campo telefone é obrigatório.',
        'telefone.digits_between' => 'O campo telefone deve ter entre 9 e 11 dígitos.',

        'email.required' => 'O campo email é obrigatório.',
        'email.email' => 'O campo email deve ser um endereço de email válido.',
        'email.max' => 'O campo email não pode ter mais que 255 caracteres.',

        'cep.required' => 'O campo CEP é obrigatório.',
        'cep.digits' => 'O campo CEP deve ter exatamente 8 dígitos.',

        'numero.required' => 'O campo número é obrigatório.',
        'numero.integer' => 'O campo número deve ser um número inteiro.',
        'numero.min' => 'O campo número deve ser pelo menos 1.',
        'numero.max' => 'O campo número não pode ser maior que 5.',
    ];

    /**
     * Busca o cliente e exibe na modal as informações
     *
     */
    public function searchClient()
    {
        try {

            if($this->validarCPF($this->cpfTelefone)){
                $this->validate($this->rules_cpf);
            }else{
                $this->validate($this->rules_telefone);
            }

            // Pesquisar o cliente no banco de dados com base no CPF
            $this->client = Cliente::where('cpf', $this->cpfTelefone)->orWhere('telefone', $this->cpfTelefone)->first();

            // Verificar se o cliente foi encontrado
            if (!$this->client) {
                session()->flash('notfound', 'Cliente não encontrado.');
            }else{
                $this->clienteId = $this->client->id;
                $this->nome = $this->client->nome;
                $this->cpf = $this->client->cpf;
                $this->email = $this->client->email;
                $this->telefone = $this->client->telefone;
                $this->cep = $this->client->cep;
                $this->logradouro = $this->client->logradouro;
                $this->numero = $this->client->numero;
                $this->complemento = $this->client->complemento;
                $this->bairro = $this->client->bairro;
                $this->localidade = $this->client->localidade;
                $this->uf = $this->client->uf;
                $this->taxa = "R$ ".number_format($this->client->taxa, 2, ',', '.');
            }
        } catch (ValidationException $e) {
            $this->client = null;
            session()->flash('error', 'Informe o CPF com 11 digitos ou Telefone.');
        }
    }

    /***
     * Busca CEP nos correios
     */
    public function searchCep(){
        dd('busca cep' . $this->cep);
    }

    /***
    * SALVAR DADOS DO CLIENTE
     */
    public function saveClient(){
//dd($this->getClienteData());
        try {
            $this->validate($this->rules, $this->messages);

            if ($this->clienteId) {
                // Atualiza cliente existente
                $cliente = Cliente::find($this->clienteId);

                $cliente->update($this->getClienteData());
            } else {
                // Cria um novo cliente
                Cliente::create($this->getClienteData());
            }

            session()->flash('message', 'Cliente salvo com sucesso!');
            $this->resetInputFields();

        }catch (ValidationException $e) {
           // $this->client = null;
            $this->emit('validationError', $e->validator->errors()->all());
        }
    }


    /**
     * Inclui o cliente na venda
    */
    public function associarCliente(){
      //  dd('includeClient' . $this->clienteId , Auth::id());

        try {
            $carts = Carts::where('user_id', Auth::id())->where("status", "ABERTO")->get();

            // Itera sobre cada carrinho encontrado e atualiza o cliente_id
            foreach ($carts as $cart) {
                $cart->cliente_id = $this->clienteId;
                $cart->save();
            }

            $this->emit('message', 'Cliente adicionado à venda com sucesso!!',IconConstants::ICON_SUCCESS,IconConstants::COLOR_GREEN);
            $this->emit('refresh',true);
            $this->resetInputFields();

            //session()->flash('message', 'Cliente adicionado à venda com sucesso!');
           // $this->emit('updateTotal');
           // $this->emit('atualizarCarrinho');
           // $this->emitTo('incluir-cliente','atualizarCliente');
           // $this->emit('focus-input-search', null);

        }catch (Exception $e){
            session()->flash('error', $e->getMessage());
        }

    }

    /**
     * PEGA OS DADOS
    */
    private function getClienteData()
    {
        return [
            'cpf' => $this->cpf,
            'telefone' => $this->telefone,
            'nome' => $this->nome,
            'email' => $this->email,
            'cep' => $this->cep,
            'logradouro' => $this->logradouro,
            'numero' => (integer)$this->numero,
            'complemento' => $this->complemento,
            'bairro' => $this->bairro,
            'localidade' => $this->localidade,
            'uf' => $this->uf,
            'taxa' => $this->converterMoedaParaDecimal($this->taxa)
        ];
    }

    public function resetInputFields()
    {
        $this->cpfTelefone = "";
        $this->nome = '';
        $this->cpf = '';
        $this->email = '';
        $this->telefone = '';
        $this->cep = '';
        $this->endereco = '';
        $this->numero = '';
        $this->complemento = '';
        $this->bairro = '';
        $this->localidade = '';
        $this->uf = '';
        $this->taxa = '';
        $this->client  =null;
    }

    public function render()
    {
        return view('livewire.search-client');
    }

    function validarCPF($cpf) {
        // Remove caracteres não numéricos
        $cpf = preg_replace('/[^0-9]/', '', $cpf);

        // Verifica se o CPF tem 11 dígitos
        if (strlen($cpf) != 11) {
            return false;
        }

        // Verifica se todos os dígitos são iguais
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        // Calcula os dígitos verificadores
        for ($i = 9; $i < 11; $i++) {
            $digito = 0;
            for ($j = 0; $j < $i; $j++) {
                $digito += $cpf[$j] * (($i + 1) - $j);
            }
            $digito = ((10 * $digito) % 11) % 10;
            if ($cpf[$i] != $digito) {
                return false;
            }
        }

        return true;
    }

    function converterMoedaParaDecimal($valor)
    {
        // Remove o símbolo de moeda e quaisquer espaços em branco
        $valor = preg_replace('/[^\d,]/', '', $valor);

        // Substitui a vírgula decimal por um ponto decimal
        $valor = str_replace(',', '.', $valor);

        // Converte o valor para um float e garante duas casas decimais
        return number_format((float)$valor, 2, '.', '');
    }


}
