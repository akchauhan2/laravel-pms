public function index()
{
$projects = Project::with('tasks', 'users')->get();
return response()->json($projects);
}

public function store(Request $request)
{
$project = Project::create($request->all());
return response()->json($project, 201);
}