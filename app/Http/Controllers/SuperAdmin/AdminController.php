<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $admins = User::where('role_id', 2)->get(); // 2 = Admin
        return view('superadmin.admins.index', compact('admins'));
    }


    // Formulaire de création
    public function create()
    {
        return view('superadmin.admins.create');
    }

    // Enregistrement d’un nouvel admin
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role_id' => 2, // Admin
            'is_approved' => true,
        ]);

        return redirect()->route('superadmin.users.index')->with('success', 'Admin créé avec succès.');
    }

    // Affichage d’un admin
    public function show($id)
    {
        $admin = User::findOrFail($id);
        return view('superadmin.admins.show', compact('admin'));
    }

    // Formulaire de modification
    public function edit($id)
    {
        $admin = User::findOrFail($id);
        return view('superadmin.admins.edit', compact('admin'));
    }

    // Mise à jour
    public function update(Request $request, $id)
    {
        $admin = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $admin->id,
        ]);

        $admin->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return redirect()->route('superadmin.users.index')->with('success', 'Admin mis à jour.');
    }

    // Suppression
    public function destroy($id)
    {
        $admin = User::findOrFail($id);
        $admin->delete();

        return redirect()->route('superadmin.users.index')->with('success', 'Admin supprimé.');
    }
    
    public function assignRole(Request $request, $id)
    {
        $admin = User::findOrFail($id);
        $admin->function = $request->input('function');
        $admin->save();

        return redirect()->back()->with('success', 'Fonction attribuée avec succès.');
    }

    public function approve($id)
    {
        // ...
    }

    public function reject($id)
    {
        // ...
    }
}
