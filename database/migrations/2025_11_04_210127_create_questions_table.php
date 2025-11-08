<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subchapter_id')->constrained()->cascadeOnDelete();
            $table->text('question_text');
            $table->text('explanation');
            $table->integer('answerIndex')->nullable(); // index of correct option
            $table->text('tags')->nullable();
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('questions');
    }
};

