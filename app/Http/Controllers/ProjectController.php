<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

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
        $projects = Project::all();
        $projects = $this->truncatedescription($projects);
        return response()->json(['successFlag' => true, "responseList" => $projects]);
    }


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
        return response()->json(Project::findOrFail($id));
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
