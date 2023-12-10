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
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            $table->string("order_id");
            $table->unsignedBigInteger("produk_id");
            $table->unsignedBigInteger("pembeli_id");
            $table->string("total_harga");
            $table->string("status");
            $table->timestamps();

            $table->foreign('produk_id')->references('id')->on('produks');
            $table->foreign('pembeli_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};
