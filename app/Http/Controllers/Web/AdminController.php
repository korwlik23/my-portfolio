<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    public function __construct(private AuditLogger $auditLogger) {}

    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('status', 'active')->count(),
            'banned_users' => User::where('status', 'banned')->count(),
            'total_roles' => Role::count(),
        ];

        $recentUsers = User::with('roles')->latest()->limit(8)->get();

        return view('admin.dashboard', compact('stats', 'recentUsers'));
    }

    public function users(Request $request)
    {
        $query = User::with('roles')->orderBy('name');

        if ($request->filled('role')) {
            $query->role($request->role);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->paginate(30)->withQueryString();
        $roles = Role::orderBy('name')->get();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function createUser()
    {
        $roles = Role::orderBy('name')->get();

        return view('admin.users.create', compact('roles'));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'birth_date' => 'nullable|date|before:today',
            'password' => 'required|string|min:8',
            'role' => ['required', Rule::exists('roles', 'name')->where('guard_name', 'web')],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'birth_date' => $request->birth_date,
            'password' => bcrypt($request->password),
            'role_type' => 'system',
            'status' => 'active',
        ]);

        $user->assignRole($request->role);
        $this->auditLogger->log('user.created', $user, "Created user: {$user->email}", [], [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $request->role,
        ]);

        return redirect('/admin/users')->with('success', __('messages.create_success'));
    }

    public function editUser(User $user)
    {
        $roles = Role::orderBy('name')->get();

        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function updateUser(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'birth_date' => 'nullable|date|before:today',
            'role' => ['required', Rule::exists('roles', 'name')->where('guard_name', 'web')],
            'status' => ['required', Rule::in(['active', 'inactive', 'banned'])],
        ]);

        $oldValues = [
            'user' => $user->only(['name', 'email', 'birth_date', 'status']),
            'roles' => $user->getRoleNames()->all(),
        ];

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'birth_date' => $data['birth_date'] ?? null,
            'status' => $data['status'],
            'ban_reason' => $data['status'] === 'banned' ? $user->ban_reason : null,
            'banned_until' => $data['status'] === 'banned' ? $user->banned_until : null,
        ]);
        $user->syncRoles([$data['role']]);

        $this->auditLogger->log('user.updated', $user, "Updated user: {$user->email}", $oldValues, [
            'user' => $user->fresh()->only(['name', 'email', 'birth_date', 'status']),
            'roles' => [$data['role']],
        ]);

        return redirect()->route('admin.users')->with('success', __('messages.update_success'));
    }

    public function destroyUser(User $user)
    {
        abort_if($user->id === auth()->id(), 403, __('messages.cannot_delete_self'));

        $oldValues = [
            'user' => $user->only(['name', 'email', 'status']),
            'roles' => $user->getRoleNames()->all(),
        ];

        $this->auditLogger->log('user.deleted', $user, "Deleted user: {$user->email}", $oldValues);
        $user->delete();

        return redirect()->route('admin.users')->with('success', __('messages.delete_success'));
    }

    public function banUser(Request $request, User $user)
    {
        abort_if($user->id === auth()->id(), 403, __('messages.cannot_delete_self'));

        $request->validate([
            'reason' => 'required|string|max:500',
            'duration' => 'required|string',
        ]);

        $bannedUntil = $request->duration === 'permanent'
            ? null
            : now()->addMinutes((int) $request->duration);
        $oldValues = $user->only(['status', 'ban_reason', 'banned_until']);

        $user->update([
            'status' => \App\Enums\UserStatus::Banned,
            'ban_reason' => $request->reason,
            'banned_until' => $bannedUntil,
        ]);
        $this->auditLogger->log('user.banned', $user, "Banned user: {$user->email}", $oldValues, $user->only(['status', 'ban_reason', 'banned_until']));

        return redirect()->back()->with('success', __('messages.ban_success') ?? 'User banned successfully.');
    }

    public function unbanUser(User $user)
    {
        $oldValues = $user->only(['status', 'ban_reason', 'banned_until']);
        $user->unban();
        $this->auditLogger->log('user.unbanned', $user, "Unbanned user: {$user->email}", $oldValues, $user->only(['status', 'ban_reason', 'banned_until']));

        return redirect()->back()->with('success', __('messages.unban_success') ?? 'User unbanned successfully.');
    }

    public function resetUserPassword(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user->update([
            'password' => bcrypt($request->password),
        ]);
        $this->auditLogger->log('user.password_reset', $user, "Reset password for user: {$user->email}");

        return redirect()->back()->with('success', __('messages.password_reset_success') ?? 'Password reset successfully.');
    }
}
