<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
  public function run(): void
  {
    $salesUsers = User::query()->where('role', 'sales')->get();

    foreach ($salesUsers as $sales) {
      $count = rand(3, 5);
      for ($i = 0; $i < $count; $i++) {
        $name = fake()->randomElement([
          'Widget Prime',
          'Service Support',
          'Internet 100 Mbps',
          'Internet 50 Mbps',
          'Bundle Internet TV 30 Mbps',
          'Bundle Alpha Internet TV 50 Mbps',
        ]) . ' ' . Str::upper(Str::random(2));

        $cost   = fake()->numberBetween(50_000, 200_000);
        $cost   = round($cost, -3);

        $margin = fake()->numberBetween(10, 35);
        $sell   = round($cost * (1 + $margin / 100), -3);

        Product::firstOrCreate(
          [
            'sku' => Str::upper(Str::random(8)),
            'name'           => $name,
            'unit'           => fake()->randomElement(['pcs', 'pack', 'set', 'license']),
            'cost_price'     => $cost,
            'margin_percent' => $margin,
            'sell_price'     => $sell,
            'description'    => fake()->sentence(8),
          ]
        );
      }
    }
  }
}
