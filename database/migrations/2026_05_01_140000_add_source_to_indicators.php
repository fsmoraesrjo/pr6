<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('indicators', function (Blueprint $table) {
            $table->string('source', 32)->default('manual')->after('chart_type')->index();
            $table->json('source_config')->nullable()->after('source');
            $table->timestamp('last_synced_at')->nullable()->after('source_config');
            $table->string('icon', 64)->nullable()->after('last_synced_at');
            $table->string('color', 9)->nullable()->after('icon');
        });
    }

    public function down(): void
    {
        Schema::table('indicators', function (Blueprint $table) {
            $table->dropColumn(['source', 'source_config', 'last_synced_at', 'icon', 'color']);
        });
    }
};
