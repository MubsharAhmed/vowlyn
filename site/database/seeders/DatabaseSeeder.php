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
        $localAdminEmail = env('LOCAL_ADMIN_EMAIL');
        $localAdminPassword = env('LOCAL_ADMIN_PASSWORD');

        if (app()->environment('local') && filled($localAdminEmail) && filled($localAdminPassword)) {
            User::query()->updateOrCreate(
                ['email' => $localAdminEmail],
                [
                    'name' => 'Vowlyn Local Admin',
                    'password' => Hash::make($localAdminPassword),
                    'email_verified_at' => Carbon::now(),
                ]
            );
        }

        $this->call([
            ContactRequestSeeder::class,
        ]);
    }
}
