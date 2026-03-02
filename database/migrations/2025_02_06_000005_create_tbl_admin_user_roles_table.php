<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_admin_user_roles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_ID');
            $table->unsignedBigInteger('role_ID');
            $table->timestamps();

            $table->foreign('admin_ID')->references('id')->on('tbl_admin')->onDelete('cascade');
            $table->foreign('role_ID')->references('id')->on('tbl_roles')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_admin_user_roles');
    }
};
