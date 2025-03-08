<?php

namespace App\Http\Controllers;

use App\Models\Discussion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DiscussionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->except(['index', 'show']);
    }

    public function index()
    {
        return response()->json(Discussion::with(['project', 'user'])->get());
    }

    public function getDiscussionsByProject($projectId)
    {
        // Fetch discussions related to the specific project
        $discussions = Discussion::with(['user'])
            ->where('project_id', $projectId)
            ->get();

        return response()->json($discussions);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'content' => 'required|string',
            'project_id' => 'required|exists:projects,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $data = $request->all();
        if (isset($data['id'])) {
            $discussion = Discussion::findOrFail($data['id']);
            $discussion->update($data);
            return response()->json(['successFlag' => true, 'message' => 'Updated Successfully', 'data' => $discussion]);
        } else {
            $discussion = Discussion::create($data);
            return response()->json(['successFlag' => true, 'message' => 'Created Successfully', 'data' => $discussion], 201); // 201 Created
        }
    }

    public function show($id)
    {
        return response()->json(Discussion::with(['project', 'user'])->findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $discussion = Discussion::findOrFail($id);
        $discussion->update($request->all());
        return response()->json(['successFlag' => true, 'message' => 'Updated Successfully', 'data' => $discussion]);
    }

    public function destroy($id)
    {
        $discussion = Discussion::findOrFail($id);
        $discussion->delete();
        return response()->json(['successFlag' => true, 'message' => 'Deleted Successfully', 'data' => null], 200); // 204 No Content
    }
}
