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
        Schema::create('stok', function (Blueprint $table) {
            $table->id('id_stok');
            $table->string('id_produk', 10);
            $table->integer('stok');
            $table->date('tgl_penerimaan');
            $table->date('tgl_kadaluwarsa')->nullable();
            $table->timestamps();

            $table->foreign('id_produk')->references('id_produk')->on('produk')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stok', function (Blueprint $table) {
            $table->dropForeign(['id_produk']);
        });

        Schema::dropIfExists('stok');
    }
};
