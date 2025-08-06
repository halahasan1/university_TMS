<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\UserService;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserStoreRequest;
use App\Http\Requests\User\UserUpdateRequest;

class UserController extends Controller
{
    public function __construct(protected UserService $userService) {}

    public function index(Request $request)
    {
        $query = User::with('roles');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
            });
        }

        if ($request->filled('role')) {
            $role = $request->input('role');
            $query->whereHas('roles', fn($q) => $q->where('name', $role));
        }

        $users = $query->paginate(10)->appends($request->query());
        $roles = Role::pluck('name');

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function show($id)
    {
        $user = $this->userService->find($id);
        return view('admin.users.show', compact('user'));
    }

    public function create()
    {
        $roles = Role::pluck('name');
        return view('admin.users.create', compact('roles'));
    }

    public function store(UserStoreRequest $request)
    {
        $this->userService->create($request->validated());
        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function edit($id)
    {
        $user = $this->userService->find($id);
        $roles = Role::pluck('name');
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(UserUpdateRequest $request, $id)
    {
        $this->userService->update($id, $request->validated());
        return redirect()->route('admin.users.index')->with('success', 'User updated.');
    }

    public function destroy($id)
    {
        $this->userService->delete($id);
        return redirect()->route('admin.users.index')->with('success', 'User deleted.');
    }
}
