<?php

namespace App\Models;

use App\Models\Deal;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
  use HasFactory, SoftDeletes;
  protected $fillable = [
    'user_id',
    'lead_id',
    'name',
    'contact',
    'email',
    'address',
    'notes'
  ];

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function lead()
  {
    return $this->belongsTo(Lead::class);
  }

  public function deals()
  {
    return $this->hasMany(Deal::class);
  }
}
