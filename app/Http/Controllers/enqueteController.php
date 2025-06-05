<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Enquete;

class EnqueteController extends Controller
{
    /**
     * Mostrar o painel com todas as enquetes
     */
    public function index()
    {
        $enquetes = Enquete::all();
        return view('enquetes.painel', compact('enquetes'));
    }

    /**
     * Salvar uma nova enquete
     */
    public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'nome' => 'required|string|max:255',
        'opcoes' => 'required|string',
        'inicio' => 'required|date',
        'fim' => 'required|date|after_or_equal:inicio',
    ]);

    if ($validator->fails()) {
        return Response::json(['success' => false, 'errors' => $validator->errors()]);
    }

    // Prepara os votos iniciais
    $opcoesArray = explode(';', $request->opcoes);
    $votosIniciais = array_fill(0, count($opcoesArray), 0);
    $votosString = implode(';', $votosIniciais);

    // Cria a enquete
    $enquete = Enquete::create([
        'nome' => $request->nome,
        'opcoes' => $request->opcoes,
        'votos_qtd' => $votosString,
        'inicio' => $request->inicio,
        'fim' => $request->fim,
    ]);

    // Renderiza o Blade enquete-item com os dados da nova enquete
    $html = View::make('enquetes.enquete-item', ['e' => $enquete])->render();

    return Response::json([
        'success' => true,
        'message' => 'Enquete criada com sucesso!',
        'enquete' => $enquete,
        'html' => $html,
        'status' => $enquete->status
    ]);
}
    /**
     * Atualizar uma enquete
     */
    public function update(Request $request, $id)
    {
        $enquete = Enquete::findOrFail($id);

        $request->validate([
            'nome' => 'required|string|max:255',
            'opcoes' => 'required|string',
            'inicio' => 'required|date',
            'fim' => 'required|date|after_or_equal:inicio',
        ]);

        // Se opções mudarem, reiniciar votos
        if ($enquete->opcoes != $request->opcoes) {
            $opcoesArray = explode(';', $request->opcoes);
            $votosIniciais = array_fill(0, count($opcoesArray), 0);
            $enquete->votos = implode(';', $votosIniciais);
        }

        $enquete->update([
            'nome' => $request->nome,
            'opcoes' => $request->opcoes,
            'votos_qtd' => $enquete->votos,
            'inicio' => $request->inicio,
            'fim' => $request->fim,
        ]);

        return redirect()->route('enquetes.index')->with('success', 'Enquete atualizada com sucesso!');
    }

    /**
     * Excluir uma enquete
     */
   public function destroy($id)
{
    $enquete = Enquete::findOrFail($id);
    $enquete->delete();

    if (request()->ajax()) {
        return response()->json([
            'success' => true,
            'message' => 'Enquete excluída com sucesso!'
        ]);
    }

    return redirect()->route('enquetes.index')->with('success', 'Enquete excluída com sucesso!');
}

    /**
     * Mostrar a página de votação de uma enquete
     */
    public function mostrarEnquete($id)
    {
        $enquete = Enquete::findOrFail($id);
        return view('enquetes.enquete', compact('enquete'));
    }

    /**
 * Processa o voto do usuário
 */public function votar(Request $request, $id)
{
    $request->validate([
        'opcao' => 'required|integer|min:0',
    ]);

    $sessionKey = 'votou_enquete_' . $id;

    //Comentado para testes
  /*  if (session()->has($sessionKey)) {
        return response()->json(['success' => false, 'error' => 'Você já votou nessa enquete.']);
    }*/

    $enquete = Enquete::findOrFail($id);

    $votosArray = explode(';', $enquete->votos_qtd);
    $indiceOpcao = $request->input('opcao');

    if (!isset($votosArray[$indiceOpcao])) {
        return response()->json(['success' => false, 'error' => 'Opção inválida.']);
    }

    $votosArray[$indiceOpcao] = (int)$votosArray[$indiceOpcao] + 1;

    $enquete->update([
        'votos_qtd' => implode(';', $votosArray),
    ]);

    session([$sessionKey => true]);

    return response()->json([
        'success' => true,
        'message' => 'Seu voto foi computado!',
        'votos_atualizados' => $votosArray
    ]);
}

}