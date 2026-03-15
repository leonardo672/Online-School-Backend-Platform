<?php
// app/Http/Controllers/UserController.php
namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserService;
use App\Services\UserNotificationService;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected UserService $userService;
    protected UserNotificationService $notificationService;

    public function __construct(
        UserService $userService,
        UserNotificationService $notificationService
    ) {
        $this->userService = $userService;
        $this->notificationService = $notificationService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['role', 'search']);
        $users = $this->userService->getPaginatedUsers(10, $filters);
        $roles = User::ROLES;
        
        return view('users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = User::ROLES;
        return view('users.create', compact('roles'));
    }

    public function store(StoreUserRequest $request)
    {
        $user = $this->userService->createUser($request->validated());
        $this->notificationService->sendWelcomeEmail($user);
        
        return redirect()->route('users.index')
            ->with('success', 'User created successfully!'); // Simple and clear
    }

    public function show(string $id)
    {
        $user = User::with('certificates')->findOrFail($id);
        return view('users.show', compact('user'));
    }

    public function edit(string $id)
    {
        $user = $this->userService->find($id);
        $roles = User::ROLES;
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(UpdateUserRequest $request, string $id)
    {
        $user = $this->userService->updateUser($id, $request->validated());
        
        return redirect()->route('users.index')
            ->with('success', 'User updated successfully!'); // Simple and clear
    }

    public function destroy(string $id)
    {
        $user = $this->userService->find($id);
        
        if ($user->certificates()->count() > 0) {
            return redirect()->route('users.index')
                ->with('error', 'Cannot delete user with certificates.'); // Simple error
        }
        
        $this->userService->delete($id);
        
        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully.'); // Simple and clear
    }
}