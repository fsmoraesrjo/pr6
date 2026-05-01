<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 32)->unique();
            $table->string('short_name', 32);
            $table->string('full_name', 160);
            $table->string('tagline', 200)->nullable();
            $table->text('description')->nullable();
            $table->string('domain_dev', 160)->nullable()->index();
            $table->string('domain_prod', 160)->nullable()->index();
            $table->string('accent_color', 9)->default('#B92828');
            $table->string('accent_soft_color', 9)->default('#FCE4E5');
            $table->string('accent_deep_color', 9)->default('#8E1B1B');
            $table->string('logo_path')->nullable();
            $table->string('icon', 64)->nullable();
            $table->string('cover_path')->nullable();
            $table->boolean('is_root')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('order')->default(0);
            $table->json('contact')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
