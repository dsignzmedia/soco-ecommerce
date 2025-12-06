<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class MasterAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if master admin already exists
        $existingAdmin = User::where('email', 'admin@theskoolstore.com')->first();
        
        if (!$existingAdmin) {
            User::create([
                'name' => 'Master Admin',
                'email' => 'admin@theskoolstore.com',
                'phone' => '+91 9876543210',
                'password' => Hash::make('admin123'),
                'role' => User::ROLE_MASTER_ADMIN,
                'email_verified_at' => now(),
            ]);
            
            $this->command->info('Master Admin account created successfully!');
            $this->command->info('Email: admin@theskoolstore.com');
            $this->command->info('Password: admin123');
        } else {
            // Update existing admin to ensure correct role
            $existingAdmin->update(['role' => User::ROLE_MASTER_ADMIN]);
            $this->command->warn('Master Admin account already exists. Role updated.');
        }

        // Check if inventory admin already exists
        $existingInventoryAdmin = User::where('email', 'inventory@theskoolstore.com')->first();
        
        if (!$existingInventoryAdmin) {
            User::create([
                'name' => 'Inventory Admin',
                'email' => 'inventory@theskoolstore.com',
                'phone' => '+91 9876543211',
                'password' => Hash::make('inventory123'),
                'role' => User::ROLE_INVENTORY_ADMIN,
                'email_verified_at' => now(),
            ]);
            
            $this->command->info('Inventory Admin account created successfully!');
            $this->command->info('Email: inventory@theskoolstore.com');
            $this->command->info('Password: inventory123');
        } else {
            // Update existing admin to ensure correct role
            $existingInventoryAdmin->update(['role' => User::ROLE_INVENTORY_ADMIN]);
            $this->command->warn('Inventory Admin account already exists. Role updated.');
        }
    }
}
