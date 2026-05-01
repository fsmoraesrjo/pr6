<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('form_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('service_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('form_type', ['ouvidoria', 'contato', 'servico', 'interno'])->default('contato');
            $table->string('name', 160);
            $table->text('email_encrypted');
            $table->text('cpf_encrypted')->nullable();
            $table->string('phone', 32)->nullable();
            $table->string('subject', 220)->nullable();
            $table->longText('message');
            $table->enum('status', ['novo', 'em_analise', 'respondido', 'arquivado'])->default('novo');
            $table->longText('response')->nullable();
            $table->timestamp('response_at')->nullable();
            $table->timestamp('deadline_at')->nullable()->index();
            $table->string('ip_hash', 64)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('consent_logs', function (Blueprint $table) {
            $table->id();
            $table->string('session_hash', 64)->index();
            $table->string('consent_type', 32);
            $table->boolean('granted')->default(true);
            $table->timestamp('granted_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->string('ip_hash', 64)->nullable();
            $table->string('user_agent_hash', 64)->nullable();
            $table->timestamps();
        });

        Schema::create('data_subject_requests', function (Blueprint $table) {
            $table->id();
            $table->string('requester_name', 160);
            $table->text('email_encrypted');
            $table->text('cpf_encrypted');
            $table->enum('request_type', ['acesso', 'correcao', 'exclusao', 'portabilidade', 'revogacao_consentimento', 'oposicao']);
            $table->longText('description')->nullable();
            $table->enum('status', ['recebido', 'em_analise', 'concluido', 'rejeitado'])->default('recebido');
            $table->longText('response')->nullable();
            $table->timestamp('deadline_at')->index();
            $table->timestamp('completed_at')->nullable();
            $table->string('ip_hash', 64)->nullable();
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 64);
            $table->string('model_type', 160)->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->json('changes')->nullable();
            $table->string('ip_hash', 64)->nullable();
            $table->timestamps();

            $table->index(['model_type', 'model_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('data_subject_requests');
        Schema::dropIfExists('consent_logs');
        Schema::dropIfExists('form_submissions');
    }
};
