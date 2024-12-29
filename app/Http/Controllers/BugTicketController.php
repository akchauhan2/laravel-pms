<?php
// app/Http/Controllers/BugTicketController.php
// app/Http/Controllers/BugTicketController.php

namespace App\Http\Controllers;

use App\Models\BugTicket;
use Illuminate\Http\Request;

class BugTicketController extends Controller
{
    // Get all bug tickets
    public function index()
    {
        $bugTickets = BugTicket::with(['project', 'assignedUser'])->get(); // Eager load related models
        return response()->json($bugTickets);
    }

    // Store a new bug ticket
    public function store(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'description' => 'required|string',
            'status' => 'required|string',
            'project_id' => 'required|exists:projects,id', // Validate project_id exists in the projects table
            'assigned_to' => 'nullable|exists:users,id', // Validate assigned_to exists in the users table
            'screenshot' => 'nullable|string',
        ]);

        // Create the bug ticket
        $bugTicket = BugTicket::create($request->all());

        // Return the created bug ticket with 201 status
        return response()->json($bugTicket, 201);
    }

    // Show a single bug ticket by ID
    public function show($id)
    {
        $bugTicket = BugTicket::with(['project', 'assignedUser'])->findOrFail($id); // Eager load related models
        return response()->json($bugTicket);
    }

    // Update an existing bug ticket
    public function update(Request $request, $id)
    {
        $bugTicket = BugTicket::findOrFail($id);

        // Validate incoming request
        $request->validate([
            'description' => 'nullable|string',
            'status' => 'nullable|string',
            'project_id' => 'nullable|exists:projects,id',
            'assigned_to' => 'nullable|exists:users,id',
            'screenshot' => 'nullable|string',
        ]);

        // Update the bug ticket with validated data
        $bugTicket->update($request->all());

        // Return the updated bug ticket
        return response()->json($bugTicket);
    }

    // Delete a bug ticket
    public function destroy($id)
    {
        $bugTicket = BugTicket::findOrFail($id);
        $bugTicket->delete();
        return response()->json(null, 204); // 204 No Content
    }
}
