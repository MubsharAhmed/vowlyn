<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin user for the Filament panel (/admin)
        User::query()->updateOrCreate(
            ['email' => 'admin@vowlyn.com'],
            [
                'name' => 'Vowlyn Admin',
                'password' => Hash::make('vowlyn-admin'),
                'email_verified_at' => Carbon::now(),
            ]
        );

        $this->call([
            ContactRequestSeeder::class,
        ]);
    }
}
