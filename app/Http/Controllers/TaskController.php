<?php
// app/Http/Controllers/TaskController.php

namespace App\Http\Controllers;

use App\Models\Task;  // Assuming you have a Task model
use Illuminate\Http\Request;

class TaskController extends Controller
{
    // Get all tasks
    public function index()
    {
        $tasks = Task::with(['project', 'assignedUser'])->get();
        return response()->json([
            'successFlag' => true,
            'responseList' => $tasks
        ]);
    }

    // Store a new task
    public function store(Request $request)
    {
        // Validate that project_id is present
        $request->validate([
            'project_id' => 'required|exists:projects,id'
        ]);

        $task = Task::create($request->all());
        return response()->json($task, 201);
    }

    // Show a single task by ID
    public function show($id)
    {
        $task = Task::with(['project', 'assignedUser'])->findOrFail($id);
        return response()->json($task);
    }

    // Update an existing task
    public function update(Request $request, $id)
    {
        $task = Task::findOrFail($id);
        $task->update($request->all());
        return response()->json($task);
    }

    // Update the status of a task
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string'
        ]);

        $task = Task::findOrFail($id);
        $task->status = $request->input('status');
        $task->save();

        return response()->json($task);
    }

    // Get all tasks by project ID
    public function getTasksByProject($projectId)
    {
        $tasks = Task::where('project_id', $projectId)->with(['project', 'assignedUser'])->get();
        return response()->json([
            'successFlag' => true,
            'responseList' => $tasks
        ]);
    }

    // Delete a task
    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        $task->delete();
        return response()->json(null, 204); // 204 No Content
    }
}
