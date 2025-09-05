<?php

namespace App\Models;

use App\Models\Deal;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DealItem extends Model
{
  use HasFactory, SoftDeletes;

  protected $fillable = [
    'product_id',
    'quantity',
    'unit_price',
  ];

  protected $casts = [
    'unit_price'          => 'decimal:2',
    'quantity'            => 'integer',
    'deleted_at'          => 'datetime',
  ];

  public function deal()
  {
    return $this->belongsTo(Deal::class);
  }
  public function product()
  {
    return $this->belongsTo(Product::class);
  }

  public function isBelowBaseSell(): bool
  {
    return (float) $this->unit_price < (float) $this->base_sell_price;
  }
}
