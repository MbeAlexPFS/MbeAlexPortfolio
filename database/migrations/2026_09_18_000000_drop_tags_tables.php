<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('article_tag');
        Schema::dropIfExists('project_tag');
        Schema::dropIfExists('tags');
    }

    public function down(): void
    {
        Schema::dropIfExists('tags');
    }
};