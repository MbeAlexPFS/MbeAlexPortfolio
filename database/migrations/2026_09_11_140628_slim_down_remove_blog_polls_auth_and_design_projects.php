<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('projects')->where('type', '!=', 'web_static')->delete();

        Schema::dropIfExists('answer_option');
        Schema::dropIfExists('answers');
        Schema::dropIfExists('question_options');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('polls');
        Schema::dropIfExists('comments');
        Schema::dropIfExists('article_tag');
        Schema::dropIfExists('articles');
        Schema::dropIfExists('views');

        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_google_id_unique');
            $table->dropColumn([
                'google_id',
                'otp_code',
                'otp_expires_at',
                'newsletter_articles',
                'newsletter_polls',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('google_id')->unique()->nullable()->after('password');
            $table->string('otp_code', 6)->nullable()->after('is_verified');
            $table->timestamp('otp_expires_at')->nullable()->after('otp_code');
            $table->boolean('newsletter_articles')->default(false)->after('avatar_url');
            $table->boolean('newsletter_polls')->default(false)->after('newsletter_articles');
        });

        Schema::create('views', function (Blueprint $table) {
            $table->id();
            $table->morphs('viewable');
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('content');
            $table->longText('content_html')->nullable();
            $table->text('excerpt')->nullable();
            $table->string('image_url')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });

        Schema::create('article_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
        });

        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('article_id')->constrained()->cascadeOnDelete();
            $table->text('content');
            $table->boolean('is_published')->default(false);
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('polls', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->timestamps();
        });

        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poll_id')->constrained()->cascadeOnDelete();
            $table->string('text');
            $table->string('type');
            $table->boolean('is_required')->default(true);
            $table->unsignedInteger('order_index')->default(0);
            $table->timestamps();
        });

        Schema::create('question_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->string('text');
            $table->unsignedInteger('order_index')->default(0);
            $table->timestamps();
        });

        Schema::create('answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->string('text_response')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('answer_option', function (Blueprint $table) {
            $table->id();
            $table->foreignId('answer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_option_id')->constrained()->cascadeOnDelete();
        });
    }
};
