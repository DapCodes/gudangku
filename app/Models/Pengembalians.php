<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengembalians extends Model
{
    use HasFactory;

    protected $fillable = [
       'id',
       'jumlah',
       'tanggal_kembali',
       'nama_peminjam',
       'status',
       'id_peminjam',
       'id_barang', 
    ];

    public $timestamps = true;


    public function barang() {
        return $this->belongsTo(Barangs::class,'id_barang');
    }

    public function peminjamans() {
        return $this->belongsTo(Peminjams::class,'id_peminjam');
    }
}