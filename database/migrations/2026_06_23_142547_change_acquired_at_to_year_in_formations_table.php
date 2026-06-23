<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('formations', function (Blueprint $table) {
            $table->dropColumn('acquired_at');
        });

        Schema::table('formations', function (Blueprint $table) {
            $table->string('year', 4)->after('institution');
        });
    }

    public function down(): void
    {
        Schema::table('formations', function (Blueprint $table) {
            $table->dropColumn('year');
        });

        Schema::table('formations', function (Blueprint $table) {
            $table->date('acquired_at')->after('institution');
        });
    }
};
