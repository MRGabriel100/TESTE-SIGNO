<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Enquete extends Model
{
    protected $table = 'enquetes';

    protected $fillable = [
        'nome',
        'opcoes',
        'votos_qtd', 
        'inicio',
        'fim',
        'status'
    ];

    public $timestamps = false;

    // Retorna array das opções
    public function getOpcoesArrayAttribute()
    {
        return explode(';', $this->opcoes);
    }

    // Retorna array dos votos
    public function getVotosArrayAttribute()
    {
        return explode(';', $this->votos_qtd);
    }

    // Retorna as opções com os votos em array associativo
    public function getOpcoesComVotosAttribute()
    {
        $opcoes = $this->getOpcoesArrayAttribute();
        $votos = $this->getVotosArrayAttribute();

        $resultado = [];

        foreach ($opcoes as $i => $opcao) {
            $resultado[] = [
                'nome' => $opcao,
                'qtd' => $votos[$i] ?? 0,
            ];
        }

        return $resultado;
    }

    // Calcula o status dinamicamente com base nas datas
    public function getStatusAttribute($value)
    {
        $hoje = Carbon::today();
        $inicio =  $this->inicio;
        $fim = $this->fim;

        if ($hoje->lt($inicio)) {
            return 'não iniciado';
        } elseif ($hoje->betweenIncluded($inicio, $fim)) {
            return 'em andamento';
        } else {
            return 'finalizado';
        }
    }

    public function getDataInicioAttribute()
{
    return Carbon::createFromFormat('Y-m-d', $this->inicio)->format('d/m/Y');
}

    public function getDataFimAttribute()
    {
    return Carbon::createFromFormat('Y-m-d', $this->fim)->format('d/m/Y');
}
}