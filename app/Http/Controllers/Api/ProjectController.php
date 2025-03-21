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
                'deadline' => 'nullable|date'
            ]);

            $project=Project::create($validated);

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

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $project,
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
                'user_id' => 'required|integer|exists:users,id',
            ]);

            if ($project->users()->where('user_id', $validated['user_id'])->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur déjà attaché à ce projet',
                ], 409);
            }

            $project->users()->attach($validated['user_id']);

            return response()->json([
                'success' => true,
                'message' => 'Utilisateur ajouté avec succès',
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
        //
    }
}
