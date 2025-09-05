<?php

namespace App\Models;

use App\Models\Deal;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PipelineStage extends Model
{
  use HasFactory, SoftDeletes;
  protected $fillable = [
    'key',
    'name',
    'order',
    'is_closed',
    'is_won',
    'is_lost'
  ];

  protected $casts = [
    'order' => 'integer',
    'is_closed' => 'boolean',
    'is_won' => 'boolean',
    'is_lost' => 'boolean',
    'deleted_at' => 'datetime',
  ];

  public function deals()
  {
    return $this->hasMany(Deal::class);
  }

  public function scopeOrdered($query)
  {
    return $query->orderBy('order');
  }
  public function scopeClosed($query, bool $closed = true)
  {
    return $query->where('is_closed', $closed);
  }
}
