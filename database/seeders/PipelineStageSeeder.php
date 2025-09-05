<?php

namespace Database\Seeders;

use App\Models\PipelineStage;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PipelineStageSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $stages = [
      [
        'key' => 'prospect',
        'name' => 'Prospect',
        'order' => 1,
        'is_closed' => false,
        'is_won' => false,
        'is_lost' => false,
      ],
      [
        'key' => 'negotiation',
        'name' => 'Negotiation',
        'order' => 2,
        'is_closed' => false,
        'is_won' => false,
        'is_lost' => false,
      ],
      [
        'key' => 'waiting_approval',
        'name' => 'Waiting Approval',
        'order' => 3,
        'is_closed' => false,
        'is_won' => false,
        'is_lost' => false,
      ],
      [
        'key' => 'won',
        'name' => 'Won',
        'order' => 4,
        'is_closed' => true,
        'is_won' => true,
        'is_lost' => false,
      ],
      [
        'key' => 'lost',
        'name' => 'Lost',
        'order' => 5,
        'is_closed' => true,
        'is_won' => false,
        'is_lost' => true,
      ],
    ];

    foreach ($stages as $stage) {
      PipelineStage::updateOrCreate(
        ['key' => $stage['key']],
        $stage
      );
    }
  }
}
