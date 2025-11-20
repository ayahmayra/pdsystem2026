<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AssignDefaultRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Assign super-admin role to first user (or specific user by email)
        $superAdminUser = User::where('email', 'admin@example.com')->first();
        if ($superAdminUser) {
            $superAdminUser->assignRole('super-admin');
            $this->command->info('Super admin role assigned to: ' . $superAdminUser->email);
        } else {
            // If no specific super admin found, assign to first user
            $firstUser = User::first();
            if ($firstUser) {
                $firstUser->assignRole('super-admin');
                $this->command->info('Super admin role assigned to first user: ' . $firstUser->email);
            }
        }

        // You can add more specific role assignments here
        // For example, assign specific roles to specific users based on their email or other criteria
        
        // Example: Assign admin role to specific users
        // $adminUsers = User::whereIn('email', ['admin1@example.com', 'admin2@example.com'])->get();
        // foreach ($adminUsers as $user) {
        //     $user->assignRole('admin');
        //     $this->command->info('Admin role assigned to: ' . $user->email);
        // }

        // Example: Assign bendahara-pengeluaran role to specific users
        // $bendaharaUsers = User::whereIn('email', ['bendahara@example.com'])->get();
        // foreach ($bendaharaUsers as $user) {
        //     $user->assignRole('bendahara-pengeluaran');
        //     $this->command->info('Bendahara Pengeluaran role assigned to: ' . $user->email);
        // }

        $this->command->info('Default roles assignment completed!');
    }
}