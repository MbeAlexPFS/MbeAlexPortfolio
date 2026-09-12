<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_progress', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->morphs('reference');
            $table->integer('total')->default(0);
            $table->integer('completed')->default(0);
            $table->string('status')->default('pending');
            $table->string('error')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_progress');
    }
};
