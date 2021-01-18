<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConfirmacaoRequest extends FormRequest
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
            'nome'                      => 'required|max:95|min:3',
            'email'                     => 'required|email',
            'nome_empresa'              => 'required|max:95|min:2',
            'password'                  => 'required|max:20|min:6|confirmed',
            'password_confirmation'     => 'required'
        ];
    }
}
