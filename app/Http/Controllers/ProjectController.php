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
        $projects = Project::with('creator')
            ->withCount(['tasks', 'tasks as completed_tasks_count' => function ($query) {
                $query->where('status', 'completed');
            }])->get();

        $projects = $this->truncatedescription($projects);

        return response()->json([
            'successFlag' => true,
            'responseList' => $projects->map(function ($project) {
                return [
                    'id' => $project->id,
                    'name' => $project->name,
                    'description' => $project->description,
                    'status' => $project->status,
                    'start_date' => $project->start_date,
                    'end_date' => $project->end_date,
                    'created_by' => $project->created_by,
                    'creator' => $project->creator, // Include creator details
                    'totalTasks' => $project->tasks_count,
                    'completedTasks' => $project->completed_tasks_count,
                ];
            }),
        ]);
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
        $project = Project::with('creator')->findOrFail($id);
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

    public function search(Request $request)
    {
        $query = Project::query();

        if ($request->has('created_by')) {
            $query->where('created_by', $request->input('created_by'));
        }

        if ($request->has('created_at')) {
            $query->whereDate('created_at', $request->input('created_at'));
        }

        if ($request->has('due_on')) {
            $query->whereDate('end_date', $request->input('due_on'));
        }

        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        $projects = $query->get();

        return response()->json($projects);
    }
}
