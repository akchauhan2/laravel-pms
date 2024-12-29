<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    // Get all projects
    public function index()
    {
        return response()->json(Project::all());
    }

    // Store a new project
    public function store(Request $request)
    {
        $project = Project::create($request->all());
        return response()->json($project, 201); // 201 Created
    }

    // Show a single project by ID
    public function show($id)
    {
        $project = Project::findOrFail($id);
        return response()->json($project);
    }

    // Update an existing project
    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);
        $project->update($request->all());
        return response()->json($project);
    }

    // Delete a project
    public function destroy($id)
    {
        $project = Project::findOrFail($id);
        $project->delete();
        return response()->json(null, 204); // 204 No Content
    }
}
