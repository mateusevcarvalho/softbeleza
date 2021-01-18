<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AutenticacaoController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/token', [AutenticacaoController::class, 'login']);
Route::get('/locais', 'Api\LocaisController@index');
Route::get('/tipos-estabelecimentos', 'Api\TiposEstabelecimentosController@index');
Route::post('/cadastro', [AutenticacaoController::class, 'create']);
Route::post('/confirmar', [AutenticacaoController::class, 'confirmar']);
Route::post('/enviar-email-recuperacao-senha', [AutenticacaoController::class, 'enviarEmailRecupearSenha']);
Route::put('/recuperacao-senha/{uuid}', [AutenticacaoController::class, 'recuperarSenha'])->name('recuperar-senha');

Route::get('/buscar-tenant/{uuid}', 'Api\AutenticacaoController@buscarTenantUuid');

Route::get('/estados', 'Api\LocaisController@estados');
Route::get('/cidades', 'Api\LocaisController@cidades');

Route::group(['namespace' => 'Api', 'middleware' => ['auth:sanctum', 'tenant']], function () {
    Route::get('/controle-acesso', 'AutenticacaoController@controleAcesso');
    Route::get('/usuarios', 'AutenticacaoController@usuarios');
    Route::get('/logout', 'AutenticacaoController@logout');

    Route::get('/estabelecimentos', 'EstabelecimentosController@index');
    Route::put('/estabelecimentos/{id}', 'EstabelecimentosController@update');
    Route::get('/estabelecimentos/horarios-funcionamento', 'EstabelecimentosController@buscarHorarioFuncionamento');
    Route::post('/estabelecimentos/horarios-funcionamento', 'EstabelecimentosController@horarioFuncionamento');

    Route::apiResource('servicos', 'ServicosController');
    Route::apiResource('servico-categorias', 'ServicoCategoriasController');

    Route::apiResource('profissionais', 'ProfissionaisController');
    Route::get('profissionais/horarios-trabalho', 'ProfissionaisController@showHorarios');
    Route::post('profissionais/horarios-trabalho', 'ProfissionaisController@horarios');
    Route::post('profissionais/servicos', 'ProfissionaisController@storeServicos');
    Route::delete('profissionais/servicos/{id}', 'ProfissionaisController@destroyServicos');

    Route::apiResource('meios-pagamentos', 'MeiosPagamentosController');
    Route::apiResource('clientes', 'ClientesController');
    Route::apiResource('produtos', 'ProdutosController');
    Route::apiResource('fornecedores', 'FornecedoresController');
    Route::apiResource('agendamentos', 'AgendamentosController');

    Route::get('movimentacoes-estoque/tipos-movimentacoes', 'MovimentacoesEstoquesController@tiposMovimentacoes');
    Route::get('movimentacoes-estoque', 'MovimentacoesEstoquesController@index');
    Route::post('movimentacoes-estoque', 'MovimentacoesEstoquesController@store');

    Route::get('caixas', 'CaixasController@index');
    Route::post('caixas', 'CaixasController@store');
    Route::put('caixas/{id}', 'CaixasController@update');
    Route::put('reabrir-caixa/{id}', 'CaixasController@reabrir');

    Route::get('comandas', 'ComandasController@index');
    Route::post('comandas', 'ComandasController@store');
    Route::put('comandas/{id}', 'ComandasController@update');
    Route::delete('comandas/{id}', 'ComandasController@destroy');
    Route::put('estornar-comanda/{id}', 'ComandasController@estornar');

    Route::put('meus-dados', 'AutenticacaoController@meusDados');
});
