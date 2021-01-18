<?php

namespace App\Http\Requests;

use App\Rules\ServicoUniqueCode;
use Illuminate\Foundation\Http\FormRequest;

class ServicosRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'codigo'                => ['required', new ServicoUniqueCode($this->get('id'))],
            'nome'                  => 'required|max:90',
            'valor'                 => 'required',
            'duracao'               => 'required',
            'servico_categoria_id'  => 'required',
            'rateio'                => 'nullable',
            'descricao'             => 'nullable|max:3000',
            'possui_desconto'       => 'required',
            'data_desconto_inicio'  => 'required_if:possui_desconto,true',
            'data_desconto_fim'     => 'required_if:possui_desconto,true',
            'hora_inicio'           => 'required_if:possui_desconto,true',
            'hora_fim'              => 'required_if:possui_desconto,true',
            'valor_desconto'        => 'required_if:possui_desconto,true',
        ];
    }
}
