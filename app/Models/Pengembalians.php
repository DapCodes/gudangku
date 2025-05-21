<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengembalians extends Model
{
    use HasFactory;

    protected $fillable = [
       'id',
       'kode_barang',
       'jumlah',
       'tanggal_kembali',
       'nama_peminjam',
       'status',
       'id_peminjam',
       'id_barang', 
       'ruangan_id',
    ];

    public $timestamps = true;

    public function ruangan()
    {
        return $this->belongsTo(Ruangans::class, 'ruangan_id');
    }


    public function barang() {
        return $this->belongsTo(Barangs::class,'id_barang');
    }

    public function peminjamans() {
        return $this->belongsTo(Peminjamans::class,'id_peminjam');
    }
}