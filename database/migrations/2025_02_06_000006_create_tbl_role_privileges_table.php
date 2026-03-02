<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_role_privileges', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('role_ID');
            $table->unsignedBigInteger('module_ID');
            $table->timestamps();

            $table->foreign('role_ID')->references('id')->on('tbl_roles')->onDelete('cascade');
            $table->foreign('module_ID')->references('id')->on('tbl_modules')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_role_privileges');
    }
};
