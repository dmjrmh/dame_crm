<?php

namespace App\Services;

use App\Models\Deal;
use App\Models\PipelineStage;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;

class DealApprovalService
{
  public function evaluate(Deal $deal, Collection $items): array
  {
    // load products
    $items = $items->map(function ($row) {
      $row['sell_price'] = $row['product']->sell_price ?? $row['product']->sell_price ?? $row['product']->sell_price;
      return $row;
    });

    $hasBelowSell = $items->contains(function ($row) {
      return (float)$row['unit_price'] < (float)$row['product']->sell_price;
    });

    $now = Carbon::now();

    if (!$hasBelowSell) {
      // auto approved → won
      $wonStageId = PipelineStage::query()->where('is_won', true)->value('id')
        ?? PipelineStage::query()->where('key', 'won')->value('id');

      return [
        'approval_status'   => 'approved',
        'approved_at'       => $now,
        'approver_id'       => null,
        'approval_notes'    => 'Auto-approved: all unit prices ≥ sell price.',
        'pipeline_stage_id' => $wonStageId,
        'closed_at'         => $now,
      ];
    }

    // needs approval → waiting approval
    $waitingStageId = PipelineStage::query()->where('key', 'waiting_approval')->value('id');

    return [
      'approval_status'   => 'pending',
      'approved_at'       => null,
      'approver_id'       => null,
      'approval_notes'    => null,
      'pipeline_stage_id' => $waitingStageId,
      'closed_at'         => null,
    ];
  }
}
