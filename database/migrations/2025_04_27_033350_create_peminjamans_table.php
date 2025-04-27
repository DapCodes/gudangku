<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('peminjamans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_barang')->unique();
            $table->unsignedBigInteger('jumlah');
            $table->date('tanggal_pinjam');
            $table->date('tanggal_kembali');
            $table->string('nama_peminjam');
            $table->string('status');
            $table->unsignedBigInteger('id_barang');
            $table->timestamps();
            $table->foreign('id_barang')->references('id')->on('barangs')->onDelete('cascade');

        });
    }   

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('peminjamans');
    }
};
