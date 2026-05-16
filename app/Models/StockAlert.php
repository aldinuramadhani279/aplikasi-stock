<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'type',
        'is_resolved',
        'notified_at',
        'resolved_at',
    ];

    protected $casts = [
        'is_resolved' => 'boolean',
        'notified_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
