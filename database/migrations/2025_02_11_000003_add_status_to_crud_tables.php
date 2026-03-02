<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = ['hero', 'interviews', 'reports', 'events', 'portal_users', 'participants'];
        foreach ($tables as $name) {
            Schema::table($name, function (Blueprint $table) {
                $table->unsignedTinyInteger('status')->default(1)->after('id');
            });
        }
    }

    public function down(): void
    {
        $tables = ['hero', 'interviews', 'reports', 'events', 'portal_users', 'participants'];
        foreach ($tables as $name) {
            Schema::table($name, function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
};
