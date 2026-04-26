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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('course_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('course_material_id') 
                  ->nullable()
                  ->constrained('course_materials')
                  ->nullOnDelete();

            $table->string('type')->default('mcq'); // mcq, tf, short_answer...
            $table->text('text');                  // نص السؤال

            $table->json('choices')->nullable();   // خيارات الـ MCQ
            $table->string('correct_answer')->nullable(); // index أو قيمة

            $table->string('difficulty')->nullable(); // easy/medium/hard
            $table->string('source')->default('manual'); // manual / ai
            $table->string('status')->default('draft');  // draft / approved / rejected

            $table->foreignId('created_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
