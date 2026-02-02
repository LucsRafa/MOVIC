<?php

namespace Database\Seeders;

use App\Models\User;
use App\Enums\UserRole;
use App\Models\TeacherProfile;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $teacher = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'role' => UserRole::Teacher,
                'password' => bcrypt('password'),
            ]
        );
        if ($teacher) {
            TeacherProfile::firstOrCreate(['user_id' => $teacher->id]);
        }
    }
}
