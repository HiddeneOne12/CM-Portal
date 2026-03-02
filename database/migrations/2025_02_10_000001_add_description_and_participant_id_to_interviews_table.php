<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('interviews', function (Blueprint $table) {
            $table->text('description')->nullable()->after('title');
            $table->foreignId('participant_id')->nullable()->after('description')->constrained('participants')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('interviews', function (Blueprint $table) {
            $table->dropForeign(['participant_id']);
            $table->dropColumn(['description', 'participant_id']);
        });
    }
};
