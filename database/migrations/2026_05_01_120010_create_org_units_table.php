<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('org_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('org_units')->nullOnDelete();
            $table->string('name', 160);
            $table->string('slug', 160);
            $table->enum('type', ['proreitoria', 'diretoria', 'coordenacao', 'gerencia', 'setor'])->default('setor');
            $table->text('description')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['tenant_id', 'slug']);
        });

        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('org_unit_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name', 160);
            $table->string('role_title', 160);
            $table->string('email', 160)->nullable();
            $table->string('phone', 32)->nullable();
            $table->string('photo_path')->nullable();
            $table->text('bio')->nullable();
            $table->json('social_links')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_head')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_members');
        Schema::dropIfExists('org_units');
    }
};
