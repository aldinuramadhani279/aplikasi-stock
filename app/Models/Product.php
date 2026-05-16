<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'sku',
        'unit',
        'current_stock',
        'minimum_stock',
        'maximum_stock',
        'description',
        'image',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'current_stock' => 'integer',
        'minimum_stock' => 'integer',
        'maximum_stock' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function stockAlerts()
    {
        return $this->hasMany(StockAlert::class);
    }

    public function isLowStock(): bool
    {
        return $this->current_stock <= $this->minimum_stock && $this->current_stock > 0;
    }

    public function isOutOfStock(): bool
    {
        return $this->current_stock === 0;
    }

    public function getStockStatusAttribute(): string
    {
        if ($this->isOutOfStock()) return 'HABIS';
        if ($this->isLowStock()) return 'MENIPIS';
        return 'AMAN';
    }

    public function getStockStatusColorAttribute(): string
    {
        if ($this->isOutOfStock()) return 'red';
        if ($this->isLowStock()) return 'yellow';
        return 'green';
    }
}
