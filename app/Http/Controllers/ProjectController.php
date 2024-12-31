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

    // Store a new project or update an existing project if ID is present
    public function store(Request $request)
    {
        $data = $request->all();
        if (isset($data['id'])) {
            $project = Project::findOrFail($data['id']);
            $project->update($data);
            return response()->json(['successFlag' => true, 'message' => 'Updated Successfully', 'data' => $project]);
        } else {
            $project = Project::create($data);
            return response()->json(['successFlag' => true, 'message' => 'Created Successfully', 'data' => $project], 201); // 201 Created
        }
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
        return response()->json(['successFlag' => true, 'message' => 'Updated Successfully', 'data' => $project]);
    }

    // Delete a project
    public function destroy($id)
    {
        $project = Project::findOrFail($id);
        $project->delete();
        return response()->json(['successFlag' => true, 'message' => 'Deleted Successfully', 'data' => null], 200); // 204 No Content
    }
}
