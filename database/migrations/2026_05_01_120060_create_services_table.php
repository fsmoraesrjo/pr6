<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('service_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name', 120);
            $table->string('slug', 140);
            $table->string('icon', 64)->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();

            $table->unique(['tenant_id', 'slug']);
        });

        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('service_categories')->nullOnDelete();
            $table->string('title', 200);
            $table->string('slug', 220);
            $table->string('summary', 320)->nullable();
            $table->longText('description')->nullable();
            $table->string('audience', 160)->nullable();
            $table->string('icon', 64)->nullable();
            $table->enum('request_type', ['internal_form', 'external_url', 'email', 'info_only'])->default('internal_form');
            $table->string('request_url', 320)->nullable();
            $table->string('request_email', 160)->nullable();
            $table->json('requirements')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
        Schema::dropIfExists('service_categories');
    }
};
