<?php

namespace App\Models;

use App\Models\Traits\EstabelecimentoTrait;
use App\Models\Traits\Search;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ComandaItem extends Model
{
    use SoftDeletes, Search, EstabelecimentoTrait;

    protected $table = 'comanda_itens';

    protected $fillable = [
        'comanda_id', 'produto_id', 'servico_id', 'profissional_id', 'valor', 'quantidade', 'unidade_medida',
        'tipo_desconto', 'valor_desconto', 'valor_total', 'rateio', 'descontar_taxas_rateio'
    ];

    public function produto()
    {
        return $this->belongsTo(Produto::class)->withTrashed();
    }

    public function servico()
    {
        return $this->belongsTo(Servico::class)->withTrashed();
    }
}
