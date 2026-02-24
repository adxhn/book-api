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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('author_id')->constrained()->onUpdate('cascade')->restrictOnDelete(); // Bu kayıt varken yazar silinemez
            $table->foreignId('category_id')->constrained()->onUpdate('cascade')->restrictOnDelete(); // Bu kayıt varken kategori silinemez
            $table->foreignId('publisher_id')->constrained()->onUpdate('cascade')->restrictOnDelete(); // Bu kayıt varken yayınevi silinemez
            $table->string('title', 255);
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('isbn')->unique(); // ISBN (Eşleştirme için kritik)
            $table->string('image_url')->nullable(); // Kapak görseli linki
            $table->date('publish_date')->nullable(); // ilk basım tarihi
            $table->smallInteger('page_number')->default(0);
            $table->enum('book_status', \App\Enums\BookStatus::values())->default(\App\Enums\BookStatus::ACTIVE);
            $table->timestamps();

            $table->fullText('title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
