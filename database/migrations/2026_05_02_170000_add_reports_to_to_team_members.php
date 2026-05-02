<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('team_members', function (Blueprint $table) {
            $table->foreignId('reports_to_id')
                ->nullable()
                ->after('org_unit_id')
                ->constrained('team_members')
                ->nullOnDelete();
            $table->boolean('is_advisor')->default(false)->after('is_head');
        });
    }

    public function down(): void
    {
        Schema::table('team_members', function (Blueprint $table) {
            $table->dropForeign(['reports_to_id']);
            $table->dropColumn(['reports_to_id', 'is_advisor']);
        });
    }
};
