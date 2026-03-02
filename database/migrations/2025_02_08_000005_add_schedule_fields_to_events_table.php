<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('image')->constrained('companies')->nullOnDelete()->cascadeOnUpdate();
            $table->time('start_time')->nullable()->after('company_id');
            $table->time('end_time')->nullable()->after('start_time');
            $table->string('location', 500)->nullable()->after('end_time');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn(['company_id', 'start_time', 'end_time', 'location']);
        });
    }
};
