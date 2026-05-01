<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('indicators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name', 160);
            $table->string('slug', 160);
            $table->text('description')->nullable();
            $table->string('unit', 32)->default('%');
            $table->string('category', 80)->nullable();
            $table->enum('chart_type', ['line', 'bar', 'gauge', 'progress', 'area'])->default('progress');
            $table->decimal('goal_value', 14, 4)->nullable();
            $table->boolean('is_public')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();

            $table->unique(['tenant_id', 'slug']);
        });

        Schema::create('indicator_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('indicator_id')->constrained()->cascadeOnDelete();
            $table->string('period', 16);
            $table->decimal('value', 14, 4);
            $table->decimal('goal_value', 14, 4)->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('recorded_at');
            $table->timestamps();

            $table->unique(['indicator_id', 'period']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('indicator_values');
        Schema::dropIfExists('indicators');
    }
};
