<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Meja;
use App\Models\Menu;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Akun Admin
        User::create([
            'username' => 'Mak Mitha',
            'password' => Hash::make('adminNBG123'),
            'role' => 'admin',
        ]);

        // 2. Akun Kasir
        User::create([
            'username' => 'Kasir NBG',
            'password' => Hash::make('kasirNBG'),
            'role' => 'kasir',
        ]);

        // 3. Data Meja
        for ($i = 1; $i <= 5; $i++) {
            Meja::create([
                'nomor_meja' => 'Meja ' . $i,
                'qr_token' => Str::uuid()->toString(),
            ]);
        }

        // 4. Data Menu
        $menus = [
           
        ];

        foreach ($menus as $menu) {
            Menu::create($menu);
        }
    }
}
