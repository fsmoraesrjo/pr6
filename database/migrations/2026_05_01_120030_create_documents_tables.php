<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('document_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('document_categories')->nullOnDelete();
            $table->string('name', 120);
            $table->string('slug', 140);
            $table->string('icon', 64)->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();

            $table->unique(['tenant_id', 'slug']);
        });

        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('document_categories')->nullOnDelete();
            $table->foreignId('current_version_id')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title', 220);
            $table->string('slug', 240);
            $table->text('description')->nullable();
            $table->unsignedBigInteger('downloads_count')->default(0);
            $table->boolean('is_public')->default(true);
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'slug']);
            $table->fullText(['title', 'description']);
        });

        Schema::create('document_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained()->cascadeOnDelete();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('version_label', 32);
            $table->string('file_path');
            $table->string('original_name', 220)->nullable();
            $table->string('file_hash', 64)->nullable();
            $table->unsignedBigInteger('size_bytes')->default(0);
            $table->string('mime_type', 120)->nullable();
            $table->text('changelog')->nullable();
            $table->boolean('is_current')->default(false);
            $table->timestamps();
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->foreign('current_version_id')->references('id')->on('document_versions')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['current_version_id']);
        });
        Schema::dropIfExists('document_versions');
        Schema::dropIfExists('documents');
        Schema::dropIfExists('document_categories');
    }
};
