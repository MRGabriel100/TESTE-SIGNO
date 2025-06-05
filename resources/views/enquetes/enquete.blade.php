<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <title>{{ $enquete->nome }}</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    <section id="paginaVotacao">
<div id="tituloVotacao">
<h2>Votação da enquete - {{ $enquete->nome }}</h2>
<h4>
    <span>De: {{ $enquete->data_inicio }}</span>
    <span>Até: {{ $enquete->data_fim }}</span>
</h4>
</div>
@if($enquete->status === 'não iniciado')
        <h2 class="avisoEnquete">Essa enquete ainda não iniciou!</h2>
@elseif ($enquete->status === 'finalizado')
        <h2 class="avisoEnquete">Essa enquete já acabou!</h2>
@endif

@if(session('success'))
    <div class="mensagem sucesso">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="mensagem erro">{{ session('error') }}</div>
@endif
    <div id="botoesVotos">
    @csrf
    <h4>Vote em uma opção</h4>
    <ul id="opcoesVoto">
        @foreach ($enquete->opcoes_com_votos as $i => $opc)
        @php
            $opcNome = $opc['nome'];
        @endphp
            <li>
                <button  name="opcao" id="opcao_{{ $opcNome }}" value="{{ $i }}" onclick="votar('{{ $enquete->id }}','{{ $i }}', '{{ $opcNome}}')"
                @if ( $enquete->status != 'em andamento')
                   disabled
                @endif 
                >
                    {{ $opcNome }} <span>({{ $opc['qtd'] }} votos)</span>
                </button>
            </li>
        @endforeach
    </ul>
<br><br>
<a href="{{ route('enquetes.index') }}">Voltar ao painel</a>
</div>
</section>
<script>
    //Faz a Requisição para Voto
    function votar(enqueteId, indiceOpcao, opcao){

        fetch(`/enquete/${enqueteId}/votar`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                opcao: indiceOpcao
            })
        })
        .then(response => response.json())
        .then(data => {

            if (data.success) {
                alert(data.message);

                atualizarOpcoes(opcao, data.votos_atualizados[indiceOpcao], opcao);
            } else {
                alert(data.error);
            }
        });
    }

    // Atualiza os votos visualmente
    function atualizarOpcoes(id, qtd, opcao){
        const opc = document.getElementById(`opcao_${opcao}`);
        const el = opc.querySelector('span');
        opc.value = qtd;

        el.innerText = `(${qtd} Votos)`;

    
    }
    
</script>
</body>
</html>