<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get incomplete tasks ordered by creation date (oldest first)
        $incompleteTasks = $user->tasks()
            ->where('status', 'incomplete')
            ->orderBy('created_at', 'asc')
            ->get();

        // Get complete tasks ordered by completion date (most recent first)
        $completeTasks = $user->tasks()
            ->where('status', 'complete')
            ->orderBy('completed_at', 'desc')
            ->get();

        return view('tasks.index', compact('incompleteTasks', 'completeTasks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tasks.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Auth::user()->tasks()->create($validated);

        return redirect()->route('dashboard')->with('success', 'Task created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        $this->authorize('view', $task);
        return view('tasks.show', compact('task'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        $this->authorize('update', $task);
        return view('tasks.edit', compact('task'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $task->update($validated);

        return redirect()->route('dashboard')->with('success', 'Task updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);
        
        $task->delete();

        return redirect()->route('dashboard')->with('success', 'Task deleted successfully!');
    }

    /**
     * Toggle task completion status.
     */
    public function toggleComplete(Task $task)
    {
        $this->authorize('update', $task);

        if ($task->isComplete()) {
            $task->markAsIncomplete();
        } else {
            $task->markAsComplete();
        }

        return redirect()->route('dashboard');
    }
}
