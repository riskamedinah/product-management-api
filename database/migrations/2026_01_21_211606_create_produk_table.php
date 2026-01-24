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
        Schema::create('produk', function (Blueprint $table) {
            $table->string('id_produk', 10)->primary();
            $table->string('nama_produk', 100);
            $table->decimal('harga', 15, 2);
            $table->boolean('status')->default(1);
            $table->text('image_url')->nullable();
            $table->string('id_kategori', 10);
            $table->timestamps();

            $table->foreign('id_kategori')->references('id_kategori')->on('kategori')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produk', function (Blueprint $table) {
            $table->dropForeign(['id_kategori']);
        });

        Schema::dropIfExists('produk');
    }
};
