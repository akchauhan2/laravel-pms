<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->except(['index', 'show']);
    }

    private function truncatedescription($projects)
    {
        foreach ($projects as $project) {
            if (isset($project->description) && strlen($project->description) > 400) {
                $project->description = substr($project->description, 0, 400) . '...';
            }
        }
        return $projects;
    }

    public function index()
    {
        $projects = Project::with('creator:id,email,name,avatar')->get();
        $projects = $this->truncatedescription($projects);
        return response()->json(['successFlag' => true, "responseList" => $projects]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $user = Auth::guard('api')->user(); // Ensure using 'api' guard
        if ($user === null) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        $project = new Project();
        $project->name = $request->name;
        $project->description = $request->description;
        $project->status = $request->status;
        $project->start_date = $request->start_date;
        $project->end_date = $request->end_date;
        $project->created_by = $user->id; // Ensure $user is not null before accessing id

        $project->save();

        return response()->json(['successFlag' => true, 'message' => 'Created Successfully', 'data' => $project], 201);
    }

    public function show($id)
    {
        $project = Project::with('creator:id,email,name,avatar')->findOrFail($id);
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
