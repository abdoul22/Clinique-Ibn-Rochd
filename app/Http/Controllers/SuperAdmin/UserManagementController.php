<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::whereHas('role', fn($q) => $q->where('name', 'admin'))->get();

        return view('superadmin.users.index', compact('users'));
    }

    public function approve(User $user)
    {
        $user->update(['is_approved' => true]);

        return redirect()->back()->with('success', 'Compte approuvé avec succès.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->back()->with('success', 'Compte supprimé avec succès.');
    }
}
