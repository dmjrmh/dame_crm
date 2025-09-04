<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
  use HasFactory, SoftDeletes;

  protected $fillable = [
    'name',
    'sku',
    'unit',
    'cost_price',
    'margin_percent',
    'sell_price',
    'description',
  ];

  protected $casts = [
    'cost_price' => 'decimal:2',
    'margin_percent' => 'decimal:2',
    'sell_price' => 'decimal:2',
  ];

  
  public function getMarginAmountAttribute(): float
  {
    return round((float)$this->sell_price - (float)$this->cost_price, 2);
  }
}
