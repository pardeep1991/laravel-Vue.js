<?php

namespace App\Http\Controllers;

use App\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index() {
        return Task::with('keyword')->orderBy('created_at', 'desc')->paginate(10);
    }

    public function store(Request $request) {
        $task = Task::create($request->all());
        return response()->json($task, 201);
    }

    public function update(Request $request, Task $task) {
        $task->update($request->all());
        return response()->json($task, 200);
    }

    public function delete(Request $request, Task $task) {
        $task->delete();
        return response()->json(null, 204);
    }
}
