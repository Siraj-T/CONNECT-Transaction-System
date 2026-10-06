<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $role = $request->query('role', 'all');
        
        $query = User::with('roles', 'resellerProfile', 'customerProfile');
        
        if ($role !== 'all') {
            $query->role($role);
        }

        $users = $query->paginate(20);
        return view('admin.users.index', compact('users', 'role'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.users.form', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'role'     => 'required|exists:roles,name',
            'phone'    => 'nullable|string|max:20',
        ]);

        $user = User::create([
            'name'              => $validated['name'],
            'email'             => $validated['email'],
            'password'          => Hash::make($validated['password']),
            'phone'             => $validated['phone'],
            'is_active'         => $request->has('is_active'),
            'email_verified_at' => now(),
        ]);

        $user->assignRole($validated['role']);

        if ($validated['role'] === 'reseller') {
            $user->resellerProfile()->create([
                'business_name'   => $request->input('business_name'),
                'commission_rate' => $request->input('commission_rate', 5.00),
                'is_approved'     => $request->has('is_approved'),
            ]);
        } elseif ($validated['role'] === 'customer') {
            $user->customerProfile()->create([
                'national_id' => $request->input('national_id'),
                'address'     => $request->input('address'),
            ]);
        }

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('admin.users.form', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'role'     => 'required|exists:roles,name',
            'phone'    => 'nullable|string|max:20',
        ]);

        $user->update([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'phone'     => $validated['phone'],
            'is_active' => $request->has('is_active'),
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        $user->syncRoles([$validated['role']]);

        if ($validated['role'] === 'reseller') {
            $user->resellerProfile()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'business_name'   => $request->input('business_name'),
                    'commission_rate' => $request->input('commission_rate', 5.00),
                    'is_approved'     => $request->has('is_approved'),
                ]
            );
        } elseif ($validated['role'] === 'customer') {
            $user->customerProfile()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'national_id' => $request->input('national_id'),
                    'address'     => $request->input('address'),
                ]
            );
        }

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Cannot delete yourself.');
        }

        // Check for dependencies to avoid SQL Integrity Constraint Exception
        if ($user->transactions()->exists() || 
            $user->walletLedger()->exists() || 
            $user->generatedVouchers()->exists() || 
            $user->soldVouchers()->exists() || 
            $user->redeemedVouchers()->exists()) {
            
            // Soft-disable the user instead of deleting
            $user->update(['is_active' => false]);
            return redirect()->route('admin.users.index')->with('success', "User has related financial records and cannot be deleted. The account has been deactivated instead.");
        }

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
}
