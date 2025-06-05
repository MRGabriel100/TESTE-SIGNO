<li id="{{ $e->id }}">
  <label for="{{ $e->nome }}">    
<span><strong>{{ $e->nome }}</strong></span>
   <span class="inicio">Início: {{ $e->data_inicio }}</span>
<span class="fim">Fim: {{ $e->data_fim }}</span>
    <br>
    <a href="/enquete/{{ $e->id }}">Página da Enquete</a>

    <form action="{{ route('enquetes.destroy', $e->id) }}" method="POST" class="form-delete">
        @csrf @method('DELETE')
        <button type="submit">Excluir Enquete</button>
    </form>
</label>

    <input type="checkbox" id="{{ $e->nome }}">
    <ul id="opcoesEnquete-{{ $e->id }}"  class="dadosEnquete">
        @foreach ($e->opcoes_com_votos as $opc)
            <li>
                <span>{{ $opc['nome'] }}</span> - 
                <span>{{ $opc['qtd'] }} Votos</span>
            </li>
        @endforeach
    </ul>
</li>