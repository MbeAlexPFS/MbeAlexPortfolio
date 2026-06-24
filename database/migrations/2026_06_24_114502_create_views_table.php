<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('views', function (Blueprint $table) {
            $table->id();
            $table->morphs('viewable');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['viewable_type', 'viewable_id', 'ip_address']);
            $table->index(['viewable_type', 'viewable_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('views');
    }
};
