<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewClientCreated;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where('role', 'client')->latest()->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'client',
            'is_active' => true,
        ]);

        // Send email with credentials
        try {
            Mail::to($user->email)->send(new NewClientCreated($user, $request->password));
        } catch (\Exception $e) {
            // Log error or ignore if mail fails in dev
        }

        return redirect()->route('admin.users.index')->with('success', 'Client created successfully and notified.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->is_active = $request->has('is_active');
        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'Client updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->invoices()->exists()) {
            return redirect()->route('admin.users.index')->with('error', 'Cannot delete client with existing invoices.');
        }
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Client deleted successfully.');
    }
}
