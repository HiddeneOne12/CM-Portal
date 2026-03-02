<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_modules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('module_category_ID');
            $table->string('module_name', 155);
            $table->string('route', 155);
            $table->boolean('show_in_menu')->default(true);
            $table->string('css_class', 100)->nullable();
            $table->unsignedInteger('display_order')->default(0);
            $table->timestamps();

            $table->foreign('module_category_ID')->references('id')->on('tbl_module_categories')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_modules');
    }
};
