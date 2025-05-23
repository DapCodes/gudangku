<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barangs extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_barang',
        'nama',
        'merek',
        'foto',
        'stok',
        'status_barang',
    ];

    public $timestamps = true;

    public function barangmasuk()
    {
        return $this->hasMany(BarangMasuk::class);
    }
    public function barangkeluar()
    {
        return $this->hasMany(BarangKeluar::class);
    }
    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class);
    }
    public function pengembalian()
    {
        return $this->hasMany(Pengembalian::class);
    }
    public function barangruangan()
    {
        return $this->hasMany(BarangRuangan::class);
    }

    public function deleteImage(){
        if($this->cover && file_exists(public_path('image/barang' . $this->cover))) {
            return unlink(public_path('image/barang' . $this->cover));
        }
    }
}
