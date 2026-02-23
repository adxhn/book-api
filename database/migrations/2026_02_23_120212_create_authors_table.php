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
        Schema::create('authors', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique(); // Yazarın tam adı (Eşleştirme için benzersiz olması iyidir)
            $table->string('slug', 120)->unique(); // SEO dostu URL: /yazar/stefan-zweig
            $table->text('bio')->nullable(); // Yazarın biyografisi
            $table->string('photo_url')->nullable(); // Yazarın profil fotoğrafı
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('authors');
    }
};
