<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class TaskController extends Controller
{
    use AuthorizesRequests;

    protected TaskService $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    public function index()
    {
        $user = Auth::user();
        $tasks = $this->taskService->getTasksForUser($user);

        $assignedToYouCount = $tasks->where('assigned_to', $user->id)->count();
        $createdByYouCount = $tasks->where('created_by', $user->id)->count();

        return view('tasks.index', compact('tasks', 'assignedToYouCount', 'createdByYouCount'));
    }

    public function create()
    {
        $assignableUsers = $this->taskService->getAssignableUsers(Auth::user());
        return view('tasks.create', compact('assignableUsers'));
    }

    public function store(StoreTaskRequest $request)
    {
        $this->taskService->createTask($request->validated(), Auth::user());
        return redirect()->route('tasks.index')->with('success', 'Task created successfully.');
    }

    public function show(Task $task)
    {
        $this->authorize('view', $task);
        return view('tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        $this->authorize('update', $task);
        $assignableUsers = $this->taskService->getAssignableUsers(Auth::user());
        return view('tasks.edit', compact('task', 'assignableUsers'));
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        $this->authorize('update', $task);
        $this->taskService->updateTask($task, $request->validated());
        return redirect()->route('tasks.index')->with('success', 'Task updated.');
    }

    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);
        $this->taskService->deleteTask($task);
        return redirect()->route('tasks.index')->with('success', 'Task deleted.');
    }

    public function complete(Task $task)
    {
        $this->authorize('complete', $task);
        $this->taskService->completeTask($task);
        return redirect()->back()->with('success', 'Task marked as completed.');
    }
}

