<?php

namespace App\Models;

use App\Models\Cart;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = ['nama', 'deskripsi', 'kategori', 'stok', 'harga'];

    public function cart()
    {
        return $this->hasMany(Cart::class);
    }
}
