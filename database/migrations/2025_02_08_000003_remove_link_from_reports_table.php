<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('reports', 'link')) {
            Schema::table('reports', function (Blueprint $table) {
                $table->dropColumn('link');
            });
        }
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->string('link', 500)->nullable()->after('report_pdf');
        });
    }
};
