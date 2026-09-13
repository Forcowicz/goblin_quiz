<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'forcowicz@gmail.com'],
            ['name' => 'Forcowicz', 'password' => 'password'],
        );

        User::firstOrCreate(
            ['email' => 'aleksandra@goblinquiz.app'],
            ['name' => 'Aleksandra', 'password' => 'password'],
        );
    }
}
