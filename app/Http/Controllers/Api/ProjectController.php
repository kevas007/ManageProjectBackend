<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use Symfony\Component\HttpFoundation\JsonResponse;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $projects = Project::with(['state','users'])->get();
            return response()->json([
                'message' => 'get all  projects',
                'data'   => $projects,
            ], 200);
        }
        catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
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
    public function store(Request $request): JsonResponse
    {
        try {
            $validated =  $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'deadline' => 'nullable|date',
                'state_id' => 'required|exists:states,id',
                'users' => 'nullable|array', // 👈 facultatif mais accepté
                'users.*' => 'integer|exists:users,id', // 👈 chaque user doit exister
            ]);

           // $project=Project::create($validated);
            // Création du projet
            $project = Project::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'deadline' => $validated['deadline'] ?? null,
                'state_id' => $validated['state_id'],
            ]);

            // Liaison des utilisateurs si fournis
            if (isset($validated['users'])) {
                $project->users()->sync($validated['users']);
            }

            // Retourne projet + relations (optionnel)
            $project->load('users');

            return response()->json([
                'success' => true,
                'message' => 'Création réussie',
                'data' => $project,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }

        catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        try {
            $show= $project->load(['state','users']);
            return response()->json([
                'success' => true,
                'data' => $show,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project) :JsonResponse
    {
        try {
            $validated =  $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'deadline' => 'nullable|date',
                'state_id'=>'required|integer',
            ]);
            $project->update($validated);


            return response()->json([
                'success' => true,
                'message' => 'Création réussie',
                'data' => $project
            ], 201);
        }
        catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function attachUser(Request $request, Project $project)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'nullable|array',
                'user_id.*' => 'integer|exists:users,id',
            ]);

            // sync remplace tous les users liés au projet : c'est ce qu'on veut !
            $project->users()->sync($validated['user_id']);

            return response()->json([
                'success' => true,
                'message' => 'Liste des utilisateurs synchronisée avec succès.',
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        try {
            $project->delete();
            return response()->json([
                'success' => true,
            ]);
        }
        catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }

}
