<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SalesUserSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    User::firstOrCreate(
      ['username' => 'elaen'],
      [
        'name' => 'Elaen Wazowski',
        'email' => 'elaen@smart.test',
        'password' => Hash::make('AtminSalesOne'),
        'role' => 'sales'
      ]
    );

    User::firstOrCreate(
      ['username' => 'lorenza'],
      [
        'name' => 'Lorenza Giordano',
        'email' => 'lorenza@smart.test',
        'password' => Hash::make('AtminSalesTwo'),
        'role' => 'sales'
      ]
    );

    User::firstOrCreate(
      ['username' => 'sulley'],
      [
        'name' => 'James Sulley',
        'email' => 'sulley@smart.test',
        'password' => Hash::make('AtminSalesThree'),
        'role' => 'sales'
      ]
    );
  }
}
