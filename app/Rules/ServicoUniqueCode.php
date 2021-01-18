<?php

namespace App\Rules;

use App\Models\Servico;
use Illuminate\Contracts\Validation\Rule;

class ServicoUniqueCode implements Rule
{
    private $servico_id;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($servico_id = null)
    {
        $this->servico_id = $servico_id;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $servico = Servico::where('codigo', $value)->first();

        if ($this->servico_id && $servico) {
            $servicoValidate = Servico::find($this->servico_id);
            return $servico->id == $servicoValidate->id;
        }

        return !$servico;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Código já cadastrado no estabelecimento.';
    }
}
