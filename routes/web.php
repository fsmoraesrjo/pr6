<?php

use App\Http\Controllers\Site\AgendaController;
use App\Http\Controllers\Site\DocumentController;
use App\Http\Controllers\Site\HomeController;
use App\Http\Controllers\Site\NewsController;
use App\Http\Controllers\Site\ServiceController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/noticias', [NewsController::class, 'index'])->name('news.index');
Route::get('/noticias/{slug}', [NewsController::class, 'show'])->name('news.show');

Route::get('/documentos', [DocumentController::class, 'index'])->name('documents.index');
Route::get('/documentos/{slug}', [DocumentController::class, 'show'])->name('documents.show');
Route::get('/documentos/{slug}/download/{versionId?}', [DocumentController::class, 'download'])->name('documents.download');

Route::get('/agenda', [AgendaController::class, 'index'])->name('agenda');
Route::get('/agenda/feed', [AgendaController::class, 'feed'])->name('agenda.feed');
Route::get('/agenda/{slug}.ics', [AgendaController::class, 'ics'])->name('agenda.ics');

Route::view('/contratos', 'site.placeholder', ['titulo' => 'Contratos'])->name('contratos');
Route::view('/transparencia', 'site.placeholder', ['titulo' => 'Transparência'])->name('transparencia');

Route::get('/servicos', [ServiceController::class, 'index'])->name('services.index');
Route::get('/servicos/{slug}', [ServiceController::class, 'show'])->name('services.show');

Route::view('/sobre', 'site.placeholder', ['titulo' => 'Sobre'])->name('sobre');
Route::view('/indicadores', 'site.placeholder', ['titulo' => 'Indicadores'])->name('indicadores');
Route::view('/contato', 'site.placeholder', ['titulo' => 'Contato'])->name('contato');
Route::redirect('/fale-conosco', '/contato');
Route::get('/pessoas', [\App\Http\Controllers\Site\PeopleController::class, 'index'])->name('pessoas');
Route::get('/organograma', [\App\Http\Controllers\Site\PeopleController::class, 'chart'])->name('organograma');
Route::redirect('/equipe', '/pessoas');
Route::view('/privacidade', 'site.placeholder', ['titulo' => 'Política de privacidade'])->name('privacidade');
Route::view('/lgpd', 'site.placeholder', ['titulo' => 'Encarregado de dados (DPO)'])->name('lgpd');
Route::view('/acessibilidade', 'site.placeholder', ['titulo' => 'Acessibilidade'])->name('acessibilidade');
