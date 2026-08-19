<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view users')->only(['index', 'show']);
        $this->middleware('can:create users')->only(['create', 'store']);
        $this->middleware('can:edit users')->only(['edit', 'update']);
        $this->middleware('can:delete users')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $actor = auth()->user();
        $query = User::with('roles');

        if (!$this->isSuperAdmin($actor)) {
            $query->whereDoesntHave('roles', function ($builder) {
                $builder->whereIn('name', ['super-admin', 'admin']);
            });
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('role')) {
            $requestedRole = (string) $request->role;
            if (!in_array($requestedRole, $this->assignableRoleNames($actor), true) && !$this->isSuperAdmin($actor)) {
                $query->whereRaw('1 = 0');
            } else {
                $query->role($requestedRole);
            }
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->latest()->paginate(20);
        $roles = Role::query()->whereIn('name', $this->assignableRoleNames($actor))->get();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::query()->whereIn('name', $this->assignableRoleNames(auth()->user()))->get();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $actor = auth()->user();
        $assignableRoles = $this->assignableRoleNames($actor);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', Rule::in($assignableRoles)],
            'status' => 'required|in:active,inactive,banned',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'status' => $request->status,
            'email_verified_at' => now(),
        ]);

        $user->assignRole($request->role);

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        $actor = auth()->user();
        if (!$this->canManageUser($actor, $user)) {
            abort(403, 'You are not allowed to view this user.');
        }

        $user->load(['roles', 'addresses', 'vendor']);

        // Order stats
        $orderStats = [
            'total' => $user->orders()->count(),
            'pending' => $user->orders()->where('status', 'pending')->count(),
            'delivered' => $user->orders()->where('status', 'delivered')->count(),
            'cancelled' => $user->orders()->where('status', 'cancelled')->count(),
            'total_spent' => $user->orders()->where('payment_status', 'paid')->sum('total'),
        ];

        // Recent orders
        $recentOrders = $user->orders()
            ->with('vendor')
            ->latest()
            ->take(10)
            ->get();

        // Recent reviews
        $recentReviews = $user->reviews()
            ->with('product')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.users.show', compact('user', 'orderStats', 'recentOrders', 'recentReviews'));
    }

    public function edit(User $user)
    {
        $actor = auth()->user();
        if (!$this->canManageUser($actor, $user)) {
            abort(403, 'You are not allowed to manage this user.');
        }

        $roles = Role::query()->whereIn('name', $this->assignableRoleNames($actor))->get();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $actor = auth()->user();
        if (!$this->canManageUser($actor, $user)) {
            abort(403, 'You are not allowed to manage this user.');
        }

        $assignableRoles = $this->assignableRoleNames($actor);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
            'role' => ['required', Rule::in($assignableRoles)],
            'status' => 'required|in:active,inactive,banned',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'status' => $request->status,
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        $user->syncRoles([$request->role]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $actor = auth()->user();

        if ($user->id === $actor->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        if (!$this->canManageUser($actor, $user)) {
            return back()->with('error', 'You are not allowed to delete this user.');
        }

        if ($user->hasRole('super-admin') && User::role('super-admin')->count() <= 1) {
            return back()->with('error', 'Cannot delete the last super-admin account.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }

    private function assignableRoleNames(User $actor): array
    {
        if ($this->isSuperAdmin($actor)) {
            return Role::query()->pluck('name')->toArray();
        }

        return ['customer', 'vendor'];
    }

    private function isSuperAdmin(User $user): bool
    {
        return $user->hasRole('super-admin');
    }

    private function canManageUser(User $actor, User $target): bool
    {
        if ($this->isSuperAdmin($actor)) {
            return true;
        }

        return !$target->hasAnyRole(['super-admin', 'admin']);
    }
}
