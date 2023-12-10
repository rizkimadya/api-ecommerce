<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PenjualSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'nama' => 'Penjual',
            'email' => 'penjual@gmail.com',
            'password' => bcrypt('penjual'),
            'role' => 'penjual',
        ]);
    }
}
