<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <title>Painel de Enquetes</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

<h1>Painel de Enquetes</h1>

@if(session('success'))
    <div class="alert">{{ session('success') }}</div>
@endif
<section id="hero">

 <div id="novaEnquete">
    <h2 class="tituloArea">Criar Nova Enquete</h2>
<form id="formEnquete">
    @csrf
    <label>Nome da Enquete<br>
    <input type="text" name="nome" placeholder="Nome da enquete" required></label>
    <label>Opções<br>
    <input type="text" id="opcoesInput" name="opcoes" placeholder="Separe as opções com ," oninput="addOpc(this.Element, this.value)"><br><br>
    <ul id="opcLista"></ul></label>
    <label>Início
        <br><input type="date" name="inicio" required></label>
    <label>Fim
        <br><input type="date" name="fim" required></label>
    <button type="submit">Criar Enquete</button>
</form>
</div>
@php
    $naoIniciadas = $enquetes->filter(fn($e) => $e->status === 'não iniciado');
    $emAndamento = $enquetes->filter(fn($e) => $e->status === 'em andamento');
    $finalizadas = $enquetes->filter(fn($e) => $e->status === 'finalizado');
@endphp

<div id="listaEnquetes">
    <h2 class="tituloArea">Lista de Enquetes</h2>
    <h3 class="tituloEnquete">Não Iniciadas</h3>
    <ul>
        @forelse ($naoIniciadas as $e)
            @include('enquetes.enquete-item')
        @empty
            <li>Nenhuma enquete não iniciada.</li>
        @endforelse
    </ul>

    <h3 class="tituloEnquete">Em Andamento</h3>
    <ul>
        @forelse ($emAndamento as $e)
            @include('enquetes.enquete-item')
        @empty
            <li>Nenhuma enquete em andamento.</li>
        @endforelse
    </ul>

    <h3 class="tituloEnquete">Finalizadas</h3>
    <ul>
        @forelse ($finalizadas as $e)
            @include('enquetes.enquete-item')
        @empty
            <li>Nenhuma enquete finalizada.</li>
        @endforelse
    </ul>
</div>

</section>
<script>
    const inputElement = document.getElementById('opcoesInput');
    arrOpc = [];
    function addOpc(element, value){
        const lista = document.getElementById('opcLista');
         if (value.includes(',')) {
            const newItem = value.trim().slice(0, -1).trim();

            // Validação: item não vazio e não duplicado
            if (newItem && !arrOpc.includes(newItem)) {
                arrOpc.push(newItem);

                const opc = document.createElement('li');
                opc.id = newItem;
                opc.innerHTML = `${newItem} <button type="button" onclick="exclui_opc('${newItem}')">X</button>`;
                opcLista.appendChild(opc);
            }

            inputElement.value = '';
        }
    }

     function exclui_opc(nome) {
        // Remove do array
        const index = arrOpc.indexOf(nome);
        if (index > -1) {
            arrOpc.splice(index, 1);
        }

        const li = document.getElementById(nome);
        if (li) {
            li.remove();
        }
    }

      document.getElementById('formEnquete').addEventListener('submit', function (e) {
        e.preventDefault();

     

        if (arrOpc.length < 3) {
           alert('Por favor, adicione pelo 3 opções.</p>');
            return;
        }

        document.getElementById('opcoesInput').value = arrOpc.join(';');
        // Coleta os dados do formulário
        const formData = new FormData(this);

        // Envia para o Laravel via fetch
        fetch("{{ route('enquetes.index') }}", {
            method: "POST",
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
        alert("Enquete adicionada com sucesso!");

        // Limpa formulário
        arrOpc.length = 0;
        opcLista.innerHTML = '';
        document.getElementById('formEnquete').reset();

        // === INJETA O ITEM NO HTML ===
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = data.html.trim();
        const liElement = tempDiv.firstChild;

        let listaId = '';

        switch (data.status) {
            case 'não iniciado':
                listaId = '#listaEnquetes > ul:nth-of-type(1)';
                break;
            case 'em andamento':
                listaId = '#listaEnquetes > ul:nth-of-type(2)';
                break;
            case 'finalizado':
                listaId = '#listaEnquetes > ul:nth-of-type(3)';
                break;
        }

        const lista = document.querySelector(listaId);
        if (lista) {
            lista.appendChild(liElement);
        }
    } else {
        alert('Erro ao criar a enquete.');
    }
})
        .catch(error => {
            console.error('Erro:', error);
             alert('Por favor, adicione pelo menos uma opção.');;
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
    // Captura todos os formulários de exclusão
    document.querySelectorAll('form.form-delete').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            if (!confirm('Tem certeza que deseja excluir esta enquete?')) {
                return;
            }

            const formAction = this.getAttribute('action');
            const token = this.querySelector('input[name="_token"]').value;

            fetch(formAction, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-HTTP-Method-Override': 'DELETE'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erro ao excluir a enquete.');
                }
                return response.json();
            })
            .then(data => {
                alert(data.message || 'Enquete excluída com sucesso!');
                const li = this.closest('li');
                if (li) li.remove();
            })
            .catch(error => {
                console.error(error);
                alert('Ocorreu um erro ao excluir a enquete.');
            });
        });
    });
});
</script>
</body>
</html>