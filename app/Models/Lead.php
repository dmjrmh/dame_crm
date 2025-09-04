<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lead extends Model
{
    use HasFactory,SoftDeletes;

    const STATUS_NEW = 'new';
    const STATUS_FOLLOW_UP = 'follow_up';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_CONVERTED = 'converted';
    const STATUS_LOST = 'lost';

    protected $fillable = [
      'name', 'contact', 'address', 'needs', 'status', 'user_id', 
    ];

    public function user() {
      return $this->belongsTo(User::class);
    }
}
