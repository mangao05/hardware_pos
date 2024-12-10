<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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
                'data' => $users->with('roles.role')->get(),
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
    public function store(CreateUserRequest $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'username' => 'required|string|unique:users,username',
                'password' => 'required|string|min:8|confirmed',
                'firstname' => 'required|string',
                'lastname' => 'required|string',
                'is_active' => 'required|boolean',
                'role' => 'required|integer|exists:roles,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation errors occurred',
                    'errors' => $validator->errors(),
                    'code' => 422
                ], 422);
            }

            DB::beginTransaction();

            $user = User::create([
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'is_active' => $request->is_active
            ]);

            // Assign role to the user
            $user->roles()->create(['role_id' => $request->role]);
            $user = User::with('roles.role')->find($user->id);

            DB::commit();
            return response()->json([
                'data' => $user,
                'code' => 200,
                'message' => 'User created successfully'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
                'code' => 500
            ], 500);
        }
    }

    public function view($id)
    {
        try {
            $user = User::with('roles.role')->findOrFail($id);
            return response()->json([
                'data' => $user,
                'code' => 200,
                'message' => 'User retrieved successfully'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
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
            $user = User::with('roles.role')->findOrFail($id);

            $userData = [];

            if ($request->has('username')) {
                $userData['username'] = $request->username;
            }

            if ($request->has('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            if ($request->has('firstname')) {
                $userData['firstname'] = $request->firstname;
            }

            if ($request->has('lastname')) {
                $userData['lastname'] = $request->lastname;
            }

            if ($request->has('is_active')) {
                $userData['is_active'] = $request->is_active;
            }

            // Update only the fields that are present in the request
            if (!empty($userData)) {
                $user->update($userData);
            }

            if ($request->has('role')) {
                $user->roles()->updateOrCreate(
                    ['user_id' => $user->id], // Condition to find the current role, assuming a user can only have one role
                    ['role_id' => $request->role] // The values to update or create
                );
            }
            $user = User::with('roles.role')->find($user->id);
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
            DB::beginTransaction();
            $user = User::findOrFail($id);
            $user->delete();

            DB::commit();
            return response()->json([
                'code' => 200,
                'message' => 'User soft-deleted successfully'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
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
            DB::beginTransaction();
            $user = User::withTrashed()->findOrFail($id);
            $user->restore();
            $user = User::with('roles.role')->find($user->id);
            DB::commit();
            return response()->json([
                'data' => $user,
                'code' => 200,
                'message' => 'User restored successfully'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
                'code' => 500
            ], 500);
        }
    }
}
