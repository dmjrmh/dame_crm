<?php

namespace Database\Seeders;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class LeadSeeder extends Seeder
{
  public function run(): void
  {
    $faker = Faker::create('id_ID');

    $salesUsers = User::query()->where('role', 'sales')->get();
    foreach ($salesUsers as $sales) {
      $count = rand(5, 7);
      for ($i = 0; $i < $count; $i++) {
        Lead::firstOrCreate(
          [
            'user_id' => $sales->id,
            'name'    => $faker->name(),
            'contact' => '08' . rand(1111111111, 9999999999),
            'address' => $faker->address(),
            'needs'   => $faker->randomElement([
              'Kebutuhan produk A skala kecil',
              'Repeat order produk B',
              'Penawaran proyek integrasi',
              'Trial 1 bulan',
              'Diskusi pricing & volume'
            ]),
            'status'  => $faker->randomElement(['New', 'Follow up', 'In Progress']),
          ]
        );
      }
    }
  }
}
