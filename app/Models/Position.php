<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'name',
        'tax',
        'price',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
