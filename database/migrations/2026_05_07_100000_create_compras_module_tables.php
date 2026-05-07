<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Catalogo de equipamentos disponiveis para entrar em pesquisas
        Schema::create('compras_equipamentos', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 200);
            $table->string('categoria', 80)->index();
            $table->text('descricao')->nullable();
            $table->string('especificacao_resumida', 500)->nullable();
            $table->string('unidade_medida', 30)->default('Unidade');
            $table->text('observacoes')->nullable();
            $table->boolean('ativo')->default(true)->index();
            $table->timestamps();
        });

        // Respondentes pre-cadastrados (NAO sao users PR6 — login via magic link)
        Schema::create('compras_respondentes', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 150);
            $table->string('email', 150);
            $table->foreignId('org_unit_id')->constrained('org_units')->restrictOnDelete();
            $table->string('cargo', 120)->nullable();
            $table->string('telefone', 30)->nullable();
            $table->boolean('ativo')->default(true)->index();
            $table->timestamps();
            $table->unique(['email', 'org_unit_id']);
        });

        // Pesquisas — ciclo: rascunho -> aberta -> encerrada -> consolidada / cancelada
        Schema::create('compras_pesquisas', function (Blueprint $table) {
            $table->id();
            $table->string('titulo', 255);
            $table->text('descricao')->nullable();
            $table->text('justificativa')->nullable();
            $table->text('instrucoes')->nullable();
            $table->string('status', 30)->default('rascunho')->index();
            $table->date('data_abertura')->nullable();
            $table->date('data_encerramento')->nullable();
            $table->foreignId('criado_por_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('aberta_em')->nullable();
            $table->timestamp('encerrada_em')->nullable();
            $table->timestamp('consolidada_em')->nullable();
            $table->timestamps();
        });

        // Itens da pesquisa (ordem importa para apresentacao)
        Schema::create('compras_pesquisa_equipamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesquisa_id')->constrained('compras_pesquisas')->cascadeOnDelete();
            $table->foreignId('equipamento_id')->constrained('compras_equipamentos')->restrictOnDelete();
            $table->unsignedSmallInteger('ordem')->default(0);
            $table->text('observacoes')->nullable();
            $table->timestamps();
            $table->unique(['pesquisa_id', 'equipamento_id']);
            $table->index(['pesquisa_id', 'ordem']);
        });

        // Setores destinatarios (snapshot da unidade no momento da pesquisa)
        Schema::create('compras_pesquisa_setores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesquisa_id')->constrained('compras_pesquisas')->cascadeOnDelete();
            $table->foreignId('org_unit_id')->constrained('org_units')->restrictOnDelete();
            $table->string('unidade_nome_snapshot', 200);
            $table->string('unidade_sigla_snapshot', 30)->nullable();
            $table->boolean('respondido')->default(false)->index();
            $table->boolean('sem_demanda')->default(false);
            $table->timestamp('respondido_em')->nullable();
            $table->foreignId('respondido_por_respondente_id')->nullable()
                  ->constrained('compras_respondentes')->nullOnDelete();
            $table->timestamps();
            $table->unique(['pesquisa_id', 'org_unit_id']);
        });

        // Quantidade respondida por (setor x item da pesquisa)
        Schema::create('compras_respostas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesquisa_setor_id')->constrained('compras_pesquisa_setores')->cascadeOnDelete();
            $table->foreignId('pesquisa_equipamento_id')->constrained('compras_pesquisa_equipamentos')->cascadeOnDelete();
            $table->unsignedInteger('quantidade')->default(0);
            $table->text('observacoes')->nullable();
            $table->timestamps();
            $table->unique(['pesquisa_setor_id', 'pesquisa_equipamento_id'], 'compras_resp_setor_eq_unique');
        });

        // Distribuicao fisica (campus / pavilhao / sala) por resposta
        Schema::create('compras_resposta_destinos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resposta_id')->constrained('compras_respostas')->cascadeOnDelete();
            $table->foreignId('org_unit_id')->nullable()->constrained('org_units')->nullOnDelete();
            $table->string('campus', 100)->nullable();
            $table->string('pavilhao', 100)->nullable();
            $table->string('andar', 30)->nullable();
            $table->string('sala', 50)->nullable();
            $table->string('ambiente_complementar', 200)->nullable();
            $table->text('descricao_local')->nullable();
            $table->unsignedInteger('quantidade_destino')->default(1);
            $table->timestamps();
            $table->index('resposta_id');
        });

        // Substituicao de patrimonio antigo
        Schema::create('compras_resposta_trocas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resposta_id')->constrained('compras_respostas')->cascadeOnDelete();
            $table->string('tipo_troca', 30); // ex: 'substituicao', 'expansao', 'reposicao'
            $table->string('equipamento_antigo_tipo', 200)->nullable();
            $table->string('equipamento_antigo_patrimonio', 50)->nullable();
            $table->string('equipamento_antigo_estado', 30)->nullable();
            $table->string('destino_equipamento_antigo', 200)->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();
            $table->index('resposta_id');
        });

        // Magic link tokens (single-use, hash SHA-256)
        Schema::create('compras_login_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('respondente_id')->constrained('compras_respondentes')->cascadeOnDelete();
            $table->foreignId('pesquisa_id')->constrained('compras_pesquisas')->cascadeOnDelete();
            $table->string('token_hash', 64)->unique(); // SHA-256 hex
            $table->timestamp('expires_at')->index();
            $table->timestamp('used_at')->nullable();
            $table->string('used_ip', 45)->nullable();
            $table->string('used_user_agent', 255)->nullable();
            $table->timestamps();
            $table->index(['respondente_id', 'pesquisa_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compras_login_tokens');
        Schema::dropIfExists('compras_resposta_trocas');
        Schema::dropIfExists('compras_resposta_destinos');
        Schema::dropIfExists('compras_respostas');
        Schema::dropIfExists('compras_pesquisa_setores');
        Schema::dropIfExists('compras_pesquisa_equipamentos');
        Schema::dropIfExists('compras_pesquisas');
        Schema::dropIfExists('compras_respondentes');
        Schema::dropIfExists('compras_equipamentos');
    }
};
