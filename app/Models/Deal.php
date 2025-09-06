<?php

namespace App\Models;

use App\Models\Lead;
use App\Models\User;
use App\Models\Customer;
use App\Models\PipelineStage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Deal extends Model
{
  use HasFactory, SoftDeletes;
  public const APPROVAL_STATUSES = ['none', 'pending', 'approved', 'rejected'];

  protected $fillable = [
    'user_id',
    'customer_id',
    'lead_id',
    'title',
    'amount',
    'pipeline_stage_id',
    'approval_status',
    'approved_at',
    'approver_id',
    'approval_notes',
    'notes',
  ];

  protected $casts = [
    'amount' => 'decimal:2',
    'probability' => 'integer',
    'expected_close_date' => 'date',
    'approved_at' => 'datetime',
    'closed_at' => 'datetime',
    'deleted_at' => 'datetime',
  ];

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function items()
  {
    return $this->hasMany(DealItem::class);
  }

  public function pipelineStage(){
    return $this->belongsTo(PipelineStage::class);
  }
  
  public function customer()
  {
    return $this->belongsTo(Customer::class);
  }

  public function lead()
  {
    return $this->belongsTo(Lead::class);
  }
}
