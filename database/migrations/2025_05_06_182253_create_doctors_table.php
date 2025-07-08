<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();

            $table->string('name');

            $table->unsignedBigInteger('specialty_id')->nullable();

            $table->timestamps();

            $table->foreign('specialty_id')->references('id')->on('specialties')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};