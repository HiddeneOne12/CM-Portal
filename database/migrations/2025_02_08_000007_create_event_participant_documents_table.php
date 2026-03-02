<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_participant_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_participant_id')->constrained('event_participants')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('title');
            $table->string('file_path', 500);
            $table->unsignedInteger('file_size')->nullable()->comment('Size in bytes for display');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_participant_documents');
    }
};
