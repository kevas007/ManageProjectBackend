<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStateRequest;
use App\Http\Requests\UpdateStateRequest;
use App\Models\State;
use Illuminate\Support\Facades\Auth;
use PhpParser\Node\Scalar\String_;
use Symfony\Component\HttpFoundation\JsonResponse;

class StateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() : JsonResponse
    {
        try {
            $states = State::all();
            return response()->json(
                [
                    'message' => 'All states',
                    'data'   => $states,
                ], 200
            );
        }
        catch (\Throwable $th) {
            return response()->json([
                'message' => 'Something went wrong',

            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStateRequest $request): JsonResponse
    {
        try {


         $user = Auth::user();

         if (!in_array($user->role_id, [1, 2])) {
             return response()->json(['error' => 'Access denied.'], 403);
         }
             $state = new State();

            $state->name = $request['name'];
            $state->description = $request['description'];
            $state->save();
            return response()->json([
                'message' => 'State created',
                'state'   => $state,
            ]);
        }
        catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(State $state, String $id): JsonResponse
    {
        try {
            $find = $state::find($id);
            if (!$find) {
                return response()->json([
                    'message' => 'State not found',
                ], 401);
            }
            return response()->json([
                'message' => 'State found',
                'state'   => $find
            ],201);

        }
        catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(State $state)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStateRequest $request, $id): JsonResponse
    {
        try {
            $user = Auth::user();

            if (!in_array($user->role_id, [1, 2])) {
                return response()->json(['error' => 'Access denied.'], 403);
            }

            // Recherche de l'état via son id ou renvoie une exception si non trouvé
            $state = State::findOrFail($id);

            // Récupération des données validées
            $validated = $request->validated();

            // Mise à jour de l'état avec les données validées
            $state->update($validated);

            return response()->json([
                'message' => 'State updated',
                'state'   => $state,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(String $id): JsonResponse
    {
        try {
            $user = Auth::user();


            if (!in_array($user->role_id, [1, 2])) {
                return response()->json(['error' => 'Access denied.'], 403);
            }

            $userToDelete = State::findOrFail($id);
            $userToDelete->delete(); // Soft delete (ensure the User model uses SoftDeletes)

            return response()->json([
                'message' => 'State deleted successfully.',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }
}
