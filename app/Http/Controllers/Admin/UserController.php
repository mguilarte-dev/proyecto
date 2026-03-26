<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use App\Mail\NewPasswordGenerated;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(10);
        return Inertia::render('Admin/Users/Index', [
            'users' => $users
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/Users/Create');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return Inertia::render('Admin/Users/Edit', [
            'user' => $user
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => ['required', Rule::in(['admin', 'gerente', 'empleado'])],
            'department' => 'nullable|string|max:100',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'department' => $request->department,
        ]);

        return redirect()->route('admin.users.index')->with('message', 'Usuario creado exitosamente.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', Rule::in(['admin', 'gerente', 'empleado'])],
            'department' => 'nullable|string|max:100',
        ]);

        $user->update($request->only('name', 'email', 'role', 'department'));

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('admin.users.index')->with('message', 'Usuario actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->withErrors(['message' => 'No puedes eliminarte a ti mismo.']);
        }

        $user->delete();
        return redirect()->route('admin.users.index')->with('message', 'Usuario eliminado exitosamente.');
    }

    /**
     * Generate a new random password for the user and send it via email.
     */
    public function resetPassword(User $user)
    {
        $newPassword = Str::random(12); // Generate random 12-character password

        $user->update([
            'password' => Hash::make($newPassword)
        ]);

        // Send email with new password
        Mail::to($user->email)->send(new NewPasswordGenerated($user, $newPassword));

        return redirect()->back()->with('message', 'Nueva contraseña generada y enviada por email al usuario.');
    }

    /**
     * Update user email.
     */
    public function updateEmail(Request $request, User $user)
    {
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        ]);

        $user->update(['email' => $request->email]);

        return redirect()->back()->with('message', 'Email del usuario actualizado exitosamente.');
    }
}
