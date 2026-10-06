@extends('layouts.app')
@section('title', 'User Management')

@section('content')
<div class="page-header">
    <h1 class="page-title">User Management</h1>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">Create User</a>
</div>

<div class="card mb-6" style="display: flex; gap: 8px; padding: 12px 24px;">
    <a href="{{ route('admin.users.index', ['role' => 'all']) }}" class="badge {{ $role === 'all' ? 'badge-primary' : 'text-muted' }}" style="{{ $role === 'all' ? '' : 'background: none;' }}">All Users</a>
    <a href="{{ route('admin.users.index', ['role' => 'admin']) }}" class="badge {{ $role === 'admin' ? 'badge-primary' : 'text-muted' }}" style="{{ $role === 'admin' ? '' : 'background: none;' }}">Admins</a>
    <a href="{{ route('admin.users.index', ['role' => 'reseller']) }}" class="badge {{ $role === 'reseller' ? 'badge-primary' : 'text-muted' }}" style="{{ $role === 'reseller' ? '' : 'background: none;' }}">Resellers</a>
    <a href="{{ route('admin.users.index', ['role' => 'customer']) }}" class="badge {{ $role === 'customer' ? 'badge-primary' : 'text-muted' }}" style="{{ $role === 'customer' ? '' : 'background: none;' }}">Customers</a>
</div>

<div class="card">
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>User</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--color-surface); border: 1px solid var(--color-border); display: flex; justify-content: center; align-items: center;">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--color-text-muted);">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            </div>
                            <div>
                                <div class="font-semibold">{{ $user->name }}</div>
                                <div class="text-xs text-muted">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge" style="background: var(--color-surface-hover); color: var(--color-text-main); border: 1px solid var(--color-border)">
                            {{ ucfirst($user->roles->first()?->name ?? 'None') }}
                        </span>
                    </td>
                    <td>
                        @if($user->is_active)
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-danger">Disabled</span>
                        @endif
                    </td>
                    <td class="text-sm">{{ $user->created_at->format('M d, Y') }}</td>
                    <td>
                        <a href="{{ route('admin.users.edit', $user) }}" class="text-sm font-medium" style="margin-right: 12px;">Edit</a>
                        @if(Auth::id() !== $user->id)
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm font-medium text-danger" style="border: none; background: none; color: var(--color-danger); cursor: pointer;">Delete</button>
                            </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div style="margin-top: 16px;">
        {{ $users->links('pagination.apple') }}
    </div>
</div>
@endsection
