<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin User
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('12345678'),
            ]
        );

        // Branches and Stores
        $branches = ['Branch Jakarta', 'Branch Bandung', 'Branch Surabaya'];

        foreach ($branches as $branchName) {
            $branch = Branch::create(['name' => $branchName]);

            for ($i = 1; $i <= 3; $i++) {
                Store::create([
                    'branch_id' => $branch->id,
                    'name' => "Store $i - $branchName",
                ]);
            }
        }
    }
}
