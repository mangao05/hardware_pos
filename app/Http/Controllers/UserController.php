<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Exception;

class UserController extends Controller
{
    // Display all users, including an option to view soft-deleted users
    public function index(Request $request)
    {
        try {
            $users = User::query();

            // Check if soft-deleted users should be included
            if ($request->include_deleted) {
                $users->withTrashed();
            }

            return response()->json([
                'data' => $users->get(),
                'code' => 200,
                'message' => 'Users retrieved successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => 500
            ], 500);
        }
    }

    // Create a new user
    public function store(Request $request)
    {
        try {
            $user = User::create([
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'is_active' => $request->is_active
            ]);

            // Assign role to the user
            $user->roles()->create(['role_id' => $request->role]);

            return response()->json([
                'data' => $user,
                'code' => 200,
                'message' => 'User created successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => 500
            ], 500);
        }
    }

    // Update an existing user
    public function update(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            $user->update([
                'username' => $request->username,
                'password' => $request->password ? Hash::make($request->password) : $user->password,
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'is_active' => $request->is_active
            ]);

            return response()->json([
                'data' => $user,
                'code' => 200,
                'message' => 'User updated successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => 500
            ], 500);
        }
    }

    // Soft delete a user
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return response()->json([
                'code' => 200,
                'message' => 'User soft-deleted successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => 500
            ], 500);
        }
    }

    // Restore a soft-deleted user
    public function restore($id)
    {
        try {
            $user = User::withTrashed()->findOrFail($id);
            $user->restore();

            return response()->json([
                'data' => $user,
                'code' => 200,
                'message' => 'User restored successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => 500
            ], 500);
        }
    }
}
