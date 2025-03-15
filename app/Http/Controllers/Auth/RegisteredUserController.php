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
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function register(Request $request): JsonResponse
    {
        try {
            // Validation avec message personnalisé pour l'unicité de l'email
            $data = $request->validate([
                'name'     => ['required', 'string', 'max:255'],
                'email'    => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
                'password' => ['required', Rules\Password::defaults()],
            ], [
                'email.unique' => 'L\'adresse email est déjà utilisée.',
            ]);

            // Forcer l'email en minuscules
            $data['email'] = strtolower($data['email']);

            // Récupérer le premier rôle
            $role = Role::first();
            if (!$role) {
                return response()->json(['error' => 'Aucun rôle défini.'], 500);
            }

            // Création de l'utilisateur
            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'role_id'  => $role->id,
                'password' => Hash::make($data['password']),
            ]);
            $token = $user->createToken('auth_token')->plainTextToken;
            event(new Registered($user));


            return response()->json([
                'message' => 'Utilisateur créé avec succès.',
                'user'    => $user,
                'token'   => $token,
            ]);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }
    public function login(Request $request): JsonResponse
    {
        // Validation des identifiants
        $credentials = $request->validate([
            'email'    => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string'],
        ]);

        // Recherche de l'utilisateur par email (en normalisant l'email)
        $user = User::where('email', strtolower($credentials['email']))->first();

        // Vérification de l'existence de l'utilisateur et du mot de passe
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json(['error' => 'Identifiants invalides.'], 401);
        }

        // Création du token d'authentification
        $token = $user->createToken('auth_token')->plainTextToken;


        return response()->json([
            'message' => 'Connexion réussie.',
            'user'    => $user,
            'token'   => $token,
        ]);
    }


    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $authUser = Auth::user();
            if (!$authUser) {
                return response()->json(['error' => 'Utilisateur non authentifié.'], 401);
            }

            // Si l'utilisateur n'est pas administrateur, il ne peut modifier que son propre profil.
            if ($authUser->role_id != 1 && $authUser->id != $id) {
                return response()->json(['error' => 'Vous n\'êtes pas autorisé à modifier cet utilisateur.'], 403);
            }

            // Détermine quel utilisateur sera mis à jour
            if ($authUser->role_id == 1) {
                // L'administrateur peut mettre à jour n'importe quel utilisateur
                $userToUpdate = User::findOrFail($id);
                $rules = [
                    'name'     => ['required', 'string', 'max:255'],
                    'email'    => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($userToUpdate->id)],
                    'password' => ['nullable', Rules\Password::defaults()],
                    'role_id'  => ['required']
                ];
            } else {
                // L'utilisateur standard ne peut mettre à jour que son propre profil
                $userToUpdate = $authUser;
                $rules = [
                    'name'     => ['required', 'string', 'max:255'],
                    'email'    => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($userToUpdate->id)],
                    'password' => ['nullable', Rules\Password::defaults()]
                ];
            }

            $data = $request->validate($rules, [
                'email.unique' => "L'email est déjà utilisé par un autre utilisateur.",
            ]);

            // Normalisation de l'email
            $data['email'] = strtolower($data['email']);

            // Mise à jour des informations
            $userToUpdate->name  = $data['name'];
            $userToUpdate->email = $data['email'];
            // Seul l'admin peut mettre à jour le rôle
            if ($authUser->role_id == 1 && isset($data['role_id'])) {
                $userToUpdate->role_id = $data['role_id'];
            }
            if (!empty($data['password'])) {
                $userToUpdate->password = Hash::make($data['password']);
            }

            $userToUpdate->save();

            return response()->json([
                'message' => 'Utilisateur mis à jour avec succès.',
                'user'    => $userToUpdate,
            ]);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $authUser = Auth::user();
            if (!$authUser) {
                return response()->json(['error' => 'Utilisateur non authentifié.'], 401);
            }

            // Seul un administrateur peut supprimer n'importe quel utilisateur,
            // tandis qu'un utilisateur standard ne peut supprimer que son propre compte.
            if ($authUser->role_id != 1 && $authUser->id != $id) {
                return response()->json(['error' => 'Vous n\'êtes pas autorisé à supprimer cet utilisateur.'], 403);
            }

            $userToDelete = User::findOrFail($id);
            $userToDelete->delete();

            return response()->json([
                'message' => 'Utilisateur supprimé avec succès.'
            ]);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

}
