<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('language_id')->nullable();
            $table->string('locale', 10)->nullable()->index();
            $table->string('tag')->nullable()->index();
            $table->string('name')->nullable();
            $table->string('key');
            $table->text('content');
            $table->timestamps();

            $table->index('key');
            $table->index('content');

            $table->foreign('language_id')->references('id')->on('languages')->onDelete('cascade');
            $table->unique(['key', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
};
