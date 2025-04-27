<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangMasuks extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'id_barang',
        'jumlah',
        'tanggal_masuk',
        'keterangan',
    ];

    public $timestamps = true;

    public function barang()
    {
        return $this->belongsTo(Barangs::class, 'id_barang');
    }

    public function deleteImage(){
        if($this->cover && file_exists(public_path('image/barang-masuk' . $this->cover))) {
            return unlink(public_path('image/barang-masuk' . $this->cover));
        }
    }
}
