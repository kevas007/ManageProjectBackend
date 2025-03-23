<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\Support\Str;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     */
    public function register(Request $request): JsonResponse
    {
        try {
            // Validate data with custom messages
            $data = $request->validate([
                'name'     => ['required', 'string', 'max:255'],
                'email'    => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
                'password' => ['required', Rules\Password::defaults()],
            ], [
                'email.unique' => 'The email address is already in use.',
            ]);

            // Normalize email to lowercase
            $data['email'] = strtolower($data['email']);

            // Retrieve the default role (adjust based on your business logic)
            $role = Role::first();
            if (!$role) {
                return response()->json(['error' => 'No role defined.'], 500);
            }

            // Create the user
            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'role_id'  => $role->id,
                'password' => Hash::make($data['password']),
            ]);

            // Fire the Registered event (useful for sending a confirmation email, etc.)
            event(new Registered($user));

            // Create the authentication token
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'User created successfully.',
                'user'    => $user,
                'token'   => $token,
            ], 201);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    /**
     * Authenticate an existing user.
     */
    public function login(Request $request): JsonResponse
    {
        try {
            // Validate credentials
            $credentials = $request->validate([
                'email'    => ['required', 'string', 'email', 'max:255'],
                'password' => ['required', 'string'],
            ]);

            // Normalize email to lowercase
            $credentials['email'] = strtolower($credentials['email']);

            // Retrieve the user by email
            $user = User::where('email', $credentials['email'])->first();
            if (!$user || !Hash::check($credentials['password'], $user->password)) {
                return response()->json(['error' => 'Invalid credentials.'], 401);
            }

            // Create the authentication token
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Login successful.',
                'user'    => $user,
                'token'   => $token,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    /**
     * Update user information.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $authUser = Auth::user();
            if (!$authUser) {
                return response()->json(['error' => 'Unauthenticated user.'], 401);
            }

            // Only an admin or the user themself can update the profile
            if ($authUser->role_id != 1 && $authUser->id != $id) {
                return response()->json(['error' => 'You are not authorized to update this user.'], 403);
            }

            // Define the user to update
            $userToUpdate = $authUser->role_id == 1 ? User::findOrFail($id) : $authUser;

            // Define validation rules based on the role
            $rules = [
                'name'     => ['required', 'string', 'max:255'],
                'email'    => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($userToUpdate->id)],
                'password' => ['nullable', Rules\Password::defaults()],
            ];
            if ($authUser->role_id == 1) {
                $rules['role_id'] = ['required'];
            }

            $data = $request->validate($rules, [
                'email.unique' => 'The email is already used by another user.',
            ]);

            // Normalize email to lowercase
            $data['email'] = strtolower($data['email']);

            // Update user information
            $userToUpdate->name  = $data['name'];
            $userToUpdate->email = $data['email'];
            if ($authUser->role_id == 1 && isset($data['role_id'])) {
                $userToUpdate->role_id = $data['role_id'];
            }
            if (!empty($data['password'])) {
                $userToUpdate->password = Hash::make($data['password']);
            }
            $userToUpdate->save();

            return response()->json([
                'message' => 'User updated successfully.',
                'user'    => $userToUpdate,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    /**
     * Soft delete a user.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $authUser = Auth::user();
            if (!$authUser) {
                return response()->json(['error' => 'Unauthenticated user.'], 401);
            }

            // Only an admin or the user themself can delete the account
            if ($authUser->role_id != 1 && $authUser->id != $id) {
                return response()->json(['error' => 'You are not authorized to delete this user.'], 403);
            }

            $userToDelete = User::findOrFail($id);
            $userToDelete->delete(); // Soft delete (ensure the User model uses SoftDeletes)

            return response()->json([
                'message' => 'User deleted successfully.',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    /**
     * Log out a user by revoking their token.
     */
    public function logout(Request $request, string $id): JsonResponse
    {
        try {
            $authUser = Auth::user();
            if (!$authUser) {
                return response()->json(['error' => 'Unauthenticated usersss.'], 401);
            }

            // Only an admin or the user themself can log out the account
            if ($authUser->role_id != 1 && $authUser->id != $id) {
                return response()->json(['error' => 'You are not authorized to log out this user.'], 403);
            }

            $userToLogout = User::findOrFail($id);
            if ($authUser->id == $userToLogout->id) {
                // If the user is logging out themself, revoke only the current token
                $authUser->currentAccessToken()->delete();
            } else {
                // An admin can revoke all tokens for the target user
                $userToLogout->tokens()->delete();
            }

            return response()->json([
                'message' => 'Logout successful.',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    /**
     * Retrieve the list of users (admin access only).
     */
    public function allUser(): JsonResponse
    {
        try {
            //$authUser = Auth::user();
           // if (!$authUser) {
            //      return response()->json(['error' => 'Unauthenticated user.'], 401);
            //   }

            // Verify that the user has an admin role (here role_id 1 or 2, adjust according to your rules)
           // if (!in_array($authUser->role_id, [1, 2])) {
            //       return response()->json(['error' => 'Access denied.'], 403);
            //     }

            $users = User::with((['roles:id,name']))
                ->get();

            return response()->json([
                'message' => 'List of users',
                'data'   => $users,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

}
