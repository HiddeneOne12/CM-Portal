<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_admin', function (Blueprint $table) {
            $table->id();
            $table->string('user_name', 100)->unique();
            $table->boolean('is_active')->default(true);
            $table->string('remember_token', 255)->nullable();
            $table->string('password');
            $table->string('theme_color', 50)->nullable();
            $table->string('user_type', 20)->default('custom');
            $table->boolean('is_deleted')->default(false);
            $table->dateTime('deleted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_admin');
    }
};
