<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $kasirs = [
            ['username' => 'Kasir NBG', 'password' => 'kasirNBG'],
        ];

        foreach ($kasirs as $kasir) {
            User::updateOrCreate(
                ['username' => $kasir['username']],
                ['password' => Hash::make($kasir['password'])]
            );
        }

        echo "Kasir users seeded!\n";
    }
}
