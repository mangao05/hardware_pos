<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Traits\ResponseFormatter;
use App\Http\Requests\CreateUserRequest;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    use ResponseFormatter;
    public function index(Request $request)
    {
        try {
            $users = User::query();

            // Check if soft-deleted users should be included
            if ($request->include_deleted) {
                $users->withTrashed();
            }

            return response()->json([
                'data' => $users->with('roles.role')->paginate(request()->get('per_page') ?? 10),
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

            $validator = Validator::make($request->all(), [
                'username'   => 'sometimes|string|max:255|unique:users,username,' . $id,
                'password'   => 'nullable|string|min:6|confirmed', // Use password_confirmation field for matching
                'firstname'  => 'sometimes|string|max:255',
                'lastname'   => 'sometimes|string|max:255',
                'is_active'  => 'sometimes|boolean',
                'role'       => 'sometimes|exists:roles,id',
                'newPassword' => 'sometimes|string|min:6',
                'confirmPassword' => 'sometimes|string|same:newPassword',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => $validator->errors()->first(),
                    'code' => 422
                ], 422);
            }

            $userData = $request->only(['username', 'firstname', 'lastname', 'is_active']);

            if ($request->filled('newPassword')) {
                $userData['password'] = Hash::make($request->newPassword);
            }
           
            if (!empty($userData)) {
                $user->update($userData);
            }

            if ($request->has('role')) {
                $user->roles()->updateOrCreate(
                    ['user_id' => $user->id],
                    ['role_id' => $request->role]
                );
            }

            // Reload updated user data
            $user->load('roles.role');

            return response()->json([
                'data' => $user,
                'code' => 200,
                'message' => 'User updated successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Something went wrong: ' . $e->getMessage(),
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
