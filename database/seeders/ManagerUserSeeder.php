<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;

class ManagerUserSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    User::firstOrCreate(
      ['username' => 'manager'],
      [
        'name' => 'Manager Smart',
        'email' => 'manager@smart.test',
        'password' => Hash::make('passwordManager'),
        'role' => 'manager'
      ]
    );
  }
}
