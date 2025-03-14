<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Task;
use App\Models\BugTicket;

class DashboardController extends Controller
{
    public function getDashboardData()
    {
        $projectOverview = [
            'active' => Project::where('status', 'active')->count() + Project::where('status', 'in_progress')->count(),
            'completed' => Project::where('status', 'completed')->count(),
            'on_hold' => Project::where('status', 'on_hold')->count(),
        ];

        $taskSummary = [
            'pending' => Task::where('status', 'pending')->count(),
            'in_progress' => Task::where('status', 'in_progress')->count(),
            'completed' => Task::where('status', 'completed')->count(),
        ];

        $bugSummary = [
            'open' => BugTicket::where('status', 'open')->count(),
            'in_progress' => BugTicket::where('status', 'in_progress')->count(),
            'resolved' => BugTicket::where('status', 'resolved')->count(),
        ];

        return response()->json([
            'successFlag' => true,
            'project_overview' => $projectOverview,
            'task_summary' => $taskSummary,
            'bug_summary' => $bugSummary,
        ]);
    }
}
