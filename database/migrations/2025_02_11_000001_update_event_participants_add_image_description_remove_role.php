<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_participants', function (Blueprint $table) {
            $table->string('image', 255)->nullable()->after('participant_id');
            $table->text('description')->nullable()->after('topic');
            $table->dropColumn('role');
        });
    }

    public function down(): void
    {
        Schema::table('event_participants', function (Blueprint $table) {
            $table->string('role', 255)->nullable()->after('participant_id');
            $table->dropColumn(['image', 'description']);
        });
    }
};
