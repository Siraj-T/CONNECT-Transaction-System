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
                            <img src="{{ $user->avatar_url }}" alt="" style="width: 32px; height: 32px; border-radius: 50%;">
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
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div style="margin-top: 16px;">
        {{ $users->links() }}
    </div>
</div>
@endsection
