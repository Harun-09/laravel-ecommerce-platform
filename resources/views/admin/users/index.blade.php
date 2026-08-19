@extends('admin.layouts.app')

@section('content')
    <div class="page-header">
        <div>
            <h1>Users</h1>
            <div class="breadcrumb">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                <span>Users</span>
            </div>
        </div>
        @can('create users')
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add User
            </a>
        @endcan
    </div>

    <!-- Filters -->
    <div class="card" style="margin-bottom: 24px;">
        <div class="card-body" style="padding: 16px 24px;">
            <form action="{{ route('admin.users.index') }}" method="GET"
                style="display: flex; gap: 16px; flex-wrap: wrap; align-items: flex-end;">
                <div style="flex: 1; min-width: 200px;">
                    <label style="font-size: 13px; font-weight: 500; margin-bottom: 4px; display: block;">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Name or email"
                        value="{{ request('search') }}">
                </div>
                <div style="width: 150px;">
                    <label style="font-size: 13px; font-weight: 500; margin-bottom: 4px; display: block;">Role</label>
                    <select name="role" class="form-control">
                        <option value="">All Roles</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                                {{ ucfirst($role->name) }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="width: 150px;">
                    <label style="font-size: 13px; font-weight: 500; margin-bottom: 4px; display: block;">Status</label>
                    <select name="status" class="form-control">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="banned" {{ request('status') == 'banned' ? 'selected' : '' }}>Banned</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php $isSuperAdmin = auth()->user()->hasRole('super-admin'); @endphp
                    @forelse($users as $user)
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div
                                        style="width: 40px; height: 40px; background: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600;">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <a href="{{ route('admin.users.show', $user) }}" style="font-weight: 500; color: var(--primary); text-decoration: none;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">{{ $user->name }}</a>
                                </div>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->phone ?? '-' }}</td>
                            <td>
                                @foreach($user->roles as $role)
                                    <span class="badge badge-info">{{ ucfirst($role->name) }}</span>
                                @endforeach
                            </td>
                            <td>
                                @php
                                    $statusClass = match ($user->status) {
                                        'active' => 'success',
                                        'inactive' => 'secondary',
                                        'banned' => 'danger',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge badge-{{ $statusClass }}">{{ ucfirst($user->status) }}</span>
                            </td>
                            <td style="color: #64748b; font-size: 13px;">{{ $user->created_at->format('M d, Y') }}</td>
                            <td>
                                <div style="display: flex; gap: 8px;">
                                    @php $isPrivilegedTarget = $user->hasAnyRole(['super-admin', 'admin']); @endphp

                                    @can('edit users')
                                        @if($isSuperAdmin || !$isPrivilegedTarget)
                                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif
                                    @endcan

                                    @can('delete users')
                                        @if($user->id !== auth()->id() && ($isSuperAdmin || !$isPrivilegedTarget))
                                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                                onsubmit="return confirm('Are you sure?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; color: #64748b; padding: 40px;">No users found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div style="padding: 16px 24px; border-top: 1px solid #e2e8f0;">
                {{ $users->withQueryString()->links() }}
            </div>
        @endif
    </div>
@endsection
