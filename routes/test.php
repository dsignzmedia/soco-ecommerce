<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;
use Illuminate\Support\Facades\Log;

Route::get('/test-user-creation', function () {
    try {
        // Test creating a user
        $testEmail = 'testuser' . time() . '@example.com';
        $testUsername = 'testuser' . time();
        
        Log::info('Test - Attempting to create user', [
            'email' => $testEmail,
            'username' => $testUsername,
        ]);
        
        $user = User::create([
            'name' => $testUsername,
            'email' => $testEmail,
            'password' => bcrypt('password123'),
        ]);
        
        Log::info('Test - User created successfully', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_name' => $user->name,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
    } catch (\Exception $e) {
        Log::error('Test - Failed to create user', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Failed to create user',
            'error' => $e->getMessage(),
        ], 500);
    }
});
